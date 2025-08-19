// scripts.js - Common UI scripts for {{config('app.name')}}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for all select elements
    initializeSelect2();
    
    // Initialize Bootstrap Datepicker
    initializeDatepickers();
});

/**
 * Initialize Select2 for all select elements
 */
function initializeSelect2() {
    // Check if jQuery and Select2 are available
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        // Apply Select2 to all select elements except those with 'no-select2' class
        jQuery('select').not('.no-select2').each(function() {
            var $this = jQuery(this);
            
            // Default options
            var options = {
                theme: 'classic',
                width: '100%',
                placeholder: $this.data('placeholder') || 'Select an option'
            };
            
            // Check for remote data option
            if ($this.data('ajax-url')) {
                options.ajax = {
                    url: $this.data('ajax-url'),
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || data,
                            pagination: {
                                more: data.next_page_url !== null
                            }
                        };
                    },
                    cache: true
                };
            }
            
            // Initialize Select2 with options
            $this.select2(options);
        });
    } else {
        console.warn('Select2 or jQuery not available');
    }
}

/**
 * Initialize Bootstrap Datepicker for all date inputs
 */
function initializeDatepickers() {
    // Check if jQuery and Datepicker are available
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.datepicker !== 'undefined') {
        // Apply datepicker to all inputs with type="date" or class "datepicker"
        jQuery('input[type="date"], .datepicker').each(function() {
            var $this = jQuery(this);
            
            // Convert HTML5 date inputs to text inputs for consistent datepicker functionality
            if ($this.attr('type') === 'date') {
                $this.attr('type', 'text');
            }
            
            // Default options
            var options = {
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                todayBtn: 'linked',
                clearBtn: true
            };
            
            // Get any data attributes for customizing datepicker
            if ($this.data('date-format')) {
                options.format = $this.data('date-format');
            }
            
            if ($this.data('date-start-date')) {
                options.startDate = $this.data('date-start-date');
            }
            
            if ($this.data('date-end-date')) {
                options.endDate = $this.data('date-end-date');
            }
            
            // Initialize datepicker
            $this.datepicker(options);
        });
    } else {
        console.warn('Bootstrap Datepicker or jQuery not available');
    }
}

// Make functions available globally
window.initializeSelect2 = initializeSelect2;
window.initializeDatepickers = initializeDatepickers;
