<?php


/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define('CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0');
add_theme_support('page-attributes');

/**
 * Ensure ACF front-end forms work even inside a shortcode.
 */
// add_action('wp', function() {
//     // Only on pages that contain our shortcode
//     global $post;
//    $shortcodes = [ 'client_tables' ];

//     foreach ( $shortcodes as $sc ) {
//         if ( has_shortcode( $post->post_content, $sc ) ) {
//             acf_form_head();
//             break;
//         }
//     }
// });

/**
 * Enqueue styles
 */
function child_enqueue_styles() {
    wp_enqueue_style('astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all');
    
    // Add ACF fixes CSS
    wp_enqueue_style('acf-fixes-css', get_stylesheet_directory_uri() . '/css/acf-fixes.css', array(), '1.0.0', 'all');
}

add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);



require_once get_stylesheet_directory() . '/certification-stages.php';

// Include shortcodes
require_once get_stylesheet_directory() . '/shortcodes.php';

// Include ACF tab fix
require_once get_stylesheet_directory() . '/acf-tab-fix.php';

// Include ACF debugging - DISABLED to prevent 1GB log file growth
// require_once get_stylesheet_directory() . '/acf-debug.php';

// includers reposrts.php for dashboard stats
require_once get_stylesheet_directory() . '/includes/reports.php';

// Include custom login redirects
// require_once get_stylesheet_directory() . '/login-redirects.php';

// Include GMC Invoice CPT
require_once get_stylesheet_directory() . '/includes/class-gmc-invoice.php';


// After ACF saves any front‐end form:
add_action('acf/save_post', function($post_id){
  // Only for our Client CPT on front-end
  if ( get_post_type($post_id) !== 'client' ) return;

  // Did Next come through?
  if ( isset($_POST['acf_next_stage']) ) {
    $next = sanitize_text_field($_POST['acf_next_stage']);
    update_post_meta($post_id, 'client_stage', $next);
  }
   $org_name = get_field( 'organization_name', $post_id );
   if ($org_name){
     wp_update_post([
        'ID'         => $post_id,
        'post_title' => $org_name,
        'post_name'  => sanitize_title( $org_name ),
    ]);
   }
}, 20 );



function filter_submit_button_attributes( $attributes, $form, $args ) {
    $attributes['class'] .= ' mt-3';
    
    return $attributes;
}
add_filter( 'af/form/button_attributes/key=FORM_KEY', 'filter_submit_button_attributes', 10, 3 );



function restrict_clients_by_assigned_employee($query) {
    global $pagenow, $typenow;

    if ($typenow !== 'client' || $pagenow !== 'edit.php' || current_user_can('administrator') || current_user_can('manager')) {
        return; // Allow admins to see all clients
    }

    $current_user_id = get_current_user_id();

    $query->set('meta_query', array(
        array(
            'key' => 'assigned_employee', // ACF field for assigned employee
            'value' => $current_user_id,
            'compare' => '='
        )
    ));
}
add_action('pre_get_posts', 'restrict_clients_by_assigned_employee');



/**
 * Custom Walker class for navigation menus with icons
 * Note: Only use this if you need custom menu formatting
 */
class Astra_Custom_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= '<ul class="submenu">';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</ul>';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = !empty($item->classes) ? $item->classes : [];
        $icon_class = '';
        $has_children = in_array('menu-item-has-children', $classes);

        // Find first Font Awesome class in the classes
        foreach ($classes as $class) {
            if (strpos($class, 'fa-') !== false || strpos($class, 'fas') !== false || strpos($class, 'far') !== false || strpos($class, 'fab') !== false) {
                $icon_class = $class;
                break;
            }
        }

        $output .= '<li>';
        $output .= '<a href="' . esc_url($item->url) . '" class="' . ($has_children ? 'dropdown-toggle' : '') . '">';
        if ($icon_class) {
            $output .= '<i class="menu-icon ' . esc_attr(implode(' ', $classes)) . '"></i>';
        }
        $output .= '<span class="menu-label">' . esc_html($item->title) . '</span>';
        $output .= '</a>';
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

