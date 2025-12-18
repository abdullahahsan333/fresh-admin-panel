import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const shell = document.getElementById('adminShell');
    const toggleBtn = document.getElementById('sidebarToggle');
    const profileBtn = document.getElementById('sidebarProfileBtn');
    const profileDropdownId = 'sidebarProfileDropdown';
    let profileDropdown = document.getElementById(profileDropdownId);

    function ensureProfileDropdown() {
        if (!profileDropdown) {
            profileDropdown = document.createElement('div');
            profileDropdown.id = profileDropdownId;
            profileDropdown.className = 'hidden fixed z-50 w-64 bg-white border border-gray-200 rounded-xl shadow-xl';
            profileDropdown.innerHTML = `
                <div class="p-3 flex items-center gap-3 border-b border-gray-100">
                    <img class="h-9 w-9 rounded-full" src="https://i.pravatar.cc/80?img=5" alt="">
                    <div>
                        <div class="text-sm font-semibold">${document.querySelector('#sidebarProfileBtn .font-medium')?.textContent ?? 'John Doe'}</div>
                        <div class="text-xs text-gray-500">Admin</div>
                    </div>
                </div>
                <nav class="p-2 text-sm">
                    <a href="/admin/profile" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.28 0 4-1.72 4-4s-1.72-4-4-4-4 1.72-4 4 1.72 4 4 4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 20c0-2.21 2.69-4 6-4s6 1.79 6 4"/></svg>
                        <span>My Profile</span>
                    </a>
                    <a href="/admin/settings" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M262.29 192.31a64 64 0 1 0 57.4 57.4 64.13 64.13 0 0 0-57.4-57.4zM416.39 256a154.34 154.34 0 0 1-1.53 20.79l45.21 35.46a10.81 10.81 0 0 1 2.45 13.75l-42.77 74a10.81 10.81 0 0 1-13.14 4.59l-44.9-18.08a16.11 16.11 0 0 0-15.17 1.75A164.48 164.48 0 0 1 325 400.8a15.94 15.94 0 0 0-8.82 12.14l-6.73 47.89a11.08 11.08 0 0 1-10.68 9.17h-85.54a11.11 11.11 0 0 1-10.69-8.87l-6.72-47.82a16.07 16.07 0 0 0-9-12.22 155.3 155.3 0 0 1-21.46-12.57 16 16 0 0 0-15.11-1.71l-44.89 18.07a10.81 10.81 0 0 1-13.14-4.58l-42.77-74a10.8 10.8 0 0 1 2.45-13.75l38.21-30a16.05 16.05 0 0 0 6-14.08c-.36-4.17-.58-8.33-.58-12.5s.21-8.27.58-12.35a16 16 0 0 0-6.07-13.94l-38.19-30A10.81 10.81 0 0 1 49.48 186l42.77-74a10.81 10.81 0 0 1 13.14-4.59l44.9 18.08a16.11 16.11 0 0 0 15.17-1.75A164.48 164.48 0 0 1 187 111.2a15.94 15.94 0 0 0 8.82-12.14l6.73-47.89A11.08 11.08 0 0 1 213.23 42h85.54a11.11 11.11 0 0 1 10.69 8.87l6.72 47.82a16.07 16.07 0 0 0 9 12.22 155.3 155.3 0 0 1 21.46 12.57 16 16 0 0 0 15.11 1.71l44.89-18.07a10.81 10.81 0 0 1 13.14 4.58l42.77 74a10.8 10.8 0 0 1-2.45 13.75l-38.21 30a16.05 16.05 0 0 0-6.05 14.08c.33 4.14.55 8.3.55 12.47z"></path></svg>
                        <span>Settings</span>
                    </a>
                    <a href="/admin/logout" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 12h8M14 8l4 4-4 4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h6a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                        <span>Log Out</span>
                    </a>
                </nav>
            `;
            document.body.appendChild(profileDropdown);
        }
    }

    function positionProfileDropdown() {
        if (!profileDropdown || !profileBtn) return;
        const btnRect = profileBtn.getBoundingClientRect();
        const collapsed = shell && shell.classList.contains('sidebar-collapsed');
        const left = collapsed ? (btnRect.left + 52) : (btnRect.left + 200);
        profileDropdown.style.left = `${left}px`;
        profileDropdown.style.bottom = `${window.innerHeight - btnRect.top + 12}px`;
    }

    if (shell && toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const collapsed = shell.classList.toggle('sidebar-collapsed');
            toggleBtn.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
            positionProfileDropdown();
        });
    }

    if (profileBtn) {
        ensureProfileDropdown();
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            ensureProfileDropdown();
            positionProfileDropdown();
            profileDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                profileDropdown.classList.add('hidden');
            }
        });
        window.addEventListener('resize', positionProfileDropdown);
    }
});
