<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicQuarter;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AcademicQuarterController extends Controller
{
    public function index(Request $request)
    {
        // Auto-setup fallback: if no school years exist yet, create default initial year
        if (SchoolYear::count() === 0) {
            $curY = (int) date('Y');
            $curM = (int) date('n');
            $defaultName = 'SY ' . (($curM >= 6) ? $curY . '-' . ($curY + 1) : ($curY - 1) . '-' . $curY);

            $defaultSy = SchoolYear::create([
                'name'      => $defaultName,
                'is_active' => true,
            ]);

            // Link existing unassigned quarters or create fresh Q1-Q4
            $unassignedQuarters = AcademicQuarter::whereNull('school_year_id')->get();
            if ($unassignedQuarters->isNotEmpty()) {
                AcademicQuarter::whereNull('school_year_id')->update(['school_year_id' => $defaultSy->id]);
            } else {
                foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $q) {
                    AcademicQuarter::create([
                        'name'           => $q,
                        'school_year_id' => $defaultSy->id,
                    ]);
                }
            }
        }

        // Auto-sync active status using the hybrid logic
        AcademicQuarter::syncActiveStatus();

        $schoolYears = SchoolYear::with('quarters')->orderByDesc('is_active')->orderByDesc('id')->get();
        $activeSchoolYear = SchoolYear::active();

        // Determine which school year is currently selected in the UI dropdown
        $selectedYearId = $request->query('year', session('selected_year_id'));
        if (!$selectedYearId || !$schoolYears->contains('id', (int) $selectedYearId)) {
            $selectedYearId = $activeSchoolYear ? $activeSchoolYear->id : ($schoolYears->first()->id ?? null);
        }

        // Also pass active school year's quarters as $quarters for backward compatibility with views
        $quarters = $activeSchoolYear ? $activeSchoolYear->quarters : AcademicQuarter::orderBy('id', 'asc')->get();

        // Prepare candidate academic years for the Add School Year modal
        $baseYear = (int) date('Y');
        $existingNames = $schoolYears->pluck('name')->all();
        $existingNamesLower = array_map('strtolower', $existingNames);

        $candidateYears = [];
        for ($y = $baseYear - 2; $y <= $baseYear + 7; $y++) {
            $range = $y . '-' . ($y + 1);
            $isTaken = in_array($range, $existingNames)
                || in_array('SY ' . $range, $existingNames)
                || in_array(strtolower($range), $existingNamesLower)
                || in_array(strtolower('SY ' . $range), $existingNamesLower);
            $candidateYears[] = [
                'label'   => $range,
                'start'   => $y,
                'end'     => $y + 1,
                'isTaken' => $isTaken,
            ];
        }

        $defaultCandidateItem = collect($candidateYears)->first(function ($item) use ($baseYear) {
            return !$item['isTaken'] && $item['start'] >= $baseYear;
        }) ?: collect($candidateYears)->firstWhere('isTaken', false);

        $defaultCandidate = $defaultCandidateItem ? $defaultCandidateItem['label'] : ($baseYear . '-' . ($baseYear + 1));
        $defaultStartYear = $defaultCandidateItem ? $defaultCandidateItem['start'] : $baseYear;
        $defaultEndYear = $defaultCandidateItem ? $defaultCandidateItem['end'] : ($baseYear + 1);

        return view('admin.quarters.index', compact(
            'schoolYears',
            'activeSchoolYear',
            'quarters',
            'selectedYearId',
            'baseYear',
            'candidateYears',
            'defaultCandidate',
            'defaultStartYear',
            'defaultEndYear',
            'existingNamesLower'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_year_name' => 'required|string|max:50|unique:school_years,name',
        ], [
            'school_year_name.required' => 'Please enter or choose a name for the new School Year.',
            'school_year_name.unique'   => 'A School Year with this name already exists.',
        ]);

        $sy = SchoolYear::create([
            'name'      => trim($request->school_year_name),
            'is_active' => false, // New years start in standby mode
        ]);

        foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $q) {
            AcademicQuarter::create([
                'name'           => $q,
                'school_year_id' => $sy->id,
                'is_active'      => false,
            ]);
        }

        return redirect()->route('admin.quarters.index', ['year' => $sy->id])
            ->with('selected_year_id', $sy->id)
            ->with('success', "School Year {$sy->name} created in Standby mode! You can now configure its quarter dates.");
    }

    public function updateAll(Request $request, $schoolYearId = null)
    {
        $request->validate([
            'quarters'              => 'required|array',
            'quarters.*.start_date' => 'nullable|date',
            'quarters.*.end_date'   => 'nullable|date|after_or_equal:quarters.*.start_date',
        ]);

        // Validate date ranges and overlaps within the submitted quarters
        $ranges = [];
        foreach ($request->quarters as $id => $data) {
            if (!empty($data['start_date']) && !empty($data['end_date'])) {
                $start = Carbon::parse($data['start_date']);
                $end   = Carbon::parse($data['end_date']);

                if ($start->gt($end)) {
                    $quarter = AcademicQuarter::find($id);
                    $qName = $quarter ? $quarter->name : 'Quarter';
                    return back()
                        ->withInput()
                        ->withErrors([
                            'overlap' => "Invalid date range: {$qName} start date cannot be after end date."
                        ]);
                }

                foreach ($ranges as $existingId => $existing) {
                    if ($start->lte($existing['end']) && $end->gte($existing['start'])) {
                        $existingQuarter = AcademicQuarter::find($existingId);
                        $currentQuarter  = AcademicQuarter::find($id);
                        $existName = $existingQuarter ? $existingQuarter->name : 'Quarter';
                        $currName  = $currentQuarter ? $currentQuarter->name : 'Quarter';
                        return back()
                            ->withInput()
                            ->withErrors([
                                'overlap' => "Date overlap detected: {$currName} ({$start->format('M d, Y')} - {$end->format('M d, Y')}) overlaps with {$existName} ({$existing['start']->format('M d, Y')} - {$existing['end']->format('M d, Y')}). Each quarter must have non-overlapping dates."
                            ]);
                    }
                }

                $ranges[$id] = ['start' => $start, 'end' => $end];
            }
        }

        // Update dates for each quarter
        foreach ($request->quarters as $id => $data) {
            $quarter = AcademicQuarter::find($id);
            if ($quarter) {
                $quarter->update([
                    'start_date' => !empty($data['start_date']) ? $data['start_date'] : null,
                    'end_date'   => !empty($data['end_date']) ? $data['end_date'] : null,
                ]);
            }
        }

        // Auto-sync active status using Hybrid Activation Logic
        AcademicQuarter::syncActiveStatus();

        return redirect()->route('admin.quarters.index', ['year' => $schoolYearId])
            ->with('selected_year_id', $schoolYearId)
            ->with('success', 'Academic Calendar updated successfully!');
    }

    public function setActive($id)
    {
        $schoolYear = SchoolYear::findOrFail($id);
        $schoolYear->setAsActive();

        return redirect()->route('admin.quarters.index', ['year' => $schoolYear->id])
            ->with('selected_year_id', $schoolYear->id)
            ->with('success', "School Year {$schoolYear->name} is now the Active Year. Quarter statuses have been synced.");
    }

    public function destroy($id)
    {
        $schoolYear = SchoolYear::findOrFail($id);

        if ($schoolYear->is_active) {
            return back()->withErrors(['delete' => 'Cannot delete the currently active School Year. Please set another School Year as active first.']);
        }

        $name = $schoolYear->name;
        $schoolYear->delete(); // Cascades to child academic_quarters

        return redirect()->route('admin.quarters.index')
            ->with('success', "School Year {$name} and its calendar settings have been deleted.");
    }
}
