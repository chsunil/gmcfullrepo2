/**
 * This file contains fixes for ACF field groups in tabs
 */

jQuery(document).ready(function($) {
    // Function to initialize ACF fields in a tab
    function initializeAcfFieldsInTab(tabId) {
        const tabPane = document.getElementById(tabId);
        if (!tabPane) return;
        
        // Force ACF to initialize fields in this tab
        if (typeof acf !== 'undefined') {
            // Get all ACF fields in this tab
            const acfFields = tabPane.querySelectorAll('.acf-field');
            
            if (acfFields.length === 0) {
                // If no fields are found, add a placeholder message
                const acfForm = tabPane.querySelector('.acf-form');
                if (acfForm) {
                    const message = document.createElement('div');
                    message.className = 'alert alert-info mt-3';
                    message.textContent = 'Please complete the form fields for this section.';
                    acfForm.appendChild(message);
                }
            } else {
                // Trigger ACF to refresh fields
                acf.doAction('ready');
                
                // Make sure all field groups are visible
                const fieldGroups = tabPane.querySelectorAll('.acf-field-group');
                fieldGroups.forEach(group => {
                    group.style.display = 'block';
                });
            }
        }
    }
    
    // Initialize all tabs on page load
    $('.nav-tabs .nav-link').each(function() {
        const tabId = $(this).attr('data-tab') || 
                     $(this).attr('data-bs-target')?.substring(1) || 
                     $(this).attr('href')?.substring(1);
        
        if (tabId) {
            initializeAcfFieldsInTab(tabId);
        }
    });
    
    // Initialize tab when clicked
    $('.nav-tabs .nav-link').on('click', function() {
        const tabId = $(this).attr('data-tab') || 
                     $(this).attr('data-bs-target')?.substring(1) || 
                     $(this).attr('href')?.substring(1);
        
        if (tabId) {
            setTimeout(() => {
                initializeAcfFieldsInTab(tabId);
            }, 100);
        }
    });
    
    // Also listen for Bootstrap's tab events
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const tabId = $(e.target).attr('data-bs-target')?.substring(1) || 
                     $(e.target).attr('href')?.substring(1);
        
        if (tabId) {
            initializeAcfFieldsInTab(tabId);
        }
    });
});