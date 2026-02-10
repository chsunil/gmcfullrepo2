<?php
function custom_login_redirect() {
    global $pagenow;
    if ($pagenow == 'wp-login.php' && !isset($_GET['action'])) {
        wp_redirect(home_url('/login'));
        exit;
    }

    // if ($pagenow == 'wp-login.php' && isset($_GET['action']) && $_GET['action'] == 'lostpassword') {
    //     wp_redirect(home_url('/forgot-password'));
    //     exit;
    // }
}
add_action('init', 'custom_login_redirect');
?>