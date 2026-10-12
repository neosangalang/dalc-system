<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\SystemAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['comments.user'])->latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        // Added validation for 'type'
        $request->validate([
            'type'    => 'required|string|max:50',
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        // Added 'type' to the creation array
        $announcement = Announcement::create([
            'type'       => $request->type,
            'title'      => $request->title,
            'content'    => $request->content,
            'image_path' => $imagePath,
        ]);

        // Notify all active Guardians and Teachers
        try {
            $recipients = \App\Models\User::whereIn('role', ['guardian', 'teacher'])->where('is_active', true)->get();
            foreach ($recipients as $recipient) {
                $url = $recipient->role === 'guardian' ? route('guardian.announcements.index') : route('teacher.announcements');
                $recipient->notify(new \App\Notifications\SystemAlert(
                    "New Center Announcement: {$announcement->title}",
                    $url
                ));
            }
        } catch (\Exception $e) {
            Log::error('Notification failed on announcement create: ' . $e->getMessage());
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement posted successfully!');
    }

    public function update(Request $request, $id)
    {
        // Added validation for 'type'
        $request->validate([
            'type'    => 'required|string|max:50',
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $announcement = Announcement::findOrFail($id);

        if ($request->hasFile('image')) {
            // Delete the old image from storage if it exists
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            
            // Store the new image
            $announcement->image_path = $request->file('image')->store('announcements', 'public');
        }

        // Added 'type' update assignment
        $announcement->type = $request->type;
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->save();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);

        // Delete the image from storage when the announcement is deleted
        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully!');
    }
}