<?php
/**
 * IMS – F-01 Application Form Template
 * Adapted from QMS-F01 with IMS specific fields
 */
if (!defined('ABSPATH')) exit;

// ──────────────────────────────────────────────
// Helper: get field value with fallback
// ──────────────────────────────────────────────
function ims01_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return (!empty($val) && !is_array($val)) ? esc_html($val) : '';
}

// Helper: checkbox symbol
function ims01_checkbox($checked = false) {
    return $checked ? '[X]' : '[  ]';
}

// ──────────────────────────────────────────────
// Pull all fields
// ──────────────────────────────────────────────
$org_name          = ims01_field('organization_name', $post_id);

// Address
$addr_grp    = get_field('address', $post_id) ?: [];
$head_office = esc_html($addr_grp['head_office']          ?? '');
$main_site   = esc_html($addr_grp['main_operative_site']  ?? '');
$other_sites = esc_html($addr_grp['other_sites']          ?? '');

// Organisation type
$org_type_val      = get_field('organization_type', $post_id);

// Previous certification
$client_type_val   = get_field('previous_certification', $post_id);
$is_initial        = ($client_type_val === 'Initial Certification');
$is_recert         = ($client_type_val === 'Re-Certification');
$is_trans_surv     = ($client_type_val === 'Transfer at Surveillance');
$is_trans_recert   = ($client_type_val === 'Transfer at Re Certification');

// Top Management
$top_mgmt  = get_field('contact_person', $post_id) ?: [];
$tm_name   = esc_html($top_mgmt['top_management']     ?? '');
$tm_mobile = esc_html($top_mgmt['mobile_number']      ?? '');

// Contact Person (F-01 specific)
$f01cp       = get_field('f01contact_person', $post_id) ?: [];
$cp_name     = esc_html($f01cp['contact_person_name'] ?? '');
$cp_position = esc_html($f01cp['contact_position']    ?? '');
$cp_mobile   = esc_html($f01cp['contact_mobile']      ?? '');
$cp_fax      = esc_html($f01cp['fax']                 ?? '');
$cp_tel      = esc_html($f01cp['tel']                 ?? '');
$cp_email    = esc_html($f01cp['contact_email']       ?? '');
$cp_website  = esc_html($f01cp['website']             ?? '');

// Certification & Scope
$products_services  = ims01_field('products_services', $post_id);
$scope_cert         = ims01_field('scope_of_certification', $post_id);
$cert_scheme        = ims01_field('cert_scheme', $post_id);
$accreditation      = ims01_field('accreditation', $post_id);
$exclusions         = ims01_field('exclusions_only_for_iso_9001', $post_id);
$justification      = ims01_field('exclusions_only_for_iso_9002_Justification', $post_id);
$other_standards    = ims01_field('is_your_organization_certified_by_gmcspl_with_any_other_standards', $post_id);
$seasonal           = ims01_field('if_your_business_is_seasonal_please_indicate_working_period_with_full_strength_of_employees', $post_id);
$safety             = ims01_field('safety_conditions_if_applicable', $post_id);
$customer_premises  = ims01_field('please_indicate_any_activitiesservices_that_performed_at_your_customer_premises', $post_id);
$ict_used           = ims01_field('types_and_extent_ict_used_by_the_organization_and_competency_level', $post_id);
$applicable_legal   = ims01_field('applicable_legal_and_statutory_requirements', $post_id);
$compliance_val     = get_field('compliance', $post_id);

// LEVEL OF INTEGRATION (IMS only)
$int_grp    = get_field('level_of_integration', $post_id) ?: [];
$int_docs   = esc_html($int_grp['if_documents_for_all_systems_are_integrated'] ?? '');
$int_mrm    = esc_html($int_grp['2_if_management_review_is_common_for_all_systems'] ?? '');
$int_audit  = esc_html($int_grp['3if_internal_audit_is_covering_all_systems_under_ims'] ?? '');
$int_policy = esc_html($int_grp['4if_policy_&_objectives_are_integrated_inder_ims'] ?? '');
$int_capa   = esc_html($int_grp['5_if_corrective_and_preventive_action_measurement_and_continual_improvement_are_integrated'] ?? '');
$int_ops    = esc_html($int_grp['6_are_systems_processes_are_integrated_under_ims'] ?? '');
$int_resp   = esc_html($int_grp['7are_support_and_responsibilities_are_integrated_under_ims'] ?? '');
$hira       = esc_html($int_grp['attach_hira_if_available'] ?? '');

