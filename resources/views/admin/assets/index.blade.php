@extends('layouts.admin')
@section('content')

<form id="assetsForm" action="{{ route('admin.assets.store') }}" method="POST">
    @csrf
    
    <input id="ipHidden" type="hidden" value="">
    <script>
        window.__servers = {!! json_encode(($server ?? collect())->pluck('ip')->values()) !!};
    </script>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div class="text-xl font-semibold">Monitoring — Assets & Services</div>
            <button id="saveAssetsBtn" type="submit" class="btn btn-primary">Save Configuration</button>
        </div>
    </div>
    
    <div class="grid grid-cols-2 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-md font-medium">Your Assets</div>
            <div class="mt-4 flex gap-3">
                <input id="assetInput" type="text" class="flex-1 border rounded-lg px-3 h-10" placeholder="Add IP">
                <button id="assetAddBtn" type="button" class="btn btn-primary">Add</button>
            </div>
            <div id="assetsList" class="mt-6 space-y-2">
                <div class="text-sm text-gray-500 bg-gray-50 rounded-lg p-4">
                    Add an IP to start monitoring.
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-sm font-medium">Utils — Hostnames</div>
            <div class="mt-3 flex gap-3">
                <input id="hostnameInput" name="hostname" type="text" class="flex-1 border border-gray-300 rounded-lg px-3 h-10 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[rgb(var(--color-primary))]" placeholder="Add hostname (e.g. db.example.com)">
                <button id="addHostnameBtn" type="button" class="btn btn-primary h-10 px-4">Add</button>
            </div>
            <div id="hostnamesList" class="mt-4 rounded-lg border border-[rgb(var(--color-primary)/.3)] bg-[rgb(var(--color-primary)/.06)] p-4 text-sm text-gray-600">
                No hostnames yet. Add one to start.
            </div>
            <div id="hostnamesHidden" class="hidden"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 mt-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-md font-medium">Selected Asset</div>
            <div class="mt-1 text-sm text-gray-600"><span id="selectedAssetIp">No IP</span> Server</div>

            <div class="mt-6">
                <div class="text-md font-medium">
                    Services to Monitor <span class="ml-1 text-gray-500">(Selected: <span id="servicesCount">0</span>)</span>
                </div>
                <div id="servicesSelectContainer" class="mt-3 flex flex-wrap gap-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="services[]" value="linux" class="peer sr-only">
                        <span class="px-3 h-9 inline-flex items-center rounded-full border border-gray-300 text-gray-700 peer-checked:bg-[rgb(var(--color-primary))] peer-checked:text-white peer-checked:border-transparent">Linux</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="services[]" value="mysql" class="peer sr-only">
                        <span class="px-3 h-9 inline-flex items-center rounded-full border border-gray-300 text-gray-700 peer-checked:bg-[rgb(var(--color-primary))] peer-checked:text-white peer-checked:border-transparent">MySQL</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="services[]" value="mongodb" class="peer sr-only">
                        <span class="px-3 h-9 inline-flex items-center rounded-full border border-gray-300 text-gray-700 peer-checked:bg-[rgb(var(--color-primary))] peer-checked:text-white peer-checked:border-transparent">MongoDB</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="services[]" value="redis" class="peer sr-only">
                        <span class="px-3 h-9 inline-flex items-center rounded-full border border-gray-300 text-gray-700 peer-checked:bg-[rgb(var(--color-primary))] peer-checked:text-white peer-checked:border-transparent">Redis</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="services[]" value="api_log" class="peer sr-only">
                        <span class="px-3 h-9 inline-flex items-center rounded-full border border-gray-300 text-gray-700 peer-checked:bg-[rgb(var(--color-primary))] peer-checked:text-white peer-checked:border-transparent">API Log</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="services[]" value="scheduler" class="peer sr-only">
                        <span class="px-3 h-9 inline-flex items-center rounded-full border border-gray-300 text-gray-700 peer-checked:bg-[rgb(var(--color-primary))] peer-checked:text-white peer-checked:border-transparent">Scheduler</span>
                    </label>
                </div>
                <div id="selectedServicesChips" class="mt-4 flex flex-wrap gap-2"></div>
            </div>

            <div class="mt-6">
                <div class="flex items-center gap-2">
                    <div class="text-md font-medium">Configuration Preview</div>
                    <span class="text-xs rounded px-2 py-1 bg-gray-100 text-gray-600">config.yml</span>
                </div>
                <div class="relative mt-3">
                    <input id="serverIdHidden" type="hidden" name="server_id" value="">
                    <button type="button" class="absolute top-2 right-5 inline-flex items-center justify-center h-8 w-8 rounded-lg bg-[rgb(var(--color-primary))] text-white" title="Copy" aria-label="Copy" data-copy-target="#yamlConfigCode">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                    <pre class="h-96 bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-auto"><code id="yamlConfigCode">global:
  app_name: "Live Shopping"
  purpose: "A E-commerce Project for API Logs."
  ip: "128.199.73.128"</code></pre>
                </div>
            </div>
        </div>
    </div>
</form>

@include('partials.assets-readme')

@endsection
