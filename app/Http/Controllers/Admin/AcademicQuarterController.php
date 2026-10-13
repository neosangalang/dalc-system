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
            'quarters'              => 'required|array',
            'quarters.*.start_date' => 'nullable|date',
            'quarters.*.end_date'   => 'nullable|date',
        ]);

        // Load all quarters ordered by sequence
        $allQuarters = AcademicQuarter::orderBy('id', 'asc')->get()->keyBy('id');
        $ranges = [];

        // First pass: validate individual quarter date completeness and start <= end
        foreach ($request->quarters as $id => $data) {
            $quarter = $allQuarters->get($id) ?? AcademicQuarter::find($id);
            if (!$quarter) continue;

            $hasStart = !empty($data['start_date']);
            $hasEnd   = !empty($data['end_date']);

            if ($hasStart xor $hasEnd) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'overlap' => "Incomplete date range for {$quarter->name}: Please provide both start date and end date."
                    ]);
            }

            if ($hasStart && $hasEnd) {
                $start = Carbon::parse($data['start_date'])->startOfDay();
                $end   = Carbon::parse($data['end_date'])->startOfDay();

                if ($start->gt($end)) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'overlap' => "Invalid date range: {$quarter->name} start date ({$start->format('M d, Y')}) cannot be after end date ({$end->format('M d, Y')})."
                        ]);
                }

                $ranges[$id] = [
                    'quarter' => $quarter,
                    'start'   => $start,
                    'end'     => $end,
                ];
            }
        }

        // Second pass: validate non-overlapping dates and sequential chronological order
        foreach ($ranges as $idA => $rangeA) {
            foreach ($ranges as $idB => $rangeB) {
                if ($idA === $idB) continue;

                $qA = $rangeA['quarter'];
                $qB = $rangeB['quarter'];

                // Overlap check
                if ($rangeA['start']->lte($rangeB['end']) && $rangeA['end']->gte($rangeB['start'])) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'overlap' => "Date overlap detected: {$qA->name} ({$rangeA['start']->format('M d, Y')} - {$rangeA['end']->format('M d, Y')}) overlaps with {$qB->name} ({$rangeB['start']->format('M d, Y')} - {$rangeB['end']->format('M d, Y')}). Each quarter must have non-overlapping dates."
                        ]);
                }

                // Chronological order check: earlier quarter in sequence must end before subsequent quarter begins
                if ($qA->id < $qB->id && $rangeA['end']->gte($rangeB['start'])) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'overlap' => "Chronological sequence error: {$qB->name} ({$rangeB['start']->format('M d, Y')}) cannot start before or during {$qA->name} ({$rangeA['start']->format('M d, Y')} - {$rangeA['end']->format('M d, Y')}). Quarters must be sequential."
                        ]);
                }
            }
        }

        // Update dates for each quarter cleanly
        foreach ($request->quarters as $id => $data) {
            $quarter = $allQuarters->get($id) ?? AcademicQuarter::find($id);
            if ($quarter) {
                $quarter->update([
                    'start_date' => !empty($data['start_date']) ? Carbon::parse($data['start_date'])->toDateString() : null,
                    'end_date'   => !empty($data['end_date']) ? Carbon::parse($data['end_date'])->toDateString() : null,
                ]);
            }
        }

        // Auto-sync active status based on today's date
        AcademicQuarter::syncActiveStatus();

        return back()->with('success', 'Academic Calendar updated successfully!');
    }
}