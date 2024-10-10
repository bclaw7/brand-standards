document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const openSidebarBtn = document.getElementById('open-sidebar');
    const closeSidebarBtn = document.getElementById('close-sidebar');

    openSidebarBtn.addEventListener('click', function () {
        sidebar.classList.add('active');
    });

    closeSidebarBtn.addEventListener('click', function () {
        sidebar.classList.remove('active');
    });

    // Close sidebar when clicking outside of it
    document.addEventListener('click', function (event) {
        if (!sidebar.contains(event.target) && !openSidebarBtn.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    });
});