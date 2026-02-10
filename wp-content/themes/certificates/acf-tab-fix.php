<?php
/**
 * Force ACF to load field groups for all tabs
 */
function force_acf_load_all_field_groups() {
    global $certification_stages;
    
    if (!function_exists('acf_get_field_group')) {
        return;
    }
    
    // Check if we're on a client page
    $is_client_page = is_page('create-client') || (isset($_GET['stage']) && isset($_GET['new_post_id']));
    
    if (!$is_client_page) {
        return;
    }
    
    // Get post ID from URL or use a default
    $post_id = isset($_GET['new_post_id']) ? intval($_GET['new_post_id']) : 0;
    
    // If we're on create-client page but don't have a post ID, try to find one
    if (is_page('create-client') && !$post_id) {
        // Try to get post ID from form if available
        if (isset($_POST['post_id'])) {
            $post_id = intval($_POST['post_id']);
        }
    }
    
    // Get certification type
    $type = $post_id ? (get_field('certification_type', $post_id) ?: 'qms') : 'qms';
    $stages = isset($certification_stages[$type]) ? $certification_stages[$type] : [];
    
    // Get all field groups
    $all_field_groups = acf_get_field_groups();
    
    // Loop through all stages and preload their field groups
    foreach ($stages as $slug => $stage) {
        if (!empty($stage['group'])) {
            $field_group = acf_get_field_group($stage['group']);
            if ($field_group) {
                // Force ACF to load this field group
                $fields = acf_get_fields($field_group);
                
                // Force the field group to be active
                add_filter('acf/get_field_group', function($group) use ($field_group) {
                    if ($group['key'] === $field_group['key']) {
                        $group['active'] = true;
                    }
                    return $group;
                });
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
        return true;
    }
    return $matches;
}, 99, 2);