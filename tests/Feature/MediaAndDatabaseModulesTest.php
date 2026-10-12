<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\DailyLog;
use App\Services\ImageOptimizationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaAndDatabaseModulesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Automated Image Optimization.
     */
    public function test_image_optimization_service_compresses_and_stores_locally(): void
    {
        Storage::fake('local');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_img');
        $gd = imagecreatetruecolor(1800, 1400);
        $blue = imagecolorallocate($gd, 30, 80, 200);
        imagefill($gd, 0, 0, $blue);
        imagejpeg($gd, $tempPath, 100);
        imagedestroy($gd);

        $uploadedFile = new UploadedFile($tempPath, 'sample_photo.jpg', 'image/jpeg', null, true);

        $storedPath = ImageOptimizationService::optimizeAndStore(
            file: $uploadedFile,
            folder: 'private_log_photos',
            disk: 'local',
            maxWidth: 1200,
            maxHeight: 1200,
            quality: 80
        );

        @unlink($tempPath);

        // Verify file stored on private disk
        Storage::disk('local')->assertExists($storedPath);

        // Verify stored image dimensions were constrained to max 1200px
        $storedContent = Storage::disk('local')->get($storedPath);
        $imageInfo = getimagesizefromstring($storedContent);
        $this->assertLessThanOrEqual(1200, $imageInfo[0]);
        $this->assertLessThanOrEqual(1200, $imageInfo[1]);
    }

    /**
     * Test Image Security and Access Control.
     */
    public function test_secure_file_controller_enforces_active_session_authorization(): void
    {
        Storage::fake('local');

        $teacher = User::factory()->create(['role' => 'teacher']);
        $guardian = User::factory()->create(['role' => 'guardian']);
        $otherGuardian = User::factory()->create(['role' => 'guardian']);

        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Special Ed Room 1',
            'date_of_birth' => '2015-05-01',
            'gender' => 'Male',
            'diagnosis' => 'Speech Delay',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
            'status' => 'active',
        ]);

        $filePath = 'private_log_photos/sample.jpg';
        Storage::disk('local')->put($filePath, 'fake-image-binary');

        $log = DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'log_date' => '2026-10-12',
            'quarter' => 'Q1',
            'notes' => 'Great performance today.',
            'image_path' => $filePath,
        ]);

        // 1. Unauthenticated request -> redirects to login
        $response = $this->get(route('secure.log-photo', $log->id));
        $response->assertRedirect(route('login'));

        // 2. Unauthorized user -> 403 Forbidden
        $response = $this->actingAs($otherGuardian)->get(route('secure.log-photo', $log->id));
        $response->assertStatus(403);

        // 3. Authorized Guardian -> 200 OK
        $response = $this->actingAs($guardian)->get(route('secure.log-photo', $log->id));
        $response->assertStatus(200);

        // 4. Authorized Teacher -> 200 OK
        $response = $this->actingAs($teacher)->get(route('secure.log-photo', $log->id));
        $response->assertStatus(200);
    }

    /**
     * Test Two Factor Secret Field Encryption.
     */
    public function test_two_factor_secret_is_encrypted_in_database(): void
    {
        $plainSecret = 'JBSWY3DPEHPK3PXP';
        $user = User::factory()->create([
            'two_factor_secret' => $plainSecret,
        ]);

        // Raw database column must NOT equal the plaintext secret
        $rawSecret = DB::table('users')->where('id', $user->id)->value('two_factor_secret');
        $this->assertNotEquals($plainSecret, $rawSecret);

        // Model attribute cast must automatically decrypt it back
        $freshUser = User::find($user->id);
        $this->assertEquals($plainSecret, $freshUser->two_factor_secret);
    }

    /**
     * Test Automated Database Backup Command.
     */
    public function test_database_backup_command_executes_successfully(): void
    {
        $exitCode = Artisan::call('db:backup');
        $this->assertEquals(0, $exitCode);

        $backups = glob(storage_path('app/backups/dalc_backup_*.sql.gz'));
        $this->assertNotEmpty($backups);
        $this->assertGreaterThan(0, filesize($backups[0]));
    }
}
