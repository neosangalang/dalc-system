<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AcademicQuarter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class AcademicQuarterCalendarTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private AcademicQuarter $q1;
    private AcademicQuarter $q2;
    private AcademicQuarter $q3;
    private AcademicQuarter $q4;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->q1 = AcademicQuarter::create(['name' => 'Q1']);
        $this->q2 = AcademicQuarter::create(['name' => 'Q2']);
        $this->q3 = AcademicQuarter::create(['name' => 'Q3']);
        $this->q4 = AcademicQuarter::create(['name' => 'Q4']);
    }

    public function test_academic_quarters_page_renders_with_formatted_dates_and_statuses(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $this->q1->update([
            'start_date' => Carbon::today()->subDays(10)->format('Y-m-d'),
            'end_date'   => Carbon::today()->addDays(10)->format('Y-m-d'),
        ]);

        AcademicQuarter::syncActiveStatus();

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.quarters.index'));

        $response->assertStatus(200);
        $response->assertSee('Active Now');
        $response->assertSee($this->q1->start_date_formatted);
        $response->assertSee($this->q1->end_date_formatted);
    }

    public function test_saving_valid_non_overlapping_quarters_succeeds_and_syncs_active_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.quarters.update'), [
                'quarters' => [
                    $this->q1->id => [
                        'start_date' => '2026-08-01',
                        'end_date'   => '2026-10-15',
                    ],
                    $this->q2->id => [
                        'start_date' => '2026-10-16',
                        'end_date'   => '2026-12-20',
                    ],
                    $this->q3->id => [
                        'start_date' => '2027-01-05',
                        'end_date'   => '2027-03-20',
                    ],
                    $this->q4->id => [
                        'start_date' => '2027-03-25',
                        'end_date'   => '2027-05-30',
                    ],
                ],
            ]);

        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->q1->refresh();
        $this->q2->refresh();

        $this->assertEquals('2026-08-01', $this->q1->start_date_formatted);
        $this->assertEquals('2026-10-15', $this->q1->end_date_formatted);
        $this->assertEquals('2026-10-16', $this->q2->start_date_formatted);
        $this->assertEquals('2026-12-20', $this->q2->end_date_formatted);

        // Check active status is true for Q1 since today (2026-10-13) is between 2026-08-01 and 2026-10-15
        $this->assertTrue((bool)$this->q1->is_active);
        $this->assertFalse((bool)$this->q2->is_active);
    }

    public function test_saving_overlapping_quarters_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.quarters.update'), [
                'quarters' => [
                    $this->q1->id => [
                        'start_date' => '2026-08-01',
                        'end_date'   => '2026-10-20',
                    ],
                    $this->q2->id => [
                        'start_date' => '2026-10-15', // Overlaps with Q1
                        'end_date'   => '2026-12-20',
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('overlap');
    }

    public function test_saving_chronologically_inverted_quarters_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.quarters.update'), [
                'quarters' => [
                    $this->q1->id => [
                        'start_date' => '2026-08-01',
                        'end_date'   => '2026-10-15',
                    ],
                    $this->q2->id => [
                        'start_date' => '2026-05-01', // Before Q1 starts
                        'end_date'   => '2026-07-15',
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('overlap');
    }

    public function test_saving_quarter_with_start_date_after_end_date_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.quarters.update'), [
                'quarters' => [
                    $this->q1->id => [
                        'start_date' => '2026-10-20',
                        'end_date'   => '2026-10-10', // End date before start date
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('overlap');
    }

    public function test_saving_incomplete_quarter_dates_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.quarters.update'), [
                'quarters' => [
                    $this->q1->id => [
                        'start_date' => '2026-08-01',
                        'end_date'   => null,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('overlap');
    }
}
