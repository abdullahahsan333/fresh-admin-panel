<?php

namespace App\Http\Controllers\User;

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
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $data = $this->baseViewData;
        return view('user.projects.create', $data);
    }

    public function store(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }

        $validated = $request->validate([
            'description' => 'nullable|string',
        ]);

        Project::create([
            'user_id' => Auth::guard('web')->id(),
            'name' => 'LIVO',
            'description' => $validated['description'] ?? null,
        ]);
        
        return redirect()->intended(route('user.assets.index'))->with('success', 'Project created');
    }
}