function register_custom_sidebar() {
    register_sidebar([
        'name'          => 'Custom Sidebar',
        'id'            => 'custom_sidebar',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'register_custom_sidebar');

// Enques scripts and styles 

/**
 * Enqueue local Chart.js for Dashboard template.
 */
// Scripts and styles are now handled by enqueue_theme_assets function

/**
 * Enqueue all scripts and styles needed for the theme
 */
function enqueue_theme_assets() {
    // Ensure jQuery is loaded
    wp_enqueue_script('jquery');
    
    // Google Fonts - Public Sans (Sneat default font)
    wp_enqueue_style(
        'google-fonts-public-sans',
        'https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap',
        array(),
        null
    );
    
    // Sneat Template Core CSS
    wp_enqueue_style(
        'sneat-core-css',
        get_stylesheet_directory_uri() . '/sneat-assets/vendor/css/core.css',
        array('google-fonts-public-sans'),
        '1.0.0'
    );
    
    wp_enqueue_style(
        'sneat-demo-css',
        get_stylesheet_directory_uri() . '/sneat-assets/css/demo.css',
        array('sneat-core-css'),
        '1.0.0'
    );
    
    // Sneat Vendors CSS
    wp_enqueue_style(
        'sneat-perfect-scrollbar-css',
        get_stylesheet_directory_uri() . '/sneat-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css',
        array(),
        '1.0.0'
    );
    
    // Sneat Icons
    wp_enqueue_style(
        'sneat-boxicons-css',
        'https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css',
        array(),
        '1.0.0'
    );
    
    // Sneat Core JS
    wp_enqueue_script(
        'sneat-helpers-js',
        get_stylesheet_directory_uri() . '/sneat-assets/vendor/js/helpers.js',
        array(),
        '1.0.0',
        true
    );
    
    wp_enqueue_script(
        'sneat-config-js',
        get_stylesheet_directory_uri() . '/sneat-assets/js/config.js',
        array('sneat-helpers-js'),
        '1.0.0',
        true
    );
    
    // Sneat Vendor JS
    wp_enqueue_script(
        'sneat-popper-js',
        get_stylesheet_directory_uri() . '/sneat-assets/vendor/libs/popper/popper.js',
        array('jquery'),
        '1.0.0',
        true
    );
    
    wp_enqueue_script(
        'sneat-bootstrap-js',
        get_stylesheet_directory_uri() . '/sneat-assets/vendor/js/bootstrap.js',
        array('sneat-popper-js'),
        '1.0.0',
        true
    );
    
    wp_enqueue_script(
        'sneat-perfect-scrollbar-js',
        get_stylesheet_directory_uri() . '/sneat-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
        array('jquery'),
        '1.0.0',
        true
    );
    
    // Sneat Main JS
    wp_enqueue_script(
        'sneat-menu-js',
        get_stylesheet_directory_uri() . '/sneat-assets/vendor/js/menu.js',
        array('jquery'),
        '1.0.0',
        true
    );
    
    wp_enqueue_script(
        'sneat-main-js',
        get_stylesheet_directory_uri() . '/sneat-assets/js/main.js',
        array('jquery', 'sneat-menu-js'),
        '1.0.0',
        true
    );
    
    // Custom styles
    wp_enqueue_style('acf-fixes-css', get_stylesheet_directory_uri() . '/css/acf-fixes.css');
    
    // Client Form CSS - Only on client form template
    if (is_page_template('template-client-form.php')) {
        wp_enqueue_style(
            'client-form-css',
            get_stylesheet_directory_uri() . '/css/client-form.css',
            array('sneat-core-css'),
            '1.0.0'
        );
        
        wp_enqueue_style(
            'client-form-buttons-css',
            get_stylesheet_directory_uri() . '/css/client-form-buttons.css',
            array('client-form-css'),
            '1.0.0'
        );
    }
    
    // Custom scripts
    wp_enqueue_script(
        'custom-script',
        get_stylesheet_directory_uri() . '/js/script.js',
        array('jquery'),
        '1.0.0',
        true
    );
    
    // Tabs fix script - Essential for ACF tabs functionality
    wp_enqueue_script(
        'tabs-fix',
        get_stylesheet_directory_uri() . '/js/tabs-fix.js',
        array('jquery', 'jquery-ui-tabs', 'acf-input'),
        '1.0.0',
        true
    );
    
    // ACF Tabs Debug script - Only on create-client page
    if (is_page('create-client')) {
        wp_enqueue_script(
            'acf-tabs-debug',
            get_stylesheet_directory_uri() . '/js/acf-tabs-debug.js',
            array('jquery', 'acf-input', 'tabs-fix'),
            '1.0.0',
            true
        );
    }
    
    // Client Form script - Only on client form template
    if (is_page_template('template-client-form.php')) {
        wp_enqueue_script(
            'client-form',
            get_stylesheet_directory_uri() . '/js/client-form.js',
            array('jquery', 'acf-input', 'sneat-bootstrap-js'),
            '1.0.0',
            true
        );
        
        // Pass data to the script
        wp_localize_script('client-form', 'clientFormData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'stageNonce' => wp_create_nonce('update_client_stage_nonce'),
            'certTypeNonce' => wp_create_nonce('update_certification_type_nonce'),
        ]);
    }
    
    // Email functionality - Uncomment if needed
    if (is_page()) {
        wp_enqueue_script(
            'toast-helper',
            get_stylesheet_directory_uri() . '/js/toast-helper.js',
            array('jquery', 'sneat-bootstrap-js'), // Depend on bootstrap
            '1.0.0',
            true
        );

        wp_enqueue_script(
            'send-email-js',
            get_stylesheet_directory_uri() . '/js/send-email.js',
            array('jquery', 'bootstrap-js'),
            '1.0.0',
            true
        );
        
        wp_localize_script('send-email-js', 'wp_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'get_client_email_nonce' => wp_create_nonce('get_client_email_nonce'),
            'send_client_email_nonce' => wp_create_nonce('send_client_email_nonce'),
        ]);
    }
    
    // Chart.js - Only load on dashboard page
    if (is_page_template('page-dashboard.php')) {
        wp_enqueue_script(
            'chartjs-local',
            get_stylesheet_directory_uri() . '/js/chart.js',
            array('jquery'),
            '4.3.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_theme_assets');



function restrict_wp_admin_access() {
    // Check if the user is logged in
    if (is_user_logged_in()) {
        $user = wp_get_current_user(); // Get the current user object

        // Check if the user has the "Administrator" role
        if (!in_array('administrator', (array) $user->roles)) {
            wp_redirect(home_url()); // Redirect non-admin users to the homepage or any other page
            exit;
        }
    } else {
        // If not logged in, redirect to the login page
        wp_redirect(wp_login_url());
        exit;
    }
}

// Hook into admin_init to apply restrictions
add_action('admin_init', 'restrict_wp_admin_access');
?>
<?php

function custom_redirect_if_not_logged_in() {
    // Get the current URL dynamically
    $current_url = home_url(add_query_arg(null, null));

    // Define the dynamic login and signup URLs
    $login_url = wp_login_url(); // This automatically adjusts to your site's login URL
    $signup_url = site_url('/signup'); // Dynamically creates a signup URL, adjust the "/signup" path as needed

    // Check if the user is logged in and not on the login page
    if (!is_user_logged_in() && $current_url != $login_url) {
        wp_redirect($login_url, 302); // Redirect to the dynamically generated signup URL
        exit;
    }
}

// Hook the function into the WordPress initialization process
add_action('template_redirect', 'custom_redirect_if_not_logged_in');
?>
<?php
function get_assigned_employee_name_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id' => '', // Accept post ID from attribute
    ), $atts);

    if (empty($atts['post_id'])) {
        return 'No post ID provided.';
    }

    $assigned_employee_data = get_field('assigned_employee', $atts['post_id']);

    if ($assigned_employee_data) {
        $assigned_employee = maybe_unserialize($assigned_employee_data); // Ensure it's an array
        
        if (is_array($assigned_employee) && !empty($assigned_employee)) {
            $employee_names = [];

            foreach ($assigned_employee as $user_id) {
                $user = get_user_by('id', $user_id);
                if ($user) {
                    $employee_names[] = esc_html($user->display_name);
                }
            }

            return !empty($employee_names) ? implode(', ', $employee_names) : 'No employees found.';
        }
    }

    return 'No employees assigned.';
}
add_shortcode('assigned_employee_name', 'get_assigned_employee_name_shortcode');

?>
<?php
// Make 'ajaxurl' available to JS
add_action('wp_head', function () {
?>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
<?php
});


add_filter('acf/load_value/name=technical_review_items', 'prefill_f02_technical_review_rows', 10, 3);
function prefill_f02_technical_review_rows($value, $post_id, $field) {
    if (!empty($value)) return $value;

    $rows = [
        'If the information about the applicant organization and its management system in application and supporting documents is sufficient for the conduct of the audit and developing the audit programme?',
        'If the requirements for certification are clearly defined and documented, and have been provided to the applicant organization?',
        'If any known difference in understanding between GMCSPL and the applicant organization is resolved?',
        'If GMCSPL has the required personnel competent in the technical area and in the geographical area?',
        'If the certification scheme and scope applied by the organization falls under the accreditation granted to GMCSPL?',
        'Checked Location(s) of the applicant organization’s operations, number of sites etc.?',
        'If interpreters are required?',
        'If there are any PPE requirements for Visitors?',
        'Any threats to impartiality?',
    ];

    return array_map(function ($requirement) {
        return [
            'field_f02_requirement' => $requirement, // static
            'field_f02_review'      => '',           // user will fill
            'field_f02_conclusion'  => '',           // dropdown
        ];
    }, $rows);
}
add_filter('acf/load_value/name=technical_review_items_copy', 'prefill_f02_technical_review_rowsf14', 10, 3);
function prefill_f02_technical_review_rowsf14($value, $post_id, $field) {
    if (!empty($value)) return $value;

    $rows = [
        'If the information about the applicant organization and its management system in application and supporting documents is sufficient for the conduct of the audit and developing the audit programme?',
        'If the requirements for certification are clearly defined and documented, and have been provided to the applicant organization?',
        'If any known difference in understanding between GMCSPL and the applicant organization is resolved?',
        'If GMCSPL has the required personnel competent in the technical area and in the geographical area?',
        'If the certification scheme and scope applied by the organization falls under the accreditation granted to GMCSPL?',
        'Checked Location(s) of the applicant organization’s operations, number of sites etc.?',
        'If interpreters are required?',
        'If there are any PPE requirements for Visitors?',
        'Any threats to impartiality?',
    ];

    return array_map(function ($requirement) {
        return [
            'field_f02_requirement' => $requirement, // static
            'field_f02_review'      => '',           // user will fill
            'field_f02_conclusion'  => '',           // dropdown
        ];
    }, $rows);
}
add_action('acf/input/admin_footer', 'lock_review_fields');
function lock_review_fields() {
?>
    <script>
        (function($) {
            acf.addAction('ready', function() {
                $('textarea[name*="[requirement]"], textarea[name*="[review]"]').attr('readonly', true).css('background-color', '#f9f9f9');
            });
        })(jQuery);
    </script>
<?php
}
add_filter('acf/load_field/name=client_stage', 'load_dynamic_client_stage_choices');
function load_dynamic_client_stage_choices($field) {
    global $certification_stages;

    // You can make this dynamic later
    $certification_type = 'ems';

    if (isset($certification_stages[$certification_type])) {
        $field['choices'] = $certification_stages[$certification_type];
    }

    return $field;
}

