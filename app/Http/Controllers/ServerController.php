<?php
namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Project;
use App\Models\Hostname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ServerController extends Controller
{
    protected array $baseViewData = [];

    public function __construct()
    {
        $this->baseViewData = menuActive('projects_assets', '', '');
    }

    protected function panel(Request $request): string
    {
        return $request->segment(1) === 'admin' ? 'admin' : 'user';
    }

    public function overview(Request $request)
    {
        $panel = $this->panel($request);
        $this->baseViewData = menuActive('projects_assets', 'overview', '');
        $data = $this->baseViewData;

        if ($panel === 'admin') {

            if (!Auth::guard('admin')->check()) {
                return redirect()->intended('/admin/login');
            }

            $data['projects'] = Project::where('admin_id', Auth::guard('admin')->id())->get();
            $projectIds = $data['projects']->pluck('id');
            $data['servers'] = Server::whereIn('project_id', $projectIds)->get();
            $data['assets'] = \App\Models\Asset::whereIn('project_id', $projectIds)->get();
            $data['hostnames'] = Hostname::whereIn('project_id', $projectIds)->get();
        } else {

            if (!Auth::guard('web')->check()) {
                return redirect()->intended(route('login'));
            }

            $project = Project::where('user_id', Auth::guard('web')->id())->first();
            
            if (!$project) {
                return redirect()->intended(route('user.projects.create'));
            }

            $data['servers'] = Server::where('project_id', $project->id)->get();
        }

        return view('server.overview', $data + ['layout' => "layouts.$panel", 'panel' => $panel]);
    }

    public function server_list(Request $request)
    {
        $panel = $this->panel($request);

        if ($panel === 'admin') {

            if (!Auth::guard('admin')->check()) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }

            $projectIds = Project::where('admin_id', Auth::guard('admin')->id())->pluck('id');
            
            $servers = Server::whereIn('project_id', $projectIds)->orderBy('ip')->get();
        } else {

            if (!Auth::guard('web')->check()) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }

            $project = Project::where('user_id', Auth::guard('web')->id())->first();
            
            if ($project) {
                $servers = Server::where('project_id', $project->id)->orderBy('ip')->get();
            } else {
                $servers = collect();
            }
        }
        $list = $servers->map(function ($s) {
            return [
                'id' => $s->id,
                'ip' => $s->ip,
                'status' => $s->status ?? null,
                'created_at' => $s->created_at ?? null,
                'cpu_usage' => 0,
                'memory_usage' => 0,
            ];
        })->values();

        return response()->json(['ok' => true, 'servers' => $list]);
    }

    protected function ensureViewAccess(string $panel, Server $server): Project
    {
        if ($panel === 'admin') {

            if (!Auth::guard('admin')->check()) {
                abort(302, '/admin/login');
            }

            $project = Project::find($server->project_id);

            if (!$project || $project->admin_id !== Auth::guard('admin')->id()) {
                abort(403);
            }

            return $project;

        } else {

            if (!Auth::guard('web')->check()) {
                abort(302, route('login'));
            }

            $project = Project::firstOrCreate(
                ['user_id' => Auth::guard('web')->id()],
                ['name' => 'LIVO', 'description' => 'User project']
            );

            if (!$project || $server->project_id !== $project->id) {
                abort(403);
            }

            return $project;

        }
    }

    protected function ensureDataAccess(string $panel, Server $server)
    {
        if ($panel === 'admin') {

            if (!Auth::guard('admin')->check()) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }

            $project = Project::find($server->project_id);

            if (!$project || $project->admin_id !== Auth::guard('admin')->id()) {
                return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
            }

            return $project;

        } else {

            if (!Auth::guard('web')->check()) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }

            $project = Project::firstOrCreate(
                ['user_id' => Auth::guard('web')->id()],
                ['name' => 'LIVO', 'description' => 'User project']
            );

            if (!$project || $server->project_id !== $project->id) {
                return response()->json(['ok' => false, 'message' => 'Forbidden'], 403);
            }

            return $project;

        }
    }

    public function linux(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureViewAccess($panel, $server);

        $this->baseViewData = menuActive('projects_assets', 'linux', '');
        $data = $this->baseViewData;
        
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'linux';
        $data['server'] = $server;

        return view('server.linux', $data + ['layout' => "layouts.$panel", 'panel' => $panel]);
    }

    public function linux_data(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureDataAccess($panel, $server);

        if ($project instanceof JsonResponse) return $project;

        try {
            $linuxData = fetchLinuxData($server->ip, 'livo', 60);
            $summary = calculateLinuxSummary($linuxData);
            $diskLabels = [];
            $diskSeries = [];
            if (!empty($linuxData)) {
                $slice = array_slice($linuxData, -6);
                foreach ($slice as $point) {
                    $timestamp = $point['timestamp'] ?? '';
                    $metrics = $point['metrics'] ?? [];
                    $used = (float)($metrics['disk_used_gb'] ?? 0);
                    $total = (float)($metrics['disk_total_gb'] ?? 0);
                    $pct = $total > 0 ? round(($used / $total) * 100, 2) : 0.0;
                    if ($timestamp) {
                        try {
                            $date = new \DateTime($timestamp);
                            $diskLabels[] = $date->format('H:i');
                        } catch (\Exception $e) {
                            $diskLabels[] = $timestamp;
                        }
                    } else {
                        $diskLabels[] = '';
                    }
                    $diskSeries[] = $pct;
                }
            }

            $chartData = [
                'cpu' => processLatestChartData($linuxData, 'cpu_usage_percent', 6),
                'memory' => processLatestChartData($linuxData, 'memory_used_percent', 6),
                'disk' => ['labels' => $diskLabels, 'data' => $diskSeries],
                'network' => [
                    'labels' => processLatestChartData($linuxData, 'net_input_mb', 6)['labels'] ?? [],
                    'input' => processLatestChartData($linuxData, 'net_input_mb', 6)['data'] ?? [],
                    'output' => processLatestChartData($linuxData, 'net_output_mb', 6)['data'] ?? []
                ]
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
                'message' => empty($linuxData) ? 'No data available from API' : null
            ]);
        } catch (\Exception $e) {
            Log::error('Linux data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch Linux data', 'error' => $e->getMessage()], 500);
        }
    }

    public function mysql(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureViewAccess($panel, $server);

        $this->baseViewData = menuActive('projects_assets', 'mysql', '');
        $data = $this->baseViewData;

        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'mysql';
        $data['server'] = $server;

        return view('server.mysql', $data + ['layout' => "layouts.$panel", 'panel' => $panel]);
    }

    public function mysql_data(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureDataAccess($panel, $server);
        if ($project instanceof JsonResponse) return $project;

        try {
            $mysqlData = fetchMySQLData($server->ip, $project->name ?? 'livo', 60);
            $summary = calculateMySQLSummary($mysqlData);
            
            $slowQueries = [];

            try {
                $slowQueries = fetchMySQLSlowQueries($server->ip, $project->name ?? 'livo', 15);
            } catch (\Exception $e) {
                $slowQueries = [];
            }

            $chartData = [
                'queries' => processLatestChartData($mysqlData, 'queries_per_second', 12),
                'connections' => processLatestChartData($mysqlData, 'current_connections', 12),
                'buffer_hit' => [
                    'labels' => processLatestChartData($mysqlData, 'current_connections', 12)['labels'] ?? [],
                    'data' => []
                ],
                'network' => processNetworkSpeedChartData($mysqlData, 12),
                'avg_query_time_ms' => processLatestChartData($mysqlData, 'avg_query_time_ms', 12),
            ];

            if (!empty($mysqlData)) {
                $bufferHitData = [];
                foreach ($mysqlData as $dataPoint) {
                    $metrics = $dataPoint['metrics'] ?? [];
                    $reads = $metrics['innodb_buffer_pool_reads'] ?? 0;
                    $requests = $metrics['innodb_buffer_pool_read_requests'] ?? 0;
                    $hitRate = $requests > 0 ? (1 - ($reads / $requests)) * 100 : 0;
                    $bufferHitData[] = round($hitRate, 2);
                }
                $chartData['buffer_hit']['data'] = array_slice($bufferHitData, -12);
                $latest = end($mysqlData);
                $metricsLatest = $latest['metrics'] ?? [];
                $chartData['avg_query_time_by_type'] = [
                    'select' => isset($metricsLatest['select_avg_query_time_ms']) ? (float)$metricsLatest['select_avg_query_time_ms'] : (isset($metricsLatest['select_avg_ms']) ? (float)$metricsLatest['select_avg_ms'] : null),
                    'insert' => isset($metricsLatest['insert_avg_query_time_ms']) ? (float)$metricsLatest['insert_avg_query_time_ms'] : (isset($metricsLatest['create_avg_query_time_ms']) ? (float)$metricsLatest['create_avg_query_time_ms'] : (isset($metricsLatest['insert_avg_ms']) ? (float)$metricsLatest['insert_avg_ms'] : null)),
                    'update' => isset($metricsLatest['update_avg_query_time_ms']) ? (float)$metricsLatest['update_avg_query_time_ms'] : (isset($metricsLatest['update_avg_ms']) ? (float)$metricsLatest['update_avg_ms'] : null),
                    'delete' => isset($metricsLatest['delete_avg_query_time_ms']) ? (float)$metricsLatest['delete_avg_query_time_ms'] : (isset($metricsLatest['delete_avg_ms']) ? (float)$metricsLatest['delete_avg_ms'] : null),
                ];
            }

            $apiStatus = getAPIServerStatus($server->ip);

            return response()->json([
                'ok' => !empty($mysqlData) || $apiStatus['connected'],
                'server_id' => $server->id,
                'ip' => $server->ip,
                'summary' => $summary,
                'chartData' => $chartData,
                'slowQueries' => $slowQueries,
                'slowQueriesCount' => count($slowQueries),
                'apiStatus' => $apiStatus,
                'message' => empty($mysqlData) ? 'No data available from API' : null
            ]);
        } catch (\Exception $e) {
            Log::error('MySQL data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch MySQL data', 'error' => $e->getMessage()], 500);
        }
    }

    public function mysql_slow_queries(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureDataAccess($panel, $server);
        if ($project instanceof JsonResponse) return $project;

        try {
            $minutes = (int) $request->query('minutes', 15);
            $limit = (int) $request->query('limit', 50);
            $slowQueries = fetchMySQLSlowQueries($server->ip, $project->name ?? 'livo', $minutes);
            
            if ($limit > 0 && count($slowQueries) > $limit) {
                $slowQueries = array_slice($slowQueries, 0, $limit);
            }

            return response()->json([
                'ok' => true,
                'server_id' => $server->id,
                'ip' => $server->ip,
                'slowQueries' => $slowQueries,
                'count' => count($slowQueries),
                'minutes' => $minutes,
                'limit' => $limit,
            ]);
        } catch (\Exception $e) {
            Log::error('MySQL slow queries fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch slow queries', 'error' => $e->getMessage()], 500);
        }
    }

    public function mysql_warnings(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureDataAccess($panel, $server);
        if ($project instanceof JsonResponse) return $project;

        try {
            $mysqlData = fetchMySQLData($server->ip, $project->name ?? 'livo', 15);
            $warnings = detectMySQLWarnings($mysqlData);

            return response()->json([
                'ok' => true,
                'server_id' => $server->id,
                'ip' => $server->ip,
                'warnings' => $warnings,
                'count' => count($warnings)
            ]);
        } catch (\Exception $e) {
            Log::error('MySQL warnings fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch MySQL warnings', 'error' => $e->getMessage()], 500);
        }
    }

    public function mysql_errors(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureDataAccess($panel, $server);
        if ($project instanceof JsonResponse) return $project;

        try {
            $mysqlData = fetchMySQLData($server->ip, $project->name ?? 'livo', 15);
            $errors = detectMySQLErrors($mysqlData);

            return response()->json([
                'ok' => true,
                'server_id' => $server->id,
                'ip' => $server->ip,
                'errors' => $errors,
                'count' => count($errors)
            ]);
        } catch (\Exception $e) {
            Log::error('MySQL errors fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch MySQL errors', 'error' => $e->getMessage()], 500);
        }
    }

    public function mongodb(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);

        $project = $this->ensureViewAccess($panel, $server);
        $this->baseViewData = menuActive('projects_assets', 'mongodb', '');
        $data = $this->baseViewData;

        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'mongodb';
        $data['server'] = $server;

        return view('server.mongodb', $data + ['layout' => "layouts.$panel", 'panel' => $panel]);
    }

    public function mongodb_data(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureDataAccess($panel, $server);
        if ($project instanceof JsonResponse) return $project;

        try {
            $mongodbData = fetchMongoDBData($server->ip, 'livo', 60);
            $summary = calculateMongoDBSummary($mongodbData);
            $ops = processLatestChartData($mongodbData, 'opcounters_total', 12);
            $opsBreakdown = [
                'query' => processLatestChartData($mongodbData, 'opcounters_query', 12),
                'insert' => processLatestChartData($mongodbData, 'opcounters_insert', 12),
                'update' => processLatestChartData($mongodbData, 'opcounters_update', 12),
                'delete' => processLatestChartData($mongodbData, 'opcounters_delete', 12),
                'command' => processLatestChartData($mongodbData, 'opcounters_command', 12),
            ];
            $latest = !empty($mongodbData) ? end($mongodbData) : [];
            $latestMetrics = is_array($latest) ? ($latest['metrics'] ?? []) : [];
            $memKey = isset($latestMetrics['mem_resident_mb']) ? 'mem_resident_mb' : 'memory_resident';
            $mem = processLatestChartData($mongodbData, $memKey, 12);
            $netIn = processLatestChartData($mongodbData, 'network_bytes_in', 12);
            $netOut = processLatestChartData($mongodbData, 'network_bytes_out', 12);

            if ($memKey === 'memory_resident' && !empty($mem['data'])) {
                $mem['data'] = array_map(function($v){ return round(($v ?? 0) / 1024, 2); }, $mem['data']);
            }

            if (!empty($netIn['data'])) {
                $netIn['data'] = array_map(function($v){ return round(($v ?? 0) / 1024 / 1024, 2); }, $netIn['data']);
            }

            if (!empty($netOut['data'])) {
                $netOut['data'] = array_map(function($v){ return round(($v ?? 0) / 1024 / 1024, 2); }, $netOut['data']);
            }

            $chartData = [
                'operations' => $ops,
                'opsBreakdown' => $opsBreakdown,
                'memory' => $mem,
                'network' => [
                    'labels' => $netIn['labels'] ?? [],
                    'in' => $netIn['data'] ?? [],
                    'out' => $netOut['data'] ?? []
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
            return response()->json(['ok' => false, 'message' => 'Failed to fetch MongoDB data', 'error' => $e->getMessage()], 500);
        }
    }

    public function mongodb_concerns(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureDataAccess($panel, $server);
        if ($project instanceof JsonResponse) return $project;
        
        try {
            $date = $request->query('date') ?: date('Y-m-d');
            $end = $request->query('end');
            $start = $request->query('start');
            if (!$start || !$end) {
                $now = new \DateTime('now', new \DateTimeZone('UTC'));
                $end = $now->format('H:i:s');
                $startTime = clone $now;
                $startTime->modify('-5 minutes');
                $start = $startTime->format('H:i:s');
            }
            
            $slowQueries = fetchSlowQueriesRange($server->ip, $project->name ?? 'livo', $date, $start, $end);
            $logs = fetchMongoLogs($server->ip, $project->name ?? 'livo', $date, $start, $end);
            
            return response()->json([
                'ok' => true,
                'server_id' => $server->id,
                'ip' => $server->ip,
                'slowQueries' => $slowQueries,
                'warnings' => $logs['warns'] ?? [],
                'errors' => $logs['errors'] ?? [],
                'counts' => [
                    'slow' => count($slowQueries),
                    'warns' => is_array($logs['warns'] ?? null) ? count($logs['warns']) : 0,
                    'errors' => is_array($logs['errors'] ?? null) ? count($logs['errors']) : 0,
                ],
                'date' => $date,
                'start' => $start,
                'end' => $end,
            ]);
        } catch (\Exception $e) {
            Log::error('MongoDB concerns fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch performance concerns', 'error' => $e->getMessage()], 500);
        }
    }

    public function redis(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);

        $project = $this->ensureViewAccess($panel, $server);
        $this->baseViewData = menuActive('projects_assets', 'redis', '');
        $data = $this->baseViewData;

        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'redis';
        $data['server'] = $server;

        return view('server.redis', $data + ['layout' => "layouts.$panel", 'panel' => $panel]);
    }

    public function redis_data(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureDataAccess($panel, $server);
        if ($project instanceof JsonResponse) return $project;

        try {
            $redisData = fetchRedisData($server->ip, 'livo', 60);
            $summary = calculateRedisSummary($redisData);

            $chartData = [
                'ops' => processLatestChartData($redisData, 'instantaneous_ops_per_sec', 10),
                'memory' => processLatestChartData($redisData, 'used_memory', 10),
                'network' => [
                    'labels' => processLatestChartData($redisData, 'instantaneous_ops_per_sec', 10)['labels'] ?? [],
                    'input' => processLatestChartData($redisData, 'total_net_input_bytes', 10)['data'] ?? [],
                    'output' => processLatestChartData($redisData, 'total_net_output_bytes', 10)['data'] ?? []
                ]
            ];

            if (!empty($chartData['memory']['data'])) {
                $chartData['memory']['data'] = array_map(function($v){ return round(($v ?? 0) / 1024 / 1024, 2); }, $chartData['memory']['data']);
            }

            $apiStatus = getAPIServerStatus($server->ip);

            return response()->json([
                'ok' => !empty($redisData) || $apiStatus['connected'],
                'server_id' => $server->id,
                'ip' => $server->ip,
                'summary' => $summary,
                'chartData' => $chartData,
                'apiStatus' => $apiStatus,
                'message' => empty($redisData) ? 'No data available from API' : null
            ]);
        } catch (\Exception $e) {
            Log::error('Redis data fetch failed', ['error' => $e->getMessage(), 'server' => $server->ip]);
            return response()->json(['ok' => false, 'message' => 'Failed to fetch Redis data', 'error' => $e->getMessage()], 500);
        }
    }

    public function api_log(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);

        $project = $this->ensureViewAccess($panel, $server);
        $this->baseViewData = menuActive('projects_assets', 'api_log', '');
        $data = $this->baseViewData;

        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'api_log';
        $data['server'] = $server;

        $apiLogs = fetchApiLogs($server->ip, 'livo', 60);
        $summary = calculateApiSummary($apiLogs, 60);
        $groupedLogs = groupApiLogs($apiLogs);

        $data['summary'] = $summary;
        $data['logs'] = $apiLogs;
        $data['groupedLogs'] = $groupedLogs;
        $data['firstLog'] = !empty($apiLogs) ? reset($apiLogs) : null;

        return view('server.api_log', $data + ['layout' => "layouts.$panel", 'panel' => $panel]);
    }

    public function api_log_data(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);

        $project = $this->ensureDataAccess($panel, $server);
        if ($project instanceof JsonResponse) return $project;

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

    public function scheduler(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);

        $project = $this->ensureViewAccess($panel, $server);
        $this->baseViewData = menuActive('projects_assets', 'scheduler', '');
        $data = $this->baseViewData;

        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'scheduler';
        $data['server'] = $server;

        return view('server.scheduler', $data + ['layout' => "layouts.$panel", 'panel' => $panel]);
    }

    public function scheduler_data(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);

        $project = $this->ensureDataAccess($panel, $server);
        if ($project instanceof JsonResponse) return $project;

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
        $meta = [
            'app' => null,
            'ip' => $server->ip,
            'purpose' => null,
            'timestamp' => null,
        ];

        // Handle both single-payload and list-of-payloads responses
        if (is_array($data) && isset($data['metrics'])) {
            // Single payload object
            $meta['app'] = $data['app'] ?? $meta['app'];
            $meta['ip'] = $data['ip'] ?? $meta['ip'];
            $meta['purpose'] = $data['purpose'] ?? $meta['purpose'];
            $meta['timestamp'] = $data['timestamp'] ?? $meta['timestamp'];
            if (isset($data['metrics']['scheduler_logs']) && is_array($data['metrics']['scheduler_logs'])) {
                foreach ($data['metrics']['scheduler_logs'] as $log) {
                    if (is_array($log)) $logs[] = $log;
                }
            }
        } elseif (is_array($data) && array_is_list($data)) {
            // Array of payload items
            if (!empty($data) && is_array($data[0])) {
                $meta['app'] = $data[0]['app'] ?? $meta['app'];
                $meta['ip'] = $data[0]['ip'] ?? $meta['ip'];
                $meta['purpose'] = $data[0]['purpose'] ?? $meta['purpose'];
                $meta['timestamp'] = $data[0]['timestamp'] ?? $meta['timestamp'];
            }
            foreach ($data as $item) {
                if (is_array($item) && isset($item['metrics']['scheduler_logs']) && is_array($item['metrics']['scheduler_logs'])) {
                    foreach ($item['metrics']['scheduler_logs'] as $log) {
                        if (is_array($log)) $logs[] = $log;
                    }
                }
            }
        }
        $ok = !empty($data) || !empty($logs);
        $message = $ok ? null : 'No scheduler data';
        $apiStatus = getAPIServerStatus($server->ip);

        return response()->json([
            'ok' => $ok || ($apiStatus['connected'] ?? false),
            'server_id' => $server->id,
            'ip' => $server->ip,
            'meta' => $meta,
            'summary' => $summary,
            'logs' => $logs,
            'apiStatus' => $apiStatus,
            'message' => $message,
        ]);
    }

    public function ssl(Request $request, $id)
    {
        $panel = $this->panel($request);
        $server = Server::findOrFail($id);
        $project = $this->ensureViewAccess($panel, $server);

        $this->baseViewData = menuActive('projects_assets', 'ssl', '');
        $data = $this->baseViewData;
        
        $data['activeProjectId'] = $project->id;
        $data['activeServerId'] = $server->id;
        $data['activeService'] = 'ssl';
        $data['subMenu'] = 'ssl';

        $data['server']         = $server;
        $data['hostnames']      = Hostname::where('project_id', $project->id)->get();

        return view('server.ssl', $data + ['layout' => "layouts.$panel", 'panel' => $panel]);
    }
}
