<?php
/*
Template Name: User View
*/
get_header();

$user_id = isset($_GET['id']) ? absint($_GET['id']) : 0;

$user = get_user_by('ID', $user_id);
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
                                    <h5 class="mb-0">User View : <?php echo esc_html($user->display_name); ?></h5>
                                    <small>View user account details</small>
                                </div>
                                <div class="card-body">
                                    <?php if ($user): ?>
                                        <!-- <h3><?php echo esc_html($user->display_name); ?></h3> -->
                                        <p><strong>Email:</strong> <?php echo esc_html($user->user_email); ?></p>
                                        <p><strong>Username:</strong> <?php echo esc_html($user->user_login); ?></p>
                                        <p><strong>Registered:</strong> <?php echo esc_html($user->user_registered); ?></p>
                                        <p><strong>Role:</strong> <?php echo implode(', ', $user->roles); ?></p>
                                        <p><strong>Support Type (NAC):</strong>
                                            <?php
                                            $nac_support = get_field('nac_support', 'user_' . $user_id);

                                            if (!empty($nac_support) && is_array($nac_support)) {
                                                echo '<p><strong>NAC Support:</strong> ' . implode(', ', array_map('esc_html', $nac_support)) . '</p>';
                                            } else {
                                                echo '<p><strong>NAC Support:</strong> None</p>';
                                            }
                                            ?></p>
                                        <p><strong>Profile Picture:</strong><br>
                                            <?php echo get_avatar($user_id, 96); ?>
                                        </p>
                                        <a href="<?php echo add_query_arg(['user_id' => $user_id], get_permalink(get_page_by_path('user-edit'))); ?>" class="btn btn-primary">Edit</a>
                                    <?php else: ?>
                                        <p>User not found.</p>
                                    <?php endif; ?>
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

<?php get_footer(); ?>