<header class="h-12 lg:h-16 bg-white border-b border-gray-200 flex items-center justify-between px-2 sm:px-4 ">
    <div class="flex items-center gap-2">
        <button id="mobileMenuBtn" class="inline-flex lg:hidden items-center justify-center h-9 w-9 rounded-lg hover:bg-gray-100" title="Open mobile menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <button id="sidebarToggle" class="hidden lg:inline-flex items-center justify-center h-9 w-9 lg:h-10 lg:w-10 rounded-lg hover:bg-gray-100" title="Toggle sidebar" aria-pressed="false">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h12M4 12h8M4 18h12"/></svg>
        </button>
    </div>

    <div class="flex items-center gap-2"></div>
    
    <div class="flex items-center gap-4">
        <button id="userTopbarProfileBtn" 
            data-name="{{ auth('web')->user()->name ?? 'John Doe' }}"
            data-avatar="{{ ($webUser && !empty($webUser->avatar)) ? asset($webUser->avatar) : 'https://i.pravatar.cc/80?img=5' }}"
            class="h-9 w-9 lg:h-10 lg:w-10 rounded-full overflow-hidden hover:ring-2 ring-gray-200">
            <img class="h-full w-full rounded-full object-cover" src="{{ ($webUser && !empty($webUser->avatar)) ? asset($webUser->avatar) : 'https://i.pravatar.cc/80?img=5' }}" alt="">
        </button>
    </div>
</header>
