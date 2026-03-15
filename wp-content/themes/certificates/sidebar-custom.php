<?php
/**
 * Clean Sidebar – Client Workflow
 * Astra Child + Sneat
 */

// Load certification stages helper
require_once get_stylesheet_directory() . "/certification-stages.php";

// Resolve post ID - use new_post_id from URL parameter when editing a client
// Otherwise fall back to the current post ID
global $post;
if (isset($_GET['new_post_id']) && intval($_GET['new_post_id']) > 0) {
    $post_id = intval($_GET['new_post_id']);
    $is_new = false;  // Editing existing client
} else {
    $post_id = $post->ID ?? 0;
    $is_new = true;   // Creating new client (no post_id in URL)
}

// Resolve client context from the correct post ID
$client_stage = get_field("client_stage", $post_id) ?: "draft";
$certification_type = get_field("certification_type", $post_id) ?: "qms";

// Use ?stage= URL param to highlight the viewed stage; fall back to stored client_stage
$active_stage = (isset($_GET['stage']) && !empty($_GET['stage']))
    ? sanitize_text_field($_GET['stage'])
    : $client_stage;

// Detect client form template
$is_create_client = is_page_template("template-client-form.php");

$all_certification_stages = get_certification_stages();
$stages = $all_certification_stages[$certification_type] ?? [];

// IMPORTANT
?>

<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- App Brand Logo -->
    <div class="app-brand demo">
      <!--  <a href="<?php echo home_url(); ?>" class="app-brand-link">
             <span class="app-brand-logo demo">
                <span class="text-primary">
                    <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <path d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z" id="path-1"></path>
                            <path d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z" id="path-3"></path>
                            <path d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 0,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 0,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z" id="path-4"></path>
                            <path d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z" id="path-5"></path>
                        </defs>
                        <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                                <g id="Icon" transform="translate(27.000000, 15.000000)">
                                    <g id="Mask" transform="translate(0.000000, 8.000000)">
                                        <mask id="mask-2" fill="white"><use xlink:href="#path-1"></use></mask>
                                        <use fill="currentColor" xlink:href="#path-1"></use>
                                        <g id="Path-3" mask="url(#mask-2)">
                                            <use fill="currentColor" xlink:href="#path-3"></use>
                                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                        </g>
                                        <g id="Path-4" mask="url(#mask-2)">
                                            <use fill="currentColor" xlink:href="#path-4"></use>
                                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                        </g>
                                    </g>
                                    <g id="Triangle" transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000)">
                                        <use fill="currentColor" xlink:href="#path-5"></use>
                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </span>
            </span> 
           
        </a>-->
         <span class="app-brand-text demo menu-text fw-bold ms-2">
                <?php echo astra_logo(); ?>
            </span>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left align-middle"></i>
        </a>
    </div>

    <!-- <div class="menu-divider mt-0"></div> -->
    <!-- <div class="menu-inner-shadow"></div> -->
     <div class="mb-2"></div>

