<?php
/**
 * Template Name: User Add
 */

acf_form_head();
get_header();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user']) && wp_verify_nonce($_POST['_wpnonce'], 'create_user')) {
    // Create new user
    $user_login = sanitize_user($_POST['user_login']);
    $user_email = sanitize_email($_POST['user_email']);
    $user_pass = $_POST['user_pass'];
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $display_name = sanitize_text_field($_POST['display_name']);
    
    // Basic validation
    $error = null;
    if (empty($user_login)) {
        $error = 'Username is required.';
    } elseif (empty($user_email)) {
        $error = 'Email is required.';
    } elseif (empty($user_pass)) {
        $error = 'Password is required.';
    } elseif (username_exists($user_login)) {
        $error = 'Username already exists.';
    } elseif (email_exists($user_email)) {
        $error = 'Email already exists.';
    }
    
    if (!$error) {
        // Create the user
        $user_id = wp_insert_user([
            'user_login' => $user_login,
            'user_email' => $user_email,
            'user_pass' => $user_pass,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => $display_name ?: "$first_name $last_name",
            'role' => $_POST['role']
        ]);
        
        if (!is_wp_error($user_id)) {
            // Update user meta
            update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone'])); 
            update_user_meta($user_id, 'organization', sanitize_text_field($_POST['organization']));
            update_user_meta($user_id, 'address', sanitize_text_field($_POST['address']));
            update_user_meta($user_id, 'city', sanitize_text_field($_POST['city']));
            update_user_meta($user_id, 'state', sanitize_text_field($_POST['state']));
            update_user_meta($user_id, 'zip', sanitize_text_field($_POST['zip']));
            update_user_meta($user_id, 'country', sanitize_text_field($_POST['country']));
            update_user_meta($user_id, 'language', sanitize_text_field($_POST['language']));
            update_user_meta($user_id, 'timezone', sanitize_text_field($_POST['timezone']));
            update_user_meta($user_id, 'currency', sanitize_text_field($_POST['currency']));
            
            // Save ACF fields if any
            if (function_exists('acf_save_post')) {
                acf_save_post('user_' . $user_id);
            }
            
            // Redirect to user view page with success message
            wp_redirect(add_query_arg(['id' => $user_id, 'created' => 1], get_permalink(get_page_by_path('user-view'))));
            exit;
        } else {
            $error = $user_id->get_error_message();
        }
    }
}

