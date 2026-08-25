<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicQuarter;
use Illuminate\Http\Request;

class AcademicQuarterController extends Controller
{
    public function index()
    {
        $quarters = AcademicQuarter::orderBy('name')->get();

        // Clever auto-setup: If the table is empty, create the 4 default quarters automatically
        if ($quarters->isEmpty()) {
            $defaults = ['Q1', 'Q2', 'Q3', 'Q4'];
            foreach ($defaults as $q) {
                AcademicQuarter::create(['name' => $q]);
            }
            $quarters = AcademicQuarter::orderBy('name')->get();
        }

        return view('admin.quarters.index', compact('quarters'));
    }

    public function updateAll(Request $request)
    {
        $request->validate([
            'quarters' => 'required|array',
            'quarters.*.start_date' => 'nullable|date',
            'quarters.*.end_date' => 'nullable|date',
            'active_quarter_id' => 'nullable|exists:academic_quarters,id',
        ]);

        // Set ALL quarters to inactive first
        AcademicQuarter::query()->update(['is_active' => false]);

        // Activate the selected quarter if one was specified
        if ($request->filled('active_quarter_id')) {
            AcademicQuarter::where('id', $request->active_quarter_id)->update(['is_active' => true]);
        }

        // Update dates for each quarter
        foreach ($request->quarters as $id => $data) {
            $quarter = AcademicQuarter::find($id);
            if ($quarter) {
                $quarter->update([
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                ]);
            }
        }

        return back()->with('success', 'Academic Calendar updated successfully!');
    }
}