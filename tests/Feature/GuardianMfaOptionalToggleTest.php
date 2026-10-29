<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class GuardianMfaOptionalToggleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_guardian_with_normal_signin_can_access_dashboard_without_mfa(): void
    {
        $guardian = User::create([
            'name' => 'Guardian Jane',
            'username' => 'guardianjane',
            'email' => 'jane@example.com',
            'contact_number' => '09123456781',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => false,
            'two_factor_secret' => null,
        ]);

        $response = $this->actingAs($guardian)->get(route('guardian.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('guardian.dashboard');
    }

    public function test_guardian_with_normal_signin_is_redirected_away_from_mfa_setup_and_challenge(): void
    {
        $guardian = User::create([
            'name' => 'Guardian John',
            'username' => 'guardianjohn',
            'email' => 'john@example.com',
            'contact_number' => '09123456782',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => false,
        ]);

        $setupResponse = $this->actingAs($guardian)->get(route('security.mfa.setup'));
        $setupResponse->assertRedirect(route('guardian.dashboard'));

        $challengeResponse = $this->actingAs($guardian)->get(route('security.mfa.challenge'));
        $challengeResponse->assertRedirect(route('guardian.dashboard'));
    }

    public function test_guardian_with_mfa_enabled_is_forced_to_mfa_setup_when_no_secret_exists(): void
    {
        $guardian = User::create([
            'name' => 'Guardian Secure',
            'username' => 'guardiansec',
            'email' => 'sec@example.com',
            'contact_number' => '09123456783',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'two_factor_secret' => null,
        ]);

        $response = $this->actingAs($guardian)->get(route('guardian.dashboard'));

        $response->assertRedirect(route('security.mfa.setup'));
    }

    public function test_guardian_with_mfa_enabled_is_forced_to_mfa_challenge_when_secret_exists(): void
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $guardian = User::create([
            'name' => 'Guardian Secret',
            'username' => 'guardiansecret',
            'email' => 'sec2@example.com',
            'contact_number' => '09123456784',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'two_factor_secret' => $secret,
        ]);

        $response = $this->actingAs($guardian)->get(route('guardian.dashboard'));

        $response->assertRedirect(route('security.mfa.challenge'));
    }

    public function test_first_time_password_change_for_guardian_with_normal_signin_redirects_to_dashboard(): void
    {
        $guardian = User::create([
            'name' => 'New Guardian',
            'username' => 'newguardian',
            'email' => 'new@example.com',
            'contact_number' => '09123456785',
            'password' => Hash::make('Temporary123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => null, // First-time login
            'mfa_enabled' => false,
        ]);

        $response = $this->actingAs($guardian)->post(route('security.setup.store'), [
            'password' => 'NewStrongPass123!',
            'password_confirmation' => 'NewStrongPass123!',
            'agree_terms' => '1',
        ]);

        $response->assertRedirect(route('guardian.dashboard'));
        $this->assertNotNull($guardian->fresh()->password_changed_at);
    }

    public function test_admin_can_toggle_guardian_mfa(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'contact_number' => '09123456786',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'two_factor_secret' => 'SECRETKEYFORADMIN1',
        ]);

        $guardian = User::create([
            'name' => 'Guardian Toggle',
            'username' => 'guardiantoggle',
            'email' => 'toggle@example.com',
            'contact_number' => '09123456787',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => false,
            'two_factor_secret' => null,
        ]);

        // Toggle from false to true
        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->patch(route('guardians.toggle-mfa', $guardian->id));

        $response->assertSessionHas('success');
        $this->assertTrue($guardian->fresh()->mfa_enabled);

        // Toggle from true back to false
        $guardian->update(['two_factor_secret' => 'LEFTOVERSECRETKEY']);
        $response2 = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->patch(route('guardians.toggle-mfa', $guardian->id));

        $response2->assertSessionHas('success');
        $this->assertFalse($guardian->fresh()->mfa_enabled);
        $this->assertNull($guardian->fresh()->two_factor_secret);
    }

    public function test_teacher_assigned_to_student_can_toggle_guardian_mfa(): void
    {
        $teacher = User::create([
            'name' => 'Teacher Maria',
            'username' => 'teachermaria',
            'email' => 'maria@example.com',
            'contact_number' => '09123456788',
            'password' => Hash::make('Password123!'),
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'can_manage_credentials' => false,
            'two_factor_secret' => 'SECRETTEACHER1',
        ]);

        $guardian = User::create([
            'name' => 'Assigned Guardian',
            'username' => 'assignedguardian',
            'email' => 'assigned@example.com',
            'contact_number' => '09123456789',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => false,
        ]);

        Student::create([
            'first_name' => 'Timmy',
            'last_name' => 'Cruz',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Grade 1 - Joy',
            'date_of_birth' => '2018-01-01',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->patch(route('guardians.toggle-mfa', $guardian->id));

        $response->assertSessionHas('success');
        $this->assertTrue($guardian->fresh()->mfa_enabled);
    }

    public function test_teacher_not_assigned_and_without_permission_cannot_toggle_unrelated_guardian(): void
    {
        $teacher = User::create([
            'name' => 'Teacher Unrelated',
            'username' => 'teacherunrelated',
            'email' => 'unrelated@example.com',
            'contact_number' => '09123456790',
            'password' => Hash::make('Password123!'),
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'can_manage_credentials' => false,
            'two_factor_secret' => 'SECRETTEACHER2',
        ]);

        $guardian = User::create([
            'name' => 'Other Guardian',
            'username' => 'otherguardian',
            'email' => 'other@example.com',
            'contact_number' => '09123456791',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => false,
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->patch(route('guardians.toggle-mfa', $guardian->id));

        $response->assertStatus(403);
    }

    public function test_teacher_with_credential_permission_can_toggle_any_guardian(): void
    {
        $teacher = User::create([
            'name' => 'Teacher Cred Manager',
            'username' => 'teachercred',
            'email' => 'credteacher@example.com',
            'contact_number' => '09123456792',
            'password' => Hash::make('Password123!'),
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'can_manage_credentials' => true,
            'two_factor_secret' => 'SECRETTEACHER3',
        ]);

        $guardian = User::create([
            'name' => 'Any Guardian',
            'username' => 'anyguardian',
            'email' => 'any@example.com',
            'contact_number' => '09123456793',
            'password' => Hash::make('Password123!'),
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => false,
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->patch(route('guardians.toggle-mfa', $guardian->id));

        $response->assertSessionHas('success');
        $this->assertTrue($guardian->fresh()->mfa_enabled);
    }

    public function test_cannot_toggle_mfa_for_teacher_or_admin_accounts(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'contact_number' => '09123456794',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'two_factor_secret' => 'ADMINSECRET1',
        ]);

        $teacher = User::create([
            'name' => 'Staff Teacher',
            'username' => 'staffteacher',
            'email' => 'staff@example.com',
            'contact_number' => '09123456795',
            'password' => Hash::make('Password123!'),
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'mfa_enabled' => true,
            'two_factor_secret' => 'TEACHERSECRET1',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->patch(route('guardians.toggle-mfa', $teacher->id));

        $response->assertSessionHasErrors();
        $this->assertTrue($teacher->fresh()->mfa_enabled);
    }
}