// Already Certified
$already_cert_group = get_field('field_68173ed2b229a', $post_id);
$already_cert_val   = isset($already_cert_group['alrady_certified'])         ? $already_cert_group['alrady_certified']         : '';
$already_cert_det   = isset($already_cert_group['already_certified_details']) ? esc_html($already_cert_group['already_certified_details']) : '';

// Internal Audit & MRM
$audit_mrm  = get_field('internal_audit_&_mrm', $post_id);
$ia_status  = isset($audit_mrm['internal_audit_status']) ? $audit_mrm['internal_audit_status'] : '';
$ia_date    = isset($audit_mrm['internal_audit_date'])   ? esc_html($audit_mrm['internal_audit_date'])   : '';
$mrm_status = isset($audit_mrm['mrm_status'])            ? $audit_mrm['mrm_status']            : '';
$mrm_date   = isset($audit_mrm['mrm-audit_date'])        ? esc_html($audit_mrm['mrm-audit_date'])        : '';

// Matrix & EPME (kept identical to QMS)
$epme_dynamic_val = get_field('epme_dynamic', $post_id);
$epme_matrix      = get_field('epme_matrix',  $post_id);
$epme_raw = null;
if ( !empty($epme_dynamic_val) && is_array($epme_dynamic_val) && isset($epme_dynamic_val['matrix']) ) {
    $epme_raw = $epme_dynamic_val;
} elseif ( !empty($epme_matrix) && is_array($epme_matrix) && isset($epme_matrix['matrix']) ) {
    $epme_raw = $epme_matrix;
}
$epme_source = (array)($epme_raw['matrix'] ?? []);
$epme_depts  = [];
if ($epme_raw) {
    foreach ( array_merge((array)($epme_raw['office_depts'] ?? []),(array)($epme_raw['main_depts'] ?? []),(array)($epme_raw['temp_depts'] ?? [])) as $d ) {
        $d = trim($d);
        if ($d !== '' && !in_array($d, $epme_depts)) $epme_depts[] = $d;
    }
}
$col_keys = ['Off1st','Off2nd','Off3rd','mos1st','mos2nd','mos3rd','ts1st','ts2nd','ts3rd'];
$epme_fixed_rows = ['Part time'=>'Part time','Temporary'=>'Temporary','Contract'=>'Contract','Others @@'=>'Others@@','Out of above how many are working away from Organization.'=>'Out of above how many are working away from Organization'];

// Transfer details
$transfer_val      = get_field('for_transferring_certification_from_other_certification_body__', $post_id);
$transfer_group    = get_field('field_684813198bf27', $post_id);
$trans_cb          = esc_html($transfer_group['name_of_cb_attach_certificate'] ?? '');
$trans_audit       = esc_html($transfer_group['latest_audit_initialsurveillancere_certification_attach_report'] ?? '');

