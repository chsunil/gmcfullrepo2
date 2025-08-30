<?php
/*
 * Template Name: Audit Dates (Editable)
 * Template Post Type: page
 */

// ✅ Safe ACF check before calling acf_form_head()
if (function_exists('acf_form_head')) {
    acf_form_head();
}

get_header();

// Initialize error message and client_id (from URL param or current post ID)
$client_id = isset($_GET['id']) ? intval($_GET['id']) : get_the_ID();
$error = '';

if (!$client_id) {
    $error = 'Please append ?id=CLIENT_ID to the URL.';
}

if (!function_exists('get_field')) {
    echo '<div class="notice">ACF not active.</div>';
    get_footer();
    exit;
}

// Display error message if client ID is missing
if ($error) {
    echo '<div class="alert alert-danger" role="alert">' . esc_html($error) . '</div>';
}

?>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">
    <!-- Sidebar -->
    <?php get_sidebar('custom'); ?>

    <!-- Layout container -->
    <div class="layout-page">
      <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

          <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
              <?php echo esc_html($error); ?>
            </div>
          <?php endif; ?>

          <div class="row">
            <div class="col-xl-12">
              <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><?php echo get_the_title(); ?></h5>
                </div>
                <div class="card-body">
                  <?php echo '<div class="alert alert-success">✅ Audit Dates Saved Successfully.</div>'; ?>
                 <form method="post">
        <?php wp_nonce_field('save_audit_dates', 'audit_dates_nonce'); ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Audit Item</th>
                    <th>Initial</th>
                    <th>Surveillance-1</th>
                    <th>Surveillance-2</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Array for the audit date field names and labels
                $fields = [
                    'Application Date' => 'application_date',
                    'Application Review Date' => 'application_review_date',
                    'Agreement' => 'agreement_date',
                    'Auditor Allocation' => 'auditor_allocation',
                    'Stage-1 Intimation Date / Yearly Surveillance Intimation' => 'stage1_intimation',
                    'Stage-1 Audit' => 'stage1_audit',
                    'Stage-2 Intimation Date / Surveillance Intimation Date' => 'stage2_intimation',
                    'Stage-2 Audit / Surveillance Audit Date' => 'stage2_audit',
                    'Certification Decision Date' => 'certification_decision',
                    'Internal Audit Date' => 'internal_audit',
                    'MRM Date' => 'mrm_date',
                    'Certificate Issue Date' => 'certificate_issue',
                    'Certificate Expiry Date' => 'certificate_expiry'
                ];

                // Render each row with date inputs
                $i = 1;
                foreach ($fields as $label => $base) {
                    $v1 = esc_attr(get_field("{$base}_initial"));
                    $v2 = esc_attr(get_field("{$base}_surv1"));
                    $v3 = esc_attr(get_field("{$base}_surv2"));

                    echo "<tr>
                        <td>{$i}</td>
                        <td>{$label}</td>
                        <td><input class='form-control' type='date' name='{$base}_initial' value='{$v1}' class='form-control'></td>
                        <td><input class='form-control' type='date' name='{$base}_surv1' value='{$v2}' class='form-control'></td>
                        <td><input class='form-control' type='date' name='{$base}_surv2' value='{$v3}' class='form-control'></td>
                    </tr>";
                    $i++;
                }
                ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Save Audit Dates</button>
    </form>
</div>
<?php
// Handle save logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['audit_dates_nonce']) && wp_verify_nonce($_POST['audit_dates_nonce'], 'save_audit_dates')) {
    foreach ($fields as $label => $base) {
        update_field("{$base}_initial", sanitize_text_field($_POST["{$base}_initial"]));
        update_field("{$base}_surv1", sanitize_text_field($_POST["{$base}_surv1"]));
        update_field("{$base}_surv2", sanitize_text_field($_POST["{$base}_surv2"]));
    }

    // Redirect to avoid resubmission
    wp_redirect(add_query_arg('updated', 'true'));
    exit;
}

?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <footer class="content-footer footer bg-footer-theme">
          <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
            <div class="mb-2 mb-md-0">© <?php echo date('Y'); ?> GMC</div>
          </div>
        </footer>

        <div class="content-backdrop fade"></div>
      </div>
    </div>
  </div>

  <div class="layout-overlay layout-menu-toggle"></div>
</div>

<?php get_footer(); ?>