// add_action('acf/save_post', 'client_stage_save_redirect', 20);
function client_stage_save_redirect($post_id) {
    if (get_post_type($post_id) !== 'client') return;
    if (defined('DOING_AJAX') && DOING_AJAX) return;

    $stage = get_field('client_stage', $post_id);
    if ($stage) {
        wp_redirect(add_query_arg('stage', $stage, get_permalink($post_id)));
        exit;
    }
}

// AJAX: Update ACF 'client_stage' field from Next button
add_action('wp_ajax_update_client_stage', 'ajax_update_client_stage');
function ajax_update_client_stage() {
    // Verify permissions
    if (!current_user_can('edit_posts') || !isset($_POST['post_id'], $_POST['next_stage'])) {
        wp_send_json_error(['message' => 'Missing permissions or parameters']);
    }

    // Validate the data
    $post_id = intval($_POST['post_id']);
    $next_stage = sanitize_text_field($_POST['next_stage']);

    if (!$post_id || !$next_stage) {
        wp_send_json_error(['message' => 'Invalid data']);
    }

    // Update the client_stage field
    update_field('client_stage', $next_stage, $post_id);
    wp_send_json_success(['message' => 'Client stage updated', 'stage' => $next_stage]);
}

// Create a new post when first creating a client
add_action('wp_ajax_create_new_client_post', 'create_new_client_post');
// AJAX function to create a new client post// Handle the creation of a new client post
function create_new_client_post() {
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'create_post_nonce')) {
        wp_send_json_error(['message' => 'Nonce verification failed']);
    }

    // Create a new 'client' post
    $post_id = wp_insert_post(array(
        'post_title'   => 'New Client', // Placeholder title, you can customize it
        'post_type'    => 'client',
        'post_status'  => 'draft', // Default status is draft
    ));

    if ($post_id) {
        // Return the post ID
        wp_send_json_success(['post_id' => $post_id]);
    } else {
        wp_send_json_error(['message' => 'Failed to create post']);
    }
}
add_action('wp_ajax_create_new_client_post', 'create_new_client_post');

// delete user

add_action('wp_ajax_delete_auditor', 'delete_auditor_callback');
add_action('wp_ajax_nopriv_delete_auditor', 'delete_auditor_callback');

function delete_auditor_callback() {
    if (!isset($_POST['user_id']) || !current_user_can('delete_users')) {
        wp_send_json_error('Unauthorized action');
        return;
    }

    $user_id = intval($_POST['user_id']);
    require_once(ABSPATH . 'wp-admin/includes/user.php');

    if (wp_delete_user($user_id)) {
        wp_send_json_success('User deleted successfully');
    } else {
        wp_send_json_error('Failed to delete user');
    }
}


// Fetch client email and PDF data
// Fetch client email and PDF URL based on Post ID
function get_client_email_data() {
    if (!isset($_POST['post_id']) || !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_client_email_nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
    }

    $post_id = intval($_POST['post_id']);
    $contact_email = get_field('top_management_contact_person_contact_email', $post_id);
    $pdf_url = get_field('f03_pdf', $post_id);
    $pdf_filename = basename($pdf_url);
    $client_name = get_the_title($post_id);  // Assuming client name is the post title

    if (!$contact_email || !$pdf_url) {
        wp_send_json_error(['message' => 'Email or PDF URL not found.']);
    }

    wp_send_json_success([
        'contact_email' => $contact_email,
        'pdf_url' => $pdf_url,
        'pdf_filename' => $pdf_filename,
        'client_name' => $client_name
    ]);
}

add_action('wp_ajax_get_client_email', 'get_client_email_data');
add_action('wp_ajax_nopriv_get_client_email', 'get_client_email_data');

// Send email functionality
add_action('wp_ajax_send_pdf_email','send_pdf_email');
function send_pdf_email() {

    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'send_pdf_email_nonce') ) {
        wp_send_json_error(['message' => 'Invalid nonce']);
    }

    $to      = sanitize_email($_POST['email'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? 'Certificate PDF');
    $message = wp_kses_post($_POST['message'] ?? '');
    $pdf_url = esc_url_raw($_POST['pdf_url'] ?? '');

    if ( empty($to) || empty($pdf_url) ) {
        wp_send_json_error(['message' => 'Missing required fields']);
    }

    /**
     * Resolve PDF file path safely
     */
    $filepath = null;

    // Case 1: PDF inside uploads
    $upload = wp_get_upload_dir();
    if ( str_contains($pdf_url, $upload['baseurl']) ) {
        $filepath = str_replace($upload['baseurl'], $upload['basedir'], $pdf_url);
    } else {
        // Case 2: PDF inside project (theme / custom folders)
        $filepath = realpath( ABSPATH . ltrim( parse_url($pdf_url, PHP_URL_PATH), '/' ) );
    }

    if ( ! $filepath || ! file_exists($filepath) ) {
        error_log('PDF not found: ' . $pdf_url);
        wp_send_json_error(['message' => 'PDF file not found on server']);
    }

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: GMC Certificates <no-reply@yourdomain.com>',
    ];

    $sent = wp_mail($to, $subject, nl2br($message), $headers, [$filepath]);

    if ( ! $sent ) {
        error_log(print_r([
            'to' => $to,
            'subject' => $subject,
            'attachment' => $filepath,
            'error' => error_get_last(),
        ], true));

        wp_send_json_error(['message' => 'Email failed to send']);
    }

    wp_send_json_success(['message' => 'Email sent successfully']);
}

add_action('wp_ajax_nopriv_send_pdf_email', 'send_pdf_email');
// Disable ACF clone field to edit
add_filter('acf/prepare_field/type=clone', function($field) {
    $field['disabled'] = true;
    return $field;
});

