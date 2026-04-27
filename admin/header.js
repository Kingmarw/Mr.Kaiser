// sidebar of admin panel
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');
const mobileBtn = document.getElementById('mobile-menu-btn');
const closeBtn = document.getElementById('close-sidebar-btn');

function toggleSidebar() {
    sidebar.classList.toggle('translate-x-full');
    overlay.classList.toggle('hidden');
}

if(mobileBtn) mobileBtn.addEventListener('click', toggleSidebar);
if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
if(overlay) overlay.addEventListener('click', toggleSidebar);