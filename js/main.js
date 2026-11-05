// Modern JavaScript for Natural Cosmetics Website

// Enhanced cart functionality
function updateCartCount(count) {
    const cartBadge = document.querySelector('.badge.bg-success');
    if (cartBadge) {
        cartBadge.textContent = count;
    }
}

// Add to cart with animation
function addToCart(productId, quantity = 1) {
    // Show animation
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (productCard) {
        productCard.style.transform = 'scale(0.95)';
        setTimeout(() => {
            productCard.style.transform = '';
        }, 300);
    }

    // Show notification
    showNotification('Product added to cart!', 'success');
}

// Form validation with modern styling
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        }
    });

    return isValid;
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Enhanced DOM content loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle form submissions with validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!validateForm(form)) {
                e.preventDefault();
                showNotification('Please fill in all required fields.', 'danger');
            }
        });
    });

    // Handle quantity changes in cart
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
            if (this.value < 1) {
                this.value = 1;
            }
            // Update cart via AJAX if needed
        });
    });

    // Handle mobile menu toggle with animation
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function () {
            const navbarCollapse = document.querySelector('.navbar-collapse');
            navbarCollapse.classList.toggle('show');
        });
    }

    // Add animation to product cards when they come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]:not([href="#"])');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add floating animation to hero elements
    const heroElements = document.querySelectorAll('.hero-section h1, .hero-section .lead, .hero-section .btn');
    heroElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';

        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 300 + (index * 150));
    });
});

// Cart update functions with AJAX
function updateCartItem(cartId, quantity) {
    // Show loading state
    const quantityInput = document.querySelector(`input[name="quantity"][value="${quantity}"]`);
    if (quantityInput) {
        quantityInput.disabled = true;
        quantityInput.style.opacity = '0.6';
    }

    // Simulate AJAX call
    setTimeout(() => {
        if (quantityInput) {
            quantityInput.disabled = false;
            quantityInput.style.opacity = '1';
        }
        showNotification('Cart updated successfully!', 'success');
    }, 500);
}

function removeCartItem(cartId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        // Show loading state
        showNotification('Removing item from cart...', 'info');

        // Simulate AJAX call
        setTimeout(() => {
            showNotification('Item removed from cart!', 'success');
            // Remove item from DOM
            const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
            if (cartItem) {
                cartItem.style.opacity = '0';
                cartItem.style.transform = 'translateX(-100%)';
                setTimeout(() => {
                    cartItem.remove();
                    // Update cart count
                    updateCartCount(getCartItemCount());
                }, 300);
            }
        }, 500);
    }
}

// Get cart item count
function getCartItemCount() {
    const cartItems = document.querySelectorAll('.cart-item');
    return cartItems.length;
}

// Product filtering with animation
function filterProductsByCategory(categoryId) {
    // Add animation to filter
    const categoryBtn = document.querySelector(`[data-category-id="${categoryId}"]`);
    if (categoryBtn) {
        categoryBtn.classList.add('animate__pulse');
        setTimeout(() => {
            categoryBtn.classList.remove('animate__pulse');
        }, 600);
    }

    showNotification('Filtering products...', 'info');
}

// Search functionality with debouncing
let searchTimeout;
function searchProducts(query) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (query.length > 0) {
            showNotification(`Searching for: ${query}`, 'info');
        }
    }, 500);
}

// Toggle password visibility with icon
function togglePasswordVisibility(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);

    if (field.type === 'password') {
        field.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        field.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

// Handle responsive design adjustments
function handleResponsiveDesign() {
    const windowWidth = window.innerWidth;

    // Adjust layout based on screen size
    if (windowWidth <= 768) {
        // Mobile-specific adjustments
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.style.height = 'auto';
        });
    }
}

// Initialize responsive design handling
window.addEventListener('resize', handleResponsiveDesign);
handleResponsiveDesign();

// Admin panel specific functions
function confirmAction(message) {
    return confirm(message);
}

// Handle admin form submissions
function handleAdminForm(formId, action) {
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!validateForm(form)) {
                e.preventDefault();
                showNotification('Please fill in all required fields.', 'danger');
            }
        });
    }
}

// Initialize theme (dark/light mode)
function initTheme() {
    const theme = localStorage.getItem('theme') || 'light';
    document.body.setAttribute('data-theme', theme);
}

// Toggle theme
function toggleTheme() {
    const currentTheme = document.body.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';

    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);

    showNotification(`Switched to ${newTheme} mode`, 'info');
}

// Initialize when page loads
initTheme();

// Admin Sidebar Toggle functionality
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');

    // Only initialize if elements exist (we are on an admin page)
    if (sidebar && sidebarToggle && mainContent) {
        // Toggle sidebar on button click
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');

            // On mobile, show/hide overlay
            if (window.innerWidth <= 992 && sidebarOverlay) {
                sidebarOverlay.classList.toggle('active');
                document.body.style.overflow = sidebarOverlay.classList.contains('active') ? 'hidden' : '';
            }
        });

        // Close sidebar when clicking on overlay (mobile)
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function () {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Handle responsive behavior
        window.addEventListener('resize', function () {
            if (window.innerWidth > 992) {
                // On desktop, always show sidebar expanded
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            } else {
                // On mobile, collapse sidebar by default (only if we didn't deliberately expand it? simpler to just collapse)
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
        });

        // Initialize based on screen size
        if (window.innerWidth <= 992) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
    }
});

// Hidden Admin Login Shortcut (Ctrl + Shift + L)
document.addEventListener('keydown', function (e) {
    // Check for Ctrl + Shift + L
    if (e.ctrlKey && e.shiftKey && (e.key === 'L' || e.key === 'l')) {
        e.preventDefault();
        // Redirect to login page
        // Use absolute path or relative based on current location
        const path = window.location.pathname;
        if (path.includes('/admin/')) {
            window.location.href = '../login.php';
        } else {
            window.location.href = 'login.php';
        }
    }
});