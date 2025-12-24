<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Asset;
use App\Models\Hostname;
use Throwable;
use Illuminate\Support\Facades\View;

class AssetController extends Controller
{
    protected array $baseViewData = [];

    public function __construct()
    {
        $this->baseViewData = menuActive('projects_assets', '', '');
    }

    public function index()
    {
        $data = $this->baseViewData;
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        $data['server'] = Server::get();
        return view('admin.assets.index', $data);
    }

    public function store(Request $request)
    {
        try {
            if (!Auth::guard('admin')->check()) {
                return redirect()->intended('/admin/login');
            }
            $validated = $request->validate([
                'server_id' => 'nullable|integer',
                'hostname' => 'nullable|string|max:255',
                'services' => 'array',
                'services.*' => 'string',
                'hostnames' => 'array',
                'hostnames.*' => 'string',
            ]);
            $project = Project::firstOrCreate(
                ['admin_id' => Auth::guard('admin')->id()],
                ['name' => Auth::guard('admin')->user()->name . '\'s Project', 'description' => 'Default project']
            );
            $server = null;
            if (!empty($validated['server_id'])) {
                $server = Server::where('project_id', $project->id)->where('id', $validated['server_id'])->first();
            }
            if (!$server && $request->filled('ip')) {
                $server = Server::firstOrCreate(
                    ['ip' => $request->ip],
                    ['project_id' => $project->id]
                );
            }
            if (!$server) {
                $server = Server::where('project_id', $project->id)->latest('id')->first();
            }
            if (!$server) {
                throw new \RuntimeException('No server selected for configuration');
            }
            $services = collect($validated['services'] ?? []);
            $hostnames = collect($validated['hostnames'] ?? []);
            if (!empty($validated['hostname'])) {
                $hostnames->push($validated['hostname']);
            }
            Asset::where('project_id', $project->id)->where('server_id', $server->id)->delete();
            Hostname::where('project_id', $project->id)->where('server_id', $server->id)->delete();
            if ($services->count()) {
                $serviceRows = [];
                foreach ($services->unique() as $svc) {
                    $serviceRows[] = [
                        'project_id' => $project->id,
                        'server_id' => $server->id,
                        'service_name' => $svc,
                    ];
                }
                if (!empty($serviceRows)) {
                    Asset::insert($serviceRows);
                }
            }
            if ($hostnames->count()) {
                $hostnameRows = [];
                foreach ($hostnames->unique() as $hn) {
                    $hostnameRows[] = [
                        'project_id' => $project->id,
                        'server_id' => $server->id,
                        'hostname' => $hn,
                    ];
                }
                if (!empty($hostnameRows)) {
                    Hostname::insert($hostnameRows);
                }
            }
            return redirect()->intended(route('admin.assets.index'))->with('success', 'Configuration saved for server '.$server->ip);
        } catch (Throwable $e) {
            Log::error('Assets store failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => $e->getMessage()])->with('error', 'Failed to save configuration');
        }
    }
    
    public function server_store(Request $request)
    {
        try {
            if (!Auth::guard('admin')->check()) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }
            $validated = $request->validate([
                'ip' => 'required|string|max:255',
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
        } catch (Throwable $e) {
            Log::error('Server store failed', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Failed to add server', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function server_details(Request $request)
    {
        try {
            if (!Auth::guard('admin')->check()) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }
            $request->validate([
                'ip' => 'required|string|max:255',
            ]);
            $project = Project::firstOrCreate(
                ['admin_id' => Auth::guard('admin')->id()],
                ['name' => Auth::guard('admin')->user()->name . '\'s Project', 'description' => 'Default project']
            );
            $server = Server::where('project_id', $project->id)->where('ip', $request->ip)->first();
            if (!$server) {
                return response()->json(['ok' => false, 'message' => 'Server not found'], 404);
            }
            $services = Asset::where('project_id', $project->id)->where('server_id', $server->id)->pluck('service_name')->unique()->values();
            $hostnames = Hostname::where('project_id', $project->id)->where('server_id', $server->id)->pluck('hostname')->unique()->values();
            return response()->json([
                'ok' => true,
                'project_id' => $project->id,
                'server_id' => $server->id,
                'ip' => $server->ip,
                'services' => $services,
                'hostnames' => $hostnames,
            ]);
        } catch (Throwable $e) {
            Log::error('Server details failed', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Failed to load server details', 'error' => $e->getMessage()], 500);
        }
    }

    public function overview()
    {
        $this->baseViewData = menuActive('projects_assets', 'overview', '');
        $data = $this->baseViewData;

        $data['projects'] = Project::where('admin_id', Auth::guard('admin')->id())->get();
        $data['servers'] = Server::where('project_id', $data['projects']->pluck('id')->toArray())->get();
        $data['assets'] = Asset::where('project_id', $data['projects']->pluck('id')->toArray())->get();
        $data['hostnames'] = Hostname::where('project_id', $data['projects']->pluck('id')->toArray())->get();

        return view('admin.assets.overview', $data);
    }

    public function linux(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        // Basic permission check: Admin must own the project
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $this->baseViewData = menuActive('projects_assets', 'linux', '');
        $data = $this->baseViewData;
        
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'linux';
        $data['server'] = $server;

        return view('admin.server.linux', $data);
    }

    public function mysql(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        // Basic permission check: Admin must own the project
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $this->baseViewData = menuActive('projects_assets', 'mysql', '');
        $data = $this->baseViewData;
        
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'mysql';
        $data['server'] = $server;

        return view('admin.server.mysql', $data);
    }

    public function mongodb(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        // Basic permission check: Admin must own the project
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $this->baseViewData = menuActive('projects_assets', 'mongodb', '');
        $data = $this->baseViewData;
        
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'mongodb';
        $data['server'] = $server;

        return view('admin.server.mongodb', $data);
    }

    public function redis(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        // Basic permission check: Admin must own the project
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $this->baseViewData = menuActive('projects_assets', 'redis', '');
        $data = $this->baseViewData;
        
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'redis';
        $data['server'] = $server;

        return view('admin.server.redis', $data);
    }

    public function api_log(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        // Basic permission check: Admin must own the project
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $this->baseViewData = menuActive('projects_assets', 'api_log', '');
        $data = $this->baseViewData;
        
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'api_log';
        $data['server'] = $server;

        return view('admin.server.api_log', $data);
    }

    public function scheduler(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        // Basic permission check: Admin must own the project
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $this->baseViewData = menuActive('projects_assets', 'scheduler', '');
        $data = $this->baseViewData;
        
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'scheduler';
        $data['server'] = $server;

        return view('admin.server.scheduler', $data);
    }

    public function ssl(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        // Basic permission check: Admin must own the project
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $this->baseViewData = menuActive('projects_assets', 'ssl', '');
        $data = $this->baseViewData;
        
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'ssl';
        $data['subMenu'] = 'ssl';
        $data['server'] = $server;

        return view('admin.server.ssl', $data);
    }
}
