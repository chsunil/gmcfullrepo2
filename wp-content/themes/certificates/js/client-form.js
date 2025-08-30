/**
 * Client Form JavaScript
 * Handles tabs, ACF fields, and stage navigation for the client form template
 */
jQuery(document).ready(function($) {
    console.log('Client Form JS loaded');
    
    // Initialize Bootstrap 5 tabs
    function initTabs() {
        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const stageParam = urlParams.get('stage');
        
        // Activate the correct tab based on URL parameter
        if (stageParam) {
            const tabToActivate = $(`#clientTab [data-tab="${stageParam}"]`);
            if (tabToActivate.length) {
                try {
                    const tab = new bootstrap.Tab(tabToActivate[0]);
                    tab.show();
                } catch (e) {
                    console.error('Error activating tab:', e);
                }
            }
        }
        
        // Add event listener for tab changes
        $('#clientTab .nav-link').on('shown.bs.tab', function(event) {
            const targetId = $(this).data('bs-target') || $(this).attr('href');
            const targetPane = $(targetId);
            
            // When a tab is activated, ensure ACF fields are properly initialized
            if (typeof acf !== 'undefined') {
                acf.doAction('ready');
                
                // Force ACF to show field groups in this tab
                targetPane.find('.acf-field-group, .acf-fields').css('display', 'block');
                
                // Apply Sneat styling to ACF fields in this tab
                applySneatStyling(targetPane);
            }
        });
    }
    
    // Apply Sneat styling to ACF fields
    function applySneatStyling(container) {
        const $container = container || $('body');
        
        // Add Bootstrap classes to ACF fields
        $container.find('.acf-field').addClass('mb-3');
        $container.find('.acf-field input[type="text"], .acf-field input[type="email"], .acf-field input[type="number"], .acf-field textarea, .acf-field select').addClass('form-control');
        $container.find('.acf-field input[type="checkbox"], .acf-field input[type="radio"]').addClass('form-check-input');
        $container.find('.acf-field .acf-label').addClass('form-label');
        
        // Style ACF repeater tables
        $container.find('.acf-table').addClass('table table-bordered');
        $container.find('.acf-repeater .acf-row-handle').addClass('align-middle text-center');
        
        // Style ACF buttons
        $container.find('.acf-button').addClass('btn btn-sm');
        $container.find('.acf-button[data-event="add-row"]').addClass('btn-primary');
        $container.find('.acf-button[data-event="remove-row"]').addClass('btn-danger');
        
        // Style ACF tabs if present
        $container.find('.acf-tab-wrap').addClass('mb-3');
        $container.find('.acf-tab-group').addClass('nav nav-tabs');
        $container.find('.acf-tab-group li').addClass('nav-item');
        $container.find('.acf-tab-group li a').addClass('nav-link');
        $container.find('.acf-tab-group li.active a').addClass('active');
    }
    
    // Fix ACF field visibility in tabs
    function fixAcfFieldVisibility() {
        // Force all ACF field groups to be visible
        $('.acf-field-group, .acf-fields').css('display', 'block');
        
        // Apply Sneat styling to all ACF fields
        applySneatStyling();
        
        // Ensure active tab content is visible
        const activeTabId = $('#clientTab .nav-link.active').data('bs-target') || $('#clientTab .nav-link.active').attr('href');
        if (activeTabId) {
            $(activeTabId).addClass('active show');
            
            // Force field groups in active tab to be visible
            $(activeTabId).find('.acf-field-group, .acf-fields').css('display', 'block');
        }
    }
    
    // Initialize tabs
    initTabs();
    
    // Run when ACF is ready
    if (typeof acf !== 'undefined') {
        acf.addAction('ready', fixAcfFieldVisibility);
        
        // Run again after a short delay to catch any late-loading fields
        setTimeout(fixAcfFieldVisibility, 500);
    } else {
        // Check periodically for ACF to become available
        const acfCheckInterval = setInterval(function() {
            if (typeof acf !== 'undefined') {
                clearInterval(acfCheckInterval);
                fixAcfFieldVisibility();
                acf.addAction('ready', fixAcfFieldVisibility);
            }
        }, 500);
    }
    
    // Handle certification type change
    $('#certification_type_selector').on('change', function() {
        const type = $(this).val();
        const postId = $('#post_id').val();
        
        if (!postId) return;
        
        // Update certification type via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_certification_type',
                post_id: postId,
                type: type,
                nonce: clientFormData.certTypeNonce
            },
            success: function(response) {
                if (response.success) {
                    // Reload the page to show the new certification type's tabs
                    window.location.reload();
                } else {
                    console.error('Error updating certification type:', response.data);
                }
            }
        });
    });
    
    // Handle next stage button clicks
    $('.next-stage-btn').on('click', function() {
        const currentStage = $(this).data('current');
        const nextStage = $(this).data('next');
        const postId = $('#post_id').val();
        
        if (!postId || !nextStage) return;
        
        // Update client stage via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_client_stage',
                post_id: postId,
                next_stage: nextStage,
                nonce: clientFormData.stageNonce
            },
            success: function(response) {
                if (response.success) {
                    // Activate the next tab
                    const nextTab = $(`#clientTab [data-tab="${nextStage}"]`);
                    if (nextTab.length) {
                        const tab = new bootstrap.Tab(nextTab[0]);
                        tab.show();
                    } else {
                        // If tab doesn't exist yet, reload the page
                        window.location.reload();
                    }
                } else {
                    console.error('Error updating client stage:', response.data);
                }
            }
        });
    });
});