<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class GuardianStudentBindingAndSecurityVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $guardianWithStudent;
    protected User $guardianWithoutStudent;
    protected Student $student;
    protected string $secret = 'KRMVDTG2PJTF24LZ'; // Base32 valid secret

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'contact_number' => '09123456780',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'two_factor_secret' => $this->secret,
        ]);

        $this->teacher = User::create([
            'name' => 'Teacher With Permission',
            'username' => 'teachercred',
            'email' => 'teacher@example.com',
            'contact_number' => '09123456781',
            'password' => Hash::make('Password123!'),
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'can_manage_credentials' => true,
            'two_factor_secret' => $this->secret,
        ]);

        $this->guardianWithStudent = User::create([
            'name' => 'Guardian With Child',
            'username' => 'guardianchild',
            'email' => 'guardianchild@example.com',
            'contact_number' => '09123456782',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => false,
        ]);

        $this->guardianWithoutStudent = User::create([
            'name' => 'Guardian Solo',
            'username' => 'guardiansolo',
            'email' => 'guardiansolo@example.com',
            'contact_number' => '09123456783',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => false,
        ]);

        $this->student = Student::create([
            'first_name' => 'Neo',
            'last_name' => 'Sangalang',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Kinder',
            'date_of_birth' => '2018-05-15',
            'gender' => 'male',
            'diagnosis' => 'Speech Delay',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardianWithStudent->id,
            'status' => 'active',
        ]);
    }

    public function test_accounts_view_displays_bound_student_for_guardian(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.accounts.index', ['tab' => 'guardians']));

        $response->assertOk();
        $response->assertSee('Neo Sangalang');
        $response->assertSee('Bound Student:');
        $response->assertSee('No student profile bound');
    }

    public function test_admin_sees_admin_security_verification_label(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.accounts.index', ['tab' => 'guardians']));

        $response->assertOk();
        $response->assertSee('Admin Security Verification');
    }

    public function test_teacher_sees_teacher_security_verification_label(): void
    {
        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.accounts.index', ['tab' => 'guardians']));

        $response->assertOk();
        $response->assertSee('Teacher Security Verification');
        $response->assertDontSee('Admin Security Verification');
    }

    public function test_teacher_can_update_guardian_credentials_with_teacher_mfa(): void
    {
        $google2fa = new Google2FA();
        $validCode = $google2fa->getCurrentOtp($this->secret);

        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->put(route('admin.accounts.update-credentials', $this->guardianWithStudent->id), [
                'username' => 'updatedguardian',
                'email' => 'updatedguardian@example.com',
                'contact_number' => '09987654321',
                'password' => 'NewPassword123!',
                'mfa_code' => $validCode,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->guardianWithStudent->refresh();
        $this->assertEquals('updatedguardian', $this->guardianWithStudent->username);
        $this->assertEquals('updatedguardian@example.com', $this->guardianWithStudent->email);
        $this->assertEquals('09987654321', $this->guardianWithStudent->contact_number);
        $this->assertTrue(Hash::check('NewPassword123!', $this->guardianWithStudent->password));
    }

    public function test_teacher_cannot_update_guardian_credentials_with_invalid_mfa(): void
    {
        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->from(route('admin.accounts.index'))
            ->put(route('admin.accounts.update-credentials', $this->guardianWithStudent->id), [
                'username' => 'failguardian',
                'email' => 'failguardian@example.com',
                'contact_number' => '09987654322',
                'mfa_code' => '000000',
            ]);

        $response->assertRedirect(route('admin.accounts.index'));
        $response->assertSessionHasErrors(['mfa_code']);

        $this->guardianWithStudent->refresh();
        $this->assertNotEquals('failguardian', $this->guardianWithStudent->username);
    }

    public function test_teacher_cannot_update_another_teacher_credentials(): void
    {
        $otherTeacher = User::create([
            'name' => 'Other Teacher',
            'username' => 'otherteacher',
            'email' => 'otherteacher@example.com',
            'contact_number' => '09123456789',
            'password' => Hash::make('Password123!'),
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'two_factor_secret' => $this->secret,
        ]);

        $google2fa = new Google2FA();
        $validCode = $google2fa->getCurrentOtp($this->secret);

        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->put(route('admin.accounts.update-credentials', $otherTeacher->id), [
                'username' => 'hackedteacher',
                'email' => 'hackedteacher@example.com',
                'contact_number' => '09987654323',
                'mfa_code' => $validCode,
            ]);

        $response->assertStatus(403);
    }
}

