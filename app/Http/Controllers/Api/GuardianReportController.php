<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;

class GuardianReportController extends Controller
{
    /**
     * Display a listing of the reports for the mobile app.
     */
    public function index(Request $request)
    {
        // 1. Get the authenticated Guardian making the request via the mobile app
        $guardian = $request->user();

        // 2. Fetch the approved reports belonging to their student(s)
        // (Assuming your Guardian model has a relationship to Students)
        $studentIds = $guardian->students()->pluck('id');
        
        $reports = Report::whereIn('student_id', $studentIds)
                        ->where('status', 'approved')
                        ->latest()
                        ->get();

        // 3. RETURN JSON (This is the most important part for APIs!)
        return response()->json([
            'status' => 'success',
            'message' => 'Reports retrieved successfully.',
            'data' => $reports
        ], 200); 
        // 200 is the HTTP status code for "OK"
    }

    /**
     * Display a specific report's details.
     */
    public function show($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'status' => 'error',
                'message' => 'Report not found.'
            ], 404); // 404 means Not Found
        }

        return response()->json([
            'status' => 'success',
            'data' => $report
        ], 200);
    }
}