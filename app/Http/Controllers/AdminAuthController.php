<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (! Auth::guard('admin')->attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ], $remember)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    function login(Request $request) {
        if (Auth::guard('admin')->check()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        return view('livewire.auth.admin.login');
    }

    function logout(Request $request) {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->intended(route('admin.login'));
    }

    function dashboard(Request $request) {
        if (! Auth::guard('admin')->check()) {
            return redirect()->intended(route('admin.login'));
        }
        return view('admin.dashboard');
    }

    function profileSettings(Request $request) {
        if (! Auth::guard('admin')->check()) {
            return redirect()->intended(route('admin.login'));
        }
        return redirect()->route('admin.profile.edit');
    }

    function passwordSettings(Request $request) {
        if (! Auth::guard('admin')->check()) {
            return redirect()->intended(route('admin.login'));
        }
        return redirect()->route('admin.user-password.edit');
    }

    function appearanceSettings(Request $request) {
        if (! Auth::guard('admin')->check()) {
            return redirect()->intended(route('admin.login'));
        }
        return redirect()->route('admin.appearance.edit');
    }
}
