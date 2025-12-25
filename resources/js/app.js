import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const toastRoot = document.getElementById('toastRoot');
    const showToast = (type, text, timeout = 3200) => {
        if (!toastRoot || !text) return;
        const el = document.createElement('div');
        el.className = 'pointer-events-auto flex items-center gap-2 px-4 py-2 rounded-lg shadow-lg text-sm';
        let base = 'bg-gray-900 text-white';
        if (type === 'success') base = 'bg-emerald-600 text-white';
        else if (type === 'error') base = 'bg-red-600 text-white';
        else if (type === 'warning') base = 'bg-amber-500 text-black';
        else if (type === 'info') base = 'bg-blue-600 text-white';
        el.className += ' ' + base;
        const span = document.createElement('span');
        span.textContent = text;
        el.appendChild(span);
        toastRoot.appendChild(el);
        setTimeout(() => {
            el.style.opacity = '1';
        }, 10);
        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-4px)';
            setTimeout(() => {
                if (el.parentNode) el.parentNode.removeChild(el);
            }, 300);
        }, timeout);
    };
    const flashMessages = Array.isArray(window.__flash) ? window.__flash : [];
    flashMessages.forEach(m => showToast(m.type, m.text));
    const shell = document.getElementById('adminShell') || document.getElementById('userShell');
    const toggleBtn = document.getElementById('sidebarToggle') || document.getElementById('menuToggle');
    const profileBtn = document.getElementById('sidebarProfileBtn');
    const mobileProfileBtn = document.getElementById('mobileSidebarProfileBtn');
    let activeProfileBtn = null;
    const mobileOverlay = document.getElementById('mobileSidebarOverlay');
    const sidebarEl = document.getElementById('adminSidebar') || document.getElementById('userSidebar');
    const profileDropdownId = 'sidebarProfileDropdown';
    let profileDropdown = document.getElementById(profileDropdownId);
    const topbarProfileBtn = document.getElementById('topbarProfileBtn') || document.getElementById('userTopbarProfileBtn');
    const topbarDropdownId = 'topbarProfileDropdown';
    let topbarDropdown = document.getElementById(topbarDropdownId);
    const topbarNotifBtn = document.getElementById('topbarNotifBtn') || document.getElementById('userTopbarNotifBtn');
    const topbarNotifDropdownId = 'topbarNotificationsDropdown';
    let topbarNotifDropdown = document.getElementById(topbarNotifDropdownId);
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileSidebarEl = document.getElementById('mobileAdminSidebar') || document.getElementById('mobileUserSidebar');
    const mobileSidebarCloseBtn = document.getElementById('mobileAdminSidebarCloseBtn') || document.getElementById('mobileUserSidebarCloseBtn') || document.getElementById('mobileSidebarCloseBtn');
    let menuFlyout = null;
    let subFlyout = null;
    const flyouts = {};
    function hideAllFlyouts() {
        Object.values(flyouts).forEach((f) => {
            if (f && !f.classList.contains('hidden')) f.classList.add('hidden');
        });
        if (menuFlyout && !menuFlyout.classList.contains('hidden')) menuFlyout.classList.add('hidden');
        if (subFlyout && !subFlyout.classList.contains('hidden')) subFlyout.classList.add('hidden');
    }

    function ensureProfileDropdown() {
        if (!profileDropdown) {
            profileDropdown = document.createElement('div');
            profileDropdown.id = profileDropdownId;
            profileDropdown.className = 'hidden fixed z-[260] w-64 bg-white border border-gray-200 rounded-xl shadow-xl';
            const sourceBtn = activeProfileBtn || profileBtn || mobileProfileBtn || topbarProfileBtn;
            const name = sourceBtn?.dataset.name ?? 'John Doe';
            const avatarSrc = sourceBtn?.dataset.avatar ?? 'https://i.pravatar.cc/80?img=5';
            profileDropdown.innerHTML = `
                <div class="p-3 flex items-center gap-3 border-b border-gray-100">
                    <img class="h-9 w-9 rounded-full" src="${avatarSrc}" alt="${name}">
                    <div>
                        <div class="text-sm font-semibold">${name}</div>
                        <div class="text-xs text-gray-500">Admin</div>
                    </div>
                </div>
                <nav class="p-2 text-sm">
                    <a href="/admin/profile" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.28 0 4-1.72 4-4s-1.72-4-4-4-4 1.72-4 4 1.72 4 4 4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 20c0-2.21 2.69-4 6-4s6 1.79 6 4"/></svg>
                        <span>My Profile</span>
                    </a>
                    <a href="/admin/assets" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                        <span>Asset Management</span>
                    </a>
                    <a href="/admin/settings" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M262.29 192.31a64 64 0 1 0 57.4 57.4 64.13 64.13 0 0 0-57.4-57.4zM416.39 256a154.34 154.34 0 0 1-1.53 20.79l45.21 35.46a10.81 10.81 0 0 1 2.45 13.75l-42.77 74a10.81 10.81 0 0 1-13.14 4.59l-44.9-18.08a16.11 16.11 0 0 0-15.17 1.75A164.48 164.48 0 0 1 325 400.8a15.94 15.94 0 0 0-8.82 12.14l-6.73 47.89a11.08 11.08 0 0 1-10.68 9.17h-85.54a11.11 11.11 0 0 1-10.69-8.87l-6.72-47.82a16.07 16.07 0 0 0-9-12.22 155.3 155.3 0 0 1-21.46-12.57 16 16 0 0 0-15.11-1.71l-44.89 18.07a10.81 10.81 0 0 1-13.14-4.58l-42.77 74a10.8 10.8 0 0 1 2.45-13.75l-38.21 30a16.05 16.05 0 0 0 6-14.08c-.36-4.17-.58-8.33-.58-12.5s.21-8.27.58-12.35a16 16 0 0 0-6.07-13.94l-38.19-30A10.81 10.81 0 0 1 49.48 186l42.77-74a10.81 10.81 0 0 1 13.14-4.59l44.9 18.08a16.11 16.11 0 0 0 15.17-1.75A164.48 164.48 0 0 1 187 111.2a15.94 15.94 0 0 0 8.82-12.14l6.73-47.89A11.08 11.08 0 0 1 213.23 42h85.54a11.11 11.11 0 0 1 10.69 8.87l6.72 47.82a16.07 16.07 0 0 0 9 12.22 155.3 155.3 0 0 1 21.46 12.57 16 16 0 0 0 15.11 1.71l44.89-18.07a10.81 10.81 0 0 1 13.14 4.58l42.77 74a10.8 10.8 0 0 1-2.45 13.75l-38.21 30a16.05 16.05 0 0 0 6.05 14.08c.33 4.14.55 8.3.55 12.47z"></path></svg>
                        <span>Settings</span>
                    </a>
                    <a href="/admin/logout" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 12h8M14 8l4 4-4 4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h6a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                        <span>Log Out</span>
                    </a>
                </nav>
            `;
            document.body.appendChild(profileDropdown);
            if (window.location.pathname.startsWith('/user')) {
                const nav = profileDropdown.querySelector('nav');
                const aProfile = nav ? nav.querySelector('a[href="/admin/profile"]') : null;
                const aAssets = nav ? nav.querySelector('a[href="/admin/assets"]') : null;
                const aLogout = nav ? nav.querySelector('a[href="/admin/logout"]') : null;
                const aSettings = nav ? nav.querySelector('a[href="/admin/settings"]') : null;
                if (aProfile) aProfile.setAttribute('href', '/user/profile');
                if (aAssets) aAssets.setAttribute('href', '/user/assets');
                if (aLogout) aLogout.setAttribute('href', '/user/logout');
                if (aSettings && aSettings.parentNode) aSettings.parentNode.removeChild(aSettings);
                const roleEl = profileDropdown.querySelector('.text-xs.text-gray-500');
                if (roleEl) roleEl.textContent = 'User';
            }
        }
    }

    function openMobileSidebar() {
        if (!mobileSidebarEl) return;
        mobileSidebarEl.classList.add('transform', 'transition-transform', 'duration-300', '-translate-x-full');
        mobileSidebarEl.classList.remove('hidden');
        requestAnimationFrame(() => {
            mobileSidebarEl.classList.remove('-translate-x-full');
        });
        if (mobileOverlay) mobileOverlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeMobileSidebar() {
        if (!mobileSidebarEl) return;
        mobileSidebarEl.classList.add('-translate-x-full');
        setTimeout(() => {
            mobileSidebarEl.classList.add('hidden');
        }, 300);
        if (mobileOverlay) mobileOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function positionProfileDropdown() {
        const targetBtn = activeProfileBtn || profileBtn || mobileProfileBtn;
        if (!profileDropdown || !targetBtn) return;
        const btnRect = targetBtn.getBoundingClientRect();

        if (targetBtn.id === 'mobileSidebarProfileBtn') {
             const left = btnRect.left + 8;
             profileDropdown.style.left = `${left}px`;
        } else {
            const collapsed = shell && shell.classList.contains('sidebar-collapsed');
            const left = collapsed ? (btnRect.left + 52) : (btnRect.left + 240);
            profileDropdown.style.left = `${left}px`;
        }
        profileDropdown.style.bottom = `${window.innerHeight - btnRect.top + 12}px`;
    }

    if (shell && toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                if (mobileOverlay && !mobileOverlay.classList.contains('hidden')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
                return;
            }
            const collapsed = shell.classList.toggle('sidebar-collapsed');
            toggleBtn.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
            positionProfileDropdown();
            hideAllFlyouts();
            const sidebar = document.getElementById('adminSidebar') || document.getElementById('userSidebar');
            if (collapsed && sidebar) {
                Array.from(sidebar.querySelectorAll('[data-menu-toggle], [data-submenu-toggle]')).forEach((t) => {
                    t.setAttribute('aria-expanded', 'false');
                    const p = t.nextElementSibling;
                    if (p) {
                        p.classList.add('hidden');
                        p.style.removeProperty('height');
                        p.style.removeProperty('overflow');
                        p.style.removeProperty('transition-property');
                        p.style.removeProperty('transition-duration');
                        p.style.removeProperty('opacity');
                    }
                    const c = t.querySelector('.submenu-caret');
                    if (c) c.classList.remove('rotate-180');
                });
            }
        });
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', closeMobileSidebar);
        }
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeMobileSidebar();
        });
    }

    function setupSidebarMenus(sidebarElement) {
        const sidebar = sidebarElement || document.getElementById('adminSidebar') || document.getElementById('userSidebar');
        const menuTriggers = Array.from(document.querySelectorAll('[data-menu-toggle]'));
        const ACTIVE_CLASSES = ['text-[rgb(var(--color-primary))]', 'bg-[rgb(var(--color-primary)/.06)]'];
        const INACTIVE_CLASSES = ['text-gray-700'];
        const applyActive = (el, active) => {
            if (!el) return;
            const cls = el.classList;
            ACTIVE_CLASSES.forEach(c => active ? cls.add(c) : cls.remove(c));
            INACTIVE_CLASSES.forEach(c => active ? cls.remove(c) : cls.add(c));
        };

        const slideUp = (el, duration = 200) => {
            if (!el || el.classList.contains('hidden')) return;
            el.style.transitionProperty = 'height, opacity';
            el.style.transitionDuration = `${duration}ms`;
            el.style.overflow = 'hidden';
            el.style.height = `${el.scrollHeight}px`;
            el.style.opacity = '1';
            requestAnimationFrame(() => {
                el.style.height = '0px';
                el.style.opacity = '0';
            });
            setTimeout(() => {
                el.classList.add('hidden');
                el.style.removeProperty('height');
                el.style.removeProperty('overflow');
                el.style.removeProperty('transition-property');
                el.style.removeProperty('transition-duration');
                el.style.removeProperty('opacity');
            }, duration);
        };

        const slideDown = (el, duration = 200) => {
            if (!el || !el.classList.contains('hidden')) return;
            el.classList.remove('hidden');
            const height = el.scrollHeight;
            el.style.transitionProperty = 'height, opacity';
            el.style.transitionDuration = `${duration}ms`;
            el.style.overflow = 'hidden';
            el.style.height = '0px';
            el.style.opacity = '0';
            requestAnimationFrame(() => {
                el.style.height = `${height}px`;
                el.style.opacity = '1';
            });
            setTimeout(() => {
                el.style.removeProperty('height');
                el.style.removeProperty('overflow');
                el.style.removeProperty('transition-property');
                el.style.removeProperty('transition-duration');
                el.style.removeProperty('opacity');
            }, duration);
        };

        const ensureFlyoutLevel = (level) => {
            const id = `sidebarFlyout${level}`;
            const existing = document.getElementById(id);
            if (existing) {
                flyouts[level] = existing;
                return existing;
            }
            const el = document.createElement('div');
            el.id = id;
            el.className = 'hidden fixed z-[260] w-64 bg-white border border-gray-200 rounded-xl shadow-xl';
            document.body.appendChild(el);
            flyouts[level] = el;
            return el;
        };

        const positionFlyoutNextTo = (flyout, rect, offsetX = 8, offsetY = 0) => {
            const left = rect.right + offsetX;
            const top = rect.top + offsetY;
            flyout.style.left = `${left}px`;
            flyout.style.top = `${top}px`;
        };

        const renderPanelToFlyout = (panel, flyout, level = 1) => {
            const items = Array.from(panel.querySelectorAll(':scope > a'));
            flyout.innerHTML = `<div class="p-2"></div>`;
            const container = flyout.firstElementChild;
            items.forEach((a) => {
                const icon = a.querySelector('svg')?.outerHTML ?? '';
                const label = a.querySelector('.sidebar-text')?.textContent ?? a.textContent.trim();
                const hasSub = a.hasAttribute('data-submenu-toggle');
                const entry = document.createElement('button');
                entry.type = 'button';
                entry.className = 'w-full flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-50 text-left';
                entry.innerHTML = `${icon}<span class="text-sm">${label}</span>${hasSub ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>' : ''}`;
                container.appendChild(entry);
                if (hasSub) {
                    const subPanel = a.nextElementSibling;
                    entry.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const er = entry.getBoundingClientRect();
                        openFlyoutForPanel(subPanel, er, level + 1);
                    });
                } else {
                    // Navigate if entry anchor has href
                    const href = a.getAttribute('href');
                    if (href && href !== '#') {
                        entry.addEventListener('click', () => {
                            window.location.href = href;
                        });
                    }
                }
            });
        };
        const openFlyoutForPanel = (panel, rect, level) => {
            const fly = ensureFlyoutLevel(level);
            renderPanelToFlyout(panel, fly, level);
            positionFlyoutNextTo(fly, rect, 8, 0);
            fly.classList.remove('hidden');
            Object.keys(flyouts).forEach((l) => {
                const li = Number(l);
                if (li > level && flyouts[li]) flyouts[li].classList.add('hidden');
            });
        };
        const hideFlyoutsFrom = (level) => {
            Object.keys(flyouts).forEach((l) => {
                const li = Number(l);
                if (li >= level && flyouts[li] && !flyouts[li].classList.contains('hidden')) {
                    flyouts[li].classList.add('hidden');
                }
            });
        };
        

        if (!sidebar) return;
        sidebar.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-menu-toggle], [data-submenu-toggle]');
            if (!trigger) return;
            e.preventDefault();
            const isTop = trigger.hasAttribute('data-menu-toggle');
            const panel = trigger.nextElementSibling;
            if (!panel) return;
            const caret = trigger.querySelector('.submenu-caret');
            const expanded = trigger.getAttribute('aria-expanded') === 'true';
            const willExpand = !expanded;
            const isMobileSidebar = sidebar.id.startsWith('mobile');
            const collapsed = !isMobileSidebar && shell && shell.classList.contains('sidebar-collapsed');

            if (isTop) {
                const topTriggers = Array.from(sidebar.querySelectorAll('[data-menu-toggle]'));
                topTriggers.forEach(t => {
                    if (t !== trigger) {
                        t.setAttribute('aria-expanded', 'false');
                        const p = t.nextElementSibling;
                        if (p && !collapsed) slideUp(p);
                        const c = t.querySelector('.submenu-caret');
                        if (c) c.classList.remove('rotate-180');
                        applyActive(t, false);
                    }
                });
            } else {
                const parentGroup = trigger.parentElement.closest('[data-menu], [data-submenu]');
                if (parentGroup) {
                    Array.from(parentGroup.querySelectorAll(':scope > a[data-submenu-toggle]')).forEach(st => {
                        if (st !== trigger) {
                            st.setAttribute('aria-expanded', 'false');
                            const sp = st.nextElementSibling;
                            if (sp && !collapsed) slideUp(sp);
                            const sc = st.querySelector('.submenu-caret');
                            if (sc) sc.classList.remove('rotate-180');
                            applyActive(st, false);
                        }
                    });
                }
            }

            trigger.setAttribute('aria-expanded', willExpand ? 'true' : 'false');
            applyActive(trigger, willExpand);

            if (collapsed) {
                const rect = trigger.getBoundingClientRect();
                const level = isTop ? 1 : (Number(trigger.getAttribute('data-level')) || 2);
                if (willExpand) {
                    openFlyoutForPanel(panel, rect, level);
                } else {
                    hideFlyoutsFrom(level);
                }
            } else {
                if (willExpand) {
                    slideDown(panel);
                } else {
                    slideUp(panel);
                }
                if (caret) caret.classList.toggle('rotate-180', willExpand);
                if (willExpand && isTop) {
                    panel.querySelectorAll('[data-submenu-toggle]').forEach(st => {
                        st.setAttribute('aria-expanded', 'false');
                        const sp = st.nextElementSibling;
                        if (sp) slideUp(sp);
                        const sc = st.querySelector('.submenu-caret');
                        if (sc) sc.classList.remove('rotate-180');
                    });
                }
            }
        });
    }

    setupSidebarMenus();
    if (mobileSidebarEl) {
        setupSidebarMenus(mobileSidebarEl);
    }

    if (profileBtn || mobileProfileBtn) {
        ensureProfileDropdown();
        
        if (profileBtn) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                activeProfileBtn = profileBtn;
                ensureProfileDropdown();
                positionProfileDropdown();
                profileDropdown.classList.toggle('hidden');
                hideAllFlyouts();
            });
        }

        if (mobileProfileBtn) {
            mobileProfileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                activeProfileBtn = mobileProfileBtn;
                ensureProfileDropdown();
                positionProfileDropdown();
                profileDropdown.classList.toggle('hidden');
                hideAllFlyouts();
            });
        }

        document.addEventListener('click', (e) => {
            const t = e.target;
            const inSidebar = t.closest('#adminSidebar') || t.closest('#userSidebar');
            const inFlyout = t.closest('[id^="sidebarFlyout"]');
            const isMenuTrigger = t.closest('[data-menu-toggle]') || t.closest('[data-submenu-toggle]');
            const isProfileTrigger = t.closest('#sidebarProfileBtn') || t.closest('#mobileSidebarProfileBtn') || t.closest('#topbarProfileBtn') || t.closest('#topbarNotifBtn');
            if (inSidebar || inFlyout || isMenuTrigger || isProfileTrigger) return;
            if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                profileDropdown.classList.add('hidden');
            }
            if (topbarNotifDropdown && !topbarNotifDropdown.classList.contains('hidden')) {
                topbarNotifDropdown.classList.add('hidden');
            }
            if (topbarDropdown && !topbarDropdown.classList.contains('hidden')) {
                topbarDropdown.classList.add('hidden');
            }
            hideAllFlyouts();
        });
        window.addEventListener('resize', positionProfileDropdown);
    }

    function ensureTopbarDropdown() {
        if (!topbarDropdown) {
            topbarDropdown = document.createElement('div');
            topbarDropdown.id = topbarDropdownId;
            topbarDropdown.className = 'hidden fixed z-50 w-64 bg-white border border-gray-200 rounded-xl shadow-xl';
            
            const sourceBtn = topbarProfileBtn || profileBtn || mobileProfileBtn;
            const name = sourceBtn?.dataset.name ?? 'John Doe';
            const avatarSrc = sourceBtn?.dataset.avatar ?? 'https://i.pravatar.cc/80?img=5';

            topbarDropdown.innerHTML = `
                <div class="p-3 flex items-center gap-3 border-b border-gray-100">
                    <img class="h-9 w-9 rounded-full" src="${avatarSrc}" alt="${name}">
                    <div>
                        <div class="text-sm font-semibold">${name}</div>
                        <div class="text-xs text-gray-500">Admin</div>
                    </div>
                </div>
                <nav class="p-2 text-sm">
                    <a href="/admin/profile" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.28 0 4-1.72 4-4s-1.72-4-4-4-4 1.72-4 4 1.72 4 4 4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 20c0-2.21 2.69-4 6-4s6 1.79 6 4"/></svg>
                        <span>My Profile</span>
                    </a>
                    <a href="/admin/settings" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M262.29 192.31a64 64 0 1 0 57.4 57.4 64.13 64.13 0 0 0-57.4-57.4zM416.39 256a154.34 154.34 0 0 1-1.53 20.79l45.21 35.46a10.81 10.81 0 0 1 2.45 13.75l-42.77 74a10.81 10.81 0 0 1-13.14 4.59l-44.9-18.08a16.11 16.11 0 0 0-15.17 1.75A164.48 164.48 0 0 1 325 400.8a15.94 15.94 0 0 0-8.82 12.14l-6.73 47.89a11.08 11.08 0 0 1-10.68 9.17h-85.54a11.11 11.11 0 0 1-10.69-8.87l-6.72-47.82a16.07 16.07 0 0 0-9-12.22 155.3 155.3 0 0 1-21.46-12.57 16 16 0 0 0-15.11 1.71l-44.89 18.07a10.81 10.81 0 0 1 13.14-4.58l-42.77 74a10.8 10.8 0 0 1 2.45-13.75l-38.21 30a16.05 16.05 0 0 0 6-14.08c.36-4.17-.58-8.33-.58-12.5s.21-8.27.58-12.35a16 16 0 0 0-6.07-13.94l-38.19-30A10.81 10.81 0 0 1 49.48 186l42.77-74a10.81 10.81 0 0 1 13.14-4.59l44.9 18.08a16.11 16.11 0 0 0 15.17-1.75A164.48 164.48 0 0 1 187 111.2a15.94 15.94 0 0 0 8.82-12.14l6.73-47.89A11.08 11.08 0 0 1 213.23 42h85.54a11.11 11.11 0 0 1 10.69 8.87l6.72 47.82a16.07 16.07 0 0 0 9 12.22 155.3 155.3 0 0 1 21.46 12.57 16 16 0 0 0 15.11 1.71l-44.89-18.07a10.81 10.81 0 0 1 13.14 4.58l42.77 74a10.8 10.8 0 0 1-2.45 13.75l-38.21 30a16.05 16.05 0 0 0 6-14.08c.33 4.14.55 8.3.55 12.47z"></path></svg>
                        <span>Settings</span>
                    </a>
                    <a href="/admin/logout" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 12h8M14 8l4 4-4 4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h6a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                        <span>Log Out</span>
                    </a>
                </nav>
            `;
            document.body.appendChild(topbarDropdown);
            if (window.location.pathname.startsWith('/user')) {
                const nav = topbarDropdown.querySelector('nav');
                const aProfile = nav ? nav.querySelector('a[href="/admin/profile"]') : null;
                const aAssets = nav ? nav.querySelector('a[href="/admin/assets"]') : null;
                const aLogout = nav ? nav.querySelector('a[href="/admin/logout"]') : null;
                const aSettings = nav ? nav.querySelector('a[href="/admin/settings"]') : null;
                if (aProfile) aProfile.setAttribute('href', '/user/profile');
                if (aAssets) aAssets.setAttribute('href', '/user/assets');
                if (aLogout) aLogout.setAttribute('href', '/user/logout');
                if (aSettings && aSettings.parentNode) aSettings.parentNode.removeChild(aSettings);
                const roleEl = topbarDropdown.querySelector('.text-xs.text-gray-500');
                if (roleEl) roleEl.textContent = 'User';
            }
        }
    }

    function positionTopbarDropdown() {
        if (!topbarDropdown || !topbarProfileBtn) return;
        const btnRect = topbarProfileBtn.getBoundingClientRect();
        const left = btnRect.right - 256;
        const top = btnRect.bottom + 8;
        topbarDropdown.style.left = `${left}px`;
        topbarDropdown.style.top = `${top}px`;
    }

    if (topbarProfileBtn) {
        ensureTopbarDropdown();
        topbarProfileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            ensureTopbarDropdown();
            positionTopbarDropdown();
            topbarDropdown.classList.toggle('hidden');
        });
        window.addEventListener('resize', positionTopbarDropdown);
    }
    function ensureTopbarNotificationsDropdown() {
        if (!topbarNotifDropdown) {
            topbarNotifDropdown = document.createElement('div');
            topbarNotifDropdown.id = topbarNotifDropdownId;
            topbarNotifDropdown.className = 'hidden fixed z-50 w-80 bg-white border border-gray-200 rounded-xl shadow-xl';
            topbarNotifDropdown.innerHTML = `
                <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                    <div class="text-sm font-semibold">Notifications</div>
                    <span class="text-xs px-2 py-0.5 rounded bg-[rgb(var(--color-primary)/.12)] text-[rgb(var(--color-primary))]">5 new</span>
                </div>
                <div class="max-h-80 overflow-auto">
                    <a href="javascript:void(0);" class="flex items-start gap-3 px-3 py-3 hover:bg-gray-50">
                        <span class="h-6 w-6 rounded bg-blue-100 text-blue-600 grid place-items-center">ℹ️</span>
                        <div class="text-sm">
                            <div class="font-medium text-gray-800">Server 128.199.73.128 CPU spike</div>
                            <div class="text-xs text-gray-500">2 min ago</div>
                        </div>
                    </a>
                    <a href="javascript:void(0);" class="flex items-start gap-3 px-3 py-3 hover:bg-gray-50">
                        <span class="h-6 w-6 rounded bg-yellow-100 text-yellow-600 grid place-items-center">⚠️</span>
                        <div class="text-sm">
                            <div class="font-medium text-gray-800">SSL expires in 65 days</div>
                            <div class="text-xs text-gray-500">10 min ago</div>
                        </div>
                    </a>
                    <a href="javascript:void(0);" class="flex items-start gap-3 px-3 py-3 hover:bg-gray-50">
                        <span class="h-6 w-6 rounded bg-emerald-100 text-emerald-600 grid place-items-center">✅</span>
                        <div class="text-sm">
                            <div class="font-medium text-gray-800">Deployment completed</div>
                            <div class="text-xs text-gray-500">30 min ago</div>
                        </div>
                    </a>
                    <a href="javascript:void(0);" class="flex items-start gap-3 px-3 py-3 hover:bg-gray-50">
                        <span class="h-6 w-6 rounded bg-red-100 text-red-600 grid place-items-center">⛔</span>
                        <div class="text-sm">
                            <div class="font-medium text-gray-800">Redis memory high</div>
                            <div class="text-xs text-gray-500">1 hr ago</div>
                        </div>
                    </a>
                    <a href="javascript:void(0);" class="flex items-start gap-3 px-3 py-3 hover:bg-gray-50">
                        <span class="h-6 w-6 rounded bg-purple-100 text-purple-600 grid place-items-center">🔒</span>
                        <div class="text-sm">
                            <div class="font-medium text-gray-800">New admin login</div>
                            <div class="text-xs text-gray-500">2 hr ago</div>
                        </div>
                    </a>
                </div>
                <div class="p-3 border-t border-gray-100">
                    <a href="/admin/notifications" class="inline-flex items-center justify-center w-full h-9 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Show all</a>
                </div>
            `;
            document.body.appendChild(topbarNotifDropdown);
        }
        return topbarNotifDropdown;
    }
    function positionTopbarNotificationsDropdown() {
        if (!topbarNotifDropdown || !topbarNotifBtn) return;
        const btnRect = topbarNotifBtn.getBoundingClientRect();
        const left = btnRect.right - 320;
        const top = btnRect.bottom + 8;
        topbarNotifDropdown.style.left = `${left}px`;
        topbarNotifDropdown.style.top = `${top}px`;
    }
    if (topbarNotifBtn) {
        ensureTopbarNotificationsDropdown();
        topbarNotifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            ensureTopbarNotificationsDropdown();
            positionTopbarNotificationsDropdown();
            topbarNotifDropdown.classList.toggle('hidden');
        });
        window.addEventListener('resize', positionTopbarNotificationsDropdown);
    }
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (mobileOverlay && !mobileOverlay.classList.contains('hidden')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        });
    }
    if (mobileSidebarCloseBtn) {
        mobileSidebarCloseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            closeMobileSidebar();
        });
    }

    document.querySelectorAll('[data-copy-target]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-copy-target');
            const el = target ? document.querySelector(target) : null;
            if (!el) return;
            const text = (el.textContent || '').trim();
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                showToast('success', 'Copied to clipboard');
            });
        });
    });
    
    const hnInput = document.getElementById('hostnameInput');
    const hnBtn = document.getElementById('addHostnameBtn');
    const hnList = document.getElementById('hostnamesList');
    const hnHidden = document.getElementById('hostnamesHidden');
    let hostnames = [];
    const renderHostnames = () => {
        if (!hnList) return;
        if (!hostnames.length) {
            hnList.textContent = 'No hostnames yet. Add one to start.';
            return;
        }
        const container = document.createElement('div');
        container.className = 'space-y-2';
        hostnames.forEach((name, i) => {
            const row = document.createElement('div');
            row.className = 'group flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 ring-1 ring-transparent hover:ring-[rgb(var(--color-primary)/.5)] hover:shadow-sm transition-all';
            const left = document.createElement('div');
            left.className = 'flex items-center gap-3';
            const dot = document.createElement('span');
            dot.className = 'h-2 w-2 rounded-full bg-emerald-400';
            const textWrap = document.createElement('div');
            const titleEl = document.createElement(/^https?:\/\//.test(name) ? 'a' : 'span');
            if (titleEl.tagName.toLowerCase() === 'a') {
                titleEl.href = name;
                titleEl.target = '_blank';
                titleEl.rel = 'noreferrer';
            }
            titleEl.className = 'text-sm font-medium hover:underline';
            titleEl.textContent = name;
            const sub = document.createElement('div');
            sub.className = 'text-xs text-gray-500';
            sub.textContent = 'Hostname';
            textWrap.appendChild(titleEl);
            textWrap.appendChild(sub);
            left.appendChild(dot);
            left.appendChild(textWrap);
            const actions = document.createElement('div');
            actions.className = 'flex items-center gap-2';
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-secondary h-8 px-3 text-xs';
            remove.textContent = 'Remove';
            remove.addEventListener('click', () => {
                hostnames.splice(i, 1);
                renderHostnames();
            });
            actions.appendChild(remove);
            row.appendChild(left);
            row.appendChild(actions);
            container.appendChild(row);
        });
        hnList.innerHTML = '';
        hnList.appendChild(container);
        if (hnHidden) {
            hnHidden.innerHTML = '';
            hostnames.forEach((h) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'hostnames[]';
                input.value = h;
                hnHidden.appendChild(input);
            });
        }
    };
    const addHostname = () => {
        if (!hnInput) return;
        const val = (hnInput.value || '').trim();
        if (!val) return;
        if (!hostnames.includes(val)) {
            hostnames.push(val);
            renderHostnames();
        }
        hnInput.value = '';
        hnInput.focus();
    };
    if (hnBtn) hnBtn.addEventListener('click', addHostname);
    if (hnInput) hnInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') addHostname();
    });
    
    let currentIp = '';
    
    const ipHidden = document.getElementById('ipHidden');
    const serverIdHidden = document.getElementById('serverIdHidden');
    const assetInput = document.getElementById('assetInput');
    const assetAddBtn = document.getElementById('assetAddBtn');
    const assetsList = document.getElementById('assetsList');
    const selectedAssetIp = document.getElementById('selectedAssetIp');
    let servers = [];
    const serverServicesCount = {};
    const initialServers = Array.isArray(window.__servers) ? window.__servers : [];
    if (initialServers.length) {
        servers = initialServers.slice();
    }
    const isIp = (val) => /^\d{1,3}(\.\d{1,3}){3}$/.test(val);
    const setCurrentIp = (ip) => {
        currentIp = ip;
        if (selectedAssetIp) selectedAssetIp.textContent = ip || 'No IP';
        if (ipHidden) ipHidden.value = ip || '';
        if (serverIdHidden) serverIdHidden.value = '';
        buildYaml();
        if (!ip) return;
        const token = document.querySelector('#assetsForm input[name="_token"]')?.value || '';
        const detailsUrl = assetsForm?.getAttribute('data-server-details-url') || '/admin/assets/server-details';
        window.axios.get(detailsUrl, {
            params: { ip },
            headers: { 'X-CSRF-TOKEN': token }
        }).then((res) => {
            const data = res?.data || {};
            const services = Array.isArray(data.services) ? data.services : [];
            const hns = Array.isArray(data.hostnames) ? data.hostnames : [];
            if (serverIdHidden) serverIdHidden.value = String(data.server_id || '');
            const inputs = Array.from(document.querySelectorAll('input[name="services[]"]'));
            inputs.forEach(i => { i.checked = false; });
            services.forEach(svc => {
                const match = inputs.find(i => i.value === String(svc));
                if (match) match.checked = true;
            });
            serverServicesCount[ip] = services.length;
            renderSelectedServices();
            buildYaml();
            hostnames = hns.slice();
            renderHostnames();
            renderServers();
        }).catch(() => {
            // ignore
        });
    };
    const renderServers = () => {
        if (!assetsList) return;
        assetsList.innerHTML = '';
        if (!servers.length) {
            const empty = document.createElement('div');
            empty.className = 'text-sm text-gray-500 bg-gray-50 rounded-lg p-4';
            empty.textContent = 'Add an IP to start monitoring.';
            assetsList.appendChild(empty);
            return;
        }
        servers.forEach((ip) => {
            const row = document.createElement('button');
            row.type = 'button';
            const selected = ip === currentIp;
            row.className = 'w-full rounded-lg border p-3 flex items-center gap-3 text-left hover:border-[rgb(var(--color-primary))]';
            row.className += selected ? ' border-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.08)]' : ' border-gray-200';
            const dot = document.createElement('span');
            dot.className = 'h-2.5 w-2.5 rounded-full ' + (selected ? 'bg-emerald-600' : 'bg-[rgb(var(--color-primary))]');
            const meta = document.createElement('div');
            meta.className = 'text-sm';
            const title = document.createElement('div');
            title.className = 'font-medium';
            title.textContent = ip;
            const sub = document.createElement('div');
            sub.className = 'text-gray-500';
            const count = serverServicesCount[ip] ?? 0;
            sub.textContent = `Server · ${count} services`;
            meta.appendChild(title);
            meta.appendChild(sub);
            row.appendChild(dot);
            row.appendChild(meta);
            row.addEventListener('click', () => setCurrentIp(ip));
            assetsList.appendChild(row);
        });
    };
    const addServerIp = () => {
        if (!assetInput) return;
        const val = (assetInput.value || '').trim();
        if (!val || !isIp(val)) {
            showToast('warning', 'Enter a valid IP like 128.199.73.128');
            return;
        }
        const token = document.querySelector('#assetsForm input[name="_token"]')?.value || '';
        const saveUrl = assetsForm?.getAttribute('data-server-save-url') || '/admin/assets/server';
        window.axios.post(saveUrl, { ip: val }, {
            headers: { 'X-CSRF-TOKEN': token }
        }).then((res) => {
            const ip = res?.data?.server?.ip || val;
            if (!servers.includes(ip)) servers.push(ip);
            renderServers();
            setCurrentIp(ip);
            assetInput.value = '';
            assetInput.focus();
            const msg = res?.data?.message || 'Server saved';
            showToast('success', msg);
        }).catch((err) => {
            const status = err?.response?.status;
            const msg = err?.response?.data?.message || 'Failed to save server';
            const type = status === 409 ? 'warning' : 'error';
            showToast(type, msg);
        });
    };
    if (assetAddBtn) assetAddBtn.addEventListener('click', addServerIp);
    if (assetInput) assetInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') addServerIp();
    });
    
    
    const assetsForm = document.getElementById('assetsForm');
    if (assetsForm) {
        assetsForm.addEventListener('submit', (e) => {
            const sid = serverIdHidden ? (serverIdHidden.value || '').trim() : '';
            if (!sid) {
                if (servers.length) {
                    setCurrentIp(servers[0]);
                } else {
                    e.preventDefault();
                    showToast('warning', 'Add IP first, then Save Configuration');
                }
            }
        });
    }
    
    let serviceInputs = Array.from(document.querySelectorAll('input[name="services[]"]'));
    const servicesCountEl = document.getElementById('servicesCount');
    const servicesChipsEl = document.getElementById('selectedServicesChips');
    const yamlEl = document.getElementById('yamlConfigCode');
    const getSelectedServices = () => serviceInputs.filter(i => i.checked).map(i => i.value);
    const renderSelectedServices = () => {
        if (servicesCountEl) servicesCountEl.textContent = String(getSelectedServices().length);
        if (!servicesChipsEl) return;
        servicesChipsEl.innerHTML = '';
        getSelectedServices().forEach(s => {
            const chip = document.createElement('span');
            chip.className = 'px-3 h-8 inline-flex items-center rounded-full border border-[rgb(var(--color-primary)/.6)] text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.08)] text-xs shadow-sm';
            chip.textContent = s;
            servicesChipsEl.appendChild(chip);
        });
    };
    const exporterMap = {
        linux: 'linux_exporter',
        mysql: 'mysql_exporter',
        mongodb: 'mongodb_exporter',
        redis: 'redis_exporter',
        api_log: 'api_log_exporter',
        scheduler: 'scheduler_exporter',
    };
    const servicesContainer = document.getElementById('servicesSelectContainer');
    const serviceLabelMap = {
        linux: 'Linux',
        mysql: 'MySQL',
        mongodb: 'MongoDB',
        redis: 'Redis',
        api_log: 'API Log',
        scheduler: 'Scheduler',
    };
    const wireServiceInputs = () => {
        serviceInputs.forEach(i => {
            i.addEventListener('change', () => {
                renderSelectedServices();
                buildYaml();
                if (currentIp) {
                    serverServicesCount[currentIp] = getSelectedServices().length;
                    renderServers();
                }
            });
        });
    };
    const renderServiceSelect = () => {
        if (!servicesContainer) return;
        const keys = Object.keys(exporterMap);
        const frag = document.createDocumentFragment();
        keys.forEach(k => {
            const label = document.createElement('label');
            label.className = 'inline-flex items-center';
            const input = document.createElement('input');
            input.type = 'checkbox';
            input.name = 'services[]';
            input.value = k;
            input.className = 'peer sr-only';
            const chip = document.createElement('span');
            chip.className = 'px-3 h-9 inline-flex items-center rounded-full border border-gray-300 text-gray-700 peer-checked:bg-[rgb(var(--color-primary))] peer-checked:text-white peer-checked:border-transparent';
            chip.textContent = serviceLabelMap[k] || k;
            label.appendChild(input);
            label.appendChild(chip);
            frag.appendChild(label);
        });
        servicesContainer.innerHTML = '';
        servicesContainer.appendChild(frag);
        serviceInputs = Array.from(document.querySelectorAll('input[name="services[]"]'));
        wireServiceInputs();
        renderSelectedServices();
        buildYaml();
    };
    // postpone initial render until YAML builder is defined
    const getGlobalBlock = () => [
        'global:',
        '  app_name: "Live Shopping"',
        '  purpose: "A E-commerce Project for API Logs."',
        `  ip: "${currentIp || '128.199.73.128'}"`
    ].join('\n');
    const blocks = {
        linux_exporter: [
            'linux_exporter:',
            '  enabled: true',
            '  interval: 30',
            '  receiver_url: "https://api.example.com/collect"'
        ].join('\n'),
        scheduler_exporter: [
            'scheduler_exporter:',
            '  enabled: true',
            '  export_interval: 60',
            '  receiver_url: "http://157.245.207.91:4000/metrics/scheduler"',
            '  sources:',
            '    - type: cron',
            '      enabled: true',
            '      name: system_cron',
            '      syslog_path: /var/log/syslog',
            '      log_window_minutes: 1',
            '      max_logs: 50',
            '    - type: systemd',
            '      enabled: true',
            '      name: systemd_timers',
            '      syslog_path: undefined',
            '      log_window_minutes: 1',
            '      max_logs: 50'
        ].join('\n'),
        mysql_exporter: [
            'mysql_exporter:',
            '  enabled: true',
            '  mysql_host: "127.0.0.1"',
            '  mysql_port: 3306',
            '  mysql_user: "monitor_user"',
            '  mysql_password: "secure_password"',
            '  receiver_url: "https://api.example.com/collect"',
            '  export_interval: 30',
            '  receiver_url_logs: "https://api.example.com/collect"',
            '  mysql_log_file: "/var/log/mysql/error.log"',
            '  log_check_interval: 30',
            '  max_logs_per_batch: 100'
        ].join('\n'),
        mongodb_exporter: [
            'mongodb_exporter:',
            '  enabled: true',
            '  mongo_uri: "mongodb://localhost:27017"',
            '  export_interval: 30',
            '  receiver_url: "https://api.example.com/collect"',
            '  receiver_url_logs: "https://api.example.com/collect"',
            '  mongo_log_file: "/var/log/mongodb/mongod.log"',
            '  log_check_interval: 30',
            '  max_logs_per_batch: 100'
        ].join('\n'),
        redis_exporter: [
            'redis_exporter:',
            '  enabled: true',
            '  redis_host: "127.0.0.1"',
            '  redis_port: 6379',
            '  redis_password: ""',
            '  receiver_url: "https://api.example.com/collect"',
            '  export_interval: 30',
            '  receiver_url_logs: "https://api.example.com/collect"',
            '  redis_log_file: "/var/log/redis/redis-server.log"',
            '  log_check_interval: 30',
            '  max_logs_per_batch: 100'
        ].join('\n'),
        api_log_exporter: [
            'api_log_exporter:',
            '  enabled: true',
            '  export_interval: 60',
            '  receiver_url: "https://api.example.com/collect"',
            '  sources:',
            '    - type: nginx',
            '      enabled: false',
            '      name: nginx_prod',
            '      access_log_path: /var/log/nginx/access.log',
            '      log_window_minutes: 1',
            '      max_logs: 100',
            '    - type: mongoose',
            '      enabled: true',
            '      name: mongoose_logs',
            '      mongo_uri: "mongodb://localhost:27017"',
            '      collection: logs',
            '      log_window_minutes: 1',
            '      max_logs: 200'
        ].join('\n'),
    };
    const buildYaml = () => {
        if (!yamlEl) return;
        const parts = [getGlobalBlock()];
        getSelectedServices().forEach(s => {
            const exporter = exporterMap[s];
            const block = exporter ? blocks[exporter] : '';
            if (block) {
                parts.push('');
                parts.push(block);
            }
        });
        yamlEl.textContent = parts.join('\n');
    };
    renderServiceSelect();
    serviceInputs.forEach(i => {
        i.addEventListener('change', () => {
            renderSelectedServices();
            buildYaml();
            if (currentIp) {
                serverServicesCount[currentIp] = getSelectedServices().length;
                renderServers();
            }
        });
    });
    renderSelectedServices();
    buildYaml();
    renderServers();
    if (servers.length) {
        setCurrentIp(servers[0]);
    }
});
