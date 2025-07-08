<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $studentId = session('student_id');

        $existingProfile = DB::table('avatar')
            ->where('student_id', $studentId)
            ->first();

        if ($existingProfile && $existingProfile->profile) {
            Storage::disk('public')->delete($existingProfile->profile);
        }

        $path = $request->file('image')->store('images', 'public');

        DB::table('avatar')->updateOrInsert(
            ['student_id' => $studentId],
            ['profile' => $path]
        );

        return back()->with('success', 'Profile image uploaded successfully!');
    }
}
