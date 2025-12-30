<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
/**
 * Base API URL for external API calls
 */
const API_BASE_URL = "http://157.245.207.91:3001/api";
/**
 * ============================================
 * GENERAL HELPER FUNCTIONS
 * ============================================
 */
/**
 * Generate demo data for services when API is unreachable
 */
function generateDemoData($service, $minutes = 60)
{
    $data = [];
    $points = 20; // Generate 20 data points
    $now = time();
    $interval = ($minutes * 60) / $points;

    for ($i = 0; $i < $points; $i++) {
        $timestamp = date('Y-m-d H:i:s', $now - (($points - 1 - $i) * $interval));
        
        $metrics = [];
        switch ($service) {
            case 'linux':
                $metrics = [
                    'cpu_usage_percent' => rand(10, 80) + (rand(0, 99) / 100),
                    'memory_used_mb' => rand(2048, 8192),
                    'memory_free_mb' => rand(1024, 4096),
                    'swap_used_mb' => rand(0, 512),
                    'swap_total_mb' => 2048,
                    'disk_used_gb' => rand(20, 100),
                    'disk_total_gb' => 200,
                    'load_avg_1' => rand(0, 4) + (rand(0, 99) / 100),
                    'load_avg_5' => rand(0, 3) + (rand(0, 99) / 100),
                    'load_avg_15' => rand(0, 2) + (rand(0, 99) / 100),
                    'uptime_seconds' => rand(86400, 864000),
                    'net_input_mb' => rand(1, 50) + (rand(0, 99) / 100),
                    'net_output_mb' => rand(1, 50) + (rand(0, 99) / 100),
                    'process_count' => rand(100, 200),
                ];
                break;
                
            case 'mysql':
                $metrics = [
                    'max_connections' => 151,
                    'current_connections' => rand(10, 50), // Changed from threads_connected
                    'threads_running' => rand(1, 10),
                    'queries_per_second' => rand(50, 500) + (rand(0, 99) / 100),
                    'slow_queries' => rand(0, 5),
                    'table_locks_waited' => rand(0, 2),
                    'innodb_buffer_pool_reads' => rand(10, 100),
                    'innodb_buffer_pool_read_requests' => rand(1000, 5000),
                    'key_reads' => rand(0, 10),
                    'key_read_requests' => rand(100, 500),
                    'bytes_received' => rand(1024 * 1024, 50 * 1024 * 1024),
                    'bytes_sent' => rand(1024 * 1024, 100 * 1024 * 1024),
                ];
                break;
                
            case 'mongodb':
                $metrics = [
                    'connections_current' => rand(20, 100),
                    'connections_available' => 50000,
                    'opcounters_total' => rand(100, 1000),
                    'opcounters_query' => rand(50, 500),
                    'opcounters_insert' => rand(10, 100),
                    'opcounters_update' => rand(10, 100),
                    'opcounters_delete' => rand(0, 50),
                    'network_bytes_in' => rand(1024 * 1024, 50 * 1024 * 1024),
                    'network_bytes_out' => rand(1024 * 1024, 100 * 1024 * 1024),
                    'memory_resident' => rand(1024 * 1024, 4096 * 1024), // KB
                    'memory_virtual' => rand(4096 * 1024, 8192 * 1024), // KB
                    'index_hit_percent' => rand(90, 100),
                    'page_faults' => rand(0, 10),
                ];
                break;
                
            case 'redis':
                $metrics = [
                    'connected_clients' => rand(50, 200),
                    'used_memory' => rand(64 * 1024 * 1024, 512 * 1024 * 1024),
                    'used_memory_rss' => rand(128 * 1024 * 1024, 1024 * 1024 * 1024),
                    'mem_fragmentation_ratio' => rand(100, 150) / 100,
                    'instantaneous_ops_per_sec' => rand(1000, 5000),
                    'rejected_connections' => 0,
                    'keyspace_hits' => rand(5000, 10000),
                    'keyspace_misses' => rand(100, 500),
                    'evicted_keys' => rand(0, 10),
                    'expired_keys' => rand(100, 1000),
                    'total_connections_received' => rand(10000, 50000),
                ];
                break;
                
            case 'scheduler':
                $metrics = [
                    'total_jobs' => rand(50, 100),
                    'active_jobs' => rand(1, 5),
                    'failed_jobs' => rand(0, 2),
                    'pending_jobs' => rand(0, 5),
                    'avg_execution_time' => rand(1, 10) + (rand(0, 99) / 100),
                ];
                break;
        }
        
        $data[] = [
            'timestamp' => $timestamp,
            'metrics' => $metrics
        ];
    }
    
    return $data;
}

function logExternalApiRequest($entry)
{
    if (!is_array($entry)) {
        return;
    }
    if (!isset($GLOBALS['external_api_requests'])) {
        $GLOBALS['external_api_requests'] = [];
    }
    $GLOBALS['external_api_requests'][] = $entry;
}

function getExternalApiRequests()
{
    return $GLOBALS['external_api_requests'] ?? [];
}

/**
 * Fetch data from external API
 */
