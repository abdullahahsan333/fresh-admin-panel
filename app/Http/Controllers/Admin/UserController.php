<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\Server;
use App\Models\Asset;
use App\Models\Hostname;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected array $baseViewData = [];
    
    public function __construct()
    {
        $this->baseViewData = menuActive('users', 'users_index', '');
    }
    public function index()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        $data = menuActive('users', 'users_index', '');
        $data['users'] = User::orderBy('name')->get();

        return view('admin.users.index', $data);
    }

    public function assets(Request $request, $id)
    {
        $data = menuActive('users', 'users_index', '');

        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        $data['user']       = $user     =  User::findOrFail($id);
        
        $data['project']    = $project  =  Project::where('user_id', $user->id)->first();
        $data['servers']    = $project ? Server::where('project_id', $project->id)->orderBy('ip')->get() : collect();
        $data['assets']     = $project ? Asset::where('project_id', $project->id)->get() : collect();
        $data['hostnames']  = $project ? Hostname::where('project_id', $project->id)->get() : collect();

        return view('admin.users.assets', $data);
    }

    public function create()
    {
        $data = menuActive('users', 'users_create', '');
        
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        return view('admin.users.create', $data);
    }

    public function store(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:800',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $uploaded = uploadImage($request->file('avatar'), 'uploads/avatars', '', '', '', 'user');
            if ($uploaded) {
                $avatarPath = $uploaded;
            }
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'avatar' => $avatarPath,
            'status' => 1,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);
        $user = User::findOrFail($id);
        $user->status = (int) $request->input('status');
        $user->save();
        return back()->with('success', 'Status updated');
    }
}
