(function () {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');

    function closeSidebar() {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('show');
    }

    toggle?.addEventListener('click', function () {
        sidebar?.classList.toggle('open');
        overlay?.classList.toggle('show');
    });

    overlay?.addEventListener('click', closeSidebar);

    sidebar?.querySelectorAll('.admin-sidebar-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) closeSidebar();
        });
    });
})();