function fetchFromAPI($serverIp, $service, $appName = 'livo', $minutes = 60, $httpMethod = 'GET')
{
    try {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $end12 = $now->format('h:i:sA');
        
        // Start time: X minutes ago
        $startTime = clone $now;
        $startTime->modify("-{$minutes} minutes");
        $start12 = $startTime->format('h:i:sA');
        
        $dateStr = $now->format('Y-m-d');
        $formattedIp = str_replace('.', '_', $serverIp);
        
        $apiUrl12 = API_BASE_URL . "/{$appName}/{$formattedIp}/{$service}/metrics?date={$dateStr}&start={$start12}&end={$end12}";
        $apiUrl24 = API_BASE_URL . "/{$appName}/{$formattedIp}/{$service}/metrics?date={$dateStr}&start=".$startTime->format('H:i:s')."&end=".$now->format('H:i:s');
        
        $method = strtolower($httpMethod);
        $debugEnabled = class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class) && \Barryvdh\Debugbar\Facades\Debugbar::isEnabled();
        $client = Http::timeout(15)->retry(2, 1000)->withOptions(['connect_timeout' => 5]);
        $t0 = microtime(true);
        $response = $method === 'post' ? $client->post($apiUrl12) : $client->get($apiUrl12);
        if ($debugEnabled) {
            \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                'service' => $service,
                'method' => strtoupper($httpMethod),
                'url' => $apiUrl12,
                'format' => '12h',
                'status' => $response->status(),
                'ok' => $response->ok(),
                'duration_ms' => round((microtime(true) - $t0) * 1000, 2),
            ], 'external_api');
        }
        logExternalApiRequest([
            'service' => $service,
            'method' => strtoupper($httpMethod),
            'url' => $apiUrl12,
            'format' => '12h',
            'status' => $response->status(),
            'ok' => $response->ok(),
            'duration_ms' => round((microtime(true) - $t0) * 1000, 2),
        ]);
        
        if (!$response->ok()) {
            $t1 = microtime(true);
            $response = $method === 'post' ? $client->post($apiUrl24) : $client->get($apiUrl24);
            if ($debugEnabled) {
                \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                    'service' => $service,
                    'method' => strtoupper($httpMethod),
                    'url' => $apiUrl24,
                    'format' => '24h',
                    'status' => $response->status(),
                    'ok' => $response->ok(),
                    'duration_ms' => round((microtime(true) - $t1) * 1000, 2),
                ], 'external_api');
            }
            logExternalApiRequest([
                'service' => $service,
                'method' => strtoupper($httpMethod),
                'url' => $apiUrl24,
                'format' => '24h',
                'status' => $response->status(),
                'ok' => $response->ok(),
                'duration_ms' => round((microtime(true) - $t1) * 1000, 2),
            ]);
            if (!$response->ok()) {
                $status = $response->status();
                $body = $response->body();
                $snippet = is_string($body) ? mb_substr($body, 0, 500) : '';
                Log::warning("API fetch failed for {$service} ({$httpMethod}): HTTP {$status} {$snippet}");
                if ($debugEnabled) {
                    \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                        'service' => $service,
                        'method' => strtoupper($httpMethod),
                        'url' => $apiUrl24,
                        'status' => $status,
                        'error' => 'response_not_ok',
                        'body_snippet' => $snippet,
                    ], 'external_api_error');
                }
                logExternalApiRequest([
                    'service' => $service,
                    'method' => strtoupper($httpMethod),
                    'url' => $apiUrl24,
                    'status' => $status,
                    'error' => 'response_not_ok',
                    'body_snippet' => $snippet,
                ]);
                return generateDemoData($service, $minutes);
            }
        }
        
        $data = null;
        try {
            $data = $response->json();
        } catch (ConnectionException $e) {
            Log::error("API connection failed for {$service}: ".$e->getMessage());
            if ($debugEnabled) {
                \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                    'service' => $service,
                    'method' => strtoupper($httpMethod),
                    'error' => 'connection_exception',
                    'message' => $e->getMessage(),
                ], 'external_api_error');
            }
            logExternalApiRequest([
                'service' => $service,
                'method' => strtoupper($httpMethod),
                'error' => 'connection_exception',
                'message' => $e->getMessage(),
            ]);
            return generateDemoData($service, $minutes);
        } catch (\Throwable $e) {
            $fallback = json_decode($response->body(), true);
            if (is_array($fallback)) {
                $data = $fallback;
            } else {
                Log::error("API JSON decode failed for {$service}: ".$e->getMessage());
                if ($debugEnabled) {
                    \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                        'service' => $service,
                        'method' => strtoupper($httpMethod),
                        'error' => 'json_decode_failed',
                        'message' => $e->getMessage(),
                    ], 'external_api_error');
                }
                logExternalApiRequest([
                    'service' => $service,
                    'method' => strtoupper($httpMethod),
                    'error' => 'json_decode_failed',
                    'message' => $e->getMessage(),
                ]);
                $data = [];
            }
        }
        
        if (empty($data)) {
            return generateDemoData($service, $minutes);
        }
        
        return is_array($data) ? $data : generateDemoData($service, $minutes);
    } catch (\Exception $e) {
        Log::error("Failed to fetch {$service} data: " . $e->getMessage());
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class) && \Barryvdh\Debugbar\Facades\Debugbar::isEnabled()) {
            \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                'service' => $service,
                'method' => strtoupper($httpMethod),
                'error' => 'unexpected_exception',
                'message' => $e->getMessage(),
            ], 'external_api_error');
        }
        logExternalApiRequest([
            'service' => $service,
            'method' => strtoupper($httpMethod),
            'error' => 'unexpected_exception',
            'message' => $e->getMessage(),
        ]);
        return generateDemoData($service, $minutes);
    }
}

/**
 * Test API connection
 */
function testAPIConnection($serverIp, $appName = 'livo')
{
    try {
        $formattedIp = str_replace('.', '_', $serverIp);
        $testUrl = API_BASE_URL . "/{$appName}/{$formattedIp}/linux/metrics?date=" . date('Y-m-d');
        
        $debugEnabled = class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class) && \Barryvdh\Debugbar\Facades\Debugbar::isEnabled();
        $t0 = microtime(true);
        $response = Http::timeout(10)->get($testUrl);
        if ($debugEnabled) {
            \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                'service' => 'linux',
                'method' => 'GET',
                'url' => $testUrl,
                'status' => $response->status(),
                'ok' => $response->ok(),
                'duration_ms' => round((microtime(true) - $t0) * 1000, 2),
            ], 'external_api');
        }
        logExternalApiRequest([
            'service' => 'linux',
            'method' => 'GET',
            'url' => $testUrl,
            'status' => $response->status(),
            'ok' => $response->ok(),
            'duration_ms' => round((microtime(true) - $t0) * 1000, 2),
        ]);
        return $response->ok();
        
    } catch (\Exception $e) {
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class) && \Barryvdh\Debugbar\Facades\Debugbar::isEnabled()) {
            \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                'service' => 'linux',
                'method' => 'GET',
                'error' => 'unexpected_exception',
                'message' => $e->getMessage(),
            ], 'external_api_error');
        }
        logExternalApiRequest([
            'service' => 'linux',
            'method' => 'GET',
            'error' => 'unexpected_exception',
            'message' => $e->getMessage(),
        ]);
        return false;
    }
}

/**
 * Get API server status
 */
