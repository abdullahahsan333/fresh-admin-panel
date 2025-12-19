<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if(Auth::guard('web')->check()){
            return redirect()->intended('/dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            return redirect()->intended('/dashboard')->with('success', 'Logged in successfully');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->with('error', 'Invalid credentials');
    }
    public function register()
    {
        if(Auth::guard('web')->check()){
            return redirect()->intended('/dashboard');
        }

        return view('register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('web')->login($user);

        return redirect()->intended('/dashboard')->with('success', 'Account created successfully');
    }

    public function dashboard()
    {
        if(!Auth::guard('web')->check()){
            return redirect()->intended('/');
        }

        return view('dashboard');
    }

    public function logout()
    {
        Auth::guard('web')->logout();

        return redirect()->intended('/')->with('success', 'Logged out successfully');
    }
}