<?php if ($is_create_client): ?>

    <!-- CLIENT WORKFLOW SIDEBAR -->
    <div id="sidebar-workflow" class="menu-inner py-3">

        <!-- Back button -->
        <a href="javascript:void(0);"
           id="sidebar-back-btn"
           class="menu-link mx-3 mb-3 fw-semibold">
            ← Back
        </a>
        <?php 
        // echo "Sunil: " . $client_stage;
        $menuhighlight = $client_stage; ?>

        <!-- Workflow tabs -->
        <?php if (!empty($stages)): ?>
            <div class="mt-3">
                <?php
                if (empty($stages) || empty($client_stage)) {
                    echo '<div class="px-3 text-danger">Workflow data missing</div>';
                    return;
                }

                /**
                 * Define workflow groups and their stage keys
                 * Order follows the 'next' chain in certification-stages.php
                 */
                $workflow_groups = [
                    "Draft & Application" => ["draft", "f01", "f02", "f03"],
                    "Stage-1 Audit" => [
                        "f05", "f14", "sheet6", "f08", "f06", "f11", "f13", 
                        "f05a", "f07", "sheet12", "f08a", "f09", "f12", "f13a", 
                        "f16", "f17", "f24", "f19", "f15", "f25", "f10"
                    ],
                    "Surveillance-1" => [
                        "f69s1", "f05s1", "f14s1", "Sheet25", "f08s1", "f13s1", 
                        "f21s1", "f16s1", "f17s1", "f19s1", "f24s1", "f15s1"
                    ],
                    "Surveillance-2" => [
                        "f69s2", "f05s2", "f14s2", "sheet36", "f08s2", "f13s2", 
                        "f21s2", "f16s2", "f17s2", "f19s2", "f15s2", "f24s2"
                    ],
                ];

                /**
                 * Build visible stages and track enabled/disabled status:
                 * - Previous stages (before current): ENABLED
                 * - Current stage: ENABLED (and highlighted)
                 * - Future stages (after current): DISABLED
                 * 
                 * We follow the 'next' chain from 'draft' to build the correct order
                 */
                $visible_stages = [];
                $enabled_stages = [];  // Stages that are clickable (previous + current)
                
                // First, collect all visible stages (ones with ACF groups)
                foreach ($stages as $stage_key => $stage) {
                    if (!empty($stage["group"])) {
                        $visible_stages[] = $stage_key;
                    }
                }
                
                // Now follow the 'next' chain from 'draft' to build enabled stages
                // This ensures we follow the workflow order, not the array order
                $current_key = 'draft';
                $max_iterations = 100; // Safety limit to prevent infinite loops
                $iterations = 0;
                
                while ($current_key !== null && $iterations < $max_iterations) {
                    $iterations++;
                    
                    // Add this stage to enabled list if it exists and has a group
                    if (isset($stages[$current_key]) && !empty($stages[$current_key]["group"])) {
                        $enabled_stages[] = $current_key;
                    }
                    
                    // If this is the client's current stage, stop here
                    if ($current_key === $client_stage) {
                        break;
                    }
                    
                    // Move to the next stage in the workflow
                    $current_key = isset($stages[$current_key]['next']) ? $stages[$current_key]['next'] : null;
                }
                ?>

<ul class="menu-inner client-workflow-menu" id="clientTab" role="tablist">
<?php 
// echo $menuhighlight;
 ?>
<?php foreach ($workflow_groups as $group_title => $group_stages):

    // Filter only visible stages for this group
    $group_visible = array_values(
        array_intersect($group_stages, $visible_stages)
    );
    if (empty($group_visible)) {
        continue;
    }
    // Determine if this group should be open (based on viewed stage, not stored stage)
    $is_group_open = in_array($active_stage, $group_visible, true);
    ?>

    <li class="menu-item <?php echo $is_group_open ? "active open" : ""; ?>">
        <a href="javascript:void(0);" class="menu-link menu-toggle m-0">
            <!-- <i class="menu-icon tf-icons bx bx-detail"></i> -->
            <div class="text-truncate">
                <?php echo esc_html($group_title); ?>
            </div>
        </a>

        <ul class="menu-sub">

        <?php foreach ($group_visible as $stage_key):

            if (!isset($stages[$stage_key])) {
                continue;
            }

            $stage = $stages[$stage_key];
            $is_active = $stage_key === $active_stage;
            $is_enabled = in_array($stage_key, $enabled_stages, true);
            
            // Determine status: 'completed', 'current', or 'future'
            if ($is_active) {
                $status = 'current';
            } elseif ($is_enabled) {
                $status = 'completed';
            } else {
                $status = 'future';
            }
            ?>

            <?php 
            // Generate link URL if editing an existing client
            $link_url = 'javascript:void(0);';
            if (!$is_new) {
                $link_url = add_query_arg(
                    ['new_post_id' => $post_id, 'stage' => $stage_key],
                    get_permalink()
                );
            }
            ?>

            <li class="menu-item <?php echo $is_active ? 'active' : ''; ?> <?php echo !$is_enabled ? 'disabled' : ''; ?>" 
                data-status="<?php echo esc_attr($status); ?>">
              <a href="<?php echo esc_url($link_url); ?>"
                class="menu-link m-0 px-1<?php echo !$is_enabled ? 'is-locked text-muted' : ''; ?>"
                data-stage="<?php echo esc_attr($stage_key); ?>"
                <?php echo !$is_enabled ? 'aria-disabled="true"' : ''; ?>>
                <?php if ($status === 'completed'): ?>
                    <i class="bx bx-check-circle text-success me-1"></i>
                <?php elseif ($status === 'current'): ?>
                    <i class="bx bx-radio-circle-marked text-primary me-1"></i>
                <?php else: ?>
                    <i class="bx bx-lock-alt text-muted me-1"></i>
                <?php endif; ?>
                <?php echo esc_html($stage["title"]); ?>
              </a>
            </li>

        <?php
        endforeach; ?>

        </ul>
    </li>