function getAPIServerStatus($serverIp, $appName = 'livo')
{
    $isConnected = testAPIConnection($serverIp, $appName);
    
    return [
        'connected' => $isConnected,
        'message' => $isConnected ? 'API server is reachable' : 'Cannot connect to API server',
        'timestamp' => now()->toDateTimeString(),
    ];
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Format uptime to human readable format
 */
function formatUptime($seconds)
{
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    if ($days > 0) {
        return sprintf('%dd %dh %dm', $days, $hours, $minutes);
    } elseif ($hours > 0) {
        return sprintf('%dh %dm', $hours, $minutes);
    } else {
        return sprintf('%dm', $minutes);
    }
}

/**
 * ============================================
 * LINUX MONITORING FUNCTIONS
 * ============================================
 */

/**
 * Fetch Linux monitoring data
 */
function fetchLinuxData($serverIp, $appName = 'livo', $minutes = 60)
{
    return fetchFromAPI($serverIp, 'linux', $appName, $minutes);
}

/**
 * Calculate Linux system summary statistics
 */
function calculateLinuxSummary($linuxData)
{
    if (empty($linuxData)) {
        return [
            'cpu_usage' => 0,
            'memory_used' => 0,
            'memory_total' => 0,
            'memory_percent' => 0,
            'disk_used' => 0,
            'disk_total' => 0,
            'disk_percent' => 0,
            'load_avg_1' => 0,
            'load_avg_5' => 0,
            'load_avg_15' => 0,
            'uptime_seconds' => 0,
            'network_in' => 0,
            'network_out' => 0,
            'process_count' => 0,
            'swap_used' => 0,
            'swap_total' => 0,
            'swap_percent' => 0,
        ];
    }
    
    // Get latest data point
    $latest = end($linuxData);
    $metrics = $latest['metrics'] ?? [];
    
    // CPU Usage
    $cpuUsage = $metrics['cpu_usage_percent'] ?? 0;
    
    // Memory
    $memoryUsed = $metrics['memory_used_mb'] ?? 0;
    $memoryFree = $metrics['memory_free_mb'] ?? 0;
    $memoryTotal = $memoryUsed + $memoryFree;
    $memoryPercent = $memoryTotal > 0 ? ($memoryUsed / $memoryTotal * 100) : 0;
    
    // Swap
    $swapUsed = $metrics['swap_used_mb'] ?? 0;
    $swapTotal = $metrics['swap_total_mb'] ?? 0;
    $swapPercent = $swapTotal > 0 ? ($swapUsed / $swapTotal * 100) : 0;
    
    // Disk
    $diskUsed = $metrics['disk_used_gb'] ?? 0;
    $diskTotal = $metrics['disk_total_gb'] ?? 0;
    $diskPercent = $diskTotal > 0 ? ($diskUsed / $diskTotal * 100) : 0;
    
    // Load Average
    $loadAvg1 = $metrics['load_avg_1'] ?? 0;
    $loadAvg5 = $metrics['load_avg_5'] ?? 0;
    $loadAvg15 = $metrics['load_avg_15'] ?? 0;
    
    // Uptime
    $uptimeSeconds = $metrics['uptime_seconds'] ?? 0;
    
    // Network
    $networkIn = $metrics['net_input_mb'] ?? 0;
    $networkOut = $metrics['net_output_mb'] ?? 0;
    
    // Process count
    $processCount = $metrics['process_count'] ?? 0;
    
    return [
        'cpu_usage' => round($cpuUsage, 1),
        'memory_used' => round($memoryUsed, 1),
        'memory_total' => round($memoryTotal, 1),
        'memory_percent' => round($memoryPercent, 1),
        'swap_used' => round($swapUsed, 1),
        'swap_total' => round($swapTotal, 1),
        'swap_percent' => round($swapPercent, 1),
        'disk_used' => round($diskUsed, 1),
        'disk_total' => round($diskTotal, 1),
        'disk_percent' => round($diskPercent, 1),
        'load_avg_1' => round($loadAvg1, 2),
        'load_avg_5' => round($loadAvg5, 2),
        'load_avg_15' => round($loadAvg15, 2),
        'uptime_seconds' => $uptimeSeconds,
        'network_in' => round($networkIn, 2),
        'network_out' => round($networkOut, 2),
        'process_count' => $processCount,
    ];
}

/**
 * Get server status based on metrics
 */
function getServerStatus($linuxData)
{
    if (empty($linuxData)) {
        return 'unknown';
    }
    
    $summary = calculateLinuxSummary($linuxData);
    
    $cpuUsage = $summary['cpu_usage'] ?? 0;
    $memoryPercent = $summary['memory_percent'] ?? 0;
    $loadAvg = $summary['load_avg_1'] ?? 0;
    
    // Determine status
    if ($cpuUsage > 90 || $memoryPercent > 90 || $loadAvg > 4) {
        return 'critical';
    } elseif ($cpuUsage > 70 || $memoryPercent > 70 || $loadAvg > 2) {
        return 'warning';
    } else {
        return 'healthy';
    }
}

/**
 * Get top processes from Linux data
 */
function getTopProcesses($linuxData, $limit = 0)
{
    if (empty($linuxData)) {
        return [];
    }
    
    $latest = end($linuxData);
    $metrics = $latest['metrics'] ?? [];
    
    $default = ['name' => '', 'cpu' => 0, 'mem' => 0, 'pid' => 0];
    $processes = [];
    if (isset($metrics['processes']) && is_array($metrics['processes'])) {
        foreach ($metrics['processes'] as $p) {
            $name = $p['name'] ?? '';
            $cpu = isset($p['total_cpu_percent']) ? (float)$p['total_cpu_percent'] : 0;
            $mem = isset($p['total_mem_percent']) ? (float)$p['total_mem_percent'] : 0;
            $pid = isset($p['instances']) ? (int)$p['instances'] : 0;
            if ($cpu <= 0 && $mem <= 0) {
                continue;
            }
            $processes[] = ['name' => $name, 'cpu' => $cpu, 'mem' => $mem, 'pid' => $pid];
        }
    }
    if (empty($processes)) {
        $processes = [$default];
    }
    if ($limit > 0) {
        return array_slice($processes, 0, $limit);
    }
    return $processes;
}

/**
 * Process Linux data for charts
 */
function processLinuxChartData($linuxData, $metricType = 'cpu_usage_percent')
{
    if (empty($linuxData)) {
        return [
            'labels' => [],
            'data' => [],
        ];
    }
    
    // Limit to 20 data points for better performance
    $dataPoints = min(count($linuxData), 20);
    $step = max(1, floor(count($linuxData) / $dataPoints));
    
    $labels = [];
    $data = [];
    
    for ($i = 0; $i < count($linuxData); $i += $step) {
        $item = $linuxData[$i];
        $timestamp = $item['timestamp'] ?? '';
        $metrics = $item['metrics'] ?? [];
        
        if ($timestamp) {
            // Format time
            $date = new \DateTime($timestamp);
            $labels[] = $date->format('H:i');
        }
        
        // Get the requested metric
        $value = $metrics[$metricType] ?? 0;
        $data[] = round($value, 2);
    }
    
    return [
        'labels' => $labels,
        'data' => $data,
    ];
}

/**
 * ============================================
 * MYSQL MONITORING FUNCTIONS
 * ============================================
 */

/**
 * Fetch MySQL monitoring data
 */
function fetchMySQLData($serverIp, $appName = 'livo', $minutes = 60)
{
    return fetchFromAPI($serverIp, 'mysql', $appName, $minutes);
}

/**
 * Fetch MySQL slow queries
 */
function fetchMySQLSlowQueries($serverIp, $appName = 'livo', $minutes = 15)
{
    $data = fetchFromAPI($serverIp, 'slow_queries', $appName, $minutes);
    $queries = [];
    foreach ($data as $item) {
        if (!is_array($item)) continue;
        $metrics = $item['metrics'] ?? [];
        $list = $metrics['mysql_slow_queries'] ?? [];
        if (is_array($list)) {
            foreach ($list as $q) {
                if (!is_array($q)) continue;
                $queries[] = [
                    'start_time' => $q['start_time'] ?? null,
                    'user_host' => $q['user_host'] ?? null,
                    'query_time' => $q['query_time'] ?? null,
                    'sql_text' => $q['sql_text'] ?? null,
                ];
            }
        }
    }
    return $queries;
}

/**
 * Calculate MySQL summary statistics
 */
function calculateMySQLSummary($mysqlData)
{
    if (empty($mysqlData)) {
        return [
            'connections' => 0,
            'connections_used' => 0,
            'connections_percent' => 0,
            'queries_per_second' => 0,
            'slow_queries' => 0,
            'table_locks' => 0,
            'innodb_buffer_pool_hit' => 0,
            'key_buffer_hit' => 0,
            'threads_running' => 0,
            'threads_connected' => 0,
            'bytes_received' => 0,
            'bytes_sent' => 0,
            'innodb_buffer_pool_reads' => 0,
            'innodb_buffer_pool_read_requests' => 0,
            'max_connections' => 0,
            'current_connections' => 0,
        ];
    }
    
    $latest = end($mysqlData);
    $metrics = $latest['metrics'] ?? [];
    
    // Map API field names to expected field names
    $maxConnections = $metrics['max_connections'] ?? 0;
    $currentConnections = $metrics['current_connections'] ?? $metrics['threads_connected'] ?? 0;
    $connectionsPercent = $maxConnections > 0 ? ($currentConnections / $maxConnections * 100) : 0;
    
    $queriesPerSecond = $metrics['queries_per_second'] ?? 0;
    $slowQueries = $metrics['slow_queries'] ?? 0;
    $tableLocks = $metrics['table_locks_waited'] ?? 0;
    
    $innodbBufferPoolReads = $metrics['innodb_buffer_pool_reads'] ?? 0;
    $innodbBufferPoolReadRequests = $metrics['innodb_buffer_pool_read_requests'] ?? 0;
    $innodbBufferPoolHit = ($innodbBufferPoolReadRequests > 0) ? 
        (1 - ($innodbBufferPoolReads / $innodbBufferPoolReadRequests)) * 100 : 0;
    
    // Key buffer hit rate (not provided in API, using demo or default)
    $keyReads = $metrics['key_reads'] ?? 0;
    $keyReadRequests = $metrics['key_read_requests'] ?? 0;
    $keyBufferHit = ($keyReadRequests > 0) ? 
        (1 - ($keyReads / $keyReadRequests)) * 100 : 0;
    
    // Network bytes - convert from bytes to MB
    $bytesReceived = isset($metrics['bytes_received']) ? $metrics['bytes_received'] / 1024 / 1024 : 0;
    $bytesSent = isset($metrics['bytes_sent']) ? $metrics['bytes_sent'] / 1024 / 1024 : 0;
    
    return [
        'connections' => $maxConnections,
        'connections_used' => $currentConnections,
        'connections_percent' => round($connectionsPercent, 1),
        'queries_per_second' => round($queriesPerSecond, 2),
        'slow_queries' => $slowQueries,
        'table_locks' => $tableLocks,
        'innodb_buffer_pool_hit' => round($innodbBufferPoolHit, 1),
        'key_buffer_hit' => round($keyBufferHit, 1),
        'threads_running' => $metrics['threads_running'] ?? 0,
        'threads_connected' => $currentConnections,
        'bytes_received' => round($bytesReceived, 2), // MB
        'bytes_sent' => round($bytesSent, 2), // MB
        'innodb_buffer_pool_reads' => $innodbBufferPoolReads,
        'innodb_buffer_pool_read_requests' => $innodbBufferPoolReadRequests,
        'max_connections' => $maxConnections,
        'current_connections' => $currentConnections,
    ];
}

/**
 * Get MySQL status
 */
function getMySQLStatus($mysqlData)
{
    if (empty($mysqlData)) {
        return 'unknown';
    }
    
    $summary = calculateMySQLSummary($mysqlData);
    
    $connectionsPercent = $summary['connections_percent'] ?? 0;
    $threadsRunning = $summary['threads_running'] ?? 0;
   
    if ($connectionsPercent > 90 || $threadsRunning > 50) {
        return 'critical';
    } elseif ($connectionsPercent > 70 || $threadsRunning > 30) {
        return 'warning';
    } else {
        return 'healthy';
    }
}

/**
 * ============================================
 * MONGODB MONITORING FUNCTIONS
 * ============================================
 */

/**
 * Fetch MongoDB monitoring data
 */
function fetchMongoDBData($serverIp, $appName = 'livo', $minutes = 60)
{
    return fetchFromAPI($serverIp, 'mongodb', $appName, $minutes);
}

/**
 * Calculate MongoDB summary statistics
 */
function calculateMongoDBSummary($mongoData)
{
    if (empty($mongoData)) {
        return [
            'connections_current' => 0,
            'connections_available' => 0,
            'operations_per_second' => 0,
            'queries_per_second' => 0,
            'inserts_per_second' => 0,
            'updates_per_second' => 0,
            'deletes_per_second' => 0,
            'network_in' => 0,
            'network_out' => 0,
            'memory_resident' => 0,
            'memory_virtual' => 0,
            'index_hit_percent' => 0,
            'page_faults' => 0,
        ];
    }
    
    $latest = end($mongoData);
    $metrics = $latest['metrics'] ?? [];
    
    $connectionsCurrent = $metrics['connections_current'] ?? 0;
    $connectionsAvailable = $metrics['connections_available'] ?? 0;
    $connectionsPercent = $connectionsAvailable > 0 ? ($connectionsCurrent / $connectionsAvailable * 100) : 0;
    
    return [
        'connections_current' => $connectionsCurrent,
        'connections_available' => $connectionsAvailable,
        'connections_percent' => round($connectionsPercent, 1),
        'operations_per_second' => round($metrics['opcounters_total'] ?? 0, 2),
        'queries_per_second' => round($metrics['opcounters_query'] ?? 0, 2),
        'inserts_per_second' => round($metrics['opcounters_insert'] ?? 0, 2),
        'updates_per_second' => round($metrics['opcounters_update'] ?? 0, 2),
        'deletes_per_second' => round($metrics['opcounters_delete'] ?? 0, 2),
        'network_in' => round(($metrics['network_bytes_in'] ?? 0) / 1024 / 1024, 2), // MB
        'network_out' => round(($metrics['network_bytes_out'] ?? 0) / 1024 / 1024, 2), // MB
        'memory_resident' => round(($metrics['memory_resident'] ?? 0) / 1024, 1), // MB
        'memory_virtual' => round(($metrics['memory_virtual'] ?? 0) / 1024, 1), // MB
        'index_hit_percent' => round($metrics['index_hit_percent'] ?? 0, 1),
        'page_faults' => $metrics['page_faults'] ?? 0,
    ];
}

/**
 * Get MongoDB status
 */
function getMongoDBStatus($mongoData)
{
    if (empty($mongoData)) {
        return 'unknown';
    }
    
    $summary = calculateMongoDBSummary($mongoData);
    
    $connectionsPercent = $summary['connections_percent'] ?? 0;
    $memoryResident = $summary['memory_resident'] ?? 0;
    
    if ($connectionsPercent > 90 || $memoryResident > 2048) { // > 2GB
        return 'critical';
    } elseif ($connectionsPercent > 70 || $memoryResident > 1024) { // > 1GB
        return 'warning';
    } else {
        return 'healthy';
    }
}

/**
 * ============================================
 * REDIS MONITORING FUNCTIONS
 * ============================================
 */

/**
 * Fetch Redis monitoring data
 */
function fetchRedisData($serverIp, $appName = 'livo', $minutes = 60)
{
    return fetchFromAPI($serverIp, 'redis', $appName, $minutes);
}

/**
 * Calculate Redis summary statistics
 */
function calculateRedisSummary($redisData)
{
    if (empty($redisData)) {
        return [
            'connected_clients' => 0,
            'used_memory' => 0,
            'used_memory_rss' => 0,
            'memory_fragmentation_ratio' => 0,
            'hits_per_second' => 0,
            'misses_per_second' => 0,
            'hit_rate' => 0,
            'keyspace_hits' => 0,
            'keyspace_misses' => 0,
            'evicted_keys' => 0,
            'expired_keys' => 0,
            'instantaneous_ops_per_sec' => 0,
            'total_connections_received' => 0,
            'connected_slaves' => 0,
            'blocked_clients' => 0,
            'uptime_in_seconds' => 0,
            'redis_version' => null,
            'db0' => null,
            'network_input' => 0,
            'network_output' => 0,
            'commandstats_get' => 0,
            'commandstats_set' => 0,
            'commandstats_hget' => 0,
            'commandstats_hset' => 0,
        ];
    }
    
    $latest = end($redisData);
    $metrics = $latest['metrics'] ?? [];
    
    $hits = $metrics['keyspace_hits'] ?? 0;
    $misses = $metrics['keyspace_misses'] ?? 0;
    $hitRate = ($hits + $misses) > 0 ? ($hits / ($hits + $misses) * 100) : 0;
    
    return [
        'connected_clients' => $metrics['connected_clients'] ?? 0,
        'used_memory' => round(($metrics['used_memory'] ?? 0) / 1024 / 1024, 2), // MB
        'used_memory_rss' => round(($metrics['used_memory_rss'] ?? 0) / 1024 / 1024, 2), // MB
        'memory_fragmentation_ratio' => round($metrics['mem_fragmentation_ratio'] ?? 0, 2),
        'hits_per_second' => round($metrics['instantaneous_ops_per_sec'] ?? 0, 2),
        'misses_per_second' => round($metrics['rejected_connections'] ?? 0, 2),
        'hit_rate' => round($hitRate, 1),
        'keyspace_hits' => $hits,
        'keyspace_misses' => $misses,
        'evicted_keys' => $metrics['evicted_keys'] ?? 0,
        'expired_keys' => $metrics['expired_keys'] ?? 0,
        'instantaneous_ops_per_sec' => round($metrics['instantaneous_ops_per_sec'] ?? 0, 2),
        'total_connections_received' => $metrics['total_connections_received'] ?? 0,
        'connected_slaves' => $metrics['connected_slaves'] ?? 0,
        'blocked_clients' => $metrics['blocked_clients'] ?? 0,
        'uptime_in_seconds' => $metrics['uptime_in_seconds'] ?? 0,
        'redis_version' => $metrics['redis_version'] ?? null,
        'db0' => $metrics['db0'] ?? null,
        'network_input' => isset($metrics['total_net_input_bytes']) ? round(($metrics['total_net_input_bytes'] ?? 0) / 1024 / 1024, 2) : 0,
        'network_output' => isset($metrics['total_net_output_bytes']) ? round(($metrics['total_net_output_bytes'] ?? 0) / 1024 / 1024, 2) : 0,
        'commandstats_get' => $metrics['commandstats_get'] ?? 0,
        'commandstats_set' => $metrics['commandstats_set'] ?? 0,
        'commandstats_hget' => $metrics['commandstats_hget'] ?? 0,
        'commandstats_hset' => $metrics['commandstats_hset'] ?? 0,
    ];
}

/**
 * Get Redis status
 */
function getRedisStatus($redisData)
{
    if (empty($redisData)) {
        return 'unknown';
    }
    
    $summary = calculateRedisSummary($redisData);
    
    $memoryFragmentation = $summary['memory_fragmentation_ratio'] ?? 0;
    $hitRate = $summary['hit_rate'] ?? 0;
    $connectedClients = $summary['connected_clients'] ?? 0;
    
    if ($memoryFragmentation > 1.5 || $hitRate < 80 || $connectedClients > 100) {
        return 'critical';
    } elseif ($memoryFragmentation > 1.2 || $hitRate < 90 || $connectedClients > 50) {
        return 'warning';
    } else {
        return 'healthy';
    }
}

/**
 * ============================================
 * API LOGS FUNCTIONS (UPDATED TO FIX ERROR)
 * ============================================
 */

/**
 * Generate demo API logs
 */
function generateDemoApiLogs($minutes = 5)
{
    $logs = [];
    $count = 50;
    $now = time();
    $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
    $endpoints = [
        '/api/users', '/api/products', '/api/orders', '/api/auth/login', 
        '/api/auth/register', '/api/settings', '/api/notifications'
    ];
    $statuses = [200, 201, 204, 400, 401, 403, 404, 500];
    
    for ($i = 0; $i < $count; $i++) {
        $timestamp = date('Y-m-d H:i:s', $now - rand(0, $minutes * 60));
        $method = $methods[array_rand($methods)];
        $url = $endpoints[array_rand($endpoints)];
        $status = $statuses[array_rand($statuses)];
        
        // Weigh success statuses higher
        if (rand(0, 10) > 2) {
            $status = 200;
        }
        
        $log = [
            '_id' => 'demo_' . uniqid(),
            'method' => $method,
            'url' => $url,
            'status' => $status,
            'response_time_ms' => rand(10, 500) + (rand(0, 99) / 100),
            'ip' => '192.168.1.' . rand(1, 255),
            'timestamp' => $timestamp,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'request_headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Bearer token'],
            'response_headers' => ['Content-Type' => 'application/json'],
            'request_body' => ['demo' => true, 'id' => rand(1, 1000)],
            'response_body' => ['success' => $status < 400, 'data' => 'Demo data'],
        ];
        
        $logs['log' . ($i + 1)] = $log;
    }
    
    // Sort by timestamp desc
    uasort($logs, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    
    return $logs;
}

/**
 * Fetch API logs
 */
function fetchApiLogs($serverIp, $appName = 'livo', $minutes = 5, $httpMethod = 'GET')
{
    try {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $end12 = $now->format('h:i:sA');
        
        $startTime = clone $now;
        $startTime->modify("-{$minutes} minutes");
        $start12 = $startTime->format('h:i:sA');
        
        $dateStr = $now->format('Y-m-d');
        $formattedIp = str_replace('.', '_', $serverIp);
        
        $apiUrl12 = API_BASE_URL . "/{$appName}/{$formattedIp}/api_log/metrics?date={$dateStr}&start={$start12}&end={$end12}";
        $apiUrl24 = API_BASE_URL . "/{$appName}/{$formattedIp}/api_log/metrics?date={$dateStr}&start=".$startTime->format('H:i:s')."&end=".$now->format('H:i:s');
        
        $method = strtolower($httpMethod);
        $debugEnabled = class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class) && \Barryvdh\Debugbar\Facades\Debugbar::isEnabled();
        $client = Http::timeout(15)->retry(2, 1000)->withOptions(['connect_timeout' => 5]);
        $t0 = microtime(true);
        $response = $method === 'post' ? $client->post($apiUrl12) : $client->get($apiUrl12);
        if ($debugEnabled) {
            \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                'service' => 'api_log',
                'method' => strtoupper($httpMethod),
                'url' => $apiUrl12,
                'format' => '12h',
                'status' => $response->status(),
                'ok' => $response->ok(),
                'duration_ms' => round((microtime(true) - $t0) * 1000, 2),
            ], 'external_api');
        }
        logExternalApiRequest([
            'service' => 'api_log',
            'method' => strtoupper($httpMethod),
            'url' => $apiUrl12,
            'format' => '12h',
            'status' => $response->status(),
            'ok' => $response->ok(),
            'duration_ms' => round((microtime(true) - $t0) * 1000, 2),
        ]);
        
        if (!$response->ok()) {
            $t1 = microtime(true);
            $response = $method === 'post' ? $client->post($apiUrl24) : $client->get($apiUrl24);
            if ($debugEnabled) {
                \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                    'service' => 'api_log',
                    'method' => strtoupper($httpMethod),
                    'url' => $apiUrl24,
                    'format' => '24h',
                    'status' => $response->status(),
                    'ok' => $response->ok(),
                    'duration_ms' => round((microtime(true) - $t1) * 1000, 2),
                ], 'external_api');
            }
            logExternalApiRequest([
                'service' => 'api_log',
                'method' => strtoupper($httpMethod),
                'url' => $apiUrl24,
                'format' => '24h',
                'status' => $response->status(),
                'ok' => $response->ok(),
                'duration_ms' => round((microtime(true) - $t1) * 1000, 2),
            ]);
            if (!$response->ok()) {
                $status = $response->status();
                $body = $response->body();
                $snippet = is_string($body) ? mb_substr($body, 0, 500) : '';
                Log::warning("API logs fetch failed: HTTP {$status} {$snippet}");
                if ($debugEnabled) {
                    \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                        'service' => 'api_log',
                        'method' => strtoupper($httpMethod),
                        'url' => $apiUrl24,
                        'status' => $status,
                        'error' => 'response_not_ok',
                        'body_snippet' => $snippet,
                    ], 'external_api_error');
                }
                logExternalApiRequest([
                    'service' => 'api_log',
                    'method' => strtoupper($httpMethod),
                    'url' => $apiUrl24,
                    'status' => $status,
                    'error' => 'response_not_ok',
                    'body_snippet' => $snippet,
                ]);
                return generateDemoApiLogs($minutes);
            }
        }
        
        $data = null;
        try {
            $data = $response->json();
        } catch (ConnectionException $e) {
            Log::error("API logs connection failed: " . $e->getMessage());
            if ($debugEnabled) {
                \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                    'service' => 'api_log',
                    'method' => strtoupper($httpMethod),
                    'error' => 'connection_exception',
                    'message' => $e->getMessage(),
                ], 'external_api_error');
            }
            logExternalApiRequest([
                'service' => 'api_log',
                'method' => strtoupper($httpMethod),
                'error' => 'connection_exception',
                'message' => $e->getMessage(),
            ]);
            return generateDemoApiLogs($minutes);
        } catch (\Throwable $e) {
            $fallback = json_decode($response->body(), true);
            if (is_array($fallback)) {
                $data = $fallback;
            } else {
                Log::error("API logs JSON decode failed: " . $e->getMessage());
                if ($debugEnabled) {
                    \Barryvdh\Debugbar\Facades\Debugbar::addMessage([
                        'service' => 'api_log',
                        'method' => strtoupper($httpMethod),
                        'error' => 'json_decode_failed',
                        'message' => $e->getMessage(),
                    ], 'external_api_error');
                }
                logExternalApiRequest([
                    'service' => 'api_log',
                    'method' => strtoupper($httpMethod),
                    'error' => 'json_decode_failed',
                    'message' => $e->getMessage(),
                ]);
                $data = [];
            }
        }
        
        // Ensure data is an array
        if (!is_array($data) || empty($data)) {
            Log::warning("API logs returned non-array or empty data");
            return generateDemoApiLogs($minutes);
        }
        
        // Flatten API logs
        $logs = [];
        $counter = 1;
        
        foreach ($data as $item) {
            if (is_array($item) && isset($item['metrics']['api_logs']) && is_array($item['metrics']['api_logs'])) {
                foreach ($item['metrics']['api_logs'] as $log) {
                    if (is_array($log)) {
                        $log['id'] = 'log' . $counter;
                        $logs['log' . $counter] = $log;
                        $counter++;
                    }
                }
            }
        }
        
        if (empty($logs)) {
             return generateDemoApiLogs($minutes);
        }
        
        return $logs;
        
    } catch (\Exception $e) {
        Log::error('Failed to fetch API logs: ' . $e->getMessage());
        return generateDemoApiLogs($minutes);
    }
}