// Default avatar
$avatar_url = get_avatar_url(0, ['size' => 100]);
?>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Sidebar -->
        <?php get_sidebar('custom'); ?>
        
        <!-- Layout container -->
        <div class="layout-page">
      
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">

                    <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo esc_html($error); ?>
                    </div>
                    <?php endif; ?>

                    <!-- User Profile Content -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Add New User</h5>
                                    <small>Create a new user account</small>
                                </div>
                                <div class="card-body">
                                    
                                    <form method="post" action="">
                                        <?php wp_nonce_field('create_user'); ?>
                                        <input type="hidden" name="create_user" value="1">
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="user_login" class="form-label">Username <span class="text-danger">*</span></label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="user_login"
                                                        name="user_login"
                                                        value="<?php echo isset($_POST['user_login']) ? esc_attr($_POST['user_login']) : ''; ?>"
                                                        placeholder="johndoe"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="user_pass" class="form-label">Password <span class="text-danger">*</span></label>
                                                    <input
                                                        type="password"
                                                        class="form-control"
                                                        id="user_pass"
                                                        name="user_pass"
                                                        placeholder="••••••••"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="first_name" class="form-label">First Name</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="first_name"
                                                        name="first_name"
                                                        value="<?php echo isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : ''; ?>"
                                                        placeholder="John"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="last_name" class="form-label">Last Name</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="last_name"
                                                        name="last_name"
                                                        value="<?php echo isset($_POST['last_name']) ? esc_attr($_POST['last_name']) : ''; ?>"
                                                        placeholder="Doe"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="display_name" class="form-label">Display Name</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="display_name"
                                                        name="display_name"
                                                        value="<?php echo isset($_POST['display_name']) ? esc_attr($_POST['display_name']) : ''; ?>"
                                                        placeholder="John Doe"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="user_email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                                    <input
                                                        class="form-control"
                                                        type="email"
                                                        id="user_email"
                                                        name="user_email"
                                                        value="<?php echo isset($_POST['user_email']) ? esc_attr($_POST['user_email']) : ''; ?>"
                                                        placeholder="john.doe@example.com"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                                    <select id="role" name="role" class="form-select" required>
                                                        <option value="">Select Role</option>
                                                        <option value="subscriber" <?php selected(isset($_POST['role']) ? $_POST['role'] : '', 'subscriber'); ?>>Subscriber</option>
                                                        <option value="contributor" <?php selected(isset($_POST['role']) ? $_POST['role'] : '', 'contributor'); ?>>Contributor</option>
                                                        <option value="author" <?php selected(isset($_POST['role']) ? $_POST['role'] : '', 'author'); ?>>Author</option>
                                                        <option value="editor" <?php selected(isset($_POST['role']) ? $_POST['role'] : '', 'editor'); ?>>Editor</option>
                                                        <option value="administrator" <?php selected(isset($_POST['role']) ? $_POST['role'] : '', 'administrator'); ?>>Administrator</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="organization" class="form-label">Organization</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="organization"
                                                        name="organization"
                                                        value="<?php echo isset($_POST['organization']) ? esc_attr($_POST['organization']) : ''; ?>"
                                                        placeholder="GMC"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="phone">Phone Number</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                                        <input
                                                            type="text"
                                                            id="phone"
                                                            name="phone"
                                                            class="form-control"
                                                            value="<?php echo isset($_POST['phone']) ? esc_attr($_POST['phone']) : ''; ?>"
                                                            placeholder="2025550111"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="address" class="form-label">Address</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="address"
                                                        name="address"
                                                        value="<?php echo isset($_POST['address']) ? esc_attr($_POST['address']) : ''; ?>"
                                                        placeholder="Address"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="city" class="form-label">City</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="city"
                                                        name="city"
                                                        value="<?php echo isset($_POST['city']) ? esc_attr($_POST['city']) : ''; ?>"
                                                        placeholder="Hyderabad"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="state" class="form-label">State</label>
                                                    <input
                                                        class="form-control"
                                                        type="text"
                                                        id="state"
                                                        name="state"
                                                        value="<?php echo isset($_POST['state']) ? esc_attr($_POST['state']) : ''; ?>"
                                                        placeholder="Telangana"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="zip" class="form-label">Zip Code</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="zip"
                                                        name="zip"
                                                        value="<?php echo isset($_POST['zip']) ? esc_attr($_POST['zip']) : ''; ?>"
                                                        placeholder="231465"
                                                        maxlength="6"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="country">Country</label>
                                                    <select id="country" name="country" class="select2 form-select">
                                                        <option value="">Select</option>
                                                        <option value="Australia">Australia</option>
                                                        <option value="Bangladesh">Bangladesh</option>
                                                        <option value="Belarus">Belarus</option>
                                                        <option value="Brazil">Brazil</option>
                                                        <option value="Canada">Canada</option>
                                                        <option value="China">China</option>
                                                        <option value="France">France</option>
                                                        <option value="Germany">Germany</option>
                                                        <option value="India">India</option>
                                                        <option value="Indonesia">Indonesia</option>
                                                        <option value="Israel">Israel</option>
                                                        <option value="Italy">Italy</option>
                                                        <option value="Japan">Japan</option>
                                                        <option value="Korea">Korea, Republic of</option>
                                                        <option value="Mexico">Mexico</option>
                                                        <option value="Philippines">Philippines</option>
                                                        <option value="Russia">Russian Federation</option>
                                                        <option value="South Africa">South Africa</option>
                                                        <option value="Thailand">Thailand</option>
                                                        <option value="Turkey">Turkey</option>
                                                        <option value="Ukraine">Ukraine</option>
                                                        <option value="United Arab Emirates">United Arab Emirates</option>
                                                        <option value="United Kingdom">United Kingdom</option>
                                                        <option value="United States">United States</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="language" class="form-label">Language</label>
                                                    <select id="language" name="language" class="select2 form-select">
                                                        <option value="">Select Language</option>
                                                        <option value="en">English</option>
                                                        <option value="fr">French</option>
                                                        <option value="de">German</option>
                                                        <option value="pt">Portuguese</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="timezone" class="form-label">Timezone</label>
                                                    <select id="timezone" name="timezone" class="select2 form-select">
                                                        <option value="">Select Timezone</option>
                                                        <option value="-12">(GMT-12:00) International Date Line West</option>
                                                        <option value="-11">(GMT-11:00) Midway Island, Samoa</option>
                                                        <option value="-10">(GMT-10:00) Hawaii</option>
                                                        <option value="-9">(GMT-09:00) Alaska</option>
                                                        <option value="-8">(GMT-08:00) Pacific Time (US & Canada)</option>
                                                        <option value="-7">(GMT-07:00) Mountain Time (US & Canada)</option>
                                                        <option value="-6">(GMT-06:00) Central Time (US & Canada)</option>
                                                        <option value="-5">(GMT-05:00) Eastern Time (US & Canada)</option>
                                                        <option value="-4">(GMT-04:00) Atlantic Time (Canada)</option>
                                                        <option value="0">(GMT) Greenwich Mean Time</option>
                                                        <option value="1">(GMT+01:00) Central European Time</option>
                                                        <option value="2">(GMT+02:00) Eastern European Time</option>
                                                        <option value="5.5">(GMT+05:30) India Standard Time</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="currency" class="form-label">Currency</label>
                                                    <select id="currency" name="currency" class="select2 form-select">
                                                        <option value="">Select Currency</option>
                                                        <option value="usd">USD</option>
                                                        <option value="euro">Euro</option>
                                                        <option value="pound">Pound</option>
                                                        <option value="bitcoin">Bitcoin</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary me-2">Create User</button>
                                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('auditors'))); ?>" class="btn btn-outline-secondary">Cancel</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            © <?php echo date('Y'); ?> GMC
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 Elements
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        jQuery('.select2').select2();
    }
});
</script>

<?php get_footer(); ?>