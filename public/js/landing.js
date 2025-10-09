// Landing Page Enhanced Interactions
document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Counter animation for statistics
    const animateCounter = (element, target, duration = 2000) => {
        let start = 0;
        const increment = target / (duration / 16);
        
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(start).toLocaleString();
            }
        }, 16);
    };

    // Counter animation observer
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.dataset.count);
                animateCounter(entry.target, target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    // Observe counter elements
    document.querySelectorAll('[data-count]').forEach(counter => {
        counterObserver.observe(counter);
    });

    // Mobile menu toggle enhancement
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
            
            // Add/remove body scroll lock
            document.body.classList.toggle('overflow-hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    }

    // Parallax effect for hero background elements
    const heroElements = document.querySelectorAll('.animate-float, .animate-float-delayed, .animate-float-slow');
    
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        heroElements.forEach((element, index) => {
            const speed = (index + 1) * 0.3;
            element.style.transform = `translateY(${rate * speed}px)`;
        });
    });

    // Form validation for newsletter signup
    const newsletterForm = document.querySelector('footer form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            if (email && isValidEmail(email)) {
                // Here you would typically send the email to your backend
                showNotification('Thank you for subscribing!', 'success');
                this.reset();
            } else {
                showNotification('Please enter a valid email address.', 'error');
            }
        });
    }

    // Email validation helper
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transform translate-x-full transition-transform duration-300 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Performance optimization: Debounce scroll events
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        scrollTimeout = setTimeout(() => {
            // Scroll-based functionality here
            updateScrollProgress();
        }, 10);
    });

    // Scroll progress indicator
    function updateScrollProgress() {
        const scrollTop = window.pageYOffset;
        const docHeight = document.body.offsetHeight;
        const winHeight = window.innerHeight;
        const scrollPercent = scrollTop / (docHeight - winHeight);
        
        // Update any scroll progress elements
        const progressElements = document.querySelectorAll('.scroll-progress');
        progressElements.forEach(el => {
            el.style.width = `${scrollPercent * 100}%`;
        });
    }

    // Keyboard navigation enhancement
    document.addEventListener('keydown', (e) => {
        // ESC key closes mobile menu
        if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });

    // Preload critical images
    const criticalImages = [
        '/images/hero.jpg',
        '/images/league.jpg'
    ];

    criticalImages.forEach(src => {
        const img = new Image();
        img.src = src;
    });

    // Add loading states for better UX
    window.addEventListener('load', () => {
        document.body.classList.add('loaded');
        
        // Remove any loading spinners
        const loaders = document.querySelectorAll('.loading');
        loaders.forEach(loader => loader.remove());
    });
});

// Service Worker registration for PWA capabilities (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}


