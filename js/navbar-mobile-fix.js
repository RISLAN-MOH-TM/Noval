/**
 * Mobile Navbar Auto-Close Fix
 * Automatically closes the mobile navbar when clicking on any link
 */

document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (!navbarToggler || !navbarCollapse) return;
    
    // Get all navigation links
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link, .navbar-nav .dropdown-item');
    
    // Close navbar when clicking on any link (mobile only)
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Only on mobile screens (less than 992px)
            if (window.innerWidth < 992) {
                // Check if navbar is currently shown
                if (navbarCollapse.classList.contains('show')) {
                    // Use Bootstrap's collapse method to hide
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse, { toggle: false });
                    bsCollapse.hide();
                }
            }
        });
    });
    
    // Also close when clicking outside the navbar
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 992) {
            const isClickInsideNavbar = navbarCollapse.contains(event.target) || navbarToggler.contains(event.target);
            
            if (!isClickInsideNavbar && navbarCollapse.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse, { toggle: false });
                bsCollapse.hide();
            }
        }
    });
});
