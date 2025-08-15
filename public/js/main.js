// Use defer in script tag to avoid waiting for DOMContentLoaded
(function () {
    // Cached DOM elements
    const backToTopLinks = document.querySelectorAll('a[href="#top"]');
    const menuButtons = document.querySelectorAll('#mobile-menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const closeButton = document.getElementById('close-sidebar');
    const desktopLeagueSelector = document.getElementById('header-league-selector');
    const mobileLeagueSelector = document.getElementById('mobile-league-selector');
    const loadingOverlay = document.getElementById('loading-overlay');

    /** Loading Overlay functionality */
    function setupLoadingAnimation() {
        // Get all navigation links that are not anchor links or JavaScript actions
        const navLinks = document.querySelectorAll('a[href]:not([href^="#"]):not([href^="javascript"])');
        
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Skip if modifier keys are pressed (for opening in new tab, etc.)
                if (e.ctrlKey || e.metaKey || e.shiftKey) return;
                
                // Skip for external links
                const url = new URL(link.href, window.location.origin);
                if (url.origin !== window.location.origin) return;
                
                // Show loading overlay
                e.preventDefault();
                loadingOverlay.classList.add('active');
                
                // Navigate after a small delay to allow animation to be seen
                setTimeout(() => {
                    window.location.href = link.href;
                }, 400);
            });
        });
        
        // Also add loading for form submissions (like logout)
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // If it's a logout form or other navigation form
                if (form.method.toLowerCase() === 'post') {
                    loadingOverlay.classList.add('active');
                }
            });
        });
    }

    /** Smooth Scroll Back to Top */
    if (backToTopLinks.length) {
        for (const link of backToTopLinks) {
            link.addEventListener('click', e => {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, { passive: true });
        }
    }

    /** Sidebar functionality */
    if (sidebar && overlay) {
        const openSidebar = () => {
            for (const btn of menuButtons) btn?.classList.add('open');
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('sidebar-visible');
            overlay.classList.remove('hidden', 'opacity-0');
            overlay.classList.add('opacity-100');
            document.body.classList.add('overflow-hidden');
        };

        const closeSidebar = () => {
            for (const btn of menuButtons) btn?.classList.remove('open');
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('sidebar-visible');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            overlay.addEventListener('transitionend', () => overlay.classList.add('hidden'), { once: true });
            document.body.classList.remove('overflow-hidden');
        };

        for (const btn of menuButtons) {
            btn?.addEventListener('click', () =>
                sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar()
            );
        }

        closeButton?.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', e => e.key === 'Escape' && closeSidebar());
    }

    /** Sync League Selectors */
    if (desktopLeagueSelector && mobileLeagueSelector) {
        const syncAndNavigate = (source, target) => {
            target.value = source.value;
            if (source.value) {
                loadingOverlay.classList.add('active');
                window.location.href = `/leagues/${source.value}`;
            }
        };
        desktopLeagueSelector.value = mobileLeagueSelector.value;
        desktopLeagueSelector.addEventListener('change', () => syncAndNavigate(desktopLeagueSelector, mobileLeagueSelector));
        mobileLeagueSelector.addEventListener('change', () => syncAndNavigate(mobileLeagueSelector, desktopLeagueSelector));
    }

    // Set up loading animation for all navigation links
    if (loadingOverlay) {
        setupLoadingAnimation();
        
        // Hide loading overlay when back button is used
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                loadingOverlay.classList.remove('active');
            }
        });
    }
})();