add_filter('acf/load_field/key=field_6868f0469f73f', function( $field ){
    // only on the front end
    if( ! is_admin() ) {
        // clear the default pipe-separated rows
        $field['rows'] = [];
    }
    return $field;
});

add_filter('acf/fields/date_picker/input', 'my_acf_date_picker_input');
function my_acf_date_picker_input($input) {
    $input['type'] = 'date';
    return $input;
}

/**
 * Helper: Get certification stages safely
 */
function gmc_get_certification_stages( $type = 'qms' ) {
    $all = get_certification_stages();
    return $all[$type] ?? [];
}

// --- Moved from template-client-form.php ---

add_action('wp_ajax_update_certification_type', function() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'update_certification_type_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Get parameters
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    
    if (!$post_id || !$type || !in_array($type, ['qms', 'ems'])) {
        wp_send_json_error('Invalid parameters');
    }
    
    // Update certification type
    update_field('certification_type', $type, $post_id);
    
    // Reset stage to draft when changing certification type
    update_field('client_stage', 'draft', $post_id);
    
    wp_send_json_success(['message' => 'Certification type updated', 'type' => $type]);
});

// Get email template data
add_action('wp_ajax_get_email_template', function() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_email_template_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Get parameters
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $stage = isset($_POST['stage']) ? sanitize_text_field($_POST['stage']) : '';
    
    error_log('Email Template Debug - Received parameters:');
    error_log('Post ID: ' . $post_id);
    error_log('Stage: ' . $stage);
    
    if (!$post_id || !$stage) {
        error_log('Email Template Error - Missing required parameters');
        wp_send_json_error('Missing parameters');
    }
    
    // Get client data
    $client = get_post($post_id);
    if (!$client) {
        error_log('Email Template Error - Client not found for post_id: ' . $post_id);
        wp_send_json_error('Client not found');
    }
    
    $client_name = $client->post_title;
    $certification_type = get_field('certification_type', $post_id);
    
    error_log('Email Template Debug - Client data:');
    error_log('Client Name: ' . $client_name);
    error_log('Certification Type: ' . $certification_type);
    
    // Ensure we have a certification type
    if (!$certification_type) {
        $certification_type = 'qms';
        error_log('Email Template Debug - No certification type found, defaulting to: ' . $certification_type);
    }
    
    // Debug logging
    error_log('Email Template Debug - Post ID: ' . $post_id);
    error_log('Email Template Debug - Stage: ' . $stage);
    error_log('Email Template Debug - Certification Type: ' . $certification_type);
    
    // Get email templates (with safety checks)
    $certification_emails = get_certification_emails();
    if (!$certification_emails) {
        error_log('Email Template Error - No certification emails found');
        wp_send_json_error('Configuration error: No email templates found');
    }

    if (!isset($certification_emails[$certification_type])) {
        error_log('Email Template Error - No templates found for certification type: ' . $certification_type);
        wp_send_json_error('No email templates found for this certification type');
    }

    $emails = $certification_emails[$certification_type];

    // Allow case-insensitive stage matching
    $found_stage = null;
    if (isset($emails[$stage])) {
        $found_stage = $stage;
    } else {
        foreach ($emails as $k => $v) {
            if (strtolower($k) === strtolower($stage)) {
                $found_stage = $k;
                break;
            }
        }
    }

    if (!$found_stage) {
        error_log('Email Template Error - Template not found for stage: ' . $stage . ' in type: ' . $certification_type);
        error_log('Available stages: ' . print_r(array_keys($emails), true));
        wp_send_json_error('Email template not found for stage: ' . $stage);
    }

    $email_template = $emails[$found_stage];

    // Get PDF URL if applicable
    $pdf_url = '';
    $pdf_name = '';
    if (!empty($email_template['pdf_field'])) {
        $pdf_field = $email_template['pdf_field'];
        $pdf_url = get_field($pdf_field, $post_id);
        if ($pdf_url) {
            $pdf_name = basename($pdf_url);
        }
    }

    // Get client contact email - try several fallbacks
    $to_email = '';
    $email_fields = [
        'contact_person_contact_email_new',
        'top_management_contact_person_contact_email',
        'company_email',
        'contact_email',
        'email',
        'primary_email',
    ];

    foreach ($email_fields as $ef) {
        $val = get_field($ef, $post_id);
        if (empty($val)) {
            // Also check post meta
            $val = get_post_meta($post_id, $ef, true);
        }
        if (!empty($val)) {
            if (is_array($val)) {
                // ACF may return array with 'email' key
                if (isset($val['email'])) {
                    $val = $val['email'];
                } else {
                    // Try first scalar value
                    $val = reset($val);
                }
            }
            $candidate = sanitize_email($val);
            if (!empty($candidate)) {
                $to_email = $candidate;
                break;
            }
        }
    }

    // Fallback to post author's email
    if (empty($to_email) && !empty($client->post_author)) {
        $user = get_user_by('id', $client->post_author);
        if ($user && !empty($user->user_email)) {
            $to_email = sanitize_email($user->user_email);
        }
    }
    
    // Replace placeholders in subject and message
    $subject = $email_template['subject'];
    $message = $email_template['message'];
    
    // Replace placeholders
    $replacements = [
        '{{client_name}}' => $client_name,
        '{{pdf_link}}' => $pdf_url,
        '{{pdf_name}}' => $pdf_name,
    ];
    
    foreach ($replacements as $placeholder => $value) {
        $subject = str_replace($placeholder, $value, $subject);
        $message = str_replace($placeholder, $value, $message);
    }
    
    wp_send_json_success([
        'to_email' => $to_email,
        'subject' => $subject,
        'message' => $message,
        'pdf_url' => $pdf_url,
        'pdf_name' => $pdf_name,
    ]);
});