/**
 * Calculate API logs summary
 */
function calculateApiSummary($logs)
{
    if (empty($logs)) {
        return [
            'request_count' => 0,
            'requests_per_min' => 0,
            'failed_requests' => 0,
            'avg_response_time' => 0,
            'min_response_time' => 0,
            'max_response_time' => 0,
            'success_rate' => 0,
        ];
    }
    
    $totalRequests = count($logs);
    $failedRequests = 0;
    $totalResponseTime = 0;
    $responseTimeCount = 0;
    $minResponseTime = PHP_INT_MAX;
    $maxResponseTime = 0;
    
    foreach ($logs as $log) {
        if (!is_array($log)) continue;
        
        // Count failed requests (4xx or 5xx status)
        if (isset($log['status']) && is_numeric($log['status']) && $log['status'] >= 400) {
            $failedRequests++;
        }
        
        // Calculate response time
        $responseTime = $log['response_time_ms'] ?? $log['responseTime'] ?? 0;
        if (is_numeric($responseTime) && $responseTime > 0) {
            $totalResponseTime += $responseTime;
            $responseTimeCount++;
            $minResponseTime = min($minResponseTime, $responseTime);
            $maxResponseTime = max($maxResponseTime, $responseTime);
        }
    }
    
    // Calculate requests per minute
    $requestsPerMin = $totalRequests / 5;
    
    // Calculate average response time
    $avgResponseTime = $responseTimeCount > 0 ? $totalResponseTime / $responseTimeCount : 0;
    
    // Calculate success rate
    $successRate = $totalRequests > 0 ? (($totalRequests - $failedRequests) / $totalRequests * 100) : 0;
    
    if ($minResponseTime == PHP_INT_MAX) {
        $minResponseTime = 0;
    }
    
    return [
        'request_count' => $totalRequests,
        'requests_per_min' => round($requestsPerMin, 1),
        'failed_requests' => $failedRequests,
        'avg_response_time' => round($avgResponseTime, 2),
        'min_response_time' => round($minResponseTime, 2),
        'max_response_time' => round($maxResponseTime, 2),
        'success_rate' => round($successRate, 1),
    ];
}

