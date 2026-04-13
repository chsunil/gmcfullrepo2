<?php
/**
 * IMS – F-02 Review of Application Template
 * Adapted from QMS-F02 with IMS branding
 */
if (!defined('ABSPATH')) exit;

// ── helpers ───────────────────────────────────────────────────────────────────
function imsf02v($key, $pid) {
    $v = get_field($key, $pid);
    if (is_array($v)) {
        if (isset($v['display_name'])) return esc_html($v['display_name']);
        foreach ($v as $val) if (!empty($val) && is_string($val)) return esc_html($val);
        return '';
    }
    return (!empty($v)) ? esc_html($v) : '';
}
function imsf02cb($checked = false) { return $checked ? '[X]' : '[  ]'; }
function ims_user_name($val) {
    if (empty($val)) return '';
    if (is_array($val) && isset($val['display_name'])) return esc_html($val['display_name']);
    if (is_numeric($val)) {
        $u = get_userdata((int)$val);
        return $u ? esc_html($u->display_name) : '';
    }
    return esc_html($val);
}
function ims_audit_date($meta_key, $post_id, $format = 'd/m/Y', $fallback = '') {
    $v = get_post_meta($post_id, $meta_key, true);
    if (!empty($v)) { $ts = strtotime($v); return $ts ? date($format, $ts) : esc_html($v); }
    return $fallback;
}

// ── pull all ACF fields ───────────────────────────────────────────────────────
$org            = imsf02v('organization_name', $post_id);
$cert_scheme    = imsf02v('cert_scheme', $post_id);
$tech_area      = imsf02v('technical_code_area', $post_id);
$client_type    = get_field('client_type', $post_id);
$tech_review    = get_field('tech_review_2', $post_id);
$conclusion_val = get_field('conclusion_of_the_contract_review', $post_id);

// Audit Man-days Calculation
$amc            = get_field('audit_man_days_calculation', $post_id);
$mds_p07        = isset($amc['mds_asper_table_p-07']) ? (float)$amc['mds_asper_table_p-07'] : '';
$red_time       = isset($amc['reduction_in_time']) ? (float)$amc['reduction_in_time'] : '';
$actual_md      = isset($amc['actual_man_days_md']) ? (float)$amc['actual_man_days_md'] : '';
$subtotal_md    = isset($amc['sub_total_md_st_md_rounded_to_total']) ? (float)$amc['sub_total_md_st_md_rounded_to_total'] : '';

// Initial Mandays
$init_md        = $subtotal_md;
$recert_md      = $init_md > 0 ? round($init_md * 2 / 3, 2) : '';
$surv_md        = $init_md > 0 ? round($init_md * 1 / 3, 2) : '';

// Audit team logic...
$pat            = get_field('proposed_audit_team', $post_id);
$team_leader    = ims_user_name($pat['team_leader'] ?? null);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.3; margin: 15px; }
    @page { size: A4; margin: 10mm 13mm; }
    .header-table { width: 100%; border: 1.5px solid #aaa; border-collapse: collapse; }
    .header-logo { width: 90px; text-align: center; border-right: 1.5px solid #aaa; }
    .header-logo img { width: 75px; }
    .form-title { font-size: 12pt; font-weight: bold; }
    table.ft { width: 100%; border-collapse: collapse; margin-top: 4px; }
    table.ft td, table.ft th { border: 1.5px solid #aaa; padding: 4px 6px; font-size: 10pt; }
    .page-footer-bar { width: 100%; border-top: 1.5px solid #aaa; margin-top: 6px; font-size: 9pt; display: table; }
    .fr { text-align: right; font-weight: bold; display: table-cell; }
</style>
</head>
<body>

<table class="header-table">
    <tr>
        <td class="header-logo" rowspan="3"><img src="data:image/jpeg;base64,...(same)"/></td>
        <td style="text-align:center;"><span class="form-title">Review of Application (IMS)</span></td>
    </tr>
    <tr><td style="text-align:right;"><span style="font-size:9pt;font-weight:bold;">F-02 IMS (Version 1.00, 11.04.2024)</span></td></tr>
</table>

<table class="ft">
    <tr><td>Organization</td><td><?= $org ?></td></tr>
    <tr><td>Standard(s)</td><td><?= $cert_scheme ?></td></tr>
</table>

<!-- Integration / Requirements Section... -->
<table class="ft">
    <tr>
        <th>S.No</th><th>Requirements</th><th>Review</th><th>Conclusion</th>
    </tr>
    <tr>
        <td>1</td><td>Sufficiency of information...</td><td>Reviewed using F-01 IMS</td><td>OK</td>
    </tr>
    <!-- ... More rows ... -->
</table>

<div class="page-break"></div>
<!-- Audit Time Section -->
<table class="ft">
    <tr><th colspan="6">Audit-Man-days Calculation (IMS Integrated)</th></tr>
    <tr>
        <th>Standard</th><th>Table P-07</th><th>Add. Time</th><th>Red. Time</th><th>Justification</th><th>Actual MD</th>
    </tr>
    <tr>
        <td><strong>IMS</strong></td>
        <td><?= $mds_p07 ?></td><td></td><td><?= $red_time ?></td><td></td><td><?= $actual_md ?></td>
    </tr>
</table>

<div class="page-footer-bar">
    <span class="fr">F-02 IMS (Version 1.00, 11.04.2024)</span>
</div>

</body>
</html>
