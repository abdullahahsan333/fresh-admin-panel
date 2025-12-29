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

    public function overview()
    {
        $this->baseViewData = menuActive('projects_assets', 'overview', '');
        $data = $this->baseViewData;

        $data['projects'] = Project::where('admin_id', Auth::guard('admin')->id())->get();
        $projectIds = $data['projects']->pluck('id');
        $data['servers'] = Server::whereIn('project_id', $projectIds)->get();
        $data['assets'] = Asset::whereIn('project_id', $projectIds)->get();
        $data['hostnames'] = Hostname::whereIn('project_id', $projectIds)->get();

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

    public function linux_data(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $linuxData = fetchLinuxData($server->ip, 'livo', 60);
            $summary = calculateLinuxSummary($linuxData);
            
            $chartData = [
                'cpu' => processLatestChartData($linuxData, 'cpu_usage_percent', 6),
                'memory' => processLatestChartData($linuxData, 'memory_used_percent', 6),
                'disk' => processLatestChartData($linuxData, 'disk_usage_percent', 6)
            ];

            $topProcesses = getTopProcesses($linuxData, 6);
            
            $apiStatus = getAPIServerStatus($server->ip);

            return response()->json([
                'ok' => !empty($linuxData) || $apiStatus['connected'],
                'server_id' => $server->id,
                'ip' => $server->ip,
                'summary' => $summary,
                'chartData' => $chartData,
                'topProcesses' => $topProcesses,
                'apiStatus' => $apiStatus,
                'message' => empty($linuxData) ? 'No data available from API' : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Linux data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            
            return response()->json([
                'ok' => false,
                'message' => 'Failed to fetch Linux data',
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function mysql_data(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            // Use the helper function from APIHelper.php
            $mysqlData = fetchMySQLData($server->ip, 'livo', 60);
            $summary = calculateMySQLSummary($mysqlData);
            
            // Process chart data
            $chartData = [
                'queries' => processLatestChartData($mysqlData, 'queries_per_second', 12),
                'connections' => processLatestChartData($mysqlData, 'threads_connected', 12),
                'buffer_hit' => processLatestChartData($mysqlData, 'innodb_buffer_pool_hit_rate', 12)
            ];
            
            $apiStatus = getAPIServerStatus($server->ip);

            return response()->json([
                'ok' => !empty($mysqlData) || $apiStatus['connected'],
                'server_id' => $server->id,
                'ip' => $server->ip,
                'summary' => $summary,
                'chartData' => $chartData,
                'apiStatus' => $apiStatus,
                'message' => empty($mysqlData) ? 'No data available from API' : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('MySQL data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            
            return response()->json([
                'ok' => false,
                'message' => 'Failed to fetch MySQL data',
                'error' => $e->getMessage()
            ], 500);
        }
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

        // Return the correct view path - adjust based on your actual directory structure
        return view('admin.server.mongodb', $data);
    }

    public function mongodb_data(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            // Use the helper function from APIHelper.php
            $mongodbData = fetchMongoDBData($server->ip, 'livo', 60);
            $summary = calculateMongoDBSummary($mongodbData);
            
            // Process chart data
            $chartData = [
                'operations' => processLatestChartData($mongodbData, 'opcounters_total', 12),
                'memory' => processLatestChartData($mongodbData, 'memory_resident', 12),
                'network' => [
                    'in' => processLatestChartData($mongodbData, 'network_bytes_in', 12),
                    'out' => processLatestChartData($mongodbData, 'network_bytes_out', 12)
                ]
            ];
            
            $apiStatus = getAPIServerStatus($server->ip);

            return response()->json([
                'ok' => !empty($mongodbData) || $apiStatus['connected'],
                'server_id' => $server->id,
                'ip' => $server->ip,
                'summary' => $summary,
                'chartData' => $chartData,
                'apiStatus' => $apiStatus,
                'message' => empty($mongodbData) ? 'No data available from API' : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('MongoDB data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            
            return response()->json([
                'ok' => false,
                'message' => 'Failed to fetch MongoDB data',
                'error' => $e->getMessage()
            ], 500);
        }
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
        
        // Use helper functions (auto-loaded via composer)
        $apiLogs = fetchApiLogs($server->ip, 'livo', 15);
        $summary = calculateApiSummary($apiLogs);
        $groupedLogs = groupApiLogs($apiLogs);
        
        $data['summary'] = $summary;
        $data['logs'] = $apiLogs;
        $data['groupedLogs'] = $groupedLogs;
        $data['firstLog'] = !empty($apiLogs) ? reset($apiLogs) : null;

        return view('admin.server.api_log', $data);
    }

    public function api_log_data(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }

        $httpMethod = strtoupper($request->query('method', 'GET'));
        $logs = fetchApiLogs($server->ip, 'livo', 15, $httpMethod);
        $summary = calculateApiSummary($logs);
        $grouped = groupApiLogs($logs);
        $firstLog = !empty($logs) ? reset($logs) : null;
        $ok = true;
        $message = null;
        if (empty($logs)) {
            $ok = testAPIConnection($server->ip);
            if (!$ok) {
                $message = 'API server unreachable';
            }
        }

        return response()->json([
            'ok' => $ok,
            'server_id' => $server->id,
            'ip' => $server->ip,
            'method' => $httpMethod,
            'summary' => $summary,
            'logs' => $logs,
            'groupedLogs' => $grouped,
            'firstLog' => $firstLog,
            'message' => $message,
        ]);
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

    public function scheduler_data(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $server = Server::findOrFail($id);
        $project = Project::find($server->project_id);
        if ($project->admin_id !== Auth::guard('admin')->id()) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }

        $minutes = (int) $request->query('minutes', 15);
        $data = fetchSchedulerData($server->ip, 'livo', $minutes);
        $summary = calculateSchedulerSummary($data);

        $logs = [];
        foreach ($data as $item) {
            if (is_array($item) && isset($item['metrics']['scheduler_logs']) && is_array($item['metrics']['scheduler_logs'])) {
                foreach ($item['metrics']['scheduler_logs'] as $log) {
                    if (is_array($log)) {
                        $logs[] = $log;
                    }
                }
            }
        }

        $ok = !empty($data) || !empty($logs);
        $message = $ok ? null : 'No scheduler data';

        return response()->json([
            'ok' => $ok,
            'server_id' => $server->id,
            'ip' => $server->ip,
            'summary' => $summary,
            'logs' => $logs,
            'message' => $message,
        ]);
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

        // Fetch hostnames for this server
        $data['hostnames'] = Hostname::where('project_id', $project->id)->get();

        return view('admin.server.ssl', $data);
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
        if (empty($tree)) {
            $tree = [
                [
                    'project' => 'livo',
                    'servers' => [
                        [
                            'ip' => '178.128.112.23',
                            'services' => [
                                [
                                    'name' => 'linux',
                                    'dates' => [
                                        [
                                            'date' => '2025-12-22',
                                            'files' => [
                                                ['name' => '10:46:02AM.jsonl.gz', 'rel' => '178.128.112.23/linux/2025-12-22/10:46:02AM.jsonl.gz'],
                                                ['name' => '10:46:32AM.jsonl.gz', 'rel' => '178.128.112.23/linux/2025-12-22/10:46:32AM.jsonl.gz'],
                                                ['name' => '10:47:02AM.jsonl.gz', 'rel' => '178.128.112.23/linux/2025-12-22/10:47:02AM.jsonl.gz'],
                                                ['name' => '10:47:32AM.jsonl.gz', 'rel' => '178.128.112.23/linux/2025-12-22/10:47:32AM.jsonl.gz'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }
        
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
            } else {
                $demoMap = [
                    '178.128.112.23/linux/2025-12-22/10:46:02AM.jsonl.gz' =>
                        json_encode(['timestamp' => '2025-12-22 10:46:02', 'type' => 'linux', 'events' => [['user' => 'root', 'action' => 'login'], ['service' => 'nginx', 'status' => 'running']]], JSON_PRETTY_PRINT),
                    '178.128.112.23/linux/2025-12-22/10:46:32AM.jsonl.gz' =>
                        json_encode(['timestamp' => '2025-12-22 10:46:32', 'type' => 'linux', 'packages' => ['php' => '8.2.12', 'redis' => '7.2.4']], JSON_PRETTY_PRINT),
                    '178.128.112.23/linux/2025-12-22/10:47:02AM.jsonl.gz' =>
                        json_encode(['timestamp' => '2025-12-22 10:47:02', 'kernel' => '5.15.0-89-generic'], JSON_PRETTY_PRINT),
                    '178.128.112.23/linux/2025-12-22/10:47:32AM.jsonl.gz' =>
                        json_encode(['timestamp' => '2025-12-22 10:47:32', 'users' => ['root', 'www-data', 'ubuntu']], JSON_PRETTY_PRINT),
                ];
                if (isset($demoMap[$selectedRel])) {
                    $selectedContent = $demoMap[$selectedRel];
                    $selectedMeta = [
                        'file' => $selectedRel,
                    ];
                }
            }
        }
        
        $data['archiveTree'] = $tree;
        $data['selectedContent'] = $selectedContent;
        $data['selectedMeta'] = $selectedMeta;
        
        return view('admin.assets.archive', $data);
    }
}