// Send client email
add_action('wp_ajax_send_client_email', function() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'send_client_email_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Get parameters
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $stage = isset($_POST['stage']) ? sanitize_text_field($_POST['stage']) : '';
    $to = isset($_POST['to']) ? sanitize_email($_POST['to']) : '';
    $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
    $message = isset($_POST['message']) ? wp_kses_post($_POST['message']) : '';
    
    if (!$post_id || !$stage || !$to || !$subject || !$message) {
        wp_send_json_error('Missing parameters');
    }
    
    // Get certification type
    $certification_type = get_field('certification_type', $post_id) ?: 'qms';
    
    // Get email templates
    $certification_emails = get_certification_emails();
    $emails = isset($certification_emails[$certification_type]) ? $certification_emails[$certification_type] : [];
    
    if (!isset($emails[$stage])) {
        wp_send_json_error('Email template not found for this stage');
    }
    
    $email_template = $emails[$stage];
    
    // Get PDF attachment if applicable — robustly try multiple possible locations
    $attachments = [];
    $missing_attachment_warning = '';
    if (!empty($email_template['pdf_field'])) {
        $pdf_url = get_field($email_template['pdf_field'], $post_id);
        if ($pdf_url) {
            $upload_dir = wp_upload_dir();

            // Primary conversion: baseurl -> basedir
            $pdf_path_primary = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $pdf_url);
            $pdf_path_primary = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $pdf_path_primary);

            $tried_paths = [$pdf_path_primary];

            // If not found, try swapping common folder name variants
            if (!file_exists($pdf_path_primary)) {
                $tried_paths[] = str_replace('client_pdfs', 'client-pdfs', $pdf_path_primary);
                $tried_paths[] = str_replace('client-pdfs', 'client_pdfs', $pdf_path_primary);

                // Try uploads paths that include or omit the post_id folder
                $filename = basename($pdf_path_primary);
                $tried_paths[] = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'client_pdfs' . DIRECTORY_SEPARATOR . $post_id . DIRECTORY_SEPARATOR . $filename;
                $tried_paths[] = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'client-pdfs' . DIRECTORY_SEPARATOR . $post_id . DIRECTORY_SEPARATOR . $filename;
                $tried_paths[] = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $filename;
            }

            // Final attempt: check the tried paths and pick the first existing file
            foreach ($tried_paths as $p) {
                if (file_exists($p)) {
                    $attachments[] = $p;
                    break;
                }
            }

            if (empty($attachments)) {
                $missing_attachment_warning = 'Attachment not found. Tried: ' . implode('; ', $tried_paths);
                error_log('Send Client Email: ' . $missing_attachment_warning . ' (post_id=' . $post_id . ', stage=' . $stage . ')');
            }
        } else {
            $missing_attachment_warning = 'PDF URL is empty for field ' . $email_template['pdf_field'];
            error_log('Send Client Email: ' . $missing_attachment_warning . ' (post_id=' . $post_id . ', stage=' . $stage . ')');
        }
    }
    
    // Set email headers
    $admin_email = get_option('admin_email');
    $from_name = get_bloginfo('name');
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $from_name . ' <' . $admin_email . '>'
    ];
    
    // Debug log
    error_log('--------------------------------------------------');
    error_log('🚀 Attempting wp_mail:');
    error_log('To: ' . $to);
    error_log('Subject: ' . $subject);
    error_log('Headers: ' . print_r($headers, true));
    error_log('Attachments: ' . print_r($attachments, true));

    // Send email
    $sent = wp_mail($to, $subject, $message, $headers, $attachments);
    
    error_log('📌 wp_mail result: ' . ($sent ? 'TRUE (Success)' : 'FALSE (Failed)'));
    global $ts_mail_errors;
    global $phpmailer;
    if (!$sent && isset($ts_mail_errors)) {
        error_log('Mail Errors: ' . print_r($ts_mail_errors, true));
    }
    if (!$sent && isset($phpmailer)) {
        error_log('PHPMailer Error: ' . $phpmailer->ErrorInfo);
    }
    error_log('--------------------------------------------------');
    
    if ($sent) {
        // Log email sent
        update_post_meta($post_id, '_email_sent_' . $stage, current_time('mysql'));

        $response = ['message' => 'Email sent successfully'];
        if (!empty($missing_attachment_warning)) {
            $response['warning'] = $missing_attachment_warning;
        }

        wp_send_json_success($response);
    } else {
        $debug = 'Failed to send email';
        if (!empty($missing_attachment_warning)) {
            $debug .= ' — ' . $missing_attachment_warning;
        }
        error_log('Send Client Email Error: ' . $debug . ' (to=' . $to . ', post_id=' . $post_id . ', stage=' . $stage . ')');
        wp_send_json_error($debug);
    }
});

// ── Invoice: Save / Update ────────────────────────────────────────────────────
add_action('wp_ajax_gmc_save_invoice', function() {
    if (!wp_verify_nonce($_POST['gmc_nonce'] ?? '', 'gmc_save_invoice_nonce')) {
        wp_send_json_error('Security check failed.');
    }
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in.');
    }

    $invoice_id  = intval($_POST['invoice_id'] ?? 0);
    $invoice_no  = sanitize_text_field($_POST['invoice_no'] ?? '');
    $invoice_date= sanitize_text_field($_POST['invoice_date'] ?? '');
    $client_id   = intval($_POST['client_id'] ?? 0);
    $gst_type    = sanitize_text_field($_POST['gst_type'] ?? 'cgst_sgst');
    $cgst_p      = floatval($_POST['cgst_percent'] ?? 9);
    $sgst_p      = floatval($_POST['sgst_percent'] ?? 9);
    $igst_p      = floatval($_POST['igst_percent'] ?? 18);
    $subtotal    = floatval($_POST['subtotal'] ?? 0);
    $total_amt   = floatval($_POST['total_amount'] ?? 0);
    $cgst_amt    = floatval($_POST['cgst_amount'] ?? 0);
    $sgst_amt    = floatval($_POST['sgst_amount'] ?? 0);
    $igst_amt    = floatval($_POST['igst_amount'] ?? 0);
    $amt_words   = sanitize_text_field($_POST['amount_in_words'] ?? '');
    $status      = sanitize_text_field($_POST['status'] ?? 'Unpaid');
    $team_member = sanitize_text_field($_POST['team_member'] ?? '');
    $gst_regn_no = sanitize_text_field($_POST['gst_regn_no'] ?? '');

    // Line items
    $raw_items  = $_POST['line_items'] ?? [];
    $line_items = [];
    foreach ($raw_items as $item) {
        $desc = sanitize_text_field($item['description'] ?? '');
        $amt  = floatval($item['amount'] ?? 0);
        if ($desc || $amt) {
            $line_items[] = ['description' => $desc, 'amount' => $amt];
        }
    }

    // Create or Update Post
    $post_title = $invoice_no ?: ('Invoice - ' . date('Y-m-d'));
    if ($invoice_id > 0) {
        $result = wp_update_post(['ID' => $invoice_id, 'post_title' => $post_title], true);
    } else {
        $result = wp_insert_post([
            'post_type'   => 'gmc_invoice',
            'post_status' => 'publish',
            'post_title'  => $post_title,
        ], true);
    }

    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    $post_id = $invoice_id > 0 ? $invoice_id : $result;

    // Save ACF fields
    update_field('invoice_no',     $invoice_no,  $post_id);
    update_field('invoice_date',   $invoice_date,$post_id);
    update_field('client_id',      $client_id,   $post_id);
    update_field('gst_type',       $gst_type,    $post_id);
    update_field('cgst_percent',   $cgst_p,      $post_id);
    update_field('sgst_percent',   $sgst_p,      $post_id);
    update_field('igst_percent',   $igst_p,      $post_id);
    update_field('subtotal',       $subtotal,    $post_id);
    update_field('cgst_amount',    $cgst_amt,    $post_id);
    update_field('sgst_amount',    $sgst_amt,    $post_id);
    update_field('igst_amount',    $igst_amt,    $post_id);
    update_field('total_amount',   $total_amt,   $post_id);
    update_field('amount_in_words',$amt_words,   $post_id);
    update_field('status',         $status,      $post_id);
    update_field('line_items',     $line_items,  $post_id);
    update_field('team_member',    $team_member, $post_id);
    update_field('gst_regn_no',    $gst_regn_no, $post_id);

    // Generate PDF (on create or update)
    $pdf_url = gmc_generate_invoice_pdf_for_post($post_id);
    if ($pdf_url) {
        update_post_meta($post_id, 'invoice_pdf_url', $pdf_url);
    }

    $action_label = $invoice_id > 0 ? 'updated' : 'created';
    $list_url = add_query_arg('invoice_saved', $action_label, site_url('/invoices/'));

    wp_send_json_success([
        'message'  => $action_label === 'created' ? 'Invoice created successfully!' : 'Invoice updated successfully!',
        'post_id'  => $post_id,
        'redirect' => $list_url,
    ]);
});

