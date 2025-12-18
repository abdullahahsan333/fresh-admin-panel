<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4">
    <div class="flex items-center gap-2">
        <button id="sidebarToggle" class="inline-flex items-center justify-center h-10 w-10 rounded-lg hover:bg-gray-100" title="Toggle sidebar" aria-pressed="false">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h12M4 12h8M4 18h12"/></svg>
        </button>
        <button class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-lg hover:bg-gray-100" title="Menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <div class="flex items-center gap-2">
        <div class="hidden md:flex items-center gap-2 bg-gray-100 rounded-lg px-3 h-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
            <input class="bg-transparent outline-none text-sm w-96" placeholder="Search" />
        </div>
    </div>
    
    <div class="flex items-center gap-4">
        <button class="relative h-10 w-10 grid place-items-center rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 21h-2"/></svg>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">2</span>
        </button>
        <button id="topbarProfileBtn" class="h-10 w-10 rounded-full overflow-hidden hover:ring-2 ring-gray-200">
            <img class="h-full w-full rounded-full object-cover" src="https://i.pravatar.cc/80?img=5" alt="">
        </button>
    </div>
</header>
