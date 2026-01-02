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
            ['name' => 'LIVO', 'description' => 'User project']
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
        $data['assets'] = $project ? Asset::where('project_id', $project->id)->get() : collect();
        $data['hostnames'] = $project ? Hostname::where('project_id', $project->id)->get() : collect();
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
                ['status' => 1]
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

        // Fetch hostnames for this server
        $data['hostnames'] = Hostname::where('project_id', $project->id)->get();

        return view('server.ssl', $data + ['layout' => 'layouts.user', 'panel' => 'user']);
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

        return view('server.linux', $data + ['layout' => 'layouts.user', 'panel' => 'user']);
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

        return view('server.mysql', $data + ['layout' => 'layouts.user', 'panel' => 'user']);
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

        return view('server.mongodb', $data + ['layout' => 'layouts.user', 'panel' => 'user']);
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

        return view('server.redis', $data + ['layout' => 'layouts.user', 'panel' => 'user']);
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

        return view('server.api_log', $data + ['layout' => 'layouts.user', 'panel' => 'user']);
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

        return view('server.scheduler', $data + ['layout' => 'layouts.user', 'panel' => 'user']);
    }
    
    public function linux_data(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            $linuxData = fetchLinuxData($server->ip, 'livo', 60);
            $summary = calculateLinuxSummary($linuxData);
            $chartData = [
                'cpu' => processLatestChartData($linuxData, 'cpu_usage_percent', 6),
                'memory' => processLatestChartData($linuxData, 'memory_used_percent', 6),
                'network' => processNetworkSpeedChartData($linuxData, 6),
            ];
            $topProcesses = getTopProcesses($linuxData);
            $apiStatus = getAPIServerStatus($server->ip);
            return response()->json([
                'ok' => !empty($linuxData) || $apiStatus['connected'],
                'server_id' => $server->id,
                'ip' => $server->ip,
                'summary' => $summary,
                'chartData' => $chartData,
                'topProcesses' => $topProcesses,
                'apiStatus' => $apiStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('User linux data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch Linux data', 'error' => $e->getMessage()], 500);
        }
    }

    public function mysql_data(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            $mysqlData = fetchMySQLData($server->ip, 'livo', 60);
            $summary = calculateMySQLSummary($mysqlData);
            $chartData = [
                'queries' => processLatestChartData($mysqlData, 'queries_per_second', 12),
                'connections' => processLatestChartData($mysqlData, 'current_connections', 12),
                'avg_query_time_ms' => processLatestChartData($mysqlData, 'avg_query_time_ms', 12),
                'network' => processNetworkSpeedChartData($mysqlData, 12),
            ];
            $apiStatus = getAPIServerStatus($server->ip);
            return response()->json([
                'ok' => !empty($mysqlData) || $apiStatus['connected'],
                'server_id' => $server->id,
                'ip' => $server->ip,
                'summary' => $summary,
                'chartData' => $chartData,
                'apiStatus' => $apiStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('User mysql data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch MySQL data', 'error' => $e->getMessage()], 500);
        }
    }

    public function mysql_slow_queries(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            $minutes = (int) $request->query('minutes', 15);
            $slowQueries = fetchMySQLSlowQueries($server->ip, 'livo', $minutes);
            return response()->json([
                'ok' => true,
                'server_id' => $server->id,
                'ip' => $server->ip,
                'slowQueries' => $slowQueries,
                'count' => count($slowQueries),
                'minutes' => $minutes,
            ]);
        } catch (\Exception $e) {
            Log::error('User mysql slow queries fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch slow queries', 'error' => $e->getMessage()], 500);
        }
    }

    public function mysql_warnings(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            $mysqlData = fetchMySQLData($server->ip, 'livo', 15);
            $warnings = detectMySQLWarnings($mysqlData);
            return response()->json(['ok' => true, 'warnings' => $warnings, 'count' => count($warnings)]);
        } catch (\Exception $e) {
            Log::error('User mysql warnings fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch warnings', 'error' => $e->getMessage()], 500);
        }
    }

    public function mysql_errors(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            $mysqlData = fetchMySQLData($server->ip, 'livo', 15);
            $errors = detectMySQLErrors($mysqlData);
            return response()->json(['ok' => true, 'errors' => $errors, 'count' => count($errors)]);
        } catch (\Exception $e) {
            Log::error('User mysql errors fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch errors', 'error' => $e->getMessage()], 500);
        }
    }

    public function mongodb_data(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            $mongodbData = fetchMongoDBData($server->ip, 'livo', 60);
            $summary = calculateMongoDBSummary($mongodbData);
            $chartData = [
                'operations' => processLatestChartData($mongodbData, 'opcounters_total', 12),
                'memory' => processLatestChartData($mongodbData, 'memory_resident', 12),
                'network' => [
                    'labels' => processLatestChartData($mongodbData, 'network_bytes_in', 12)['labels'] ?? [],
                    'in' => processLatestChartData($mongodbData, 'network_bytes_in', 12)['data'] ?? [],
                    'out' => processLatestChartData($mongodbData, 'network_bytes_out', 12)['data'] ?? [],
                ],
            ];
            $apiStatus = getAPIServerStatus($server->ip);
            return response()->json([
                'ok' => !empty($mongodbData) || $apiStatus['connected'],
                'server_id' => $server->id,
                'ip' => $server->ip,
                'summary' => $summary,
                'chartData' => $chartData,
                'apiStatus' => $apiStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('User mongodb data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch MongoDB data', 'error' => $e->getMessage()], 500);
        }
    }

    public function redis_data(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            $redisData = fetchRedisData($server->ip, 'livo', 60);
            $summary = calculateRedisSummary($redisData);
            $chartData = [
                'ops' => processLatestChartData($redisData, 'instantaneous_ops_per_sec', 10),
                'memory' => processLatestChartData($redisData, 'used_memory', 10),
                'network' => [
                    'labels' => processLatestChartData($redisData, 'instantaneous_ops_per_sec', 10)['labels'] ?? [],
                    'input' => processLatestChartData($redisData, 'total_net_input_bytes', 10)['data'] ?? [],
                    'output' => processLatestChartData($redisData, 'total_net_output_bytes', 10)['data'] ?? [],
                ],
            ];
            if (!empty($chartData['memory']['data'])) {
                $chartData['memory']['data'] = array_map(function ($v) { return round(($v ?? 0) / 1024 / 1024, 2); }, $chartData['memory']['data']);
            }
            $apiStatus = getAPIServerStatus($server->ip);
            return response()->json([
                'ok' => !empty($redisData) || $apiStatus['connected'],
                'server_id' => $server->id,
                'ip' => $server->ip,
                'summary' => $summary,
                'chartData' => $chartData,
                'apiStatus' => $apiStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('User redis data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch Redis data', 'error' => $e->getMessage()], 500);
        }
    }

    public function api_log_data(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }
        $logs = fetchApiLogs($server->ip, 'livo', 60);
        $summary = calculateApiSummary($logs, 60);
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
            'summary' => $summary,
            'logs' => $logs,
            'groupedLogs' => $grouped,
            'firstLog' => $firstLog,
            'message' => $message,
        ]);
    }

    public function scheduler_data(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $server = Server::findOrFail($id);
        $project = $this->getUserProject();
        if (!$project || $server->project_id !== $project->id) {
            return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
        }
        $date = $request->query('date');
        $start = $request->query('start');
        $end = $request->query('end');
        if ($date && $start && $end) {
            $data = fetchSchedulerDataRange($server->ip, 'livo', $date, $start, $end);
        } else {
            $minutes = (int) $request->query('minutes', 15);
            $data = fetchSchedulerData($server->ip, 'livo', $minutes);
        }
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
}
