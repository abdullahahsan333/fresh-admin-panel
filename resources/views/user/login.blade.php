<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Login</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
    <style>
        #d3-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        .content-wrapper {
            position: relative;
            z-index: 1;
        }
        .input-with-icon {
            padding-left: 3rem;
        }
        .icon-wrapper {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            transition: color 0.2s;
        }
        .input-group:focus-within .icon-wrapper {
            color: rgb(var(--color-primary));
        }
        .glow-hover:hover {
            filter: drop-shadow(0 4px 12px rgb(var(--color-primary) / 0.3));
        }
    </style>
</head>
<body class="min-h-screen overflow-hidden font-sans antialiased">
    <svg id="d3-background"></svg>
    <div class="content-wrapper min-h-screen flex items-center justify-center p-4 md:p-6">
        <div class="w-full max-w-6xl grid lg:grid-cols-2 gap-4 lg:gap-10">
            <div class="hidden lg:block animate-fade-in">
                <div class="bg-white/10 backdrop-blur-2xl shadow-2xl rounded-3xl p-4 lg:p-8 border border-white/20">
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="bg-gradient-to-br from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary)/.6)] p-4 rounded-2xl shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl lg:text-3xl font-bold text-white">Server<span class="text-[rgb(var(--color-primary))]">Monitor</span></h2>
                                <p class="text-white/70 text-sm">Enterprise Monitoring Solution</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h1 class="text-2xl lg:text-3xl font-bold text-white leading-tight">
                                Welcome Back to Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary)/.6)]">Command Center</span>
                            </h1>
                            <p class="text-white/80 text-base lg:text-lg leading-relaxed">
                                Access real-time analytics, performance metrics, and infrastructure insights from your personalized dashboard.
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/5 p-4 rounded-xl backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-[rgb(var(--color-primary)/.2)] rounded-lg">
                                        <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-white text-sm font-medium">Live Monitoring</span>
                                        <p class="text-white/60 text-xs">24/7 tracking</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/5 p-4 rounded-xl backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-[rgb(var(--color-primary)/.2)] rounded-lg">
                                        <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-white text-sm font-medium">Smart Alerts</span>
                                        <p class="text-white/60 text-xs">Instant notifications</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/5 p-4 rounded-xl backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-[rgb(var(--color-primary)/.2)] rounded-lg">
                                        <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-white text-sm font-medium">Analytics</span>
                                        <p class="text-white/60 text-xs">Deep insights</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/5 p-4 rounded-xl backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-[rgb(var(--color-primary)/.2)] rounded-lg">
                                        <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-white text-sm font-medium">Secure & Private</span>
                                        <p class="text-white/60 text-xs">Encrypted data</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center items-center animate-slide-in">
                <div class="w-full max-w-md">
                    <div class="bg-white/95 backdrop-blur-2xl shadow-2xl rounded-3xl p-8 border border-white/20">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary)/.6)] rounded-2xl shadow-lg mb-2 glow-hover">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Welcome Back! 👋</h2>
                            <p class="text-gray-600 mt-2">Sign in to continue to your dashboard</p>
                        </div>
                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                <div class="relative input-group">
                                    <div class="icon-wrapper">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                    </div>
                                    <input type="email" name="email" 
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3.5 pl-12 focus:ring-2 focus:ring-[rgb(var(--color-primary))] focus:border-transparent transition-all duration-200 bg-white/80"
                                           placeholder="you@example.com"
                                           value="{{ old('email') }}"
                                           required>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Password</label>
                                    <a href="#" class="text-sm text-[rgb(var(--color-primary))] hover:text-[rgb(var(--color-primary)/.9)] font-medium transition-colors">
                                        Forgot password?
                                    </a>
                                </div>
                                <div class="relative input-group">
                                    <div class="icon-wrapper">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <input type="password" name="password" 
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3.5 pl-12 focus:ring-2 focus:ring-[rgb(var(--color-primary))] focus:border-transparent transition-all duration-200 bg-white/80"
                                           placeholder="••••••••"
                                           required>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="remember" class="w-4 h-4 text-[rgb(var(--color-primary))] border-gray-300 rounded focus:ring-[rgb(var(--color-primary))]">
                                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                                </label>
                                <div class="text-sm text-gray-500">
                                    <span class="inline-flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        Secure connection
                                    </span>
                                </div>
                            </div>
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary)/.8)] text-white font-semibold py-4 px-4 rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-[rgb(var(--color-primary))] focus:ring-offset-2">
                                <span class="flex items-center justify-center">
                                    Sign In
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </span>
                            </button>
                            <p class="text-center text-sm text-gray-600 mt-1">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="font-semibold text-[rgb(var(--color-primary))] hover:text-[rgb(var(--color-primary)/.9)] transition-colors hover:underline">
                                    Create account
                                </a>
                            </p>
                        </form>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500">
                            © 2024 ServerMonitor. All rights reserved. 
                            <a href="#" class="text-[rgb(var(--color-primary))] hover:text-[rgb(var(--color-primary)/.9)]">Privacy Policy</a> • 
                            <a href="#" class="text-[rgb(var(--color-primary))] hover:text-[rgb(var(--color-primary)/.9)]">Terms of Service</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="toastRoot" class="fixed z-[100] top-4 right-4 space-y-2 pointer-events-none"></div>
    @php
        $flash = [];
        if (session('success')) $flash[] = ['type' => 'success', 'text' => session('success')];
        if (session('error')) $flash[] = ['type' => 'error', 'text' => session('error')];
        if (session('warning')) $flash[] = ['type' => 'warning', 'text' => session('warning')];
        if (session('info')) $flash[] = ['type' => 'info', 'text' => session('info')];
        if ($errors && $errors->any()) {
            foreach ($errors->all() as $e) {
                $flash[] = ['type' => 'error', 'text' => $e];
            }
        }
    @endphp
    <script>
        window.__flash = {!! json_encode($flash) !!};
        const svg = d3.select("#d3-background");
        const width = window.innerWidth;
        const height = window.innerHeight;
        svg.attr("width", width).attr("height", height);
        const defs = svg.append("defs");
        const gradient = defs.append("radialGradient")
            .attr("id", "bg-gradient")
            .attr("cx", "50%")
            .attr("cy", "50%")
            .attr("r", "50%");
        gradient.append("stop").attr("offset", "0%").attr("stop-color", "#0a192f");
        gradient.append("stop").attr("offset", "100%").attr("stop-color", "#020617");
        svg.append("rect").attr("width", width).attr("height", height).attr("fill", "url(#bg-gradient)");
        const numNodes = 100;
        const nodes = Array.from({length: numNodes}, (_, i) => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.8,
            vy: (Math.random() - 0.5) * 0.8,
            radius: Math.random() * 2.5 + 0.5,
            color: i % 4 === 0 ? '#22c55e' : i % 4 === 1 ? '#eab308' : i % 4 === 2 ? '#f43f5e' : '#6366f1'
        }));
        const linkDistance = 180;
        const linksGroup = svg.append("g").attr("class", "links");
        const nodesGroup = svg.append("g").attr("class", "nodes");
        function makeId(str) { return String(str || '').replace(/[^a-zA-Z0-9_-]/g, '').toLowerCase(); }
        const nodeElements = nodesGroup.selectAll("circle").data(nodes).enter().append("circle")
            .attr("r", d => d.radius).attr("fill", d => d.color).attr("opacity", 0.9).style("filter", "drop-shadow(0 0 6px currentColor)");
        function updateLinks() {
            const links = [];
            for (let i = 0; i < nodes.length; i++) {
                for (let j = i + 1; j < nodes.length; j++) {
                    const dx = nodes[i].x - nodes[j].x;
                    const dy = nodes[i].y - nodes[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    if (distance < linkDistance) {
                        links.push({ source: nodes[i], target: nodes[j], distance });
                    }
                }
            }
            const linkElements = linksGroup.selectAll("line").data(links, d => `${d.source.x}-${d.source.y}-${d.target.x}-${d.target.y}`);
            linkElements.exit().remove();
            linkElements.enter().append("line").merge(linkElements)
                .attr("x1", d => d.source.x).attr("y1", d => d.source.y).attr("x2", d => d.target.x).attr("y2", d => d.target.y)
                .attr("stroke", d => {
                    const gradientId = `grad-${makeId(d.source.color)}-${makeId(d.target.color)}`;
                    if (!defs.select(`#${gradientId}`).node()) {
                        const linkGradient = defs.append("linearGradient").attr("id", gradientId).attr("x1", "0%").attr("y1", "0%").attr("x2", "100%").attr("y2", "0%");
                        linkGradient.append("stop").attr("offset", "0%").attr("stop-color", d.source.color).attr("stop-opacity", 0.7);
                        linkGradient.append("stop").attr("offset", "100%").attr("stop-color", d.target.color).attr("stop-opacity", 0.7);
                    }
                    return `url(#${gradientId})`;
                })
                .attr("stroke-width", 1.2).attr("opacity", d => (1 - d.distance / linkDistance) * 0.4);
        }
        function animate() {
            nodes.forEach(node => {
                node.x += node.vx; node.y += node.vy;
                if (node.x < 0 || node.x > width) { node.vx *= -0.98; node.x = node.x < 0 ? 0 : width; }
                if (node.y < 0 || node.y > height) { node.vy *= -0.98; node.y = node.y < 0 ? 0 : height; }
                node.x = Math.max(0, Math.min(width, node.x));
                node.y = Math.max(0, Math.min(height, node.y));
            });
            nodeElements.attr("cx", d => d.x).attr("cy", d => d.y);
            updateLinks();
            requestAnimationFrame(animate);
        }
        animate();
        svg.on("mousemove", function(event) {
            const [mouseX, mouseY] = d3.pointer(event);
            nodes.forEach(node => {
                const dx = mouseX - node.x; const dy = mouseY - node.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                const maxDistance = 200;
                if (distance < maxDistance) {
                    const force = (maxDistance - distance) / maxDistance;
                    const angle = Math.atan2(dy, dx);
                    node.vx -= Math.cos(angle) * force * 0.3;
                    node.vy -= Math.sin(angle) * force * 0.3;
                }
            });
        });
        window.addEventListener("resize", () => {
            const newWidth = window.innerWidth; const newHeight = window.innerHeight;
            svg.attr("width", newWidth).attr("height", newHeight);
            svg.select("rect").attr("width", newWidth).attr("height", newHeight);
            nodes.forEach(node => {
                node.x = (node.x / width) * newWidth;
                node.y = (node.y / height) * newHeight;
            });
        });
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes slide-in { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
            .animate-fade-in { animation: fade-in 0.6s ease-out; }
            .animate-slide-in { animation: slide-in 0.6s ease-out 0.2s both; }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