<?php
endforeach; ?>

</ul>
            </div>
        <?php else: ?>
            <div class="px-3 text-muted">
                No workflow stages found.
            </div>
        <?php endif; ?>

    </div>

<?php endif; ?>

    <!-- ========================= -->
    <!-- MAIN MENU (EXISTING CODE) -->
    <!-- ========================= -->
    <div id="sidebar-main-menu" <?php echo $is_create_client
        ? 'style="display:none;"'
        : ""; ?>>

        <!-- 🔽 YOUR ORIGINAL MENU HTML STARTS HERE -->
        
        <!-- <div class="app-brand demo">
            <a href="<?php echo site_url(); ?>" class="app-brand-link">
                <span class="app-brand-text demo menu-text fw-bolder ms-2">GMC</span>
            </a>
        </div> -->

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">

            <li class="menu-item <?php echo is_page("dashboard")
                ? "active"
                : ""; ?>">
                <a href="<?php echo site_url(
                    "/dashboard/"
                ); ?>" class="menu-link mx-0">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            <li class="menu-item <?php echo is_page("all-clients")
                ? "active"
                : ""; ?>">
                <a href="<?php echo site_url(
                    "/all-clients/"
                ); ?>" class="menu-link mx-0">
                    <i class="menu-icon tf-icons bx bx-arch"></i>
                    <div>All Clients</div>
                </a>
            </li>

            <li class="menu-item <?php echo is_page("auditors")
                ? "active"
                : ""; ?>">
                <a href="<?php echo site_url(
                    "/users/"
                ); ?>" class="menu-link mx-0">
                    <i class="menu-icon tf-icons bx bx-group"></i>
                    <div>Auditors</div>
                </a>
            </li>

            <li class="menu-item <?php echo is_page("settings")
                ? "active"
                : ""; ?>">
                <a href="<?php echo site_url(
                    "/settings/"
                ); ?>" class="menu-link mx-0">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div>Settings</div>
                </a>
            </li>
<li class="menu-item <?php echo is_page("invoices")
                ? "active"
                : ""; ?>">
                <a href="<?php echo site_url(
                    "/invoices/"
                ); ?>" class="menu-link mx-0">
                    <i class="menu-icon tf-icons bx bx-receipt"></i>
                    <div>Invoices</div>
                </a>
            </li>
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Account</span>
            </li>

            <li class="menu-item">
                <a href="<?php echo site_url(
                    "/user-edit/?id=" . get_current_user_id()
                ); ?>" class="menu-link mx-0">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div>My Profile</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="<?php echo wp_logout_url(
                    site_url()
                ); ?>" class="menu-link mx-0">
                    <i class="menu-icon tf-icons bx bx-power-off"></i>
                    <div>Logout</div>
                </a>
            </li>

        </ul>

    </div>

    <!-- Collapse Toggle -->
    <div class="menu-toggle-wrapper px-2 py-2">
        <ul class="menu-inner p-0 m-0">
            <li class="menu-item w-100">
                <a href="javascript:void(0);" id="menu-toggle-btn" class="menu-link mx-0">
                    <i class="menu-icon tf-icons bx bx-chevron-left-circle" style="font-size: 1.4rem;"></i>
                    <div class="menu-text">Collapse Menu</div>
                </a>
            </li>
        </ul>
    </div>

</aside>

