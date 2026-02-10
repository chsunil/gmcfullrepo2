/**
 * Tabs Navigation Fix for ACF Forms with Sneat Bootstrap 5
 */
jQuery(document).ready(function($) {
    console.log('Tabs Fix Script Loaded');
    
    // Initialize Bootstrap 5 tabs
    function initTabs() {
        console.log('Initializing tabs');
        
        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const stageParam = urlParams.get('stage');
        
        console.log('URL stage parameter:', stageParam);
        
        // Activate the correct tab based on URL parameter
        if (stageParam) {
            const tabToActivate = $(`#clientTab [data-tab="${stageParam}"]`);
            console.log('Tab to activate:', tabToActivate.length ? tabToActivate[0] : 'Not found');
            
            if (tabToActivate.length) {
                // Create a new bootstrap tab instance and show it
                try {
                    const tab = new bootstrap.Tab(tabToActivate[0]);
                    tab.show();
                    console.log('Tab activated successfully');
                } catch (e) {
                    console.error('Error activating tab:', e);
                }
            }
        } else {
            // If no stage parameter, activate the first tab
            const firstTab = $('#clientTab .nav-link').first();
            if (firstTab.length) {
                try {
                    const tab = new bootstrap.Tab(firstTab[0]);
                    tab.show();
                    console.log('First tab activated');
                } catch (e) {
                    console.error('Error activating first tab:', e);
                }
            }
        }
        
        // Add event listener for tab changes
        $('#clientTab .nav-link').on('shown.bs.tab', function(event) {
            const targetId = $(this).data('bs-target') || $(this).attr('href');
            const targetPane = $(targetId);
            
            console.log('Tab shown event:', targetId);
            
            // When a tab is activated, ensure ACF fields are properly initialized
            if (typeof acf !== 'undefined') {
                console.log('Triggering ACF ready action for tab:', targetId);
                acf.doAction('ready');
                
                // Force ACF to show field groups in this tab
                const fieldGroups = targetPane.find('.acf-field-group, .acf-fields');
                console.log('Field groups found in tab:', fieldGroups.length);
                fieldGroups.css('display', 'block');
                
                // Add placeholder for empty containers
                const emptyContainers = targetPane.find('.acf-fields-container:empty');
                emptyContainers.each(function() {
                    if (!$(this).children().length) {
                        $(this).append('<div class="alert alert-info mt-3">Please complete the form fields for this section.</div>');
                    }
                });
            }
        });
    }
    
    // Initialize tabs when document is ready
    initTabs();
    
    // Fix ACF field visibility
    function fixAcfFieldVisibility() {
        console.log('Fixing ACF field visibility');
        
        // Force all ACF field groups to be visible
        const fieldGroups = $('.acf-field-group, .acf-fields');
        console.log('Total field groups found:', fieldGroups.length);
        fieldGroups.css('display', 'block');
        
        // Add placeholder content to empty ACF field containers
        $('.acf-fields-container:empty').each(function() {
            if (!$(this).children().length) {
                $(this).append('<div class="alert alert-info mt-3">Please complete the form fields for this section.</div>');
            }
        });
        
        // Apply Sneat styling to ACF fields
        $('.acf-field').addClass('mb-3');
        $('.acf-field input[type="text"], .acf-field input[type="email"], .acf-field input[type="number"], .acf-field textarea, .acf-field select').addClass('form-control');
        $('.acf-field input[type="checkbox"], .acf-field input[type="radio"]').addClass('form-check-input');
        $('.acf-field .acf-label').addClass('form-label');
        
        // Ensure active tab content is visible
        const activeTabId = $('#clientTab .nav-link.active').data('bs-target') || $('#clientTab .nav-link.active').attr('href');
        if (activeTabId) {
            console.log('Active tab ID:', activeTabId);
            $(activeTabId).addClass('active show');
            
            // Force field groups in active tab to be visible
            $(activeTabId).find('.acf-field-group, .acf-fields').css('display', 'block');
        }
    }
    
    // Run when ACF is ready
    if (typeof acf !== 'undefined') {
        acf.addAction('ready', function() {
            console.log('ACF Ready event triggered');
            fixAcfFieldVisibility();
        });
        
        // Run again after a short delay to catch any late-loading fields
        setTimeout(fixAcfFieldVisibility, 500);
        // And again after a longer delay to catch any very late loading fields
        setTimeout(fixAcfFieldVisibility, 1500);
    } else {
        console.warn('ACF not defined - waiting for it to load');
        
        // Check periodically for ACF to become available
        const acfCheckInterval = setInterval(function() {
            if (typeof acf !== 'undefined') {
                console.log('ACF now available');
                clearInterval(acfCheckInterval);
                fixAcfFieldVisibility();
                
                acf.addAction('ready', fixAcfFieldVisibility);
            }
        }, 500);
    }
});