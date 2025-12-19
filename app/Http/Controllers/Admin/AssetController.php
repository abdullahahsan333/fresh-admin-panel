<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Asset;

class AssetController extends Controller
{
    public function __construct()
    {
        
    }

    public function index()
    {
        $data = [];
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        $data['server'] = Server::get();
        return view('admin.assets.index', $data);
    }

    public function store(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        $validated = $request->validate([
            'ip' => 'string|max:255',
            'services' => 'array',
            'services.*' => 'string',
            'hostnames' => 'array',
            'hostnames.*' => 'string',
        ]);
        $project = Project::firstOrCreate(
            ['admin_id' => Auth::guard('admin')->id()],
            ['name' => Auth::guard('admin')->user()->name . '\'s Project', 'description' => 'Default project']
        );
        $server = Server::firstOrCreate(
            ['ip' => $validated['ip']],
            ['project_id' => $project->id]
        );
        $services = collect($validated['services'] ?? []);
        $hostnames = collect($validated['hostnames'] ?? []);
        if ($services->count() && $hostnames->count()) {
            $rows = [];
            foreach ($services as $svc) {
                foreach ($hostnames as $hn) {
                    $rows[] = [
                        'server_id' => $server->id,
                        'service_name' => $svc,
                        'hostname' => $hn,
                    ];
                }
            }
            if (!empty($rows)) {
                Asset::insert($rows);
            }
        }
        return redirect()->intended(route('admin.assets.index'))->with('success', 'Configuration saved for server '.$server->ip);
    }
    
    public function server_store(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $validated = $request->validate([
            'ip' => 'string|max:255',
        ]);
        $project = Project::firstOrCreate(
            ['admin_id' => Auth::guard('admin')->id()],
            ['name' => Auth::guard('admin')->user()->name . '\'s Project', 'description' => 'Default project']
        );
        $server = Server::firstOrCreate(
            ['ip' => $validated['ip']],
            ['project_id' => $project->id]
        );
        return response()->json(['ok' => true, 'server' => $server]);
    }
    
}