/**
 * Group API logs by method and endpoint
 */
function groupApiLogs($logs)
{
    $grouped = [];
    
    foreach ($logs as $logId => $log) {
        if (!is_array($log)) continue;
        
        $method = $log['method'] ?? 'GET';
        $url = $log['url'] ?? '';
        
        // Extract endpoint from URL
        $endpoint = extractApiEndpoint($url);
        
        if (!isset($grouped[$method])) {
            $grouped[$method] = [];
        }
        
        if (!isset($grouped[$method][$endpoint])) {
            $grouped[$method][$endpoint] = [];
        }
        
        $grouped[$method][$endpoint][] = [
            'id' => $logId,
            'log' => $log,
        ];
    }
    
    return $grouped;
}

/**
 * Extract endpoint from URL
 */
function extractApiEndpoint($url)
{
    if (empty($url)) {
        return '/';
    }
    
    // Remove query string
    $url = strtok($url, '?');
    
    // Extract path
    $parsed = parse_url($url);
    return $parsed['path'] ?? $url;
}

/**
 * Get status badge class
 */
function getApiStatusClass($status)
{
    if (empty($status) && !is_numeric($status)) {
        return 'bg-gray-100 text-gray-800';
    }
    if ($status >= 500) {
        return 'bg-red-100 text-red-800';
    }
    if ($status >= 400) {
        return 'bg-yellow-100 text-yellow-800';
    }
    if ($status >= 300) {
        return 'bg-blue-100 text-blue-800';
    }
    return 'bg-green-100 text-green-800';
}

