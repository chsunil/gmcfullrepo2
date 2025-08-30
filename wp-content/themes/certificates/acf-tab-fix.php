<?php
/**
 * Force ACF to load field groups for all tabs
 */
function force_acf_load_all_field_groups() {
    global $certification_stages;
    
    if (!function_exists('acf_get_field_group')) {
        acf_debug_log('ACF functions not available');
        return;
    }
    
    // Log the request parameters
    acf_debug_log('Request parameters', $_GET);
    
    // Check if we're on a client page
    $is_client_page = is_page('create-client') || (isset($_GET['stage']) && isset($_GET['new_post_id']));
    
    if (!$is_client_page) {
        acf_debug_log('Not on a client page, skipping field group loading');
        return;
    }
    
    // Get post ID from URL or use a default
    $post_id = isset($_GET['new_post_id']) ? intval($_GET['new_post_id']) : 0;
    
    // If we're on create-client page but don't have a post ID, try to find one
    if (is_page('create-client') && !$post_id) {
        acf_debug_log('On create-client page without post ID');
        
        // Try to get post ID from form if available
        if (isset($_POST['post_id'])) {
            $post_id = intval($_POST['post_id']);
            acf_debug_log('Found post ID in form submission', $post_id);
        }
    }
    
    // Get certification type
    $type = $post_id ? (get_field('certification_type', $post_id) ?: 'qms') : 'qms';
    $stages = isset($certification_stages[$type]) ? $certification_stages[$type] : [];
    
    acf_debug_log('Certification type', $type);
    acf_debug_log('Available stages', array_keys($stages));
    
    // Get all field groups
    $all_field_groups = acf_get_field_groups();
    acf_debug_log('All field groups', array_column($all_field_groups, 'title'));
    
    // Loop through all stages and preload their field groups
    foreach ($stages as $slug => $stage) {
        if (!empty($stage['group'])) {
            $field_group = acf_get_field_group($stage['group']);
            if ($field_group) {
                acf_debug_log("Loading field group for stage {$slug}", $field_group['title']);
                
                // Force ACF to load this field group
                $fields = acf_get_fields($field_group);
                acf_debug_log("Fields loaded for {$slug}", count($fields));
                
                // Force the field group to be active
                add_filter('acf/get_field_group', function($group) use ($field_group) {
                    if ($group['key'] === $field_group['key']) {
                        $group['active'] = true;
                    }
                    return $group;
                });
            } else {
                acf_debug_log("Field group not found for stage {$slug}", $stage['group']);
            }
        }
    }
    
    // Add a script to force field groups to be visible
    add_action('wp_footer', function() {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Force all field groups to be visible after page load
            $('.acf-field-group').css('display', 'block');
            $('.acf-fields').css('display', 'block');
            
            // Log field groups found
            console.log('ACF Field Groups found:', $('.acf-field-group').length);
            console.log('ACF Fields found:', $('.acf-field').length);
        });
        </script>
        <?php
    }, 99);
}

// Run on both admin and frontend
add_action('acf/input/admin_head', 'force_acf_load_all_field_groups');
add_action('wp_head', 'force_acf_load_all_field_groups');

// Add a filter to ensure field groups are active
add_filter('acf/get_field_groups', function($groups) {
    foreach ($groups as &$group) {
        $group['active'] = true;
    }
    return $groups;
});

// Add a filter to ensure location rules don't prevent field groups from showing
add_filter('acf/location/match_field_groups', function($matches, $args) {
    // If we're on the create-client page, force all field groups to match
    if (is_page('create-client')) {
        acf_debug_log('Forcing field groups to match on create-client page');
        return true;
    }
    return $matches;
}, 99, 2);