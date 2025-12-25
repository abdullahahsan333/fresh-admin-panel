<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Asset;
use App\Models\Hostname;
use Throwable;

class AssetController extends Controller
{
    protected array $baseViewData = [];

    public function __construct()
    {
        $this->baseViewData = menuActive('projects_assets', '', '');
    }

    protected function getUserProject()
    {
        $uid = Auth::guard('web')->id();
        if (!$uid) return null;
        return Project::firstOrCreate(
            ['user_id' => $uid],
            ['name' => (Auth::guard('web')->user()->name ?? 'User') . '\'s Project', 'description' => 'User project']
        );
    }

    public function index()
    {
        $data = $this->baseViewData;
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $project = $this->getUserProject();
        $servers = $project ? Server::where('project_id', $project->id)->get() : collect();
        $data['server'] = $servers;
        return view('user.assets.index', $data);
    }

    public function server_store(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }
            $validated = $request->validate([
                'ip' => 'required|string|max:255',
            ]);
            $project = $this->getUserProject();
            if (!$project) {
                return response()->json(['ok' => false, 'message' => 'No project'], 404);
            }
            $server = Server::firstOrCreate(
                ['project_id' => $project->id, 'ip' => $validated['ip']],
                ['status' => 'active']
            );
            $msg = $server->wasRecentlyCreated ? 'IP added to your project' : 'IP already exists in your project';
            return response()->json(['ok' => true, 'server' => $server, 'message' => $msg]);
        } catch (Throwable $e) {
            Log::error('User server store failed', ['error' => $e->getMessage()]);
            $m = $e->getMessage() ?? '';
            if (str_contains($m, 'servers_ip_unique') || str_contains($m, 'Duplicate entry')) {
                return response()->json(['ok' => false, 'message' => 'This IP is already used in another project'], 409);
            }
            return response()->json(['ok' => false, 'message' => 'Failed to save server'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect()->intended(route('login'));
            }
            $validated = $request->validate([
                'server_id' => 'nullable|integer',
                'hostname' => 'nullable|string|max:255',
                'services' => 'array',
                'services.*' => 'string',
                'hostnames' => 'array',
                'hostnames.*' => 'string',
            ]);
            $project = $this->getUserProject();
            if (!$project) {
                throw new \RuntimeException('No project available');
            }
            $server = null;
            if (!empty($validated['server_id'])) {
                $server = Server::where('project_id', $project->id)->where('id', $validated['server_id'])->first();
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
            return redirect()->intended(route('user.assets.index'))->with('success', 'Configuration saved for server '.$server->ip);
        } catch (Throwable $e) {
            Log::error('User assets store failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => $e->getMessage()])->with('error', 'Failed to save configuration');
        }
    }

    public function server_details(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }
            $request->validate([
                'ip' => 'required|string|max:255',
            ]);
            $project = $this->getUserProject();
            if (!$project) {
                return response()->json(['ok' => false, 'message' => 'No project'], 404);
            }
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
            Log::error('User server details failed', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Failed to load server details', 'error' => $e->getMessage()], 500);
        }
    }

    public function ssl(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            abort(403);
        }
        $this->baseViewData = menuActive('projects_assets', 'ssl', '');
        $data = $this->baseViewData;
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'ssl';
        $data['subMenu'] = 'ssl';
        $data['server'] = $server;
        return view('user.server.ssl', $data);
    }
    
    public function linux(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            abort(403);
        }
        $this->baseViewData = menuActive('projects_assets', 'linux', '');
        $data = $this->baseViewData;
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'linux';
        $data['subMenu'] = 'linux';
        $data['server'] = $server;
        return view('user.server.linux', $data);
    }
    
    public function mysql(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            abort(403);
        }
        $this->baseViewData = menuActive('projects_assets', 'mysql', '');
        $data = $this->baseViewData;
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'mysql';
        $data['subMenu'] = 'mysql';
        $data['server'] = $server;
        return view('user.server.mysql', $data);
    }
    
    public function mongodb(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            abort(403);
        }
        $this->baseViewData = menuActive('projects_assets', 'mongodb', '');
        $data = $this->baseViewData;
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'mongodb';
        $data['subMenu'] = 'mongodb';
        $data['server'] = $server;
        return view('user.server.mongodb', $data);
    }
    
    public function redis(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            abort(403);
        }
        $this->baseViewData = menuActive('projects_assets', 'redis', '');
        $data = $this->baseViewData;
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'redis';
        $data['subMenu'] = 'redis';
        $data['server'] = $server;
        return view('user.server.redis', $data);
    }
    
    public function api_log(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            abort(403);
        }
        $this->baseViewData = menuActive('projects_assets', 'api_log', '');
        $data = $this->baseViewData;
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'api_log';
        $data['subMenu'] = 'api_log';
        $data['server'] = $server;
        return view('user.server.api_log', $data);
    }
    
    public function scheduler(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            abort(403);
        }
        $this->baseViewData = menuActive('projects_assets', 'scheduler', '');
        $data = $this->baseViewData;
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'scheduler';
        $data['subMenu'] = 'scheduler';
        $data['server'] = $server;
        return view('user.server.scheduler', $data);
    }
    
    public function import(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect()->intended(route('login'));
            }
            $validated = $request->validate([
                'admin_id' => 'nullable|integer',
                'project_id' => 'nullable|integer',
            ]);
            $userProject = $this->getUserProject();
            if (!$userProject) {
                return back()->with('error', 'User project not found');
            }
            $adminProject = null;
            if (!empty($validated['project_id'])) {
                $adminProject = Project::whereNotNull('admin_id')->where('id', $validated['project_id'])->first();
            } elseif (!empty($validated['admin_id'])) {
                $adminProject = Project::where('admin_id', $validated['admin_id'])->first();
            } else {
                $adminProject = Project::whereNotNull('admin_id')->first();
            }
            if (!$adminProject) {
                return back()->with('error', 'Admin project not found');
            }
            $adminServers = Server::where('project_id', $adminProject->id)->get();
            $serverIdMap = [];
            foreach ($adminServers as $as) {
                $us = Server::firstOrCreate(
                    ['project_id' => $userProject->id, 'ip' => $as->ip],
                    ['status' => $as->status]
                );
                $serverIdMap[$as->id] = $us->id;
            }
            Asset::where('project_id', $userProject->id)->delete();
            Hostname::where('project_id', $userProject->id)->delete();
            $adminAssets = Asset::where('project_id', $adminProject->id)->get();
            $assetRows = [];
            foreach ($adminAssets as $aa) {
                $sid = $serverIdMap[$aa->server_id] ?? null;
                if ($sid) {
                    $assetRows[] = [
                        'project_id' => $userProject->id,
                        'server_id' => $sid,
                        'service_name' => $aa->service_name,
                        'status' => $aa->status,
                    ];
                }
            }
            if (!empty($assetRows)) {
                Asset::insert($assetRows);
            }
            $adminHostnames = Hostname::where('project_id', $adminProject->id)->get();
            $hostnameRows = [];
            foreach ($adminHostnames as $hn) {
                $sid = $serverIdMap[$hn->server_id] ?? null;
                if ($sid) {
                    $hostnameRows[] = [
                        'project_id' => $userProject->id,
                        'server_id' => $sid,
                        'hostname' => $hn->hostname,
                    ];
                }
            }
            if (!empty($hostnameRows)) {
                Hostname::insert($hostnameRows);
            }
            return redirect()->intended(route('user.assets.index'))->with('success', 'Imported project data from admin');
        } catch (Throwable $e) {
            Log::error('User import failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to import project');
        }
    }

    public function overview()
    {
        $data = menuActive('projects_assets', 'overview', '');
        if (!Auth::guard('web')->check()) {
            return redirect()->intended(route('login'));
        }
        $project = $this->getUserProject();
        $servers = $project ? Server::where('project_id', $project->id)->get() : collect();
        $data['server'] = $servers;
        return view('user.assets.overview', $data);
    }
}
