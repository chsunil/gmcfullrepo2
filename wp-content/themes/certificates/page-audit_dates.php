<?php
/*
 * Template Name: Audit Dates (Editable)
 * Template Post Type: page
 */// ── Resolve client ID ─────────────────────────────────────────────────────────
$client_id = isset($_GET['id']) ? intval($_GET['id']) : 0;// ── Audit date fields (label => base key) ─────────────────────────────────────
$audit_fields = [
    'Application Date'                                        => 'application_date',
    'Application Review Date'                                 => 'application_review_date',
    'Agreement'                                               => 'agreement_date',
    'Auditor Allocation'                                      => 'auditor_allocation',
    'Stage-1 Intimation Date / Yearly Surveillance Intimation'=> 'stage1_intimation',
    'Stage-1 Audit'                                           => 'stage1_audit',
    'Stage-2 Intimation Date / Surveillance Intimation Date'  => 'stage2_intimation',
    'Stage-2 Audit / Surveillance Audit Date'                 => 'stage2_audit',
    'Certification Decision Date'                             => 'certification_decision',
    'Internal Audit Date'                                     => 'internal_audit',
    'MRM Date'                                                => 'mrm_date',
    'Certificate Issue Date'                                  => 'certificate_issue',
    'Certificate Expiry Date'                                 => 'certificate_expiry',
];// ── ACF date_picker sync map (audit meta key => ACF field key) ─────────────────
// ACF date_picker stores raw as Ymd (20260227); audit dates page uses Y-m-d (2026-02-27)
$gmc_acf_date_map = [
    'application_review_date_initial' => 'f2reviweddate',
    'stage1_audit_initial'            => 'initial_audit_to_be_held_in',
    'stage2_audit_surv1'              => '1st_surveillance_in_',
    'stage2_audit_surv2'              => '2nd_Surveillance_in',
    'internal_audit_initial'          => 'internal_audit_date',
    'mrm_date_initial'                => 'mrm-audit_date',
];// Convert ACF raw Ymd → Y-m-d for <input type="date">
function gmc_acf_to_iso($raw) {
    if (preg_match('/^\d{8}$/', $raw)) {
        return substr($raw, 0, 4) . '-' . substr($raw, 4, 2) . '-' . substr($raw, 6, 2);
    }
    return $raw; // already Y-m-d or empty
}// ── Read: post meta first, fall back to ACF field if empty ────────────────────
function gmc_get_audit_date($meta_key, $post_id) {
    global $gmc_acf_date_map;
    $v = get_post_meta($post_id, $meta_key, true);
    if (!empty($v)) return $v;    if (isset($gmc_acf_date_map[$meta_key])) {
        $acf_raw = get_post_meta($post_id, $gmc_acf_date_map[$meta_key], true);
        if (!empty($acf_raw)) return gmc_acf_to_iso($acf_raw);
    }
    return '';
}// ── Write: save to post meta + write-through to ACF field ─────────────────────
function gmc_set_audit_date($meta_key, $value, $post_id) {
    global $gmc_acf_date_map;
    update_post_meta($post_id, $meta_key, sanitize_text_field($value));    // Keep ACF field in sync (ACF stores as Ymd without dashes)
    if (!empty($value) && isset($gmc_acf_date_map[$meta_key])) {
        update_post_meta($post_id, $gmc_acf_date_map[$meta_key], str_replace('-', '', $value));
    }
}// ── Handle POST save BEFORE any output (so wp_redirect works) ─────────────────
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['audit_dates_nonce']) &&
    wp_verify_nonce($_POST['audit_dates_nonce'], 'save_audit_dates') &&
    $client_id
) {
    foreach ($audit_fields as $base) {
        gmc_set_audit_date("{$base}_initial", $_POST["{$base}_initial"] ?? '', $client_id);
        gmc_set_audit_date("{$base}_surv1",   $_POST["{$base}_surv1"]   ?? '', $client_id);
        gmc_set_audit_date("{$base}_surv2",   $_POST["{$base}_surv2"]   ?? '', $client_id);
    }
    wp_redirect(add_query_arg(['updated' => '1', 'id' => $client_id]));
    exit;
}get_header();// ── Resolve notices ────────────────────────────────────────────────────────────
$saved = isset($_GET['updated']) && $_GET['updated'] === '1';
$no_id = !$client_id;// Client name for header
$client_name = $client_id ? get_the_title($client_id) : '';
?><!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">    <?php get_sidebar('custom'); ?>    <div class="layout-page">
      <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">          <?php if ($no_id): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
              <i class="bx bx-error fs-5"></i>
              <span>No client selected. Please open this page from a client record (e.g. <code>?id=CLIENT_ID</code>).</span>
            </div>          <?php else: ?>            <?php if ($saved): ?>
              <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2" role="alert">
                <i class="bx bx-check-circle fs-5"></i>
                <span>Audit dates saved successfully.</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="mb-0">Audit Dates</h5>
                  <?php if ($client_name): ?>
                    <small class="text-muted"><?= esc_html($client_name) ?></small>
                  <?php endif; ?>
                </div>
                <?php
                $url = add_query_arg( array(
    'new_post_id' => $client_id
), site_url( 'create-client/' ) );?>               
                <a href="<?= esc_url($url) ?>" class="btn btn-sm btn-outline-secondary">
                  <i class="bx bx-arrow-back me-1"></i>Back to Client
                </a>
              </div>              <div class="card-body p-0">
                <form method="post">
                  <?php wp_nonce_field('save_audit_dates', 'audit_dates_nonce'); ?>                  <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th style="width:3%;">#</th>
                          <th style="width:30%;">Audit Item</th>
                          <th>Initial</th>
                          <th>Surveillance-1</th>
                          <th>Surveillance-2</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $i = 1; foreach ($audit_fields as $label => $base):
                            $v1 = esc_attr(gmc_get_audit_date("{$base}_initial", $client_id));
                            $v2 = esc_attr(gmc_get_audit_date("{$base}_surv1",   $client_id));
                            $v3 = esc_attr(gmc_get_audit_date("{$base}_surv2",   $client_id));
                        ?>
                        <tr>
                          <td class="text-muted small align-middle"><?= $i++ ?></td>
                          <td class="align-middle fw-medium"><?= esc_html($label) ?></td>
                          <td><input type="date" class="form-control form-control-sm" name="<?= $base ?>_initial" value="<?= $v1 ?>"></td>
                          <td><input type="date" class="form-control form-control-sm" name="<?= $base ?>_surv1"   value="<?= $v2 ?>"></td>
                          <td><input type="date" class="form-control form-control-sm" name="<?= $base ?>_surv2"   value="<?= $v3 ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div><!-- /.table-responsive -->                  <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="<?= esc_url(add_query_arg('id', $client_id)) ?>" class="btn btn-outline-secondary">
                      <i class="bx bx-reset me-1"></i>Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                      <i class="bx bx-save me-1"></i>Save Audit Dates
                    </button>
                  </div>
                </form>
              </div><!-- /.card-body -->
            </div><!-- /.card -->          <?php endif; ?>        </div><!-- /.container-xxl -->        <?php get_template_part('template-parts/content-footer'); ?>      </div><!-- /.content-wrapper -->
    </div><!-- /.layout-page -->
  </div><!-- /.layout-container -->
