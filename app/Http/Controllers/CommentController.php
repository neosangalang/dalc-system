<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in the database.
     */
    public function store(Request $request)
    {
        // 1. Make sure the form actually sent text and the hidden ID fields
        $request->validate([
            'body' => 'required|string|max:1000',
            'commentable_type' => 'required|string', // Tells us if it's an IEP, Log, or Report
            'commentable_id' => 'required|integer',  // Tells us exactly WHICH one it is
        ]);

        // 2. Save it to the database
        Comment::create([
            'user_id' => Auth::id(), // Automatically grabs the logged-in Teacher or Guardian's ID
            'commentable_type' => $request->commentable_type,
            'commentable_id' => $request->commentable_id,
            'body' => $request->body,
        ]);

        // 3. Send them right back to the page they were on
        return back()->with('success', 'Reply posted successfully!');
    }
}