<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isOwner = auth('owner')->check();
        
        return view('profile.index', compact('user', 'isOwner'));
    }
}