</div><!-- /.layout-wrapper --><?php
// Enqueue Flatpickr for dd/mm/yyyy display
wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13');
wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], '4.6.13', true);
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="date"]').forEach(function (el) {
        el.type = 'text'; // convert so Flatpickr takes over
        flatpickr(el, {
            dateFormat   : 'Y-m-d',   // stored / submitted value
            altInput     : true,       // show a separate human-readable input
            altFormat    : 'd/m/Y',   // display as dd/mm/yyyy
            allowInput   : true,       // allow manual typing in the picker input
            parseDate    : function (dateStr, format) {
                // Parse dd/mm/yyyy typed manually
                var parts = dateStr.split('/');
                if (parts.length === 3) {
                    return new Date(parts[2], parts[1] - 1, parts[0]);
                }
                return new Date(dateStr);
            },
            onReady      : function (selectedDates, dateStr, instance) {
                // Set placeholder and allow direct typing on the alt input
                if (instance.altInput) {
                    instance.altInput.placeholder = 'dd/mm/yyyy';
                    instance.altInput.addEventListener('blur', function () {
                        var val = instance.altInput.value.trim();
                        if (!val) { instance.clear(); return; }
                        var parts = val.split('/');
                        if (parts.length === 3 && parts[0].length <= 2 && parts[1].length <= 2 && parts[2].length === 4) {
                            var d = new Date(parts[2], parts[1] - 1, parts[0]);
                            if (!isNaN(d)) instance.setDate(d, false, 'd/m/Y');
                        }
                    });
                }
            },
        });
    });
});
</script><?php get_footer(); ?>
