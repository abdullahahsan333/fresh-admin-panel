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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

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
        $projectIds = Project::where('admin_id', Auth::guard('admin')->id())->pluck('id');
        $data['server'] = Server::whereIn('project_id', $projectIds)->get();
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
                ['name' => 'LIVO', 'description' => 'Default project']
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
                ['name' => 'LIVO', 'description' => 'Default project']
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

    public function archive(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->intended('/admin/login');
        }
        $this->baseViewData = menuActive('projects_assets', 'archive', '');
        $data = $this->baseViewData;
        
        $root = storage_path('app/public/archive');
        $tree = [];
        if (is_dir($root)) {
            $ips = array_values(array_filter(scandir($root), function($d){ return $d !== '.' && $d !== '..'; }));
            foreach ($ips as $ip) {
                $ipPath = $root . DIRECTORY_SEPARATOR . $ip;
                if (!is_dir($ipPath)) continue;
                $services = array_values(array_filter(scandir($ipPath), function($d){ return $d !== '.' && $d !== '..'; }));
                $svcList = [];
                foreach ($services as $svc) {
                    $svcPath = $ipPath . DIRECTORY_SEPARATOR . $svc;
                    if (!is_dir($svcPath)) continue;
                    $dates = array_values(array_filter(scandir($svcPath), function($d){ return $d !== '.' && $d !== '..'; }));
                    $dateList = [];
                    foreach ($dates as $dt) {
                        $dtPath = $svcPath . DIRECTORY_SEPARATOR . $dt;
                        if (!is_dir($dtPath)) continue;
                        $files = array_values(array_filter(scandir($dtPath), function($f){ return $f !== '.' && $f !== '..'; }));
                        $fileList = [];
                        foreach ($files as $f) {
                            $rel = $ip . '/' . $svc . '/' . $dt . '/' . $f;
                            $fileList[] = ['name' => $f, 'rel' => $rel];
                        }
                        $dateList[] = ['date' => $dt, 'files' => $fileList];
                    }
                    $svcList[] = ['name' => $svc, 'dates' => $dateList];
                }
                $tree[] = ['ip' => $ip, 'services' => $svcList];
            }
        }
        $projects = Project::where('admin_id', Auth::guard('admin')->id())->get();
        $projectIds = $projects->pluck('id');
        $servers = Server::whereIn('project_id', $projectIds)->get();
        $ipToProject = [];
        foreach ($servers as $s) {
            $proj = $projects->firstWhere('id', $s->project_id);
            $ipToProject[$s->ip] = $proj ? $proj->name : 'Archive';
        }
        $grouped = [];
        foreach ($tree as $srv) {
            $pname = $ipToProject[$srv['ip']] ?? 'Archive';
            if (!isset($grouped[$pname])) $grouped[$pname] = ['project' => $pname, 'servers' => []];
            $grouped[$pname]['servers'][] = $srv;
        }
        $tree = array_values($grouped);
        
        $selectedRel = $request->query('file');
        $selectedContent = null;
        $selectedMeta = null;
        if ($selectedRel) {
            $full = $root . DIRECTORY_SEPARATOR . str_replace(['..','\\'], ['','.'], $selectedRel);
            if (is_file($full) && str_starts_with(realpath($full), realpath($root))) {
                $content = file_get_contents($full);
                if (Str::endsWith($full, '.gz')) {
                    $content = function_exists('gzdecode') ? gzdecode($content) : $content;
                }
                $selectedContent = $content;
                $selectedMeta = [
                    'file' => $selectedRel,
                ];
            }
        }
        
        $data['archiveTree'] = $tree;
        $data['selectedContent'] = $selectedContent;
        $data['selectedMeta'] = $selectedMeta;
        
        return view('admin.assets.archive', $data);
    }

    public function archive_full(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $ip = $request->query('ip');
        $service = $request->query('service', 'linux');
        $app = $request->query('app', 'livo');
        if (!$ip || !$service) {
            return response()->json(['ok' => false, 'message' => 'Missing ip or service'], 422);
        }
        try {
            $full = fetchServiceFullArchive($ip, $service, $app);
            $dates = $full['dates'] ?? [];
            return response()->json([
                'ok' => !empty($dates),
                'ip' => $ip,
                'service' => $service,
                'app' => $app,
                'dates' => $dates,
                'raw' => $full,
            ]);
        } catch (\Throwable $e) {
            Log::error('Archive full fetch failed', ['error' => $e->getMessage(), 'ip' => $ip, 'service' => $service]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch archive', 'error' => $e->getMessage()], 500);
        }
    }
}
