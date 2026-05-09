<?php
/*
Template Name: Left Sidebar Menu
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
                <div class=" flex-grow-1">



                    <!-- User Profile Content -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Auditors</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    while (have_posts()) : the_post();
                                        the_content();
                                    endwhile;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <?php get_template_part('template-parts/content-footer'); ?>

<?php get_footer(); ?>