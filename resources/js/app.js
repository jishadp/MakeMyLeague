import './bootstrap';

// Import Flatpickr
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

// Initialize Flatpickr when DOM is ready
document.addEventListener("DOMContentLoaded", function() {
    // Initialize Flatpickr with mobile-friendly options
    flatpickr('.flatpickr', {
        dateFormat: "Y-m-d",
        allowInput: true,
        enableTime: false,
        disableMobile: false, // Enable on mobile devices
        clickOpens: true,
        monthSelectorType: "static", // Fix month navigation
        // Mobile-specific optimizations
        static: window.innerWidth <= 768, // Static positioning on mobile
        position: window.innerWidth <= 768 ? "center" : "auto", // Center on mobile, auto on desktop
        // Touch-friendly options
        animate: true,
        // Ensure proper z-index
        appendTo: document.body,
        // Mobile viewport handling
        onReady: function(selectedDates, dateStr, instance) {
            // Ensure calendar is properly positioned with a small delay
            setTimeout(() => {
                if (instance.calendarContainer) {
                    // Set high z-index for both desktop and mobile
                    instance.calendarContainer.style.zIndex = '99999';
                    
                    // Additional mobile optimizations
                    if (window.innerWidth <= 768) {
                        // Ensure calendar is properly sized on mobile
                        instance.calendarContainer.style.maxHeight = '90vh';
                        instance.calendarContainer.style.overflow = 'visible';
                        
                        // Add backdrop for mobile
                        if (!document.querySelector('.flatpickr-backdrop')) {
                            const backdrop = document.createElement('div');
                            backdrop.className = 'flatpickr-backdrop';
                            backdrop.style.cssText = `
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background: rgba(0, 0, 0, 0.5);
                                z-index: 99998;
                            `;
                            backdrop.addEventListener('click', () => instance.close());
                            document.body.appendChild(backdrop);
                        }
                    }
                }
            }, 10);
        },
        onClose: function(selectedDates, dateStr, instance) {
            // Remove backdrop when calendar closes
            const backdrop = document.querySelector('.flatpickr-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    });
});
