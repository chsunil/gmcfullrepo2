<?php
/**
 * QMS – F-01 Application Form Template
 * Redesigned to match F-01 QMS (Version 5.00, 30.10.2023) reference PDF
 */
if (!defined('ABSPATH')) exit;

// ──────────────────────────────────────────────
// Helper: get field value with fallback
// ──────────────────────────────────────────────
function qms01_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return (!empty($val) && !is_array($val)) ? esc_html($val) : '';
}

// Helper: checkbox symbol — ASCII art style for DOMPDF compatibility
// DOMPDF cannot render &#9744;/&#9746; without DejaVu font; use plain text instead
function qms01_checkbox($checked = false) {
    return $checked ? '[X]' : '[  ]';
}

// ──────────────────────────────────────────────
// Pull all fields
// ──────────────────────────────────────────────
$org_name          = qms01_field('organization_name', $post_id);
$head_office       = qms01_field('head_office', $post_id);
$main_site         = qms01_field('main_operative_site', $post_id);
$other_sites       = qms01_field('other_sites', $post_id);

// Organisation type
$org_type_val      = get_field('organization_type', $post_id); // Select or text

// Client type / certification type
$client_type_val   = get_field('client_type', $post_id);
$is_initial        = ($client_type_val === 'initial');
$is_recert         = ($client_type_val === 're_certification');
$is_trans_surv     = ($client_type_val === 'transfer_surveillance');
$is_trans_recert   = ($client_type_val === 'transfer_recertification');

// Top management – try both prefixed and plain sub-field names
$tm_group = get_field('contact_person_top_management_group', $post_id);
// Fallback: try direct field keys known from ACF export
$tm_name       = qms01_field('top_management', $post_id);
$tm_mobile     = qms01_field('mobile_number', $post_id);
if (!$tm_name) {
    $tm_name   = qms01_field('contact_person_top_management', $post_id);
}
if (!$tm_mobile) {
    $tm_mobile = qms01_field('contact_person_mobile_number', $post_id);
}

// Contact Person group
$contact        = get_field('contact_person', $post_id);
$cp_name        = isset($contact['contact_person_name']) ? esc_html($contact['contact_person_name']) : '';
$cp_position    = isset($contact['contact_position'])    ? esc_html($contact['contact_position'])    : '';
$cp_mobile      = isset($contact['contact_mobile'])      ? esc_html($contact['contact_mobile'])      : '';
$cp_fax         = isset($contact['fax'])                 ? esc_html($contact['fax'])                 : '';
$cp_tel         = isset($contact['tel'])                 ? esc_html($contact['tel'])                 : '';
$cp_email       = isset($contact['contact_email'])       ? esc_html($contact['contact_email'])       : '';
$cp_website     = isset($contact['website'])             ? esc_html($contact['website'])             : '';

// Certification & Scope
$products_services  = qms01_field('products_services', $post_id);
$scope_cert         = qms01_field('scope_of_certification', $post_id);
$cert_scheme        = qms01_field('cert_scheme', $post_id);
$accreditation      = qms01_field('accreditation', $post_id);
$exclusions         = qms01_field('exclusions_only_for_iso_9001', $post_id);
$justification      = qms01_field('exclusions_only_for_iso_9002_Justification', $post_id);
$other_standards    = qms01_field('is_your_organization_certified_by_gmcspl_with_any_other_standards', $post_id);
$seasonal           = qms01_field('if_your_business_is_seasonal_please_indicate_working_period_with_full_strength_of_employees', $post_id);
$safety             = qms01_field('safety_conditions_if_applicable', $post_id);
$customer_premises  = qms01_field('please_indicate_any_activitiesservices_that_performed_at_your_customer_premises', $post_id);
$ict_used           = qms01_field('types_and_extent_ict_used_by_the_organization_and_competency_level', $post_id);
$applicable_legal   = qms01_field('applicable_legal_and_statutory_requirements', $post_id);
$compliance_val     = get_field('compliance', $post_id);

// Already Certified (group by field key)
$already_cert_group = get_field('field_68173ed2b229a', $post_id);
$already_cert_val   = isset($already_cert_group['alrady_certified'])         ? $already_cert_group['alrady_certified']         : '';
$already_cert_det   = isset($already_cert_group['already_certified_details']) ? esc_html($already_cert_group['already_certified_details']) : '';

// Already applicant for same scheme (unnamed group field_68173ed2b41ad)
$applicant_group        = get_field('field_68173ed2b41ad', $post_id);
$applicant_val          = isset($applicant_group['organization_already_an_applicant__certified_for_the_certification_scheme_you_have_applied_for']) ? $applicant_group['organization_already_an_applicant__certified_for_the_certification_scheme_you_have_applied_for'] : '';
$applicant_details      = isset($applicant_group['details'])           ? esc_html($applicant_group['details'])           : '';
$complaints_actions     = isset($applicant_group['complaints_actions']) ? esc_html($applicant_group['complaints_actions']) : '';
$regulatory_engagement  = isset($applicant_group['engagement_by_the_organization_with_regulatory_bodies']) ? esc_html($applicant_group['engagement_by_the_organization_with_regulatory_bodies']) : '';

