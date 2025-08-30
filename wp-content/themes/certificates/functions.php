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
add_action('wp', function() {
    // Only on pages that contain our shortcode
    global $post;
   $shortcodes = [ 'client_tables' ];

    foreach ( $shortcodes as $sc ) {
        if ( has_shortcode( $post->post_content, $sc ) ) {
            acf_form_head();
            break;
        }
    }
});

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

// Include ACF debugging
require_once get_stylesheet_directory() . '/acf-debug.php';

// includers reposrts.php for dashboard stats
require_once get_stylesheet_directory() . '/includes/reports.php';

// Include custom login redirects
// require_once get_stylesheet_directory() . '/login-redirects.php';


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
    
    // Sneat Template Core CSS
    wp_enqueue_style(
        'sneat-core-css',
        get_stylesheet_directory_uri() . '/sneat-assets/vendor/css/core.css',
        array(),
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
    if (is_page_template('client-list-pdf.php') || is_page_template('page-client-single.php')) {
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
            'send_pdf_email_nonce' => wp_create_nonce('send_pdf_email_nonce'),
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

?>
<?php
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
?>
<?php
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
function send_pdf_email(){
    // 1) Verify nonce
    if ( empty($_POST['nonce'])
      || ! wp_verify_nonce($_POST['nonce'], 'send_pdf_email_nonce')
    ) {
        wp_send_json_error([ 'message' => 'Invalid nonce' ], 403);
    }

    // 2) Sanitize inputs
    $to       = sanitize_email( $_POST['to_email'] );
    $subject  = sanitize_text_field( $_POST['subject'] );
    $message  = wp_kses_post( $_POST['message'] );
    $pdf_url  = esc_url_raw( $_POST['pdf_attachment'] );

    if ( empty($to) || empty($subject) || empty($pdf_url) ) {
        wp_send_json_error([ 'message' => 'Missing required parameters' ], 400);
    }

    // 3) Convert URL to server path
    $upload = wp_get_upload_dir();
    $baseurl = $upload['baseurl'];
    $basedir = $upload['basedir'];
    $filepath = '';
    if ( 0 === strpos($pdf_url, $baseurl) ) {
        // replace baseurl with basedir
        $filepath = str_replace( $baseurl, $basedir, $pdf_url );
    }
    if ( ! $filepath || ! file_exists($filepath) ) {
        wp_send_json_error([ 'message' => 'Unable to locate attachment file' ], 404);
    }

    // 4) Send email
    $headers = [ 'Content-Type: text/html; charset=UTF-8' ];
    $attachments = [ $filepath ];
    $sent = wp_mail( $to, $subject, $message, $headers, $attachments );

    if ( $sent ) {
        wp_send_json_success([ 'message' => 'Email sent successfully' ]);
    } else {
        // log debug info
        error_log("wp_mail failed: to={$to}, subject={$subject}, attachment={$filepath}");
        wp_send_json_error([ 'message' => 'Email sending failed' ], 500);
    }
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
