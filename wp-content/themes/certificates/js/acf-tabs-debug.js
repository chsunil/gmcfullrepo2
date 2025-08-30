/**
 * ACF Tabs Debugging Script
 * This script logs information about ACF tabs and field groups to help debug issues
 */
jQuery(document).ready(function($) {
    console.log('ACF Tabs Debug Script Loaded');
    
    // Function to log tab information
    function logTabInfo() {
        console.log('=== ACF TABS DEBUG ===');
        
        // Log all tab elements
        console.log('Tab Elements:');
        $('#clientTab').each(function() {
            console.log('Client Tab Container:', this);
            console.log('HTML:', $(this).html());
            console.log('Tab Items:', $(this).find('.nav-item').length);
        });
        
        // Log all tab panes
        console.log('Tab Panes:');
        $('.tab-pane').each(function() {
            console.log('Tab Pane:', this);
            console.log('ID:', $(this).attr('id'));
            console.log('Classes:', $(this).attr('class'));
            console.log('Field Groups Inside:', $(this).find('.acf-field-group').length);
        });
        
        // Log ACF field groups
        console.log('ACF Field Groups:');
        $('.acf-field-group').each(function() {
            console.log('Field Group:', this);
            console.log('Key:', $(this).data('key'));
            console.log('Visible:', $(this).is(':visible'));
            console.log('Display Style:', $(this).css('display'));
            console.log('Parent Tab:', $(this).closest('.tab-pane').attr('id'));
        });
        
        // Log ACF fields
        console.log('ACF Fields:');
        $('.acf-field').each(function() {
            console.log('Field:', this);
            console.log('Key:', $(this).data('key'));
            console.log('Type:', $(this).data('type'));
            console.log('Visible:', $(this).is(':visible'));
        });
    }
    
    // Run on document ready
    logTabInfo();
    
    // Run when ACF is ready
    if (typeof acf !== 'undefined') {
        acf.addAction('ready', function() {
            console.log('ACF Ready Event Fired');
            logTabInfo();
        });
    }
    
    // Run when tabs are clicked
    $('#clientTab .nav-link').on('click', function() {
        console.log('Tab Clicked:', this);
        console.log('Tab Target:', $(this).data('bs-target') || $(this).attr('href'));
        
        // Log info after a short delay to allow tab to activate
        setTimeout(logTabInfo, 500);
    });
    
    // Log any errors with ACF
    if (typeof acf !== 'undefined') {
        const originalAddError = acf.validation.addError;
        acf.validation.addError = function(input, message) {
            console.log('ACF Validation Error:', input, message);
            return originalAddError.apply(this, arguments);
        };
    }
    
    // Create a server-side log entry
    function serverLog(message, data) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_debug_log',
                message: message,
                data: JSON.stringify(data)
            }
        });
    }
    
    // Log initial page state
    serverLog('Page loaded', {
        url: window.location.href,
        tabCount: $('#clientTab .nav-item').length,
        fieldGroupCount: $('.acf-field-group').length
    });
});