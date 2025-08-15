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

    /** Theme Switching functionality */
    function setupThemeSwitcher() {
        const body = document.getElementById('app-body');
        const themeCssLink = document.getElementById('theme-css');
        const themeSwitcherButton = document.getElementById('theme-switcher');
        
        // Available themes - Blue and Green toggle
        const themes = {
            'glacier-blue': {
                name: 'Blue Theme',
                cssFile: '/css/glacier-blue-theme.css',
                bodyClass: 'glacier-blue-theme',
                color: '#4a90e2',
                icon: 'blue'
            },
            'deep-teal': {
                name: 'Green Theme',
                cssFile: '/css/deep-teal-theme.css',
                bodyClass: 'deep-teal-theme',
                color: '#5c9c85',
                icon: 'green'
            }
        };
        
        // Get current theme from localStorage or default to deep-teal (green)
        let currentTheme = localStorage.getItem('selectedTheme') || 'deep-teal';
        
        // Apply theme on page load
        applyTheme(currentTheme);
        
        function applyTheme(themeId) {
            const theme = themes[themeId];
            if (!theme) return;
            
            // Update CSS file
            themeCssLink.href = theme.cssFile + '?v=' + Date.now();
            
            // Update body class
            // Remove all theme classes first
            Object.values(themes).forEach(t => {
                body.classList.remove(t.bodyClass);
            });
            
            // Add new theme class
            body.classList.add(theme.bodyClass);
            
            // Save to localStorage
            localStorage.setItem('selectedTheme', themeId);
            currentTheme = themeId;
            
            // Update button appearance and title
            updateThemeSwitcherButton(themeId);
        }
        
        function updateThemeSwitcherButton(themeId) {
            if (!themeSwitcherButton) return;
            
            const currentThemeData = themes[themeId];
            const nextThemeId = themeId === 'glacier-blue' ? 'deep-teal' : 'glacier-blue';
            const nextThemeData = themes[nextThemeId];
            
            // Update button title
            themeSwitcherButton.title = `Current: ${currentThemeData.name} | Click to switch to ${nextThemeData.name} (Ctrl+T)`;
            
            // Update button icon - show the next theme color as a hint
            const svg = themeSwitcherButton.querySelector('svg');
            if (svg) {
                // Update icon based on current theme
                if (themeId === 'glacier-blue') {
                    // Currently blue, show green hint
                    svg.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6a2 2 0 002 2h4a2 2 0 002-2V5zM21 15a2 2 0 00-2-2h-4a2 2 0 00-2 2v2a2 2 0 002 2h4a2 2 0 002-2v-2z" 
                              fill="#5c9c85" stroke="#5c9c85"/>
                    `;
                } else {
                    // Currently green, show blue hint
                    svg.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6a2 2 0 002 2h4a2 2 0 002-2V5zM21 15a2 2 0 00-2-2h-4a2 2 0 00-2 2v2a2 2 0 002 2h4a2 2 0 002-2v-2z" 
                              fill="#4a90e2" stroke="#4a90e2"/>
                    `;
                }
            }
            
            // Add visual feedback class
            themeSwitcherButton.classList.remove('theme-blue', 'theme-green');
            themeSwitcherButton.classList.add(themeId === 'glacier-blue' ? 'theme-blue' : 'theme-green');
        }
        
        // Theme switcher button click event - simple toggle between blue and green
        if (themeSwitcherButton) {
            themeSwitcherButton.addEventListener('click', function() {
                // Toggle between the two themes
                const nextTheme = currentTheme === 'glacier-blue' ? 'deep-teal' : 'glacier-blue';
                applyTheme(nextTheme);
                
                // Add click animation
                this.classList.add('animate-pulse');
                setTimeout(() => {
                    this.classList.remove('animate-pulse');
                }, 200);
            });
        }
        
        // Expose theme switching function globally for potential UI controls
        window.switchTheme = function(themeId) {
            if (themes[themeId]) {
                applyTheme(themeId);
            }
        };
        
        // Toggle function specifically for blue/green
        window.toggleTheme = function() {
            const nextTheme = currentTheme === 'glacier-blue' ? 'deep-teal' : 'glacier-blue';
            applyTheme(nextTheme);
        };
        
        // Add keyboard shortcut for theme switching (Ctrl/Cmd + T)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 't' && !e.shiftKey) {
                e.preventDefault();
                window.toggleTheme();
            }
        });
    }
    
    // Initialize theme switcher
    setupThemeSwitcher();
})();