// ── Invoice: Record Payment ───────────────────────────────────────────────────
add_action('wp_ajax_gmc_record_payment', function() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'gmc_payment_nonce')) {
        wp_send_json_error('Security check failed.');
    }
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in.');
    }

    $post_id      = intval($_POST['post_id'] ?? 0);
    $pay_date     = sanitize_text_field($_POST['payment_date'] ?? '');
    $pay_amount   = floatval($_POST['amount'] ?? 0);
    $pay_mode     = sanitize_text_field($_POST['payment_mode'] ?? '');
    $pay_ref      = sanitize_text_field($_POST['reference_no'] ?? '');

    if (!$post_id || $pay_amount <= 0 || !$pay_date || !$pay_mode) {
        wp_send_json_error('Missing required fields.');
    }

    // Append payment to repeater
    $existing = get_field('payments_received', $post_id) ?: [];
    $existing[] = [
        'payment_date' => $pay_date,
        'amount'       => $pay_amount,
        'payment_mode' => $pay_mode,
        'reference_no' => $pay_ref,
    ];
    update_field('payments_received', $existing, $post_id);

    // Auto-update status
    $total    = (float)(get_field('total_amount', $post_id) ?? 0);
    $paid_sum = array_sum(array_column($existing, 'amount'));
    if ($paid_sum >= $total) {
        update_field('status', 'Paid', $post_id);
    } elseif ($paid_sum > 0) {
        update_field('status', 'Partial', $post_id);
    }

    wp_send_json_success(['message' => 'Payment recorded.']);
});

// ── Invoice: Update Payment Entry ─────────────────────────────────────────────
add_action('wp_ajax_gmc_update_payment', function() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'gmc_payment_nonce')) {
        wp_send_json_error('Security check failed.');
    }
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in.');
    }

    $post_id    = intval($_POST['post_id'] ?? 0);
    $index      = intval($_POST['payment_index'] ?? -1);
    $pay_date   = sanitize_text_field($_POST['payment_date'] ?? '');
    $pay_amount = floatval($_POST['amount'] ?? 0);
    $pay_mode   = sanitize_text_field($_POST['payment_mode'] ?? '');
    $pay_ref    = sanitize_text_field($_POST['reference_no'] ?? '');

    if (!$post_id || $index < 0 || $pay_amount <= 0 || !$pay_date || !$pay_mode) {
        wp_send_json_error('Missing required fields.');
    }

    $payments = get_field('payments_received', $post_id) ?: [];
    if (!isset($payments[$index])) {
        wp_send_json_error('Payment entry not found.');
    }

    $payments[$index] = [
        'payment_date' => $pay_date,
        'amount'       => $pay_amount,
        'payment_mode' => $pay_mode,
        'reference_no' => $pay_ref,
    ];
    update_field('payments_received', $payments, $post_id);

    // Auto-update status
    $total    = (float)(get_field('total_amount', $post_id) ?? 0);
    $paid_sum = array_sum(array_column($payments, 'amount'));
    if ($paid_sum >= $total) {
        update_field('status', 'Paid', $post_id);
    } elseif ($paid_sum > 0) {
        update_field('status', 'Partial', $post_id);
    } else {
        update_field('status', 'Unpaid', $post_id);
    }

    $new_paid_sum = $paid_sum;
    $new_balance  = $total - $new_paid_sum;
    $new_status   = $paid_sum >= $total ? 'Paid' : ($paid_sum > 0 ? 'Partial' : 'Unpaid');

    wp_send_json_success([
        'message'  => 'Payment updated.',
        'paid_sum' => $new_paid_sum,
        'balance'  => $new_balance,
        'status'   => $new_status,
    ]);
});

// ── Invoice: Delete Payment Entry ─────────────────────────────────────────────
add_action('wp_ajax_gmc_delete_payment', function() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'gmc_payment_nonce')) {
        wp_send_json_error('Security check failed.');
    }
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in.');
    }

    $post_id = intval($_POST['post_id'] ?? 0);
    $index   = intval($_POST['payment_index'] ?? -1);

    if (!$post_id || $index < 0) {
        wp_send_json_error('Missing required fields.');
    }

    $payments = get_field('payments_received', $post_id) ?: [];
    if (!isset($payments[$index])) {
        wp_send_json_error('Payment entry not found.');
    }

    array_splice($payments, $index, 1);
    update_field('payments_received', $payments, $post_id);

    // Auto-update status
    $total    = (float)(get_field('total_amount', $post_id) ?? 0);
    $paid_sum = array_sum(array_column($payments, 'amount'));
    if ($paid_sum >= $total && $total > 0) {
        update_field('status', 'Paid', $post_id);
    } elseif ($paid_sum > 0) {
        update_field('status', 'Partial', $post_id);
    } else {
        update_field('status', 'Unpaid', $post_id);
    }

    $new_status = ($paid_sum >= $total && $total > 0) ? 'Paid' : ($paid_sum > 0 ? 'Partial' : 'Unpaid');

    wp_send_json_success([
        'message'  => 'Payment deleted.',
        'paid_sum' => $paid_sum,
        'balance'  => $total - $paid_sum,
        'status'   => $new_status,
    ]);
});

