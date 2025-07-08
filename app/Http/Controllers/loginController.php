<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class loginController extends Controller
{
    public function showLoginForm()
    {
        return view('Treasurer/login'); 
}

public function authenticate(Request $request)
{
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = DB::table('createuser') 
        ->select('id','student_id', 'username', 'role', 'firstname', 'lastname', 'yearLevel', 'block', 'gender', 'password')
        ->where('username', $credentials['username'])
        ->first();

    if (!$user) {
        return back()->withErrors(['loginError' => 'User not found.']);
    }

    if (!Hash::check($credentials['password'], $user->password)) {
        return back()->withErrors(['loginError' => 'Incorrect password.']);
    }

    Auth::loginUsingId($user->student_id);
    session([
        'id'  => $user->id,
        'student_id'  => $user->student_id,
        'username'  => $user->username,
        'role'      => $user->role,
        'firstname' => $user->firstname,
        'lastname'  => $user->lastname,
        'yearLevel' => $user->yearLevel,
        'block'     => $user->block,
        'gender'    => $user->gender
    ]);

    if ($user->role === 'TREASURER') {
        return redirect()->route('dashboard'); 
    }   
    if ($user->role === 'REPRESENTATIVE') {
        return redirect()->route('repdashboard'); 
    }
    if ($user->role === 'ADMIN') {
        return redirect()->route('AdminDashboard');
    }
    if ($user->role === 'STUDENT') {
        return redirect()->route('StudentDashboard');

    }
    return redirect()->route('login')->withErrors(['loginError' => 'Unauthorized access.']);
}

public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('success', 'You have been logged out.');
}


}