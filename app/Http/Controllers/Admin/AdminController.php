<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function loginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->intended('/admin/dashboard')->with('success', 'Logged in successfully');
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->with('error', 'Invalid credentials');
    }

    public function registerForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/dashboard');
        }
        return view('admin.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Auth::guard('admin')->login($admin);
        $project = \App\Models\Project::firstOrCreate(
            ['admin_id' => $admin->id],
            ['name' => $admin->name . '\'s Project', 'description' => 'Default project']
        );
        return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome! Your project is ready.');
    }

    public function dashboard()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        return view('admin.dashboard');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->intended('/admin/login')->with('success', 'Logged out successfully');
    }
}