// ── Invoice: PDF Generation (helper) ─────────────────────────────────────────
// $mode = 'email' → branded header/footer inside PDF (for emailing)
// $mode = 'print' → blank margins for pre-printed letterpad
function gmc_generate_invoice_pdf_for_post($post_id, $mode = 'email') {
    try {
        $autoload = WP_PLUGIN_DIR . '/client-pdf-generator/dompdf/vendor/autoload.php';
        if (!file_exists($autoload)) return '';
        require_once $autoload;

        $invoice_no   = get_field('invoice_no',     $post_id) ?: 'invoice';
        $invoice_date = get_field('invoice_date',   $post_id) ?: '';
        $gst_type     = get_field('gst_type',       $post_id) ?: 'cgst_sgst';
        $cgst_p       = (float)(get_field('cgst_percent', $post_id) ?? 9);
        $sgst_p       = (float)(get_field('sgst_percent', $post_id) ?? 9);
        $igst_p       = (float)(get_field('igst_percent', $post_id) ?? 18);
        $cgst_amt     = (float)(get_field('cgst_amount',  $post_id) ?? 0);
        $sgst_amt     = (float)(get_field('sgst_amount',  $post_id) ?? 0);
        $igst_amt     = (float)(get_field('igst_amount',  $post_id) ?? 0);
        $total_amt    = (float)(get_field('total_amount', $post_id) ?? 0);
        $amt_words    = get_field('amount_in_words', $post_id) ?: '';
        $line_items   = get_field('line_items', $post_id) ?: [];
        $team_member  = get_field('team_member', $post_id) ?: '';
        $comp_gst     = get_field('gst_regn_no', $post_id) ?: '36AAGCG3405N1ZH';

        $client_id      = get_field('client_id', $post_id);
        $client_name    = '';
        $client_address = '';
        $client_gst     = '';
        if ($client_id) {
            $client_name    = get_field('organization_name', $client_id) ?: get_the_title($client_id);
            $addr           = get_field('address', $client_id);
            $client_address = $addr['head_office'] ?? '';
            $client_gst     = get_field('cgt_regn_no', $client_id) ?: '';
        }

        $status       = get_field('status', $post_id) ?: 'Unpaid';
        $is_paid      = (strtolower($status) === 'paid');
        $title        = $is_paid ? 'INVOICE' : 'PROFORMA INVOICE';
        $inv_prefix   = $is_paid ? 'INV. No' : 'P.INV. No';

        // Build line-item rows
        $rows = '';
        foreach ($line_items as $i => $item) {
            $rows .= '<tr>'.
                '<td style="text-align:center">' . ($i+1) . '.</td>'.
                '<td>' . esc_html($item['description']) . '</td>'.
                '<td style="text-align:right">' . number_format((float)$item['amount'], 2) . '</td>'.
            '</tr>';
        }
        $c = count($line_items);
        if ($gst_type === 'cgst_sgst') {
            $rows .= sprintf('<tr><td style="text-align:center">%d.</td><td>CGST @ %s%%</td><td style="text-align:right">%s</td></tr>', $c+1, $cgst_p, number_format($cgst_amt,2));
            $rows .= sprintf('<tr><td style="text-align:center">%d.</td><td>SGST @ %s%%</td><td style="text-align:right">%s</td></tr>', $c+2, $sgst_p, number_format($sgst_amt,2));
        } else {
            $rows .= sprintf('<tr><td style="text-align:center">%d.</td><td>IGST @ %s%%</td><td style="text-align:right">%s</td></tr>', $c+1, $igst_p, number_format($igst_amt,2));
        }
        $rows .= '<tr><td colspan="2" style="text-align:right"><strong>Total:</strong></td><td style="text-align:right"><strong>'.number_format($total_amt,2).'</strong></td></tr>';

        $client_gst_html = $client_gst ? '<br><strong>GST No:</strong> ' . esc_html($client_gst) : '';
        $addr_lines      = nl2br(esc_html($client_address));
        $inv_no_esc      = esc_html($invoice_no);
        $inv_date_esc    = esc_html($invoice_date);
        $client_name_esc = esc_html($client_name);
        $amt_words_esc   = esc_html($amt_words);

        // ── Mode-dependent sections ──────────────────────────────────────────
        if ($mode === 'email') {
            // Branded margins (1 inch = 25.4mm)
            $page_css = 'html{ margin-left:15mm; margin-right:15mm; margin-top:10mm; margin-bottom:10mm; }';

            // Logo (base64-embedded)
            $logo_path = ABSPATH . 'wp-content/uploads/2025/03/GMS-300x277.jpg';
            $logo_b64  = '';
            if (file_exists($logo_path)) {
                $logo_b64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logo_path));
            }

            $header_html = <<<HEADER
<div style="text-align:center;margin-bottom:15px;">
    <img src="{$logo_b64}" style="height:70px;margin-bottom:8px;" alt="Global MCS">
</div>
HEADER;

            $footer_html = <<<FOOTER
<div style="margin-top:20px;">
    <div style="color: #2e7d32; font-size: 20pt; font-weight: bold; border-top: 2px solid #2e7d32; padding-top: 5px;">
        Global Management Certification Services Pvt. Ltd.
    </div>
    <table style="width:100%; border:none; margin-top:5px;" class="nb">
        <tr>
            <td style="width:50%; border:none; font-size:9pt; color:#333;">
                Flat No.402, Plot No.410, Matrusri Nagar, Miyapur,<br>
                Hyderabad-500 049, Telangana, India.
            </td>
            <td style="width:50%; border:none; font-size:9pt; color:#333; text-align:right;">
                Phone: 040 - 4855 9001<br>
                E-mail: info@mcsglobal.in | Web: www.mcsglobal.in
            </td>
        </tr>
    </table>
</div>
FOOTER;
        } else {
            // Print mode — large top margin for letterpad
            $page_css    = 'html{ margin-left:20mm; margin-right:20mm; margin-top:75mm; margin-bottom:25mm; }';
            $header_html = '';
            $footer_html = '';
        }

        // Signature image (base64-embedded)
        $sign_path = get_stylesheet_directory() . '/sneat-assets/img/invoicesign.jpeg';
        $sign_b64  = '';
        if (file_exists($sign_path)) {
            $sign_b64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($sign_path));
        }
        $sign_img_html = $sign_b64 ? '<img src="' . $sign_b64 . '" style="height:80px;">' : '';

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
{$page_css}
body { font-family: "Times New Roman", serif; font-size: 11pt; color: #000; line-height: 1.3; }
h2 { text-align: center; font-size: 16pt; text-decoration: underline; letter-spacing: 2px; margin: 10px 0 20px 0; font-weight: bold; }
table { border-collapse: collapse; width: 100%; margin-bottom: 0px; }
td, th { border: 1px solid #333; padding: 6px 10px; vertical-align: top; }
th { font-weight: bold; background: #f2f2f2; }
.lbl { font-weight: bold; white-space: nowrap; width: 50%; }
.r { text-align: right; }
.c { text-align: center; }
.nb td { border: none; padding: 2px 0; }
.metadata-table td { padding: 4px 8px; border: 1px solid #333; }
.sign-box { text-align: right; margin-top: 20px; }
</style>
</head>
<body>
{$header_html}
<h2>{$title}</h2>

<table style="border: 1px solid #333;">
<tr>
  <td style="width:60%; border:none; border-right: 1px solid #333;">
    <div style="margin-bottom: 5px;"><strong>To,</strong></div>
    <div style="font-weight: bold;">{$client_name_esc}</div>
    <div style="font-size: 10pt;">{$addr_lines}{$client_gst_html}</div>
  </td>
  <td style="width:40%; padding:0; border:none;">
    <table class="metadata-table" style="width:100%; border:none;">
      <tr><td class="lbl">{$inv_prefix}</td>   <td>{$inv_no_esc}</td></tr>
      <tr><td class="lbl">Date</td>           <td>{$inv_date_esc}</td></tr>
      <tr><td class="lbl">PAN No</td>         <td>AAGCG3405N</td></tr>
      <tr><td class="lbl">GST Regn. No.</td>  <td>{$comp_gst}</td></tr>
      <tr><td class="lbl" style="border-bottom:none;">SAC CODE</td> <td style="border-bottom:none;">998214</td></tr>
    </table>
  </td>
</tr>
</table>

<table style="margin-top:20px;">
  <thead>
    <tr>
      <th class="c" style="width:8%;">S.No</th>
      <th>Description</th>
      <th class="r" style="width:25%;">Total Amount (Rs.)</th>
    </tr>
  </thead>
  <tbody>
    {$rows}
  </tbody>
</table>

<table style="margin-top:20px; border: 1px solid #333;">
  <tr>
    <td style="width:40%; border:none; border-right: 1px solid #333;">
        <strong>Payment Terms:</strong><br>
        100% on presentation.
    </td>
    <td style="border:none;">
        <strong>Amount in Words:</strong><br>
        <em>{$amt_words_esc}</em>
    </td>
  </tr>
</table>

<table style="margin-top:20px; border: 1px solid #333;">
  <tr>
    <td style="border:none;">
      <strong>Bank Account Details:</strong><br>
      Global Management Certification Services Pvt. Ltd.<br>
      Bank : State Bank of India.<br>
      Branch : Road No.1, KPHB Colony, Kukatpally, Hyd.<br>
      A/c No. : 67384332714<br>
      IFSC Code : SBIN0070743
    </td>
  </tr>
</table>

<div class="sign-box">
  <div>{$sign_img_html}</div>
</div>

<div style="position: absolute; bottom: 0; width: 100%;">
    {$footer_html}
</div>

</body>
</html>
HTML;

        $dompdf = new Dompdf\Dompdf();
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdf_content = $dompdf->output();

        // Save file to uploads
        $upload    = wp_upload_dir();
        $safe_name = preg_replace('/[^a-zA-Z0-9_\-]/', '-', $invoice_no);
        $filename  = 'invoice-' . $safe_name . '-' . $post_id . '-' . $mode . '.pdf';
        $filepath  = $upload['path'] . '/' . $filename;
        $fileurl   = $upload['url']  . '/' . $filename;
        file_put_contents($filepath, $pdf_content);
        return $fileurl;
    } catch (Exception $e) {
        error_log('GMC Invoice PDF Error: ' . $e->getMessage());
        return '';
    }
}

// ── Invoice: Generate PDF via AJAX ────────────────────────────────────────────
add_action('wp_ajax_gmc_generate_invoice_pdf', function() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'gmc_payment_nonce')) {
        wp_send_json_error('Security check failed.');
    }
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in.');
    }
    $post_id = intval($_POST['post_id'] ?? 0);
    if (!$post_id) {
        wp_send_json_error('Invalid post ID.');
    }

    // Generate both versions
    $email_url = gmc_generate_invoice_pdf_for_post($post_id, 'email');
    $print_url = gmc_generate_invoice_pdf_for_post($post_id, 'print');

    if (!$email_url && !$print_url) {
        wp_send_json_error('PDF generation failed. Check server logs.');
    }

    // Store both URLs
    if ($email_url) update_post_meta($post_id, 'invoice_pdf_url', $email_url);
    if ($print_url) update_post_meta($post_id, 'invoice_pdf_print_url', $print_url);

    wp_send_json_success([
        'pdf_url'       => $email_url,
        'pdf_print_url' => $print_url,
        'message'       => 'Both PDFs generated (Email + Print).',
    ]);
});