<?php if ($is_create_client): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const backBtn  = document.getElementById('sidebar-back-btn');
    const workflow = document.getElementById('sidebar-workflow');
    const mainMenu = document.getElementById('sidebar-main-menu');

    if (backBtn) {
        backBtn.addEventListener('click', function () {
            workflow.style.display = 'none';
            mainMenu.style.display = 'block';
        });
    }
   
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

  const panes = document.querySelectorAll('.tab-pane');

  document
    .querySelectorAll('.client-workflow-menu .menu-link[data-stage]')
    .forEach(function (link) {

      link.addEventListener('click', function () {

        const stage = this.dataset.stage;
        const target = document.getElementById(stage + '-pane');
        if (!target) return;

        // Hide all panes
        panes.forEach(p => {
          p.classList.remove('active', 'show');
        });

        // Show selected pane
        target.classList.add('active', 'show');

        // Update sidebar active state
        document
          .querySelectorAll('.client-workflow-menu .menu-item')
          .forEach(i => i.classList.remove('active'));

        this.closest('.menu-item')?.classList.add('active');

      });

    });

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

  const panes = document.querySelectorAll('.tab-pane');
  const sidebar = document.querySelector('.client-workflow-menu');

  if (!sidebar) return;

  // ---- Helpers
  const hideAllPanes = () => panes.forEach(p => p.classList.remove('active','show'));
  const showPane = (stage) => {
    const t = document.getElementById(stage + '-pane');
    if (!t) return;
    hideAllPanes();
    t.classList.add('active','show');
  };

  // ---- Lock future stages
  sidebar.querySelectorAll('.menu-item[data-status="future"] .menu-link')
    .forEach(a => {
      a.classList.add('is-locked');
      a.addEventListener('click', e => e.preventDefault());
    });

  // ---- Click handler (plain JS, no Bootstrap)
  sidebar.querySelectorAll('.menu-link[data-stage]')
    .forEach(link => {
      link.addEventListener('click', function () {
        const li = this.closest('.menu-item');
        if (!li || li.dataset.status === 'future') return;

        const stage = this.dataset.stage;

        // Pane switch
        showPane(stage);

        // Active state
        sidebar.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
        li.classList.add('active');

        // Persist open group
        const group = li.closest('[data-group]');
        if (group) localStorage.setItem('wf-open-group', group.dataset.group);

        // Auto-scroll
        li.scrollIntoView({behavior:'smooth', block:'center'});
      });
    });

  // ---- Restore accordion open state
  const savedGroup = localStorage.getItem('wf-open-group');
  if (savedGroup) {
    const grp = sidebar.querySelector('[data-group="'+savedGroup+'"]');
    if (grp) grp.classList.add('open','active');
  }

  // ---- Auto-open current stage on load
  const current = sidebar.querySelector('.menu-item[data-status="current"] .menu-link');
  if (current) {
      // Only auto-click if it's an internal JS link (SPA mode)
      // Real URLs (Multi-Step mode) are already loaded by the browser, clicking them causes a reload loop
      if (current.getAttribute('href') === 'javascript:void(0);') {
          current.click();
      }
  }

});
</script>

<?php endif; // End $is_create_client 
?>

<script>
/**
 * Sidebar Collapse Toggle & Persist
 */
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.layout-wrapper');
    const toggleBtn = document.getElementById('menu-toggle-btn');
    const COLLAPSED_CLASS = 'layout-menu-collapsed';
    const STORAGE_KEY = 'sidebar-collapsed-state';

    if (!wrapper || !toggleBtn) {
        console.warn('Sidebar Sidebar Toggle: wrapper or button not found');
        return;
    }

    // 1. Initial State from localStorage
    const savedState = localStorage.getItem(STORAGE_KEY);
    if (savedState === 'true') {
        wrapper.classList.add(COLLAPSED_CLASS);
    }

    // 2. Toggle Handler
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const isCollapsed = wrapper.classList.toggle(COLLAPSED_CLASS);
        localStorage.setItem(STORAGE_KEY, isCollapsed);
        
        // Trigger window resize for DataTables etc.
        window.dispatchEvent(new Event('resize'));
    });
});
</script>
