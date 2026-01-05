@extends($layout ?? 'layouts.admin')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Scheduler</h1>
        <div id="apiStatusDot" class="hidden">
            <span class="inline-block w-2 h-2 rounded-full"></span>
        </div>
    </div>
    <div class="flex items-center gap-4">
      <div class="text-sm text-gray-500" id="last-updated">Last updated: {{ now()->format('M d, H:i') }}</div>
      <button id="refresh-btn" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-md text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </button>
    </div>
</header>

<!-- Scheduler Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b border-gray-100">
                    <th class="px-6 py-4">Source</th>
                    <th class="px-6 py-4">Job ID</th>
                    <th class="px-6 py-4">Command</th>
                    <th class="px-6 py-4">Next Run</th>
                    <th class="px-6 py-4">Last Run</th>
                    <th class="px-6 py-4">Left</th>
                    <th class="px-6 py-4">Passed</th>
                    <th class="px-6 py-4">Timestamp</th>
                    <th class="px-6 py-4">Time Ago</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm" id="scheduler-tbody"></tbody>
        </table>
    </div>
</div>
@push('scripts')
<script>
document.title = "Scheduler Dashboard";
const panel = "{{ $panel ?? 'admin' }}";
const dataUrl = "{{ route(($panel ?? 'admin').'.server.scheduler.data', $server->id) }}";

function showToast(type, message) {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.position = 'fixed';
    container.style.top = '1rem';
    container.style.right = '1rem';
    container.style.zIndex = '9999';
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '0.5rem';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = `px-3 py-2 rounded-lg shadow-sm border text-sm ${type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'}`;
  toast.textContent = message;
  container.appendChild(toast);
  setTimeout(() => {
    toast.remove();
    if (!container.childElementCount) container.remove();
  }, 3500);
}

function showApiStatus(type, message) {
  const dotWrap = document.getElementById('apiStatusDot');
  if (dotWrap) {
    const dot = dotWrap.querySelector('span') || dotWrap;
    dotWrap.classList.remove('hidden');
    dot.className = `inline-block w-2 h-2 rounded-full ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
  }
  showToast(type, type === 'success' ? 'Connected' : (message || 'Connection failed'));
}

const statusColors = {
  scheduled: "bg-green-100 text-green-700",
  running: "bg-blue-100 text-blue-700",
  failed: "bg-red-100 text-red-700",
  pending: "bg-yellow-100 text-yellow-700",
  n_a: "bg-gray-100 text-gray-500",
};

function formatStatus(status) {
  const cls = statusColors[status?.toLowerCase()] || statusColors.n_a;
  return `<span class="px-3 py-1 text-xs font-medium rounded-full ${cls}">${status || 'n/a'}</span>`;
}

function getProgress(lastRun, nextRun) {
  const last = new Date(lastRun).getTime();
  const next = new Date(nextRun).getTime();
  const now = Date.now();
  if (isNaN(last) || isNaN(next) || next <= last) return 0;
  const pct = ((now - last) / (next - last)) * 100;
  return Math.min(Math.max(pct, 0), 100);
}

function progressColor(pct) {
  if (pct < 50) return "bg-green-500";
  if (pct < 80) return "bg-yellow-500";
  return "bg-red-500";
}

async function loadSchedulerLogs() {
  try {
    const res = await fetch(dataUrl, { 
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json' 
      } 
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const contentType = res.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) throw new Error('Invalid content-type');

    const json = await res.json();
    if (json && json.apiStatus && json.apiStatus.connected) {
      showApiStatus('success', 'API connected successfully');
    } else if (json && json.ok) {
      showApiStatus('success', 'API connected successfully');
    } else {
      showApiStatus('error', json && json.message ? json.message : 'No scheduler data');
    }
    const logs = Array.isArray(json.logs) ? json.logs : [];
    const tbody = document.getElementById("scheduler-tbody");
    const updatedAt = document.getElementById("last-updated");
    tbody.innerHTML = "";

    if (logs.length === 0) {
      tbody.innerHTML = `<tr><td colspan="8" class="text-center text-gray-500 py-6">No scheduler logs found.</td></tr>`;
      updatedAt.textContent = "Last updated: " + new Date().toLocaleTimeString();
      return;
    }

    logs.forEach((log) => {
      const icon = log.source === "systemd"
        ? "ri-settings-3-line text-blue-500"
        : "ri-server-line text-indigo-500";

      const ts = log.timestamp || null;
      const timeAgo = ts ? timeSince(ts) : 'n/a';
      const nextRun = log.next_run || 'n/a';
      const lastRun = log.last_run || 'n/a';
      const left = log.left || 'n/a';
      const passed = log.passed || 'n/a';

      const row = document.createElement("tr");
      row.classList.add("hover:bg-gray-50", "transition");
      row.innerHTML = `
        <td class="px-6 py-4 flex items-center gap-2">
          <i class="${icon} text-lg"></i>
          <span class="font-medium">${log.source || 'Unknown'}</span>
        </td>
        <td class="px-6 py-4 text-gray-700">${log.job_id || 'N/A'}</td>
        <td class="px-6 py-4 text-gray-700 truncate max-w-[250px]" title="${log.command || ''}">
          ${log.command || 'N/A'}
        </td>
        <td class="px-6 py-4 text-gray-700">${nextRun}</td>
        <td class="px-6 py-4 text-gray-700">${lastRun}</td>
        <td class="px-6 py-4 text-gray-700">${left}</td>
        <td class="px-6 py-4 text-gray-700">${passed}</td>
        <td class="px-6 py-4 text-gray-600">${ts || 'N/A'}</td>
        <td class="px-6 py-4">${timeAgo}</td>
        <td class="px-6 py-4 text-center">${formatStatus(log.status)}</td>
      `;
      tbody.appendChild(row);
    });

    updatedAt.textContent = "Last updated: " + new Date().toLocaleTimeString();
  } catch (err) {
    const tbody = document.getElementById("scheduler-tbody");
    tbody.innerHTML = `<tr><td colspan="8" class="text-center text-red-500 py-6">Failed to fetch data: ${err.message}</td></tr>`;
    document.getElementById("last-updated").textContent = "Error";
    showApiStatus('error', 'Network error - cannot connect to API');
  }
}

// Initial fetch handled on DOMContentLoaded

function timeSince(ts) {
  const d = new Date(ts);
  const now = new Date();
  const diffMs = now - d;
  if (isNaN(diffMs)) return 'n/a';
  const mins = Math.floor(diffMs / 60000);
  const secs = Math.floor((diffMs % 60000) / 1000);
  if (mins > 0) return `${mins}m ${secs}s ago`;
  return `${secs}s ago`;
}

document.addEventListener('DOMContentLoaded', async function(){
  const btn = document.getElementById('refresh-btn');
  if (btn) {
    btn.addEventListener('click', async function(){
      const originalText = btn.textContent;
      btn.textContent = 'Refreshing...';
      btn.disabled = true;
      try {
        await loadSchedulerLogs();
      } finally {
        btn.textContent = originalText;
        btn.disabled = false;
      }
    });
  }
  await loadSchedulerLogs();
});
</script>
@endpush
@endsection
