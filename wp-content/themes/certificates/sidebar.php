<?php
/**
 * The sidebar containing the main widget area
 *
 * @package Astra Child
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar p-0 rounded shadow-sm position-sticky" role="complementary">
    <div class="sidebar-inner">
        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="<?php echo site_url('/dashboard/'); ?>">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo site_url('/create-client/'); ?>">
                        <i class="fas fa-plus"></i> Create Client
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo site_url('/all-clients/'); ?>">
                        <i class="fas fa-users"></i> All Clients
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo site_url('/auditors/'); ?>">
                        <i class="fas fa-user"></i> Auditors
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo site_url('/reports/'); ?>">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo wp_logout_url(site_url()); ?>">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
        <?php dynamic_sidebar('sidebar-1'); ?>
    </div>
</aside><!-- #secondary -->