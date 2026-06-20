/* Main JavaScript - Interactive Features */

document.addEventListener('DOMContentLoaded', function() {
    initMenuToggle();
    initSlider();
    initSearch();
    initFilter();
    initFormValidation();
});

// Mobile Menu Toggle
function initMenuToggle() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
}

// Premium Slider
function initSlider() {
    const sliders = document.querySelectorAll('.slider');
    
    sliders.forEach(slider => {
        const container = slider.closest('.slider-container');
        if (!container) return;
        
        const prevBtn = container.querySelector('.slider-btn-prev');
        const nextBtn = container.querySelector('.slider-btn-next');
        let currentIndex = 0;
        
        function updateSlider() {
            const itemWidth = slider.querySelector('.slider-item').offsetWidth + 20;
            slider.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                currentIndex = Math.max(0, currentIndex - 1);
                updateSlider();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                const maxIndex = slider.querySelectorAll('.slider-item').length - 1;
                currentIndex = Math.min(maxIndex, currentIndex + 1);
                updateSlider();
            });
        }
        
        // Auto-scroll
        let autoScrollInterval = setInterval(() => {
            const maxIndex = slider.querySelectorAll('.slider-item').length - 1;
            currentIndex = (currentIndex + 1) % (maxIndex + 1);
            updateSlider();
        }, 5000);
        
        // Pause on hover
        container.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
        container.addEventListener('mouseleave', () => {
            autoScrollInterval = setInterval(() => {
                const maxIndex = slider.querySelectorAll('.slider-item').length - 1;
                currentIndex = (currentIndex + 1) % (maxIndex + 1);
                updateSlider();
            }, 5000);
        });
    });
}

// Search functionality
function initSearch() {
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const query = this.querySelector('input[name="search"]').value.trim();
            if (query.length < 2) {
                e.preventDefault();
                alert('Search term must be at least 2 characters');
            }
        });
    }
}

// Price filter
function initFilter() {
    const filterForm = document.querySelector('.filter-form');
    if (filterForm) {
        filterForm.addEventListener('change', function() {
            this.submit();
        });
    }
}

// Form validation
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const password = this.querySelector('input[name="password"]');
            const confirmPassword = this.querySelector('input[name="confirm_password"]');
            
            if (password && confirmPassword) {
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    showAlert('Passwords do not match', 'danger');
                    return false;
                }
                
                if (password.value.length < 8) {
                    e.preventDefault();
                    showAlert('Password must be at least 8 characters', 'danger');
                    return false;
                }
            }
            
            const email = this.querySelector('input[type="email"]');
            if (email && !isValidEmail(email.value)) {
                e.preventDefault();
                showAlert('Invalid email address', 'danger');
                return false;
            }
        });
    });
}

// Utility functions
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

function confirmAction(message) {
    return confirm(message);
}

function formatCurrency(value, currency = 'USDT') {
    if (currency === 'BTC') {
        return '฿' + parseFloat(value).toFixed(8);
    }
    return currency + ' ' + parseFloat(value).toFixed(2);
}
