<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
    <div class="text-sm text-gray-600">Installation and Usage Instructions</div>
    <div class="mt-2 flex items-center gap-2">
        <div class="text-xl font-semibold">READ ME</div>
        <span class="inline-flex items-center justify-center h-6 px-2 rounded-full bg-[rgb(var(--color-primary)/.12)] text-[rgb(var(--color-primary))] text-xs">Guide</span>
    </div>

    <div class="mt-6 space-y-6">
        <div class="rounded-lg border border-gray-200">
            <div class="flex items-center gap-2 px-4 py-3">
                <span class="text-[rgb(var(--color-primary))]">📦</span>
                <div class="text-sm font-medium">Clone the Repository</div>
            </div>
            <div class="px-4 pb-4 text-sm text-gray-600">Clone the project repository to your server using Git.</div>
            <div class="relative mx-4 mb-4">
                <button type="button" class="absolute top-2 right-2 inline-flex items-center justify-center h-8 w-8 rounded-lg bg-[rgb(var(--color-primary))] text-white" title="Copy" aria-label="Copy" data-copy-target="#readmeCloneCode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
                <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-auto"><code id="readmeCloneCode">git clone https://github.com/apprise-tech/server-monitoring-tool.git</code></pre>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200">
            <div class="flex items-center gap-2 px-4 py-3">
                <span class="text-[rgb(var(--color-primary))]">📁</span>
                <div class="text-sm font-medium">Navigate to the Project Directory</div>
            </div>
            <div class="px-4 pb-4 text-sm text-gray-600">Change into the cloned directory.</div>
            <div class="relative mx-4 mb-4">
                <button type="button" class="absolute top-2 right-2 inline-flex items-center justify-center h-8 w-8 rounded-lg bg-[rgb(var(--color-primary))] text-white" title="Copy" aria-label="Copy" data-copy-target="#readmeCdCode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
                <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-auto"><code id="readmeCdCode">cd server-monitoring-tool</code></pre>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200">
            <div class="flex items-center gap-2 px-4 py-3">
                <span class="text-[rgb(var(--color-primary))]">⚙️</span>
                <div class="text-sm font-medium">Install Dependencies</div>
            </div>
            <div class="px-4 pb-4 text-sm text-gray-600">Install the required Node.js dependencies using npm.</div>
            <div class="relative mx-4 mb-4">
                <button type="button" class="absolute top-2 right-2 inline-flex items-center justify-center h-8 w-8 rounded-lg bg-[rgb(var(--color-primary))] text-white" title="Copy" aria-label="Copy" data-copy-target="#readmeInstallCode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
                <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-auto"><code id="readmeInstallCode">npm install</code></pre>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200">
            <div class="flex items-center gap-2 px-4 py-3">
                <span class="text-[rgb(var(--color-primary))]">🛠️</span>
                <div class="text-sm font-medium">Configure the Application</div>
            </div>
            <div class="px-4 pb-4 text-sm text-gray-600">Copy the generated `config.yml` from the configuration preview above and place it in the project root directory. Edit any necessary credentials such as database username, passwords, host, or receiver URLs, to match your environment.</div>
            <div class="relative mx-4 mb-4">
                <button type="button" class="absolute top-2 right-2 inline-flex items-center justify-center h-8 w-8 rounded-lg bg-[rgb(var(--color-primary))] text-white" title="Copy" aria-label="Copy" data-copy-target="#readmeConfigCode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
                <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-auto"><code id="readmeConfigCode">cp ./config.yml /var/www/server-monitoring/config.yml</code></pre>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200">
            <div class="flex items-center gap-2 px-4 py-3">
                <span class="text-[rgb(var(--color-primary))]">🚀</span>
                <div class="text-sm font-medium">Start the Monitoring Process</div>
            </div>
            <div class="px-4 pb-4 text-sm text-gray-600">Launch the application using PM2 (or your preferred process manager) to ensure it runs continuously in the background.</div>
            <div class="relative mx-4 mb-4">
                <button type="button" class="absolute top-2 right-2 inline-flex items-center justify-center h-8 w-8 rounded-lg bg-[rgb(var(--color-primary))] text-white" title="Copy" aria-label="Copy" data-copy-target="#readmeStartCode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
                <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-auto"><code id="readmeStartCode">pm2 start server-monitoring.js --name monitoring</code></pre>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200">
            <div class="flex items-center gap-2 px-4 py-3">
                <span class="text-[rgb(var(--color-primary))]">📊</span>
                <div class="text-sm font-medium">Verify and Access the Dashboard</div>
            </div>
            <div class="px-4 pb-4 text-sm text-gray-600">Open the monitoring dashboard to view metrics, logs, and alerts for your configured assets and services.</div>
            <div class="relative mx-4 mb-4">
                <button type="button" class="absolute top-2 right-2 inline-flex items-center justify-center h-8 w-8 rounded-lg bg-[rgb(var(--color-primary))] text-white" title="Copy" aria-label="Copy" data-copy-target="#readmeVerifyCode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
                <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-auto"><code id="readmeVerifyCode">npm run start:dashboard</code></pre>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <div class="text-sm font-medium">Additional Notes</div>
        <ul class="mt-2 list-disc list-inside text-sm text-gray-600 space-y-1">
            <li>Ensure your server has Node.js and any required dependencies installed.</li>
        </ul>
    </div>
</div>
