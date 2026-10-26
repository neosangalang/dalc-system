<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AcademicQuarter;
use App\Models\SchoolYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class AcademicQuarterCalendarTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private SchoolYear $schoolYear;
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

        $this->schoolYear = SchoolYear::create([
            'name' => 'SY 2026-2027',
            'is_active' => true,
        ]);

        $this->q1 = AcademicQuarter::create(['name' => 'Q1', 'school_year_id' => $this->schoolYear->id]);
        $this->q2 = AcademicQuarter::create(['name' => 'Q2', 'school_year_id' => $this->schoolYear->id]);
        $this->q3 = AcademicQuarter::create(['name' => 'Q3', 'school_year_id' => $this->schoolYear->id]);
        $this->q4 = AcademicQuarter::create(['name' => 'Q4', 'school_year_id' => $this->schoolYear->id]);
    }

    public function test_academic_quarters_page_renders_with_formatted_dates_and_statuses(): void
    {
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
        // Must see exact YYYY-MM-DD format in the HTML value attribute
        $response->assertSee('value="' . $this->q1->start_date_formatted . '"', false);
        $response->assertSee('value="' . $this->q1->end_date_formatted . '"', false);
        // Must NOT see the 00:00:00 timestamp inside the input value
        $response->assertDontSee('value="' . $this->q1->start_date_formatted . ' 00:00:00"', false);
    }

    public function test_saving_valid_non_overlapping_quarters_succeeds_and_retains_dates_in_ui(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.quarters.update', $this->schoolYear->id), [
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

        // Subsequent GET request must render the exact dates in the UI
        $viewResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.quarters.index'));

        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('value="2026-08-01"', false);
        $viewResponse->assertSee('value="2026-10-15"', false);
        $viewResponse->assertSee('value="2026-10-16"', false);
        $viewResponse->assertSee('value="2026-12-20"', false);
    }

    public function test_saving_overlapping_quarters_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.quarters.update', $this->schoolYear->id), [
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

    public function test_saving_quarter_with_start_date_after_end_date_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.quarters.update', $this->schoolYear->id), [
                'quarters' => [
                    $this->q1->id => [
                        'start_date' => '2026-10-20',
                        'end_date'   => '2026-10-10', // End date before start date
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('quarters.' . $this->q1->id . '.end_date');
    }

    public function test_admin_can_create_new_school_year_in_standby_mode(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.school-years.store'), [
                'school_year_name' => 'SY 2027-2028',
            ]);

        $response->assertSessionHas('success');

        $newSy = SchoolYear::where('name', 'SY 2027-2028')->first();
        $this->assertNotNull($newSy);
        $this->assertFalse($newSy->is_active, 'New school year must start in standby/inactive mode.');
        $this->assertCount(4, $newSy->quarters, 'Four quarters (Q1-Q4) must be auto-provisioned.');
    }

    public function test_hybrid_activation_logic_scoping(): void
    {
        // 1. Create a future/inactive school year with dates that happen to span today
        $futureSy = SchoolYear::create([
            'name' => 'SY 2027-2028',
            'is_active' => false,
        ]);
        $futureQ1 = AcademicQuarter::create([
            'name' => 'Q1',
            'school_year_id' => $futureSy->id,
            'start_date' => Carbon::today()->subDays(5)->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays(5)->format('Y-m-d'),
            'is_active' => false,
        ]);

        // 2. Give the active school year a Q1 that spans today
        $this->q1->update([
            'start_date' => Carbon::today()->subDays(2)->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays(20)->format('Y-m-d'),
        ]);

        // Run sync
        AcademicQuarter::syncActiveStatus();

        $this->q1->refresh();
        $futureQ1->refresh();

        // Condition check: Only active school year's quarter becomes active
        $this->assertTrue($this->q1->is_active, 'Quarter in active school year must be active.');
        $this->assertFalse($futureQ1->is_active, 'Quarter in inactive school year must NOT become active even if dates include today.');

        // 3. Switch active year to futureSy
        $futureSy->setAsActive();

        $this->schoolYear->refresh();
        $futureSy->refresh();
        $this->q1->refresh();
        $futureQ1->refresh();

        $this->assertFalse($this->schoolYear->is_active, 'Previous school year must be deactivated.');
        $this->assertTrue($futureSy->is_active, 'Selected school year must become active.');
        $this->assertFalse($this->q1->is_active, 'Quarters in deactivated school year must become inactive.');
        $this->assertTrue($futureQ1->is_active, 'Quarters in newly activated school year must auto-activate.');
    }

    public function test_cannot_delete_active_school_year_but_can_delete_inactive_year(): void
    {
        // Attempt deleting active year
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->delete(route('admin.school-years.destroy', $this->schoolYear->id));

        $response->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('school_years', ['id' => $this->schoolYear->id]);

        // Create inactive year and delete it
        $inactiveSy = SchoolYear::create(['name' => 'SY 2028-2029', 'is_active' => false]);
        $quarter = AcademicQuarter::create(['name' => 'Q1', 'school_year_id' => $inactiveSy->id]);

        $deleteResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->delete(route('admin.school-years.destroy', $inactiveSy->id));

        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('school_years', ['id' => $inactiveSy->id]);
        $this->assertDatabaseMissing('academic_quarters', ['id' => $quarter->id]);
    }
}
