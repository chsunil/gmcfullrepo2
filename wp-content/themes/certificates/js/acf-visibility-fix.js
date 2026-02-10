/**
 * ACF Field Group Visibility Fix
 * 
 * This script ensures that ACF field groups are always visible,
 * even when they don't contain any data yet.
 */
(function() {
    // Function to fix ACF field group visibility
    function fixAcfFieldGroupVisibility() {
        // Force all ACF field groups to be visible
        const acfFieldGroups = document.querySelectorAll('.acf-field-group, .acf-fields');
        acfFieldGroups.forEach(group => {
            if (group) {
                group.style.display = 'block';
            }
        });

        // Add placeholder content to empty ACF field containers
        const emptyContainers = document.querySelectorAll('.acf-fields:empty');
        emptyContainers.forEach(container => {
            if (!container.childNodes.length) {
                const placeholder = document.createElement('div');
                placeholder.className = 'acf-placeholder';
                placeholder.innerHTML = '<p>Form fields will appear here. If you don\'t see any fields, please check your field group configuration.</p>';
                container.appendChild(placeholder);
            }
        });
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
        fixAcfFieldGroupVisibility();
        
        // Also run when ACF is ready
        if (typeof acf !== 'undefined') {
            acf.addAction('ready', fixAcfFieldGroupVisibility);
            
            // Run again after a short delay to catch any late-loading fields
            setTimeout(fixAcfFieldGroupVisibility, 500);
        }
    });

    // Monitor tab changes
    document.addEventListener('click', function(e) {
        if (e.target && e.target.getAttribute('data-bs-toggle') === 'tab') {
            // Wait for tab to be shown
            setTimeout(fixAcfFieldGroupVisibility, 100);
        }
    });
})();