/**
 * Get method badge class
 */
function getApiMethodClass($method)
{
    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
        return 'bg-gray-100 text-gray-800';
    }
    if ($method == 'GET') {
        return 'bg-blue-100 text-blue-800';
    }
    if ($method == 'POST') {
        return 'bg-green-100 text-green-800';
    }
    if ($method == 'PUT') {
        return 'bg-yellow-100 text-yellow-800';
    }
    return $method == 'DELETE' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800';
}

/**
 * Format API timestamp
 */
function formatApiTimestamp($timestamp)
{
    if (empty($timestamp)) {
        return 'N/A';
    }
    
    try {
        $date = new \DateTime($timestamp);
        return $date->format('H:i:s');
    } catch (\Exception $e) {
        return $timestamp;
    }
}

/**
 * Filter API logs by search term
 */
function filterApiLogs($logs, $searchTerm)
{
    if (empty($searchTerm)) {
        return $logs;
    }
    
    $searchTerm = strtolower(trim($searchTerm));
    $filtered = [];
    
    foreach ($logs as $key => $log) {
        if (!is_array($log)) continue;
        
        $searchableText = strtolower(
            ($log['method'] ?? '') . ' ' . 
            ($log['url'] ?? '') . ' ' . 
            ($log['ip'] ?? '') . ' ' . 
            ($log['status'] ?? '') . ' ' . 
            ($log['user_agent'] ?? '')
        );
        
        if (strpos($searchableText, $searchTerm) !== false) {
            $filtered[$key] = $log;
        }
    }
    
    return $filtered;
}

