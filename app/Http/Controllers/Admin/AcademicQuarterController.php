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
            'active_quarter_id' => 'required|exists:academic_quarters,id',
        ]);

        // First, set ALL quarters to inactive
        AcademicQuarter::query()->update(['is_active' => false]);

        // Then, loop through the submitted data and update dates
        foreach ($request->quarters as $id => $data) {
            $quarter = AcademicQuarter::find($id);
            if ($quarter) {
                $quarter->update([
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'is_active' => ($id == $request->active_quarter_id) ? true : false,
                ]);
            }
        }

        return back()->with('success', 'Academic Calendar updated successfully!');
    }
}