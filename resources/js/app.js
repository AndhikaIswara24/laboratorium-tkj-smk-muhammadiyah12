

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const root = document.documentElement;

if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    root.classList.add('dark');
}

document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const themeToggle = document.getElementById('themeToggle');

    const isDesktop = () => window.matchMedia('(min-width: 1024px)').matches;

    function openMobileSidebar() {
        sidebar?.classList.remove('-translate-x-full');
        backdrop?.classList.remove('hidden');
    }

    function closeMobileSidebar() {
        sidebar?.classList.add('-translate-x-full');
        backdrop?.classList.add('hidden');
    }

    sidebarToggle?.addEventListener('click', () => {
        if (isDesktop()) {
            body.classList.toggle('sidebar-collapsed');
            localStorage.sidebar = body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded';
        } else if (sidebar?.classList.contains('-translate-x-full')) {
            openMobileSidebar();
        } else {
            closeMobileSidebar();
        }
    });

    backdrop?.addEventListener('click', closeMobileSidebar);

    if (localStorage.sidebar === 'collapsed') {
        body.classList.add('sidebar-collapsed');
    }

    themeToggle?.addEventListener('click', () => {
        root.classList.toggle('dark');
        localStorage.theme = root.classList.contains('dark') ? 'dark' : 'light';
    });

    document.querySelectorAll('[data-toast]').forEach((toast) => {
        setTimeout(() => toast.remove(), 4500);
    });
});
