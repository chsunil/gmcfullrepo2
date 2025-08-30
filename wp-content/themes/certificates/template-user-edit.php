<?php
/**
 * Template Name: User Edit
 */

acf_form_head();
get_header();

// Get user ID from URL parameter
$user_id = isset($_GET['id']) ? intval($_GET['id']) : get_current_user_id();
$user = get_user_by('id', $user_id);

// Check if user exists
if (!$user) {
    wp_redirect(home_url());
    exit;
}

// Get user meta
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$phone = get_user_meta($user_id, 'phone', true);
$address = get_user_meta($user_id, 'address', true);
$city = get_user_meta($user_id, 'city', true);
$state = get_user_meta($user_id, 'state', true);
$zip = get_user_meta($user_id, 'zip', true);
$country = get_user_meta($user_id, 'country', true);
$organization = get_user_meta($user_id, 'organization', true);
$language = get_user_meta($user_id, 'language', true);
$timezone = get_user_meta($user_id, 'timezone', true);
$currency = get_user_meta($user_id, 'currency', true);

// Get user avatar
$avatar_url = get_avatar_url($user_id, ['size' => 100]);
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

                    <!-- User Profile Content -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Profile Details</h5>
                                </div>
                                <div class="card-body">
                                    
                                    <form method="post" action="">
                                        <?php wp_nonce_field('update_user_' . $user_id); ?>
                                        <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id); ?>">
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="first_name" class="form-label">First Name</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="first_name"
                                                        name="first_name"
                                                        value="<?php echo esc_attr($first_name); ?>"
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
                                                        value="<?php echo esc_attr($last_name); ?>"
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
                                                        value="<?php echo esc_attr($user->display_name); ?>"
                                                        placeholder="John Doe"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="user_email" class="form-label">E-mail</label>
                                                    <input
                                                        class="form-control"
                                                        type="email"
                                                        id="user_email"
                                                        name="user_email"
                                                        value="<?php echo esc_attr($user->user_email); ?>"
                                                        placeholder="john.doe@example.com"
                                                    />
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
                                                        value="<?php echo esc_attr($organization); ?>"
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
                                                            value="<?php echo esc_attr($phone); ?>"
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
                                                        value="<?php echo esc_attr($address); ?>"
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
                                                        value="<?php echo esc_attr($city); ?>"
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
                                                        value="<?php echo esc_attr($state); ?>"
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
                                                        value="<?php echo esc_attr($zip); ?>"
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
                                                        <option value="Australia" <?php selected($country, 'Australia'); ?>>Australia</option>
                                                        <option value="Bangladesh" <?php selected($country, 'Bangladesh'); ?>>Bangladesh</option>
                                                        <option value="Belarus" <?php selected($country, 'Belarus'); ?>>Belarus</option>
                                                        <option value="Brazil" <?php selected($country, 'Brazil'); ?>>Brazil</option>
                                                        <option value="Canada" <?php selected($country, 'Canada'); ?>>Canada</option>
                                                        <option value="China" <?php selected($country, 'China'); ?>>China</option>
                                                        <option value="France" <?php selected($country, 'France'); ?>>France</option>
                                                        <option value="Germany" <?php selected($country, 'Germany'); ?>>Germany</option>
                                                        <option value="India" <?php selected($country, 'India'); ?>>India</option>
                                                        <option value="Indonesia" <?php selected($country, 'Indonesia'); ?>>Indonesia</option>
                                                        <option value="Israel" <?php selected($country, 'Israel'); ?>>Israel</option>
                                                        <option value="Italy" <?php selected($country, 'Italy'); ?>>Italy</option>
                                                        <option value="Japan" <?php selected($country, 'Japan'); ?>>Japan</option>
                                                        <option value="Korea" <?php selected($country, 'Korea'); ?>>Korea, Republic of</option>
                                                        <option value="Mexico" <?php selected($country, 'Mexico'); ?>>Mexico</option>
                                                        <option value="Philippines" <?php selected($country, 'Philippines'); ?>>Philippines</option>
                                                        <option value="Russia" <?php selected($country, 'Russia'); ?>>Russian Federation</option>
                                                        <option value="South Africa" <?php selected($country, 'South Africa'); ?>>South Africa</option>
                                                        <option value="Thailand" <?php selected($country, 'Thailand'); ?>>Thailand</option>
                                                        <option value="Turkey" <?php selected($country, 'Turkey'); ?>>Turkey</option>
                                                        <option value="Ukraine" <?php selected($country, 'Ukraine'); ?>>Ukraine</option>
                                                        <option value="United Arab Emirates" <?php selected($country, 'United Arab Emirates'); ?>>United Arab Emirates</option>
                                                        <option value="United Kingdom" <?php selected($country, 'United Kingdom'); ?>>United Kingdom</option>
                                                        <option value="United States" <?php selected($country, 'United States'); ?>>United States</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="language" class="form-label">Language</label>
                                                    <select id="language" name="language" class="select2 form-select">
                                                        <option value="">Select Language</option>
                                                        <option value="en" <?php selected($language, 'en'); ?>>English</option>
                                                        <option value="fr" <?php selected($language, 'fr'); ?>>French</option>
                                                        <option value="de" <?php selected($language, 'de'); ?>>German</option>
                                                        <option value="pt" <?php selected($language, 'pt'); ?>>Portuguese</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="timezone" class="form-label">Timezone</label>
                                                    <select id="timezone" name="timezone" class="select2 form-select">
                                                        <option value="">Select Timezone</option>
                                                        <option value="-12" <?php selected($timezone, '-12'); ?>>(GMT-12:00) International Date Line West</option>
                                                        <option value="-11" <?php selected($timezone, '-11'); ?>>(GMT-11:00) Midway Island, Samoa</option>
                                                        <option value="-10" <?php selected($timezone, '-10'); ?>>(GMT-10:00) Hawaii</option>
                                                        <option value="-9" <?php selected($timezone, '-9'); ?>>(GMT-09:00) Alaska</option>
                                                        <option value="-8" <?php selected($timezone, '-8'); ?>>(GMT-08:00) Pacific Time (US & Canada)</option>
                                                        <option value="-7" <?php selected($timezone, '-7'); ?>>(GMT-07:00) Mountain Time (US & Canada)</option>
                                                        <option value="-6" <?php selected($timezone, '-6'); ?>>(GMT-06:00) Central Time (US & Canada)</option>
                                                        <option value="-5" <?php selected($timezone, '-5'); ?>>(GMT-05:00) Eastern Time (US & Canada)</option>
                                                        <option value="-4" <?php selected($timezone, '-4'); ?>>(GMT-04:00) Atlantic Time (Canada)</option>
                                                        <option value="0" <?php selected($timezone, '0'); ?>>(GMT) Greenwich Mean Time</option>
                                                        <option value="1" <?php selected($timezone, '1'); ?>>(GMT+01:00) Central European Time</option>
                                                        <option value="2" <?php selected($timezone, '2'); ?>>(GMT+02:00) Eastern European Time</option>
                                                        <option value="5.5" <?php selected($timezone, '5.5'); ?>>(GMT+05:30) India Standard Time</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="currency" class="form-label">Currency</label>
                                                    <select id="currency" name="currency" class="select2 form-select">
                                                        <option value="">Select Currency</option>
                                                        <option value="usd" <?php selected($currency, 'usd'); ?>>USD</option>
                                                        <option value="euro" <?php selected($currency, 'euro'); ?>>Euro</option>
                                                        <option value="pound" <?php selected($currency, 'pound'); ?>>Pound</option>
                                                        <option value="bitcoin" <?php selected($currency, 'bitcoin'); ?>>Bitcoin</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
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
    
    // Avatar upload functionality
    const accountUserImage = document.getElementById('uploadedAvatar');
    const fileInput = document.querySelector('.account-file-input');
    const resetFileInput = document.querySelector('.account-image-reset');

    if (fileInput) {
        fileInput.onchange = () => {
            if (fileInput.files[0]) {
                accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
            }
        };
    }

    if (resetFileInput) {
        resetFileInput.onclick = () => {
            fileInput.value = '';
            accountUserImage.src = '<?php echo esc_url($avatar_url); ?>';
        };
    }
});
</script>

<?php
// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && wp_verify_nonce($_POST['_wpnonce'], 'update_user_' . $user_id)) {
    // Update WordPress user data
    wp_update_user([
        'ID' => $user_id,
        'first_name' => sanitize_text_field($_POST['first_name']),
        'last_name' => sanitize_text_field($_POST['last_name']),
        'display_name' => sanitize_text_field($_POST['display_name']),
        'user_email' => sanitize_email($_POST['user_email']),
    ]);
    
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
    wp_redirect(add_query_arg(['id' => $user_id, 'updated' => 1], get_permalink(get_page_by_path('user-view'))));
    exit;
}

get_footer();
?>
