@extends('layouts.admin')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Scheduler</h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500" id="last-updated">Last updated: {{ now()->format('M d, H:i') }}</div>
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
                    <th class="px-6 py-4">Last Run</th>
                    <th class="px-6 py-4">Next Run</th>
                    <th class="px-6 py-4">Time Left</th>
                    <th class="px-6 py-4">Time Passed</th>
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
const dataUrl = "{{ route('admin.server.scheduler.data', $server->id) }}";

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
    const res = await fetch(dataUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const json = await res.json();
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

      const progress = getProgress(log.last_run, log.next_run);
      const barColor = progressColor(progress);

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
        <td class="px-6 py-4 text-gray-600">${log.last_run || 'Never'}</td>
        <td class="px-6 py-4 text-gray-600">${log.next_run || 'N/A'}</td>
        <td class="px-6 py-4">
          <div class="flex flex-col gap-1">
            <span class="text-xs text-gray-600">${log.left || 'n/a'}</span>
            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
              <div class="${barColor} h-2.5 transition-all duration-500" style="width: ${progress}%;"></div>
            </div>
          </div>
        </td>
        <td class="px-6 py-4">${log.passed || 'n/a'}</td>
        <td class="px-6 py-4 text-center">${formatStatus(log.status)}</td>
      `;
      tbody.appendChild(row);
    });

    updatedAt.textContent = "Last updated: " + new Date().toLocaleTimeString();
  } catch (err) {
    const tbody = document.getElementById("scheduler-tbody");
    tbody.innerHTML = `<tr><td colspan="8" class="text-center text-red-500 py-6">Failed to fetch data: ${err.message}</td></tr>`;
    document.getElementById("last-updated").textContent = "Error";
  }
}

(async function() {
  await loadSchedulerLogs();
  setInterval(loadSchedulerLogs, 60000);
})();
</script>
@endpush
@endsection
