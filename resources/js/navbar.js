// Add this if you want to use Vite's hot module replacement
if (import.meta.hot) {
    import.meta.hot.accept();
}

document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const menuIcon = menuToggle.querySelector('i');
    let isExpanded = false;

    function toggleSidebar() {
        isExpanded = !isExpanded;

        // Force a reflow to ensure the transition works
        sidebar.offsetHeight;

        sidebar.classList.toggle('expanded');
        mainContent.classList.toggle('expanded');

        // Toggle icon
        menuIcon.classList.toggle('fa-bars');
        menuIcon.classList.toggle('fa-times');
    }

    menuToggle.addEventListener('click', toggleSidebar);

    // Add active class to current menu item
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            menuItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Handle responsive design
    function checkWidth() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('expanded');
            mainContent.classList.remove('expanded');
            isExpanded = false;
            menuIcon.classList.remove('fa-times');
            menuIcon.classList.add('fa-bars');
        }
    }

    window.addEventListener('resize', checkWidth);
    checkWidth(); // Initial check
});

// untuk dropdown

document.addEventListener('DOMContentLoaded', function () {
    const profileIcon = document.getElementById('profileIcon');
    const profileDropdown = document.getElementById('profileDropdown');

    profileIcon.addEventListener('click', function () {
        // Tampilkan atau sembunyikan dropdown
        profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
    });

    // Tutup dropdown jika pengguna mengklik di luar
    window.addEventListener('click', function (event) {
        if (!event.target.matches('#profileIcon')) {
            profileDropdown.style.display = 'none';
        }
    });
});
