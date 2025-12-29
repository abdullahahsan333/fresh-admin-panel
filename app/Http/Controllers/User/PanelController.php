<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanelController extends Controller
{
    protected array $baseViewData = [];

    public function __construct()
    {
        $this->baseViewData = menuActive('dashboard', '', '');
    }

    public function dashboard()
    {
        $data = $this->baseViewData;
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        return view('user.dashboard', $data);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->intended(route('login'))->with('success', 'Logged out successfully');
    }

    public function profile()
    {
        $data = $this->baseViewData;
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $data['user'] = Auth::guard('web')->user();
        return view('user.profile.edit', $data);
    }

    public function profileUpdate(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $user = Auth::guard('web')->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,webp,gif|max:2048',
        ]);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        if ($request->hasFile('avatar')) {
            $path = uploadImage($request->file('avatar'), 'uploads/avatars', '', '', '', 'user');
            if ($path) {
                $user->avatar = $path;
            }
        }
        $user->save();
        return redirect()->intended(route('user.profile'))->with('success', 'Profile updated successfully');
    }
}