// Internal Audit & MRM
$audit_mrm  = get_field('internal_audit_&_mrm', $post_id);
$ia_status  = isset($audit_mrm['internal_audit_status']) ? $audit_mrm['internal_audit_status'] : '';
$ia_date    = isset($audit_mrm['internal_audit_date'])   ? esc_html($audit_mrm['internal_audit_date'])   : '';
$mrm_status = isset($audit_mrm['mrm_status'])            ? $audit_mrm['mrm_status']            : '';
$mrm_date   = isset($audit_mrm['mrm-audit_date'])        ? esc_html($audit_mrm['mrm-audit_date'])        : '';

// Languages
$languages_group = get_field('languages_s', $post_id);
$lang_doc        = isset($languages_group['documentation']) ? esc_html($languages_group['documentation']) : '';
$lang_speak      = isset($languages_group['speaking'])      ? esc_html($languages_group['speaking'])      : '';

// Description of Technical Resources
$tech_group    = get_field('Description_of_Technical_resources', $post_id);
$tech_staff    = isset($tech_group['Machinery_and_Technical_Staff']) ? esc_html($tech_group['Machinery_and_Technical_Staff']) : '';

// Consultancy
$consult_group = get_field('Consultancy__Organization_consultant', $post_id);
$consult_data  = isset($consult_group['Consultancy__Organization_consultant_data']) ? esc_html($consult_group['Consultancy__Organization_consultant_data']) : '';
$self_prepared = isset($consult_group['self_prepared']) ? $consult_group['self_prepared'] : false;

// Outsourced Processes
$outsourced_list = get_field('outsourced_processes', $post_id);

// Process / Operations group
$ops_group  = get_field('process', $post_id);
$ops_office = isset($ops_group['Process_Operations_office'])                   ? esc_html($ops_group['Process_Operations_office'])                   : '';
$ops_mos    = isset($ops_group['Process_Operations_Main_Operative_Site_'])     ? esc_html($ops_group['Process_Operations_Main_Operative_Site_'])     : '';
$ops_ts     = isset($ops_group['Process_Operations_Temporary_Sites'])          ? esc_html($ops_group['Process_Operations_Temporary_Sites'])          : '';

// Major Machinery
$machinery_group = get_field('20:_Major_Machinery_Equipments', $post_id);
$mach_office     = isset($machinery_group['20:_Major_Machinery_Equipmentsoffice'])                    ? esc_html($machinery_group['20:_Major_Machinery_Equipmentsoffice'])                    : '';
$mach_mos        = isset($machinery_group['20:_Major_Machinery_Equipmentsmain_operative_site'])       ? esc_html($machinery_group['20:_Major_Machinery_Equipmentsmain_operative_site'])       : '';
$mach_ts         = isset($machinery_group['20:_Major_Machinery_Equipmentstemporary_sites_if_any'])    ? esc_html($machinery_group['20:_Major_Machinery_Equipmentstemporary_sites_if_any'])    : '';

// EPME matrix
$epme_matrix = get_field('epme_matrix', $post_id);

// Transfer
$transfer_val      = get_field('for_transferring_certification_from_other_certification_body__', $post_id);
$transfer_group    = get_field('field_684813198bf27', $post_id);
$trans_cb          = isset($transfer_group['name_of_cb_attach_certificate'])                                                  ? esc_html($transfer_group['name_of_cb_attach_certificate'])                                                  : '';
$trans_audit       = isset($transfer_group['latest_audit_initialsurveillancere_certification_attach_report'])                 ? esc_html($transfer_group['latest_audit_initialsurveillancere_certification_attach_report'])                 : '';
$trans_ab          = isset($transfer_group['name_of_ab_attach_certificate'])                                                  ? esc_html($transfer_group['name_of_ab_attach_certificate'])                                                  : '';
$trans_report      = isset($transfer_group['initial_certification_audit_re_certification_audit_report_attach_report'])        ? esc_html($transfer_group['initial_certification_audit_re_certification_audit_report_attach_report'])        : '';
$trans_reason      = qms01_field('reason_for_transfer', $post_id);
$trans_complaints  = qms01_field('any_complaints_from_regulatory_or_from_market', $post_id);
$cert_stage        = get_field('certiifcate_present_stage', $post_id); // array
$comm_to_cb        = qms01_field('have_you_made_any_communication_to_your_current_cb_for_authorizing_gmcspl_as_your_new_cb', $post_id);
$major_nc          = qms01_field('are_there_any_outstanding_major_non_conformities', $post_id);
$minor_nc          = qms01_field('are_there_any_outstanding_manor_non_conformities', $post_id);
$attach_audit_prog = get_field('attach_audit_program', $post_id);
$cb_contact        = qms01_field('provide_us_the_cb_contact_details_for_obtaining_the_program:', $post_id);
$not_avail_reason  = qms01_field('if_not_avalible_reasons:', $post_id);
$susp_val          = get_field('certificate_under_suspension_or_under_threat_of_suspension', $post_id);
$susp_reason       = qms01_field('state_reason', $post_id);
$branch_info       = get_field('information_about_branch_officesother_sites_to_be_certified', $post_id);
// Site 1
$site1_group = get_field('1st_site', $post_id);
$site1_addr  = isset($site1_group['address'])      ? esc_html($site1_group['address'])      : '';
$site1_act   = isset($site1_group['Activityies'])  ? esc_html($site1_group['Activityies'])  : '';
// Site 2 (uses same field name '1st_site' in JSON – second group key field_684816c98bf3b)
$site2_group = get_field('field_684816c98bf3b', $post_id);
$site2_addr  = isset($site2_group['address'])      ? esc_html($site2_group['address'])      : '';
$site2_act   = isset($site2_group['Activityies'])  ? esc_html($site2_group['Activityies'])  : '';
$desired_date = qms01_field('desired_date_of_audit', $post_id);

