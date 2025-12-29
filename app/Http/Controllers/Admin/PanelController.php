<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    protected array $baseViewData = [];

    public function __construct()
    {
        $this->baseViewData = menuActive('dashboard', '', '');
    }
    
    public function notifications()
    {
        $data = $this->baseViewData;

        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        return view('admin.notifications.index', $data);
    }
    
    public function profile()
    {
        $data = $this->baseViewData;

        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $data['admin'] = Auth::guard('admin')->user();

        return view('admin.profile.edit', $data);
    }
    public function profileUpdate(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $admin = Auth::guard('admin')->user();

        $request->validate([
            'first_name'    => 'nullable|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'email'         => 'required|email|unique:admins,email,' . $admin->id,
            'phone'         => 'nullable|string|max:32',
            'username'      => 'nullable|string|max:255|unique:admins,username,' . $admin->id,
            'address'       => 'nullable|string|max:255',
            'timezone'      => 'nullable|string|max:255',
            'currency'      => 'nullable|string|max:255',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:800',
        ]);

        $fn = trim((string) $request->first_name);
        $ln = trim((string) $request->last_name);
        
        $admin->name        = trim($fn . ' ' . $ln) ?: $admin->name;
        $admin->email       = $request->email;
        $admin->phone       = $request->phone;
        $admin->username    = $request->username;
        $admin->address     = $request->address;
        $admin->timezone    = $request->timezone;
        $admin->currency    = $request->currency;

        if ($request->hasFile('avatar')) {
            $uploaded = uploadImage($request->file('avatar'), 'uploads/avatars', '', '', '', 'avatar');
            if ($uploaded) {
                $admin->avatar = $uploaded;
            }
        }

        $admin->save();

        return back()->with('success', 'Profile updated');
    }
    public function settings()
    {
        $data = $this->baseViewData;

        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $data['site'] = getSiteInfo();

        return view('admin.settings.edit', $data);
    }
    public function settingsUpdate(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $keys = [
            'site_name','logo','favicon','copyright','phone','address','email',
            'whatsapp','skype','google_plus','facebook','twitter','instagram',
            'linkedin','youtube','meta_title','meta_keywords','meta_description'
        ];

        $rules = [
            'site_name'         => 'nullable|string|max:255',
            'copyright'         => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:64',
            'address'           => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',

            'whatsapp'          => 'nullable|string|max:255',
            'skype'             => 'nullable|string|max:255',
            'google_plus'       => 'nullable|string|max:255',
            'facebook'          => 'nullable|string|max:255',
            'twitter'           => 'nullable|string|max:255',
            'instagram'         => 'nullable|string|max:255',
            'linkedin'          => 'nullable|string|max:255',
            'youtube'           => 'nullable|string|max:255',

            'meta_title'        => 'nullable|string|max:255',
            'meta_keywords'     => 'nullable|string|max:2000',
            'meta_description'  => 'nullable|string|max:2000',

            'logo'              => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'favicon'           => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,ico|max:1024',
        ];

        $request->validate($rules);

        $now = now();
        $values = [];

        foreach ($keys as $key) {
            if ($request->filled($key)) {
                $val = $request->input($key);
                if (is_string($val)) $val = trim($val);
                $values[$key] = $val;
            }
        }

        if ($request->hasFile('logo')) {
            $uploaded = uploadImage($request->file('logo'), 'uploads/settings', '', '', '', 'logo');
            if ($uploaded) $values['logo'] = $uploaded;
        }

        if ($request->hasFile('favicon')) {
            $uploaded = uploadImage($request->file('favicon'), 'uploads/settings', '', '', '', 'favicon');
            if ($uploaded) $values['favicon'] = $uploaded;
        }

        foreach ($values as $metaKey => $metaValue) {
            if ($metaValue === null) continue;

            if (is_string($metaValue) && $metaValue === '') continue;

            DB::table('settings')->updateOrInsert(
                ['meta_key'     => $metaKey],
                ['meta_value'   => $metaValue, 'updated_at' => $now, 'created_at' => $now]
            );
        }
        
        return back()->with('success', 'Settings updated');
    }
}
