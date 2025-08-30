<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email'])) {

  // 1) Security
  if (! wp_verify_nonce($_POST['send_email_nonce'], 'send_email_action')) {
    echo '<div class="alert alert-warning">Security check failed.</div>';
    return;
  }

  // 2) Sanitize incoming data
  $to      = sanitize_email($_POST['to_email']);
  $subject = sanitize_text_field($_POST['subject']);
  $message = wp_kses_post($_POST['message']);
  $pdf_url = esc_url_raw($_POST['pdf_attachment']);

  // 3) Figure out the uploads folder on disk
  $uploads = wp_get_upload_dir();
  $baseurl = $uploads['baseurl'];  // e.g. http://localhost:10035/wp-content/uploads
  $basedir = $uploads['basedir'];  // e.g. D:\Local Sites\gmc\app\public\wp-content\uploads

  // 4) Convert the URL into a filesystem path
  if (strpos($pdf_url, $baseurl) === 0) {
    $pdf_path = str_replace($baseurl, $basedir, $pdf_url);
  } else {
    $pdf_path = '';
  }

  // 5) Prepare headers & attachments
  $headers     = ['Content-Type: text/html; charset=UTF-8'];
  $attachments = [];

  if ($pdf_path && file_exists($pdf_path)) {
    $attachments[] = $pdf_path;
  } else {
    error_log("Send Email: PDF not found at {$pdf_path}");
  }

  // 6) Send the mail
  $sent = wp_mail($to, $subject, nl2br($message), $headers, $attachments);

  // 7) Feedback
  if ($sent) {
    echo '<div class="alert alert-success">Email sent successfully!</div>';
  } else {
    echo '<div class="alert alert-danger">Failed to send email.</div>';
  }
}
?>

<?php
/**
 * Template Name: Multi-Step ACF Form with Tabs
 */

acf_form_head();
get_header();

// 1) URL params
$new_id      = $_GET['new_post_id'] ?? '';
$stage_param = sanitize_text_field($_GET['stage'] ?? 'draft');
$all_clients_url = site_url('/all-clients/');
// 2) All stages & current stage
$type   = get_field('certification_type', $new_id);
if (!$type) {
  $type = 'qms';
}
$stages = get_certification_stages()[$type] ?? [];

// 3) Determine $stage
$stage = isset($stages[$stage_param]) ? $stage_param : 'draft';

// 4) Figure out post ID for ACF
if ($stage === 'draft' && ! is_numeric($new_id)) {
  $acf_post_id   = 'new_post';
  $new_post_args = ['post_type' => 'client', 'post_status' => 'publish'];
  $real_post_id  = 0;
} else {
  $acf_post_id   = intval($new_id);
  $real_post_id  = $acf_post_id;
  $new_post_args = [];
}

// 5) Sync client_stage meta to the URL’s stage param
if ($real_post_id && $stage) {
  // Only update if it’s not already set
  $current = get_field('client_stage', $real_post_id);
  if ($current !== $stage) {
    update_field('client_stage', $stage, $real_post_id);
  }
}
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

          <!--  TAB NAV -->
          <div class="overflow-auto mb-3">
            <?php
            // Pass data to nav.php
            set_query_var('client_nav_args', [
              'stages'      => $stages,
              'stage'       => $stage,
              'real_post_id' => $real_post_id
            ]);
            get_template_part('template-parts/client/nav');
            ?>
          </div>

          <!--  TAB PANES -->
          <div class="tab-content" id="clientTabContent">
            <?php
            $keys = array_keys($stages);
            $idx = array_search($stage, $keys, true);
            $visible = array_slice($keys, 0, $idx + 1);
            foreach ($visible as $slug):
              $step = $stages[$slug];
              if (!empty($step['group'])) {
                $is_active = ($slug === $stage) ? 'show active' : '';
                echo '<div class="tab-pane fade ' . $is_active . '" id="' . $slug . '">';
                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . esc_html($step['title']) . '</h5>';
                // Use the correct post_id for each stage
                $pane_post_id = $real_post_id ? $real_post_id : $acf_post_id;
                $acf_args = [
                  'post_id'      => $pane_post_id,
                  'field_groups' => [$step['group']],
                  'submit_value' => 'Save',
                  'return' => false,
                  'updated_message' => 'Updated Succesfully ',
                  'form_attributes' => ['class' => 'row'],
                  'html_before_fields' => '<div class="acf-fields-container">',
                  'html_after_fields' => '</div>'
                ];
                if ($pane_post_id === 'new_post') {
                  $acf_args['new_post'] = $new_post_args;
                }
                set_query_var('client_stage_args', [
                  'acf_args'   => $acf_args,
                  'group'         => $step['group'],
                  'next_stage' => $step['next'] ?? '',
                  'real_post_id'  => $real_post_id,
                  'step_key'      => $slug
                ]);
                echo '<div class="row">';
                get_template_part('template-parts/client/stage', 'generic');
                get_template_part('template-parts/client/send-email-modal');
                echo '</div>'; // Close card-body
                echo '</div>'; // Close card
                echo '</div>'; // Close tab-pane
              }
            endforeach;
            ?>
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
  // Ensure ACF fields are properly initialized when tabs change
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize all ACF field groups on page load
    if (typeof acf !== 'undefined') {
      acf.doAction('ready');

      // Force ACF to show all field groups, even if empty
      const acfFieldGroups = document.querySelectorAll('.acf-field-group');
      acfFieldGroups.forEach(group => {
        if (group) {
          group.style.display = 'block';
        }
      });

      // Make sure empty containers are still visible
      const emptyContainers = document.querySelectorAll('.acf-fields-container');
      emptyContainers.forEach(container => {
        if (container && !container.innerHTML.trim()) {
          const message = document.createElement('div');
          message.className = 'alert alert-info';
          message.innerHTML = 'Please fill out the form fields below.';
          container.appendChild(message);
        }
      });
    }

    // Handle tab changes
    const tabEls = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabEls.forEach(tabEl => {
      tabEl.addEventListener('shown.bs.tab', event => {
        // Trigger ACF to refresh fields in the newly activated tab
        if (typeof acf !== 'undefined') {
          acf.doAction('ready');

          // Get the newly activated tab pane
          const targetId = event.target.getAttribute('data-bs-target') || event.target.getAttribute('href');
          const targetPane = document.querySelector(targetId);

          if (targetPane) {
            // Force ACF to show field groups in this tab
            const acfFieldGroups = targetPane.querySelectorAll('.acf-field-group');
            acfFieldGroups.forEach(group => {
              if (group) {
                group.style.display = 'block';
              }
            });
          }
        }
      });
    });
  });
</script>

<?php get_footer(); ?>