<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile page.
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $user->loadCount(['projectMembers', 'assignments']);

        return view('profile.show', [
            'user' => $user,
            'stats' => [
                'projects' => $user->project_members_count ?? 0,
                'assignments' => $user->assignments_count ?? 0,
            ],
        ]);
    }
}
