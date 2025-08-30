<?php
/*
Template Name: Client Settings
*/
acf_form_head();
get_header();
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
                                    <h5 class="mb-0"><?php echo get_the_title(); ?> </h5>
                                </div>
                                <div class="card-body">
            <?php
            // 3) Capability check
            if (! is_user_logged_in() || ! current_user_can('manage_options')) {
                echo '<p>You do not have permission to view this page.</p>';
                get_footer();
                exit;
            }

            // 4) Determine ACF “post” for current user
            $user_id = get_current_user_id();
            $post_id = 'user_' . $user_id;

            // 5) Try loading existing repeater data
            $nace_rows     = get_field('nace_codes',  $post_id) ?: [];
            $man_days_rows = get_field('man_days',    $post_id) ?: [];
            // Success message after save
            if (isset($_GET['saved'])) { ?>

                <div class="alert alert-warning alert-dismissible fade show" data-dismiss="alert" aria-label="Close" role="alert">
                    <strong>Settings saved.</strong>
                    <!-- <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button> -->
                </div>
            <?php  }

            // 6) If no data, show the ACF form to populate it


            // Replace 'group_client_settings' with your actual field‐group key
            acf_form([
                'post_id'      => $post_id,
                'field_groups' => ['group_client_settings'],
                'submit_value' => 'Save Settings',
                // reload page on save so we pick up the new data
                'return'       => add_query_arg('saved', '1', get_permalink())
            ]);


            // 7) Otherwise, render the two tables


            ?>

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