<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class ProjectController extends Controller
{
    protected array $baseViewData = [];

    public function __construct()
    {
        $this->baseViewData = menuActive('projects_assets', '', '');
    }

    public function create()
    {
        $data = $this->baseViewData;
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        return view('admin.projects.create', $data);
    }

    public function store(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create([
            'admin_id' => Auth::guard('admin')->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->intended(route('admin.assets.index'))->with('success', 'Project created');
    }
}
