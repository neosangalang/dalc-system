<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicQuarter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AcademicQuarterController extends Controller
{
    public function index()
    {
        $quarters = AcademicQuarter::orderBy('id', 'asc')->get();

        // Clever auto-setup: If the table is empty, create the 4 default quarters automatically
        if ($quarters->isEmpty()) {
            $defaults = ['Q1', 'Q2', 'Q3', 'Q4'];
            foreach ($defaults as $q) {
                AcademicQuarter::create(['name' => $q]);
            }
            $quarters = AcademicQuarter::orderBy('id', 'asc')->get();
        }

        // Auto-sync active status based on today's date
        AcademicQuarter::syncActiveStatus();
        $quarters = AcademicQuarter::orderBy('id', 'asc')->get();

        return view('admin.quarters.index', compact('quarters'));
    }

    public function updateAll(Request $request)
    {
        $request->validate([
            'quarters' => 'required|array',
            'quarters.*.start_date' => 'nullable|date',
            'quarters.*.end_date' => 'nullable|date|after_or_equal:quarters.*.start_date',
        ]);

        // Collect all submitted date ranges and validate no overlaps
        $ranges = [];
        foreach ($request->quarters as $id => $data) {
            if (!empty($data['start_date']) && !empty($data['end_date'])) {
                $start = Carbon::parse($data['start_date']);
                $end   = Carbon::parse($data['end_date']);

                // Validate start <= end
                if ($start->gt($end)) {
                    $quarter = AcademicQuarter::find($id);
                    return back()
                        ->withInput()
                        ->withErrors([
                            'overlap' => "Invalid date range: {$quarter->name} start date cannot be after end date."
                        ]);
                }

                // Check against all previously collected ranges
                foreach ($ranges as $existingId => $existing) {
                    if ($start->lte($existing['end']) && $end->gte($existing['start'])) {
                        $existingQuarter = AcademicQuarter::find($existingId);
                        $currentQuarter  = AcademicQuarter::find($id);
                        return back()
                            ->withInput()
                            ->withErrors([
                                'overlap' => "Date overlap detected: {$currentQuarter->name} ({$start->format('M d, Y')} - {$end->format('M d, Y')}) overlaps with {$existingQuarter->name} ({$existing['start']->format('M d, Y')} - {$existing['end']->format('M d, Y')}). Each quarter must have non-overlapping dates."
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
                    'start_date' => $data['start_date'],
                    'end_date'   => $data['end_date'],
                ]);
            }
        }

        // Auto-sync active status based on today's date (no manual toggle)
        AcademicQuarter::syncActiveStatus();

        return back()->with('success', 'Academic Calendar updated successfully!');
    }
}