/**
 * Sort API logs by date
 */
function sortApiLogsByDate($logs, $descending = true)
{
    uasort($logs, function($a, $b) use ($descending) {
        if (!is_array($a) || !is_array($b)) return 0;
        
        $dateA = strtotime($a['date'] ?? '');
        $dateB = strtotime($b['date'] ?? '');
        
        if ($dateA == $dateB) {
            return 0;
        }
        
        if ($descending) {
            return ($dateA > $dateB) ? -1 : 1;
        } else {
            return ($dateA < $dateB) ? -1 : 1;
        }
    });
    
    return $logs;
}

/**
 * ============================================
 * SCHEDULER FUNCTIONS
 * ============================================
 */

/**
 * Fetch Scheduler data
 */
function fetchSchedulerData($serverIp, $appName = 'livo', $minutes = 60)
{
    return fetchFromAPI($serverIp, 'scheduler', $appName, $minutes);
}

/**
 * Calculate Scheduler summary
 */
function calculateSchedulerSummary($schedulerData)
{
    if (empty($schedulerData)) {
        return [
            'total_jobs' => 0,
            'active_jobs' => 0,
            'failed_jobs' => 0,
            'success_rate' => 0,
            'avg_execution_time' => 0,
            'pending_jobs' => 0,
        ];
    }
    
    $latest = end($schedulerData);
    $metrics = $latest['metrics'] ?? [];
    
    $totalJobs = $metrics['total_jobs'] ?? 0;
    $failedJobs = $metrics['failed_jobs'] ?? 0;
    $successRate = $totalJobs > 0 ? (($totalJobs - $failedJobs) / $totalJobs * 100) : 0;
    
    return [
        'total_jobs' => $totalJobs,
        'active_jobs' => $metrics['active_jobs'] ?? 0,
        'failed_jobs' => $failedJobs,
        'success_rate' => round($successRate, 1),
        'avg_execution_time' => round($metrics['avg_execution_time'] ?? 0, 2),
        'pending_jobs' => $metrics['pending_jobs'] ?? 0,
    ];
}

