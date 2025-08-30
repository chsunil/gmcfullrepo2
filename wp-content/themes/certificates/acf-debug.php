<?php
/**
 * ACF Debugging Functions
 * 
 * This file contains functions to help debug ACF field groups and fields.
 */

// Initialize the log file
function acf_debug_init() {
    $log_file = get_stylesheet_directory() . '/acf-debug.log';
    
    // Create or clear the log file
    file_put_contents($log_file, "ACF Debug Log - " . date('Y-m-d H:i:s') . "\n\n");
    
    return $log_file;
}

// Log a message to the ACF debug log
function acf_debug_log($message, $data = null) {
    $log_file = get_stylesheet_directory() . '/acf-debug.log';
    
    // Create log file if it doesn't exist
    if (!file_exists($log_file)) {
        acf_debug_init();
    }
    
    // Format the message
    $log_message = "[" . date('Y-m-d H:i:s') . "] " . $message;
    
    // Add data if provided
    if ($data !== null) {
        if (is_array($data) || is_object($data)) {
            $log_message .= "\n" . print_r($data, true);
        } else {
            $log_message .= " - " . $data;
        }
    }
    
    // Add newline
    $log_message .= "\n";
    
    // Write to log file
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Log all field groups on a page
function acf_debug_field_groups() {
    // Get all ACF field groups
    if (function_exists('acf_get_field_groups')) {
        $field_groups = acf_get_field_groups();
        acf_debug_log("Found " . count($field_groups) . " field groups");
        
        foreach ($field_groups as $field_group) {
            acf_debug_log("Field Group: " . $field_group['title'], [
                'key' => $field_group['key'],
                'location' => $field_group['location'],
                'active' => isset($field_group['active']) ? $field_group['active'] : 'unknown'
            ]);
            
            // Get fields in this group
            if (function_exists('acf_get_fields')) {
                $fields = acf_get_fields($field_group);
                acf_debug_log("Fields in group " . $field_group['title'] . ":", $fields);
            }
        }
    } else {
        acf_debug_log("ACF functions not available");
    }
}

// Hook to log field groups on page load
function acf_debug_on_page_load() {
    // Only run on specific pages
    if (is_page('create-client')) {
        acf_debug_log("=== Page Load: create-client ===");
        acf_debug_field_groups();
    }
}
add_action('wp', 'acf_debug_on_page_load');

// Hook to log tab rendering
function acf_debug_tabs() {
    // Add this to your JavaScript via wp_add_inline_script
    add_action('wp_footer', function() {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Log when tabs are initialized
            if (typeof acf !== 'undefined') {
                acf.addAction('ready', function() {
                    console.log('ACF Ready event fired');
                    
                    // Log all tab fields found
                    $('.acf-tab-wrap').each(function(i) {
                        console.log('Tab wrapper found:', i, this);
                        console.log('Tab content:', $(this).html());
                    });
                    
                    // Log all field groups found
                    $('.acf-field-group').each(function(i) {
                        console.log('Field group found:', i, this);
                        console.log('Field group key:', $(this).data('key'));
                    });
                });
            }
        });
        </script>
        <?php
    });
}
add_action('wp', 'acf_debug_tabs');

// Log ACF form rendering
function acf_debug_form_render($args) {
    acf_debug_log("ACF Form Render", $args);
    return $args;
}
add_filter('acf/pre_render_fields', 'acf_debug_form_render', 10, 2);

// AJAX handler for JavaScript logging
function acf_debug_ajax_log() {
    if (!isset($_POST['message'])) {
        wp_send_json_error('No message provided');
    }
    
    $message = sanitize_text_field($_POST['message']);
    $data = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : null;
    
    acf_debug_log($message, $data);
    
    wp_send_json_success('Logged successfully');
}
add_action('wp_ajax_acf_debug_log', 'acf_debug_ajax_log');
add_action('wp_ajax_nopriv_acf_debug_log', 'acf_debug_ajax_log');