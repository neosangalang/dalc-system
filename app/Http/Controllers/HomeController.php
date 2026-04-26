<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            return match($role) {
                'admin'    => redirect()->route('admin.dashboard'),
                'teacher'  => redirect()->route('teacher.dashboard'),
                'guardian' => redirect()->route('guardian.dashboard'),
                default    => redirect()->route('login'),
            };
        }
        return redirect()->route('login');
    }
}