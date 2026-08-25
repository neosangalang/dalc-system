<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement; 

class AnnouncementController extends Controller
{
    // 1. Show all announcements (The New Hub)
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    // 2. Show the blank form
    public function create()
    {
        return view('admin.announcements.create');
    }

    // 3. Save the new announcement
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Announcement::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        // Redirects to the hub instead of back
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement posted successfully!');
    }

    // 4. Show the edit form
    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.edit', compact('announcement'));
    }

    // 5. Save the edited changes
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = Announcement::findOrFail($id);
        
        $announcement->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully!');
    }

    // 6. Delete the announcement
    public function destroy($id)
    {
        Announcement::findOrFail($id)->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully!');
    }
}