// Logo
$logo_url = plugins_url('assets/images/logo.jpg', dirname(__FILE__));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 11pt; color: #000; line-height: 1.3; margin: 0; }
    @page { size: A4; margin: 10mm 13mm; }
    html { margin: 10mm 15mm; }
    .page-break { page-break-before: always; }
    .header-table { width: 100%; border: 1.5px solid #000; border-collapse: collapse; margin-bottom: 5px; }
    .header-table td { padding: 4px 6px; vertical-align: middle; }
    .header-logo { width: 85px; text-align: center; border-right: 1.5px solid #000; }
    .header-logo img { width: 75px; }
    .header-company { text-align: center; }
    .header-company .co-name { font-size: 12pt; font-weight: bold; }
    .header-company .co-addr { font-size: 8.5pt; }
    .form-title { text-align: center; font-size: 13pt; font-weight: bold; margin: 3px 0; }
    .notes { font-size: 9.5pt; margin-bottom: 5px; }
    .notes ol { margin-left: 18px; margin-top: 2px; }
    table.form-table { width: 100%; border-collapse: collapse; margin-bottom: 0; border: 1px solid #000; }
    table.form-table td, table.form-table th { border: 1px solid #000; padding: 4px 6px; vertical-align: middle; font-size: 10.5pt; }
    table.form-table th { background-color: #e8e8e8; font-weight: bold; text-align: center; }
    .lbl { font-weight: normal; width: 28%; }
    .lbl-narrow { width: 22%; }
    .section-header td { background: #000; color: #fff; font-weight: bold; text-align: center; padding: 4px; font-size: 11pt; }
    .val { vertical-align: top; }
    .h16 { height: 14px; } .h24 { height: 20px; } .h32 { height: 28px; } .h48 { height: 38px; }
    .cb-row td { border: 1px solid #000; padding: 4px 8px; }
    .cb { font-size: 11pt; font-family: Arial, sans-serif; margin-right: 2px; }
    .page-footer-bar { width: 100%; border-top: 1.5px solid #000; margin-top: 6px; font-size: 9pt; position: fixed; bottom: 5mm; left: 5mm; right: 5mm; display: table; }
    .page-footer-bar .fl, .page-footer-bar .fc, .page-footer-bar .fr { display: table-cell; padding: 2px 4px; }
    .page-footer-bar .fl { text-align: left; width: 33%; }
    .page-footer-bar .fc { text-align: center; width: 34%; }
    .page-footer-bar .fr { text-align: right; width: 33%; }
    .matrix-hdr { background: #d0d0d0; font-weight: bold; font-size: 9pt; text-align: center; }
    .matrix-row-lbl { font-size: 9pt; }
    .matrix-cell { text-align: center; font-size: 9pt; min-width: 18px; }
    .note-text { font-size: 8.5pt; font-style: italic; padding: 3px 5px; border: 1px solid #000; }
</style>
</head>
<body>

<?php
function ims01_footer($page, $total = 6) {
    echo '<div class="page-footer-bar">';
    echo '<span class="fl">Global MCS</span>';
    echo '<span class="fc">' . $page . '/' . $total . '</span>';
    echo '<span class="fr">F-01 IMS (Version 1.00, 11.04.2024)</span>';
    echo '</div>';
}
?>

<!-- PAGE 1 -->
<table class="header-table">
    <tr>
        <td class="header-logo" rowspan="2">
             <img src="data:image/jpeg;base64,...(same logo base64)..." />
        </td>
        <td class="header-company">
            <div class="co-name">GLOBAL MANAGEMENT CERTIFICATION SERVICES PVT. LTD.</div>
            <div class="co-addr">Flat No.402, Plot No.410, Matrusri nagar, Miyapur, Hyderabad-500049, India.</div>
            <div class="co-addr">Phone No.:040-4855 9001, E-mail: info@mcsglobal.in Website: www.mcsglobal.in</div>
        </td>
    </tr>
    <tr><td class="form-title">APPLICATION FOR CERTIFICATION (IMS)</td></tr>
</table>

<div class="notes">
    <ol>
        <li>Please fill correctly to enable us understand your requirements and issue a formal offer.</li>
        <li>No information shall be disclosed to any third party without written consent of customer.</li>
    </ol>
</div>

<table class="form-table" style="margin-bottom:4px;">
    <tr class="cb-row">
        <td><span class="cb"><?= ims01_checkbox($is_initial) ?></span> Initial Certification</td>
        <td><span class="cb"><?= ims01_checkbox($is_recert) ?></span> Re- Certification</td>
        <td><span class="cb"><?= ims01_checkbox($is_trans_surv) ?></span> Transfer at Surveillance</td>
        <td><span class="cb"><?= ims01_checkbox($is_trans_recert) ?></span> Transfer at Re Certification</td>
    </tr>
</table>

<table class="form-table">
    <tr class="section-header"><td colspan="4">GENERAL INFORMATION</td></tr>
    <tr><td class="lbl">Organization Name</td><td colspan="3" class="val h16"><?= $org_name ?></td></tr>
    <tr>
        <td class="lbl">Organization Type</td>
        <td colspan="3">
            <?php $ot = strtolower((string)$org_type_val); ?>
            <span class="cb"><?= ims01_checkbox($ot === 'company') ?></span> Company &nbsp;
            <span class="cb"><?= ims01_checkbox($ot === 'partnership') ?></span> Partnership &nbsp;
            <span class="cb"><?= ims01_checkbox($ot === 'proprietorship') ?></span> Proprietorship &nbsp;
            <span class="cb"><?= ims01_checkbox($ot === 'other') ?></span> Other
        </td>
    </tr>
    <tr><td class="lbl">Top Management</td><td class="val h24" style="width:40%"><?= $tm_name ?></td><td class="lbl" style="width:12%">Mobile</td><td class="val h24"><?= $tm_mobile ?></td></tr>
    <tr><td class="lbl">Head Office</td><td colspan="3" class="val h32"><?= $head_office ?></td></tr>
    <tr><td class="lbl">Main Operative Site</td><td colspan="3" class="val h32"><?= $main_site ?></td></tr>
    <tr><td class="lbl">Other Sites</td><td colspan="3" class="val h24"><?= $other_sites ?></td></tr>
    <tr><td class="lbl">Products/ Services</td><td colspan="3" class="val h48"><?= $products_services ?></td></tr>
    <tr><td class="lbl">Scope of Certification</td><td colspan="3" class="val h48"><?= $scope_cert ?></td></tr>
    <tr>
        <td class="lbl">Certification Scheme</td>
        <td colspan="3" class="val h16">
            <?php
            $is9001 = stripos((string)$cert_scheme, '9001') !== false;
            $is14001 = stripos((string)$cert_scheme, '14001') !== false;
            $is45001 = stripos((string)$cert_scheme, '45001') !== false;
            ?>
            <span class="cb"><?= ims01_checkbox($is9001) ?></span> ISO 9001 &nbsp;
            <span class="cb"><?= ims01_checkbox($is14001) ?></span> ISO 14001 &nbsp;
            <span class="cb"><?= ims01_checkbox($is45001) ?></span> ISO 45001 &nbsp;
            IMS Integrated
        </td>
    </tr>
</table>
<?php ims01_footer(1); ?>

<div class="page-break"></div>
<!-- Page 2 (similar to QMS) -->
<table class="form-table">
    <tr><td class="lbl">Accreditation</td><td class="val h16"><?= $accreditation ?></td></tr>
    <tr><td class="lbl">Internal Audit (IMS)</td>
        <td>
            Planned: <?= ims01_checkbox($ia_status === 'planned') ?> &nbsp; Completed: <?= ims01_checkbox($ia_status === 'completed') ?> &nbsp; Date: <?= $ia_date ?>
        </td>
    </tr>
    <tr><td class="lbl">MRM (IMS)</td>
        <td>
            Planned: <?= ims01_checkbox($mrm_status === 'planned') ?> &nbsp; Completed: <?= ims01_checkbox($mrm_status === 'completed') ?> &nbsp; Date: <?= $mrm_date ?>
        </td>
    </tr>
    <!-- Add more QMS fields as needed -->
</table>
<?php ims01_footer(2); ?>

<div class="page-break"></div>
<!-- PAGE 6: LEVEL OF INTEGRATION (IMS UNIQUE) -->
<table class="form-table">
    <tr class="section-header"><td colspan="2">LEVEL OF INTEGRATION (For IMS only)</td></tr>
    <tr><td colspan="2" class="note-text">Please mark on the scale of 1 to 5. (1 being the lowest and 5 being the highest)</td></tr>
    <tr><td class="lbl" style="width:70%">1. If documents for all systems are integrated</td><td class="val"><?= $int_docs ?></td></tr>
    <tr><td class="lbl">2. If management review is common for all systems</td><td class="val"><?= $int_mrm ?></td></tr>
    <tr><td class="lbl">3. If internal audit is covering all systems under IMS</td><td class="val"><?= $int_audit ?></td></tr>
    <tr><td class="lbl">4. If policy & Objectives are integrated inder IMS</td><td class="val"><?= $int_policy ?></td></tr>
    <tr><td class="lbl">5. If Corrective and preventive action, measurement and improvement are integrated</td><td class="val"><?= $int_capa ?></td></tr>
    <tr><td class="lbl">6. Are systems processes are integrated under IMS</td><td class="val"><?= $int_ops ?></td></tr>
    <tr><td class="lbl">7. Are Support and responsibilities are integrated under IMS</td><td class="val"><?= $int_resp ?></td></tr>
    <tr><td class="lbl">Attach HIRA if available</td><td class="val"><?= $hira ?></td></tr>
</table>
<?php ims01_footer(6); ?>

</body>
</html>