// Declaration
$ack_group   = get_field('field_685937513d0b0', $post_id);
$ack_name    = isset($ack_group['i_acknowledge_that_ :_']) ? esc_html($ack_group['i_acknowledge_that_ :_']) : '';
$ack_design  = isset($ack_group['designation:'])           ? esc_html($ack_group['designation:'])           : '';
$ack_sign    = isset($ack_group['signature'])              ? esc_html($ack_group['signature'])              : '';
$ack_date    = isset($ack_group['date:'])                  ? esc_html($ack_group['date:'])                  : '';

// Attachments
$attachments_val = get_field('attachments', $post_id); // array of selected values
$attach_opts = [
    'Previous Certificate (for transfer only)',
    'Previous Audit report (for transfer only)',
    'Other Useful information, if any.',
    'Copy of License / GST.',
    'Copy of CFO / Inspector of Factories.',
    'Copy Profile',
    'Major Client List',
];

// Logo path (plugin directory)
$logo_url = plugins_url('assets/images/logo.jpg', dirname(__FILE__));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; }
    body {
        font-family: Arial, sans-serif;
        font-size: 11pt;
        color: #000;
        line-height: 1.35;
        margin: 15px 15px 5px 15px;
    }

    /* ── Page layout — match f03.php working approach ── */
    @page {
        size: A4;
        margin: 5mm 5mm 5mm 5mm;
    }
    body {
        margin: 15px 15px 5px 15px;
    }

    .page-break { page-break-before: always; }

    /* ── Header ── */
    .header-table {
        width: 100%;
        border: 1.5px solid #000;
        border-collapse: collapse;
        margin-bottom: 5px;
    }
    .header-table td { padding: 4px 6px; vertical-align: middle; }
    .header-logo { width: 85px; text-align: center; border-right: 1.5px solid #000; }
    .header-logo img { width: 75px; }
    .header-company { text-align: center; }
    .header-company .co-name { font-size: 12pt; font-weight: bold; }
    .header-company .co-addr { font-size: 9pt; }
    .form-title { text-align: center; font-size: 13pt; font-weight: bold; margin: 3px 0; }

    /* ── Notes ── */
    .notes { font-size: 10pt; margin-bottom: 5px; }
    .notes ol { margin-left: 18px; }
    .notes ol li { margin-bottom: 2px; }

    /* ── Generic bordered table ── */
    table.form-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    table.form-table td, table.form-table th {
        border: 1px solid #000;
        padding: 4px 6px;
        vertical-align: middle;
        font-size: 11pt;
    }
    table.form-table th {
        background-color: #e8e8e8;
        font-weight: bold;
        text-align: center;
    }
    .lbl { font-weight: normal; width: 28%; vertical-align: middle; }
    .lbl-narrow { width: 22%; }
    .section-header td {
        background: #000;
        color: #fff;
        font-weight: bold;
        text-align: center;
        padding: 4px 6px;
        font-size: 11pt;
    }
    .val { vertical-align: top; }
    /* Row height helpers ─ smaller than before to reduce blank space */
    .h12 { height: 12px; }
    .h18 { height: 18px; }
    .h26 { height: 26px; }
    .h36 { height: 36px; }
    /* Keep legacy class names mapped to smaller values */
    .h16 { height: 14px; }
    .h24 { height: 20px; }
    .h32 { height: 28px; }
    .h48 { height: 38px; }

    /* Checkbox row */
    .cb-row td { border: 1px solid #000; padding: 4px 8px; font-size: 11pt; }
    .cb { font-size: 11pt; font-family: Arial, sans-serif; margin-right: 2px; }

    /* footer bar — rendered as a normal table row, not fixed position */
    .page-footer-bar {
        width: 100%;
        border-top: 1.5px solid #000;
        margin-top: 6px;
        font-size: 10pt;
        display: table;
        position: fixed;
        bottom: 5mm;
        left: 5mm;
        right: 5mm;
    }
    .page-footer-bar .fl { display: table-cell; text-align: left;   width: 33%; padding: 2px 4px; }
    .page-footer-bar .fc { display: table-cell; text-align: center;  width: 34%; padding: 2px 4px; }
    .page-footer-bar .fr { display: table-cell; text-align: right;   width: 33%; padding: 2px 4px; }

    /* matrix */
    .matrix-hdr { background: #d0d0d0; font-weight: bold; font-size: 10pt; text-align: center; }
    .matrix-row-lbl { font-size: 10pt; }
    .matrix-cell { text-align: center; font-size: 10pt; min-width: 18px; }

    .note-text { font-size: 9pt; font-style: italic; padding: 3px 5px; border: 1px solid #000; }
</style>
</head>
<body>

<?php
// Footer helper — renders an inline footer bar for each page
function qms01_footer($page, $total = 5) {
    echo '<div class="page-footer-bar">';
    echo '<span class="fl">Global MCS</span>';
    echo '<span class="fc">' . $page . '/' . $total . '</span>';
    echo '<span class="fr">F-01 QMS (Version 5.00, 30.10.2023)</span>';
    echo '</div>';
}
?>

<!-- ═══════════════════════════════════════════════════════
     PAGE 1
     ═══════════════════════════════════════════════════════ -->

<!-- Header -->
<table class="header-table">
    <tr>
        <td class="header-logo" rowspan="2">
             <img alt="" src="data:image/jpeg;base64,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" />
      
        </td>
        <td class="header-company">
            <div class="co-name">GLOBAL MANAGEMENT CERTIFICATION SERVICES PVT. LTD.</div>
            <div class="co-addr">Flat No.402, Plot No.410, Matrusri nagar, Miyapur, Hyderabad-500049, India.</div>
            <div class="co-addr">Phone No.:040-4855 9001, E-mail: info@mcsglobal.in Website: www.mcsglobal.in</div>
        </td>
    </tr>
    <tr>
        <td class="form-title">APPLICATION FOR CERTIFICATION</td>
    </tr>
</table>

<!-- Notes -->
<div class="notes">
    <ol>
        <li>Please fill correctly to enable us understand your requirements and issue a formal offer.</li>
        <li>No information shall be disclosed to any third party without the written consent of the customer in conformity with GLOBAL MCS Policy &amp; procedures.</li>
    </ol>
</div>

<!-- Certification Type Checkboxes -->
<table class="form-table" style="margin-bottom:4px;">
    <tr class="cb-row">
        <td><span class="cb"><?= qms01_checkbox($is_initial) ?></span> Initial Certification</td>
        <td><span class="cb"><?= qms01_checkbox($is_recert) ?></span> Re- Certification</td>
        <td><span class="cb"><?= qms01_checkbox($is_trans_surv) ?></span> Transfer at Surveillance</td>
        <td><span class="cb"><?= qms01_checkbox($is_trans_recert) ?></span> Transfer at Re Certification</td>
    </tr>
</table>

<!-- GENERAL INFORMATION -->
<table class="form-table">
    <tr class="section-header"><td colspan="4">GENERAL INFORMATION</td></tr>

    <tr>
        <td class="lbl">Organization Name</td>
        <td colspan="3" class="val h16"><?= $org_name ?></td>
    </tr>

    <tr>
        <td class="lbl">Organization Type</td>
        <td colspan="3">
            <?php
            $ot = strtolower((string)$org_type_val);
            ?>
            <span class="cb"><?= qms01_checkbox($ot === 'company') ?></span> Company &nbsp;&nbsp;
            <span class="cb"><?= qms01_checkbox($ot === 'partnership') ?></span> Partnership &nbsp;&nbsp;
            <span class="cb"><?= qms01_checkbox($ot === 'proprietorship') ?></span> Proprietorship &nbsp;&nbsp;
            <span class="cb"><?= qms01_checkbox($ot === 'other') ?></span> Other
        </td>
    </tr>

    <tr>
        <td class="lbl">Name/ Designation<br>of Top Management</td>
        <td class="val h24" style="width:40%"><?= $tm_name ?></td>
        <td class="lbl" style="width:12%">Mobile no.</td>
        <td class="val h24"><?= $tm_mobile ?></td>
    </tr>

    <tr>
        <td class="lbl">Head Office</td>
        <td colspan="3" class="val h32"><?= $head_office ?></td>
    </tr>

    <tr>
        <td class="lbl">Main Operative Site</td>
        <td colspan="3" class="val h32"><?= $main_site ?></td>
    </tr>

    <tr>
        <td class="lbl">(Other Sites)</td>
        <td colspan="3" class="val h24"><?= $other_sites ?></td>
    </tr>

    <!-- Contact Person sub-table -->
    <tr>
        <td class="lbl" rowspan="4">Contact Person</td>
        <td class="lbl-narrow">Name</td>
        <td class="val h16"><?= $cp_name ?></td>
        <td style="display:table-cell">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="border:none;padding:2px;width:40%;font-size:9.5pt;">Position</td>
                    <td style="border:none;padding:2px;font-size:9.5pt;"><?= $cp_position ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="lbl-narrow">Mobile</td>
        <td class="val h16"><?= $cp_mobile ?></td>
        <td>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="border:none;padding:2px;width:40%;font-size:9.5pt;">Fax</td>
                    <td style="border:none;padding:2px;font-size:9.5pt;"><?= $cp_fax ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="lbl-narrow">Tel.</td>
        <td class="val h16"><?= $cp_tel ?></td>
        <td>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="border:none;padding:2px;width:40%;font-size:9.5pt;">e-mail</td>
                    <td style="border:none;padding:2px;font-size:9.5pt;"><?= $cp_email ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="lbl-narrow">Tel.</td>
        <td class="val h16">&nbsp;</td>
        <td>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="border:none;padding:2px;width:40%;font-size:9.5pt;">Website</td>
                    <td style="border:none;padding:2px;font-size:9.5pt;"><?= $cp_website ?></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td class="lbl">Products/ Services</td>
        <td colspan="3" class="val h48"><?= $products_services ?></td>
    </tr>

    <tr>
        <td class="lbl">Desired Scope of<br>Certification</td>
        <td colspan="3" class="val h48"><?= $scope_cert ?></td>
    </tr>

    <tr>
        <td class="lbl">Exclusions<br>(only for ISO 9001)</td>
        <td class="val h24"><?= $exclusions ?></td>
        <td class="lbl" style="width:12%">Justification</td>
        <td class="val h24"><?= $justification ?></td>
    </tr>

    <tr>
        <td class="lbl">Certification<br>Scheme Applied</td>
        <td colspan="3" class="val h16">
            <?php
            $is9001 = stripos((string)$cert_scheme, '9001') !== false;
            $is14001 = stripos((string)$cert_scheme, '14001') !== false;
            $is45001 = stripos((string)$cert_scheme, '45001') !== false;
            $is22000 = stripos((string)$cert_scheme, '22000') !== false;
            $is27001 = stripos((string)$cert_scheme, '27001') !== false;
            ?>
            <span class="cb"><?= qms01_checkbox($is9001) ?></span> ISO 9001:2015 &nbsp;
            <span class="cb"><?= qms01_checkbox($is14001) ?></span> ISO 14001:2015 &nbsp;
            <span class="cb"><?= qms01_checkbox($is45001) ?></span> ISO 45001:2018 &nbsp;
            <span class="cb"><?= qms01_checkbox($is22000) ?></span> ISO 22000 &nbsp;
            <span class="cb"><?= qms01_checkbox($is27001) ?></span> ISO 27001 &nbsp;
            <?php if ($cert_scheme && !$is9001 && !$is14001 && !$is45001 && !$is22000 && !$is27001): ?>
                &nbsp;<?= $cert_scheme ?>
            <?php endif; ?>
        </td>
    </tr>
</table>

<?php qms01_footer(1); ?>

<!-- ═══════════════════════════════════════════════════════
     PAGE 2
     ═══════════════════════════════════════════════════════ -->
<div class="page-break"></div>

<table class="form-table">
    <tr>
        <td class="lbl" style="width:28%">Accreditation:</td>
        <td class="val h16"><?= $accreditation ?></td>
    </tr>

    <!-- Already certified for OTHER scheme -->
    <tr>
        <td class="lbl" rowspan="2" style="vertical-align:top;padding-top:4px;">
            Is your Organization already certificed for any system or product certification scheme other than you have applied for?
        </td>
        <td>
            <span class="cb"><?= qms01_checkbox($already_cert_val === 'Yes') ?></span> Yes &nbsp;&nbsp;&nbsp;
            <span class="cb"><?= qms01_checkbox($already_cert_val === 'No') ?></span> No
        </td>
    </tr>
    <tr>
        <td class="val h32" style="font-size:8pt;">
            (If yes, please specify the name of the Certification Scheme, certification Body and Accreditation Board).<br>
            <?= $already_cert_det ?>
        </td>
    </tr>

    <!-- Already applicant for SAME scheme -->
    <tr>
        <td class="lbl" rowspan="4" style="vertical-align:top;padding-top:4px;">
            Is your organization already an applicant / certified for the certification scheme you have applied for and you want to change your present certification service provider?
        </td>
        <td>
            <span class="cb"><?= qms01_checkbox($applicant_val === 'Yes') ?></span> Yes &nbsp;&nbsp;&nbsp;
            <span class="cb"><?= qms01_checkbox($applicant_val === 'No') ?></span> No
        </td>
    </tr>
    <tr>
        <td class="val h24" style="font-size:8pt;">
            (If yes, please mention the name of certification body and attach a copy of the certificate along with the last audit/NC report):<br>
            <?= $applicant_details ?>
        </td>
    </tr>
    <tr>
        <td class="val h24" style="font-size:8pt;">
            <strong>In addition, answer the following questions)</strong><br>
            Any Complaints received and action taken (major complaints):<br>
            <?= $complaints_actions ?>
        </td>
    </tr>
    <tr>
        <td class="val h16" style="font-size:8pt;">
            Any current engagement by the organization with regulatory bodies in respect of legal compliance:<br>
            <?= $regulatory_engagement ?>
        </td>
    </tr>

    <!-- Internal Audit & MRM -->
    <tr>
        <td class="lbl" rowspan="2">Internal Audit<br>Details</td>
        <td>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="border:none;width:50%;padding:2px;">
                        <span class="cb"><?= qms01_checkbox($ia_status === 'planned') ?></span> Planned &nbsp;
                        <span class="cb"><?= qms01_checkbox($ia_status === 'completed') ?></span> Completed
                    </td>
                    <td style="border:none;width:50%;padding:2px;">
                        <strong>MRM Details</strong> &nbsp;
                        <span class="cb"><?= qms01_checkbox($mrm_status === 'planned') ?></span> Planned &nbsp;
                        <span class="cb"><?= qms01_checkbox($mrm_status === 'completed') ?></span> Completed
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="border:none;width:50%;padding:2px;">Date: <?= $ia_date ?></td>
                    <td style="border:none;width:50%;padding:2px;">Date: <?= $mrm_date ?></td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Applicable legal & Compliance -->
    <tr>
        <td class="lbl" rowspan="2">Applicable legal and statutory requirements</td>
        <td>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="border:none;width:70%;padding:2px;" class="val h16"><?= $applicable_legal ?></td>
                    <td style="border:none;width:30%;padding:2px;">
                        <strong>Compliance</strong><br>
                        <span class="cb"><?= qms01_checkbox($compliance_val === 'Yes') ?></span> Yes &nbsp;
                        <span class="cb"><?= qms01_checkbox($compliance_val === 'No') ?></span> No
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Language -->
    <tr>
        <td>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="border:none;padding:2px;vertical-align:middle;"><strong>Language (s)</strong> &nbsp; Documentation: <?= $lang_doc ?></td>
                    <td style="border:none;padding:2px;vertical-align:middle;">Speaking: <?= $lang_speak ?></td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Certified by GLOBAL MCS with other standards -->
    <tr>
        <td class="lbl" style="vertical-align:top;">Is your organization certified by GLOBAL MCS with any other Standards</td>
        <td class="val h24"><?= $other_standards ?></td>
    </tr>

    <!-- Seasonal -->
    <tr>
        <td class="lbl" style="vertical-align:top;font-size:8.5pt;">If your business is seasonal, please indicate working period with full strength of employees.</td>
        <td class="val h32"><?= $seasonal ?></td>
    </tr>

    <!-- Safety -->
    <tr>
        <td class="lbl">Safety conditions, if applicable</td>
        <td class="val h24"><?= $safety ?></td>
    </tr>

    <!-- Customer premises -->
    <tr>
        <td class="lbl" style="font-size:8.5pt;">Please indicate any activities/services that performed at your customer premises?</td>
        <td class="val h24"><?= $customer_premises ?></td>
    </tr>

    <!-- ICT -->
    <tr>
        <td class="lbl" style="font-size:8.5pt;">Types and Extent ICT used by the Organization and competency level.</td>
        <td class="val h16" style="font-size:8.5pt;">
            (clients to indicate the ICTs used and their effectiveness while using based on their regular usage)<br>
            <?= $ict_used ?>
        </td>
    </tr>

    <!-- Outsourced Processes header -->
    <tr>
        <td class="lbl">Outsourced</td>
        <td>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <th style="border:1px solid #000;width:34%;padding:3px;font-size:9pt;">Process</th>
                    <th style="border:1px solid #000;width:33%;padding:3px;font-size:9pt;">Supplier/Sub Contractor</th>
                    <th style="border:1px solid #000;width:33%;padding:3px;font-size:9pt;">Compliance Status or Control Exercised</th>
                </tr>
                <?php if (!empty($outsourced_list) && is_array($outsourced_list)): ?>
                    <?php foreach ($outsourced_list as $row): ?>
                    <tr>
                        <td style="border:1px solid #000;padding:3px;font-size:9pt;height:16px;"><?= esc_html($row['process'] ?? '') ?></td>
                        <td style="border:1px solid #000;padding:3px;font-size:9pt;"><?= esc_html($row['suppliersub_contractor'] ?? '') ?></td>
                        <td style="border:1px solid #000;padding:3px;font-size:9pt;"><?= esc_html($row['compliance_status_or_control_exercised'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td style="border:1px solid #000;padding:3px;height:16px;">&nbsp;</td>
                        <td style="border:1px solid #000;padding:3px;">&nbsp;</td>
                        <td style="border:1px solid #000;padding:3px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #000;padding:3px;height:16px;">&nbsp;</td>
                        <td style="border:1px solid #000;padding:3px;">&nbsp;</td>
                        <td style="border:1px solid #000;padding:3px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #000;padding:3px;height:16px;">&nbsp;</td>
                        <td style="border:1px solid #000;padding:3px;">&nbsp;</td>
                        <td style="border:1px solid #000;padding:3px;">&nbsp;</td>
                    </tr>
                <?php endif; ?>
            </table>
        </td>
    </tr>
</table>

<?php qms01_footer(2); ?>

<!-- ═══════════════════════════════════════════════════════
     PAGE 3 — Technical Resources + EPME Matrix
     ═══════════════════════════════════════════════════════ -->
<div class="page-break"></div>

<table class="form-table">
    <!-- Description of Technical Resources -->
    <tr>
        <td class="lbl" style="width:28%;">Description of<br>Technical resources</td>
        <td class="val h24">Machinery and Technical Staff: <?= $tech_staff ?></td>
    </tr>

    <!-- Consultancy -->
    <tr>
        <td class="lbl">Consultancy<br>Organization/<br>Consultant</td>
        <td>
            <?= $consult_data ?>
            <span style="float:right;">
                <span class="cb"><?= qms01_checkbox($self_prepared) ?></span> Self-Prepared
            </span>
        </td>
    </tr>
</table>

<!-- EPME Matrix heading -->
<table class="form-table" style="margin-top:4px;">
    <tr>
        <td colspan="12" style="font-weight:bold;padding:3px 5px;background:#e8e8e8;text-align:center;font-size:9pt;">
            Employee, Process, Machinery or Equipment Details- Site specific
        </td>
    </tr>
    <!-- Column headers -->
    <tr>
        <td rowspan="2" style="width:22%;font-size:8.5pt;text-align:center;">&nbsp;</td>
        <td colspan="3" class="matrix-hdr">Main Site (above)</td>
        <td colspan="3" class="matrix-hdr">Site 1</td>
        <td colspan="3" class="matrix-hdr">Site 2 ** (Temporary)</td>
    </tr>
    <tr>
        <td class="matrix-hdr">1st</td>
        <td class="matrix-hdr">2nd</td>
        <td class="matrix-hdr">3rd</td>
        <td class="matrix-hdr">1st</td>
        <td class="matrix-hdr">2nd</td>
        <td class="matrix-hdr">3rd</td>
        <td class="matrix-hdr">1st</td>
        <td class="matrix-hdr">2nd</td>
        <td class="matrix-hdr">3rd</td>
    </tr>
    <?php
    // Build row definitions matching the PDF
    $matrix_rows = [
        '1.0: Process/Activities/ Operations' => 'Process_Operations',
        '2.0: Major Machinery/Equipment\'s'   => 'Major_Machinery',
        '3.0:Shifts'                           => 'Shifts',
        '4.0: Employees (give break-up as below)' => null,
        'Top Management'                       => 'Top Management',
        'Purchase'                             => 'Purchase',
        'Stores'                               => 'Stores',
        'HR + Safety'                          => 'HR + Safety',
        'QC/QA'                                => 'QC/QA',
        'Marketing'                            => 'Marketing',
        'Design'                               => 'Design',
        'Production (Full time) Managerial'    => 'Production (Full time) Managerial',
        'Production (Full time) Non Managerial'=> 'Production (Full time) Non Managerial',
        'Part time'                            => 'Part time',
        'Temporary'                            => 'Temporary',
        'Contract'                             => 'Contract',
        'Others @@'                            => 'Others @@',
        'Out of above how many are working away from Organization.' => 'Out of above how many are working away from Organization',
        'Total Employees'                      => 'Total Employees',
    ];

    $col_keys = ['Off1st','Off2nd','Off3rd','mos1st','mos2nd','mos3rd','ts1st','ts2nd','ts3rd'];

    foreach ($matrix_rows as $row_label => $data_key):
        // Try to get data from epme_matrix array
        $row_data = [];
        if ($data_key && !empty($epme_matrix) && is_array($epme_matrix)) {
            foreach ($col_keys as $ck) {
                $row_data[$ck] = $epme_matrix[$data_key][$ck] ?? '';
            }
        }
        $is_bold = in_array($row_label, ['4.0: Employees (give break-up as below)', 'Total Employees']);
    ?>
    <tr>
        <td class="matrix-row-lbl" style="<?= $is_bold ? 'font-weight:bold;' : '' ?>"><?= esc_html($row_label) ?></td>
        <?php if ($data_key): ?>
            <?php foreach ($col_keys as $ck): ?>
                <td class="matrix-cell" style="height:14px;"><?= esc_html($row_data[$ck] ?? '') ?></td>
            <?php endforeach; ?>
        <?php else: ?>
            <td colspan="9" style="background:#e8e8e8;">&nbsp;</td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>

    <!-- Note row -->
    <tr>
        <td colspan="10" class="note-text">
            @@ organizations having additional process, managerial or non-managerial may add separate rows and provide the details. Organizations having different processes addressed than above can remove the rows, which are irrelevant and add rows based on the process and provide the details.<br>
            ** in case of multiple sites, includes temporary site add data in separate sheet and send along with this form.
        </td>
    </tr>

    <!-- Transfer row (always visible) -->
    <tr>
        <td colspan="5" style="font-weight:bold;padding:4px 5px;font-size:9.5pt;">
            For transferring certification from other certification body
        </td>
        <td colspan="2" style="padding:4px 5px;">
            <span class="cb"><?= qms01_checkbox($transfer_val === 'Yes') ?></span> Yes
        </td>
        <td colspan="3" style="padding:4px 5px;">
            <span class="cb"><?= qms01_checkbox($transfer_val === 'No') ?></span> No
        </td>
    </tr>
    <tr>
        <td colspan="10" style="font-weight:bold;padding:3px 5px;font-size:9.5pt;">
            If yes please fill the following details:
        </td>
    </tr>
</table>

<?php qms01_footer(3); ?>

<!-- ═══════════════════════════════════════════════════════
     PAGE 4 — Transfer details + branch offices
     ═══════════════════════════════════════════════════════ -->
<div class="page-break"></div>

<table class="form-table">
    <tr>
        <td class="lbl" style="width:28%;">Name of CB<br>(attach Certificate)</td>
        <td class="val h24"><?= $trans_cb ?></td>
        <td class="lbl" style="width:28%;font-size:8.5pt;">Latest Audit<br>(Initial/Surveillance/<br>Re Certification)<br>(attach report)</td>
        <td class="val h24"><?= $trans_audit ?></td>
    </tr>
    <tr>
        <td class="lbl">Name of AB<br>(attach certificate)</td>
        <td class="val h24"><?= $trans_ab ?></td>
        <td class="lbl" style="font-size:8.5pt;">Initial Certification Audit/ Re<br>Certification audit report<br>(attach report)</td>
        <td class="val h24"><?= $trans_report ?></td>
    </tr>
    <tr>
        <td class="lbl">Reason for Transfer</td>
        <td colspan="3" class="val h16"><?= $trans_reason ?></td>
    </tr>
    <tr>
        <td class="lbl" style="font-size:8.5pt;">Any Complaints from regulatory or from market?</td>
        <td colspan="3" class="val h16"><?= $trans_complaints ?></td>
    </tr>
    <tr>
        <td class="lbl">Certificate present stage</td>
        <td colspan="3" class="val" style="padding:4px;">
            <?php
            $stages = is_array($cert_stage) ? $cert_stage : [];
            $s1 = in_array('Between initial to Surveillance 1', $stages);
            $s2 = in_array('Between Surveillance 1 to Surveillance 2', $stages);
            $s3 = in_array('Between Surveillance 2 to Re certification', $stages);
            ?>
            <span class="cb"><?= qms01_checkbox($s1) ?></span> Between Initial to Surveillance 1<br>
            <span class="cb"><?= qms01_checkbox($s2) ?></span> Between Surveillance 1 to Surveillance 2<br>
            <span class="cb"><?= qms01_checkbox($s3) ?></span> Between Surveillance 2 to Recertification
        </td>
    </tr>
    <tr>
        <td class="lbl" style="font-size:8.5pt;">Have you made any communication to your current CB for authorizing GLOBAL MCS as your new CB</td>
        <td colspan="3" class="val h24"><?= $comm_to_cb ?></td>
    </tr>
    <tr>
        <td class="lbl" style="font-size:8.5pt;">Are there any outstanding Major Non conformities?</td>
        <td colspan="3" class="val h16"><?= $major_nc ?></td>
    </tr>
    <tr>
        <td class="lbl" style="font-size:8.5pt;">Are there any outstanding minor nonconformities?</td>
        <td colspan="3" class="val h16"><?= $minor_nc ?></td>
    </tr>

    <!-- Attach Audit Program -->
    <tr>
        <td class="lbl">Attach Audit Program?</td>
        <td colspan="3">
            <span class="cb"><?= qms01_checkbox($attach_audit_prog === 'Yes') ?></span> Yes &nbsp;
            <span class="cb"><?= qms01_checkbox($attach_audit_prog === 'No') ?></span> No<br>
            <span style="font-size:8.5pt;">If Not available Reasons: <?= $not_avail_reason ?></span><br>
            <span style="font-size:8.5pt;">Provide us the CB contact details for obtaining the program: <?= $cb_contact ?></span>
        </td>
    </tr>

    <!-- Certificate under suspension -->
    <tr>
        <td class="lbl" rowspan="2" style="vertical-align:top;">certificate under suspension<br>or under threat of suspension</td>
        <td colspan="3">
            <span class="cb"><?= qms01_checkbox($susp_val === 'Yes') ?></span> Yes &nbsp;&nbsp;
            <span class="cb"><?= qms01_checkbox($susp_val === 'No') ?></span> No
        </td>
    </tr>
    <tr>
        <td colspan="3" class="val h16">
            If yes, state reason : <?= $susp_reason ?>
        </td>
    </tr>

    <!-- Branch offices -->
    <tr>
        <td colspan="4" style="font-weight:bold;padding:3px 5px;">
            Information About branch offices/other sites ( to be certified)
            &nbsp;<span class="cb"><?= qms01_checkbox($branch_info === 'Yes') ?></span> Yes &nbsp;
            <span class="cb"><?= qms01_checkbox($branch_info === 'No') ?></span> No
        </td>
    </tr>
    <tr>
        <td class="lbl">1 Site address:</td>
        <td colspan="3" class="val h24"><?= $site1_addr ?></td>
    </tr>
    <tr>
        <td class="lbl">Activity(ies)</td>
        <td colspan="3" class="val h24"><?= $site1_act ?></td>
    </tr>
    <tr>
        <td class="lbl">1 Site address:</td>
        <td colspan="3" class="val h24"><?= $site2_addr ?></td>
    </tr>
    <tr>
        <td class="lbl">Activity(ies)</td>
        <td colspan="3" class="val h24"><?= $site2_act ?></td>
    </tr>
    <tr>
        <td class="lbl"># Desired date of audit</td>
        <td colspan="3" class="val h16">Tentatively in the Month of :- <?= $desired_date ?></td>
    </tr>
    <tr>
        <td colspan="4" class="note-text">
            # desired date should be the date, time and season when audit team has the opportunity to audit the organization operating on the maximum product lines, categories and sectors covered by the scope.
        </td>
    </tr>
</table>

<?php qms01_footer(4); ?>

<!-- ═══════════════════════════════════════════════════════
     PAGE 5 — Declaration & Attachments
     ═══════════════════════════════════════════════════════ -->
<div class="page-break"></div>

<!-- Acknowledgement paragraph -->
<table class="form-table">
    <tr>
        <td style="padding:6px 8px;font-size:9.5pt;border:1px solid #000;">
            <strong>I acknowledge that</strong><br>
            the information provided by me is correct as per my best knowledge and the GLOBAL MCS offer is based on the above information. If during assessments any variation is found, GLOBAL MCS may revise its arrangements and offer.<br>
            &bull; Application fee once paid is non refundable
        </td>
    </tr>
</table>

<!-- Declaration fields -->
<table class="form-table" style="margin-top:4px;">
    <tr>
        <td class="lbl">Name of the Authorized<br>Representative:</td>
        <td class="val h24"><?= $ack_name ?></td>
    </tr>
    <tr>
        <td class="lbl">Designation:</td>
        <td class="val h24"><?= $ack_design ?></td>
    </tr>
    <tr>
        <td class="lbl">Signature:</td>
        <td class="val h32">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Date:</td>
        <td class="val h24"><?= $ack_date ?></td>
    </tr>

    <!-- Attachments checklist -->
    <tr>
        <td class="lbl" style="vertical-align:top;">Attachments:</td>
        <td class="val" style="padding:6px;">
            <?php
            $checked_items = is_array($attachments_val) ? $attachments_val : [];
            foreach ($attach_opts as $opt):
                $is_checked = in_array($opt, $checked_items);
            ?>
                <span class="cb"><?= qms01_checkbox($is_checked) ?></span> <?= esc_html($opt) ?><br>
            <?php endforeach; ?>
        </td>
    </tr>
</table>

<?php qms01_footer(5); ?>

</body>
</html>