/**
 * ============================================
 * ALIAS FUNCTIONS (for backward compatibility)
 * ============================================
 */

// Linux aliases
function calculateSummary($logs) { return calculateApiSummary($logs); }
function groupLogs($logs) { return groupApiLogs($logs); }
function extractEndpoint($url) { return extractApiEndpoint($url); }
function getStatusClass($status) { return getApiStatusClass($status); }
function getMethodClass($method) { return getApiMethodClass($method); }

/**
 * ============================================
 * PROCESS CHART DATA FUNCTIONS
 * ============================================
 */

/**
 * Process data for charts (generic)
 */
function processChartData($data, $metricType, $limitPoints = 20)
{
    if (empty($data)) {
        return [
            'labels' => [],
            'data' => [],
        ];
    }
    
    $slice = array_slice($data, -min(count($data), $limitPoints));
    $labels = [];
    $chartData = [];
    foreach ($slice as $item) {
        if (!is_array($item)) continue;
        $timestamp = $item['timestamp'] ?? '';
        $metrics = $item['metrics'] ?? [];
        if ($timestamp) {
            try {
                $date = new \DateTime($timestamp);
                $labels[] = $date->format('H:i');
            } catch (\Exception $e) {
                $labels[] = $timestamp;
            }
        }
        $value = $metrics[$metricType] ?? 0;
        $chartData[] = round($value, 2);
    }
    return [
        'labels' => $labels,
        'data' => $chartData,
    ];
}

function processLatestChartData($data, $metricType, $limitPoints = 5)
{
    return processChartData($data, $metricType, $limitPoints);
}

/**
 * Get chart colors for different services
 */
function getChartColors($service)
{
    $colors = [
        'linux' => ['cpu' => '#3b82f6', 'memory' => '#8b5cf6', 'disk' => '#f59e0b'],
        'mysql' => ['connections' => '#10b981', 'queries' => '#ef4444', 'buffer' => '#8b5cf6'],
        'mongodb' => ['operations' => '#ec4899', 'connections' => '#06b6d4', 'memory' => '#84cc16'],
        'redis' => ['memory' => '#dc2626', 'hits' => '#059669', 'clients' => '#7c3aed'],
        'api_log' => ['requests' => '#3b82f6', 'response_time' => '#f59e0b', 'errors' => '#ef4444'],
    ];
    
    return $colors[$service] ?? ['primary' => '#3b82f6', 'secondary' => '#6b7280'];
}

/**
 * Get service icon
 */
function getServiceIcon($service)
{
    $icons = [
        'linux' => 'ri-server-line',
        'mysql' => 'ri-database-2-line',
        'mongodb' => 'ri-database-line',
        'redis' => 'ri-braces-line',
        'api_log' => 'ri-file-text-line',
        'scheduler' => 'ri-time-line',
        'ssl' => 'ri-shield-check-line',
    ];
    
    return $icons[$service] ?? 'ri-question-line';
}

/**
 * Get service status color
 */
function getServiceStatusColor($status)
{
    $colors = [
        'healthy' => 'text-green-600 bg-green-100',
        'warning' => 'text-yellow-600 bg-yellow-100',
        'critical' => 'text-red-600 bg-red-100',
        'unknown' => 'text-gray-600 bg-gray-100',
    ];
    
    return $colors[$status] ?? $colors['unknown'];
}