// ── Invoice: Send Email via AJAX ──────────────────────────────────────────────
add_action('wp_ajax_gmc_send_invoice_email', function() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'gmc_payment_nonce')) {
        wp_send_json_error('Security check failed.');
    }
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in.');
    }

    $post_id = intval($_POST['post_id'] ?? 0);
    $to      = sanitize_email($_POST['to'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    $pdf_url = esc_url_raw($_POST['pdf_url'] ?? '');

    if (!$to || !is_email($to) || !$subject || !$message) {
        wp_send_json_error('Missing required fields (To, Subject, Message).');
    }

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: Global Management Certification Services <noreply@' . parse_url(site_url(), PHP_URL_HOST) . '>',
    ];

    // Attach PDF if URL is from our uploads directory
    $attachments = [];
    if ($pdf_url) {
        $upload_dir = wp_upload_dir();
        // Convert URL to filesystem path
        $filepath = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $pdf_url);
        $filepath = wp_normalize_path($filepath);
        if (file_exists($filepath)) {
            $attachments[] = $filepath;
        }
    }

    $html_message = '<html><body>' . nl2br(esc_html($message)) . '</body></html>';

    $sent = wp_mail($to, $subject, $html_message, $headers, $attachments);

    if ($sent) {
        // Log that email was sent
        $existing_notes = get_post_meta($post_id, 'email_log', true) ?: [];
        $existing_notes[] = [
            'date' => current_time('d/m/Y H:i'),
            'to'   => $to,
        ];
        update_post_meta($post_id, 'email_log', $existing_notes);
        wp_send_json_success(['message' => 'Email sent to ' . $to]);
    } else {
        wp_send_json_error('wp_mail failed. Check your mail configuration (SMTP/Mailpit).');
    }
});

// ── Invoice: Delete via AJAX ──────────────────────────────────────────────────
add_action('wp_ajax_gmc_delete_invoice', function() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'gmc_payment_nonce')) {
        wp_send_json_error('Security check failed.');
    }
    if (!is_user_logged_in() || !current_user_can('delete_posts')) {
        wp_send_json_error('Permission denied.');
    }

    $post_id = intval($_POST['post_id'] ?? 0);
    if (!$post_id) {
        wp_send_json_error('Invalid invoice ID.');
    }

    // Ensure we're deleting the right post type
    if (get_post_type($post_id) !== 'gmc_invoice') {
        wp_send_json_error('Invalid post type.');
    }

    // Delete the associated PDF file from disk if it exists
    $pdf_url = get_post_meta($post_id, 'invoice_pdf_url', true);
    if ($pdf_url) {
        $upload_dir = wp_upload_dir();
        $filepath   = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $pdf_url);
        $filepath   = wp_normalize_path($filepath);
        if (file_exists($filepath)) {
            @unlink($filepath);
        }
    }

    $result = wp_delete_post($post_id, true); // true = force delete (bypass trash)

    if ($result) {
        wp_send_json_success(['message' => 'Invoice deleted.']);
    } else {
        wp_send_json_error('Could not delete the invoice. Please try again.');
    }
});

// ── Client: Delete via AJAX ───────────────────────────────────────────────────
add_action('wp_ajax_gmc_delete_client', function() {
    $post_id = intval($_POST['post_id'] ?? 0);
    $nonce   = sanitize_text_field($_POST['nonce'] ?? '');

    if (!wp_verify_nonce($nonce, 'gmc_client_delete_nonce')) {
        wp_send_json_error('Security check failed.');
    }
    if (!is_user_logged_in() || !current_user_can('administrator')) {
        wp_send_json_error('Permission denied.');
    }
    if (!$post_id || get_post_type($post_id) !== 'client') {
        wp_send_json_error('Invalid client.');
    }

    $result = wp_delete_post($post_id, true); // permanent delete

    if ($result) {
        wp_send_json_success(['message' => 'Client deleted.']);
    } else {
        wp_send_json_error('Could not delete the client.');
    }
});

