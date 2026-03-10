<?php
/**
 * QMS – F-02 Review of Application Template
 * Redesigned to exactly match F-02 QMS (Version 4.00, 17.12.2020) reference PDF
 */
if (!defined('ABSPATH')) exit;

// ── helpers ───────────────────────────────────────────────────────────────────
function f02v($key, $pid) {
    $v = get_field($key, $pid);
    return (!empty($v) && !is_array($v)) ? esc_html($v) : '';
}
function f02cb($checked = false) {
    return $checked ? '[X]' : '[  ]';
}
function f02footer($page, $total = 5) {
    echo '<div class="page-footer-bar">';
    echo '<span class="fl"></span>';
    echo '<span class="fc"></span>';
    echo '<span class="fr">F-02 QMS (Version 4.00, 17.12.2020)</span>';
    echo '</div>';
}

// ── pull all ACF fields ───────────────────────────────────────────────────────
$org            = f02v('organization_name', $post_id);
$cert_scheme    = f02v('cert_scheme', $post_id);
$tech_area      = f02v('technical_code_area', $post_id);
$proposal_ref   = f02v('proposal_ref_no', $post_id);

// Requirements table (matrix_flexible — stored as array)
$tech_review    = get_field('tech_review_2', $post_id);

// Conclusion
$conclusion_val = get_field('conclusion_of_the_contract_review', $post_id);

// Review & Audit Time Calculation (group)
$ratc           = get_field('review_and_audit_time_calculation', $post_id);
$eff_employees  = isset($ratc['effective_number_of_employees']) ? esc_html($ratc['effective_number_of_employees']) : '';
$risk_cat       = isset($ratc['risk_category'])                 ? esc_html($ratc['risk_category'])                 : '';
$mgmt_sys       = isset($ratc['management_systems'])            ? esc_html($ratc['management_systems'])            : '';
$ict_used       = isset($ratc['Type_and_extent_of_ICT_used_if_any']) ? esc_html($ratc['Type_and_extent_of_ICT_used_if_any']) : '';
$shifts_season  = isset($ratc['shifts'])                        ? esc_html($ratc['shifts'])                        : '';
$activities_other = isset($ratc['activities_performed_by_the_applicant']) ? esc_html($ratc['activities_performed_by_the_applicant']) : '';

// Audit Man-days Calculation (group)
$amc            = get_field('audit_man_days_calculation', $post_id);
$mds_p07        = isset($amc['mds_asper_table_p-07'])                          ? esc_html($amc['mds_asper_table_p-07'])                          : '';
$add_time       = '';  // Additional Time – separate field if any
$red_time       = isset($amc['reduction_in_time'])                             ? esc_html($amc['reduction_in_time'])                             : '';
$just_red       = isset($amc['justification_for_addition_and_reduction_time']) ? esc_html($amc['justification_for_addition_and_reduction_time']) : '';
$actual_md      = isset($amc['actual_man_days_md'])                            ? esc_html($amc['actual_man_days_md'])                            : '';
$subtotal_md    = isset($amc['sub_total_md_st_md_rounded_to_total'])           ? esc_html($amc['sub_total_md_st_md_rounded_to_total'])           : '';

// Audit time summary group
$ats            = get_field('add_separate_table_for_other_sites_if_sites_are_more_than_2', $post_id);
$audit_time_req = isset($ats['audit_time_required'])              ? esc_html($ats['audit_time_required'])              : '';
$init_md        = isset($ats['initial_mandays'])                  ? esc_html($ats['initial_mandays'])                  : $subtotal_md;
$recert_md      = isset($ats['re_certification=23_of_initial_md']) ? esc_html($ats['re_certification=23_of_initial_md']) : '';
$surv_md        = isset($ats['surveillance=13_of_initial_md'])    ? esc_html($ats['surveillance=13_of_initial_md'])    : '';

// Audit time implementation
$impl           = get_field('audit_time_to_be_implemented_for__initial_audit', $post_id);
$stage1_md      = isset($impl['on_site_stage1'])   ? esc_html($impl['on_site_stage1'])   : '';
$stage2_md      = isset($impl['on_site_stage_2'])  ? esc_html($impl['on_site_stage_2'])  : '';

// Multi-site fields
$diff_activities    = f02v('are_there_any_different_activities_performed_in_different_sites_how_many_similar_sites', $post_id);
$diff_operations    = f02v('are_there_any_differences_between_operations_of_similar_sites', $post_id);
$multi_sites_group  = get_field('no_of_sites_selected_for_audit_in_case_of_multi_sites_justification_on_the_selection_of_sites', $post_id);
$temp_sites         = isset($multi_sites_group['temporary_sites:']) ? esc_html($multi_sites_group['temporary_sites:']) : '';
$sites_justif       = isset($multi_sites_group['justification:'])   ? esc_html($multi_sites_group['justification:'])   : '';

// Remaining fields from JSON (additional groups stored under a wrapper group or direct keys)
// Proposed Audit Programme, Team, Review after Stage1/Surveillance/ReCert
$pap            = get_field('proposed_audit_programme', $post_id);
$init_month     = isset($pap['initial_audit_to_be_held_in'])    ? esc_html($pap['initial_audit_to_be_held_in'])    : '';
$surv1_month    = isset($pap['1st_surveillance_in'])            ? esc_html($pap['1st_surveillance_in'])            : '';
$surv2_month    = isset($pap['2nd_surveillance'])               ? esc_html($pap['2nd_surveillance'])               : '';
$renewal_month  = isset($pap['and_renewal_in'])                 ? esc_html($pap['and_renewal_in'])                 : '';
$seasonal_biz   = isset($pap['incase_of_seasonal_business'])    ? esc_html($pap['incase_of_seasonal_business'])    : '';
$time_of_audit  = isset($pap['time_of_audit'])                  ? esc_html($pap['time_of_audit'])                  : '';
$season_months  = isset($pap['season_auditmoneths'])            ? esc_html($pap['season_auditmoneths'])            : '';

// Proposed audit team
$pat            = get_field('proposed_audit_team', $post_id);
$team_leader    = isset($pat['team_leader'])        ? esc_html($pat['team_leader'])        : '';
$auditors       = isset($pat['auditors'])           ? esc_html($pat['auditors'])           : '';
$tech_expert    = isset($pat['technical_expert'])   ? esc_html($pat['technical_expert'])   : '';
$tl_supervisor  = isset($pat['team_leader_under_supervision_if_any_witness_auditor']) ? esc_html($pat['team_leader_under_supervision_if_any_witness_auditor']) : '';
$observers      = isset($pat['observers'])          ? esc_html($pat['observers'])          : '';
$interpreters   = isset($pat['interpreters'])       ? esc_html($pat['interpreters'])       : '';

$remote_audit   = f02v('audit_time_on_remote_auditing_techniques_if_used', $post_id);
$outsourcing_comp = f02v('is_audit_team_competent_to_evaluate_the_control_of_outsourcing_processes', $post_id);

// Reviewed By (Stage 1 review)
$rev_s1         = get_field('reviewed_by', $post_id);
$rev1_name      = isset($rev_s1['name'])      ? esc_html($rev_s1['name'])      : '';
$rev1_sign      = isset($rev_s1['signature']) ? esc_html($rev_s1['signature']) : '';
$rev1_date      = isset($rev_s1['date'])      ? esc_html($rev_s1['date'])      : '';
$tech_rev_name  = isset($rev_s1['technical_reviewer_if_required_name'])  ? esc_html($rev_s1['technical_reviewer_if_required_name'])  : '';
$tech_rev_sign  = isset($rev_s1['technical_reviewer_if_required_sig'])   ? esc_html($rev_s1['technical_reviewer_if_required_sig'])   : '';
$tech_rev_date  = isset($rev_s1['technical_reviewer_if_required_date'])  ? esc_html($rev_s1['technical_reviewer_if_required_date'])  : '';

$rev_stage1_details = get_field('review_after_stage_1_audit_performed', $post_id);
$s1_stage1_detail   = isset($rev_stage1_details['review_of_stage1_audit_details']) ? esc_html($rev_stage1_details['review_of_stage1_audit_details']) : '';
$s1_remarks         = isset($rev_stage1_details['remarks_or_conclusions_if_any'])  ? esc_html($rev_stage1_details['remarks_or_conclusions_if_any'])  : '';

// Review after Stage 1
$ras1           = get_field('review_after_collecting_the_information_before_plan_the_stage_1_audit', $post_id);
$ras1_changes   = isset($ras1['review_of_changes'])                    ? esc_html($ras1['review_of_changes'])                    : '';
$ras1_complaints = isset($ras1['review_action_taken_on_complaints_if_any']) ? esc_html($ras1['review_action_taken_on_complaints_if_any']) : '';
$ras1_audit_changes = isset($ras1['any_changes_proposed_on_audit_team']) ? esc_html($ras1['any_changes_proposed_on_audit_team']) : '';
$ras1_new_prog  = isset($ras1['new_audit_programme'])                  ? esc_html($ras1['new_audit_programme'])                  : '';
$ras1_rev_by    = isset($ras1['reviewed_by_name'])                     ? esc_html($ras1['reviewed_by_name'])                     : '';
$ras1_rev_sign  = isset($ras1['reviewed_by_signature'])                ? esc_html($ras1['reviewed_by_signature'])                : '';
$ras1_rev_date  = isset($ras1['reviewed_by_date'])                     ? esc_html($ras1['reviewed_by_date'])                     : '';
$ras1_tech_sign = '';
$ras1_tech_date = '';

// Review after Surveillance
$surv_rev       = get_field('review_after_collecting_the_information_before_plan_the_surveillance_audit', $post_id);
$surv_changes   = isset($surv_rev['review_of_changes'])                    ? esc_html($surv_rev['review_of_changes'])                    : '';
$surv_complaints = isset($surv_rev['review_action_taken_on_complaints_if_any']) ? esc_html($surv_rev['review_action_taken_on_complaints_if_any']) : '';
$surv_audit_chg = isset($surv_rev['any_changes_proposed_on_audit_team'])   ? esc_html($surv_rev['any_changes_proposed_on_audit_team'])   : '';
$surv_new_prog  = isset($surv_rev['new_audit_programme'])                  ? esc_html($surv_rev['new_audit_programme'])                  : '';
$surv_rev_by    = isset($surv_rev['reviewed_by_name'])                     ? esc_html($surv_rev['reviewed_by_name'])                     : '';
$surv_rev_sign  = '';
$surv_rev_date  = '';
$surv_tech_sign = '';
$surv_tech_date = '';

// Review after Re-Certification
$recert_rev     = get_field('review_after_collecting_the_information_before_plan_the_re_certification_audit', $post_id);
$recert_changes = isset($recert_rev['review_of_changes'])                    ? esc_html($recert_rev['review_of_changes'])                    : '';
$recert_complaints = isset($recert_rev['review_action_taken_on_complaints_if_any']) ? esc_html($recert_rev['review_action_taken_on_complaints_if_any']) : '';
$recert_audit_chg = isset($recert_rev['any_changes_proposed_on_audit_team']) ? esc_html($recert_rev['any_changes_proposed_on_audit_team'])   : '';
$recert_new_prog = isset($recert_rev['new_audit_programme'])                 ? esc_html($recert_rev['new_audit_programme'])                  : '';

// Annexure risk factors — stored as separate fields or a group
$annexure       = get_field('annexure_1_audit_time_calculation_sheet', $post_id);

// ─────────────────────────────────────────────────────────────────────────────
// Fixed rows for Annexure based on PDF (A & B factor lists)
$factors_a = [
    'Complicated logistics',
    'Interpreters required',
    'Very large site in comparison to number of employees',
    'Highly regulated sector',
    'Highly complex processes/ technology',
    'High risk sector- for QMS only',
    'Views of interested parties',
    'Is organization facing any legal proceedings',
    'Risk or rate of environmental/ safety accidents',
    'Outsource functions or processes',
    'Any other__',
];
$factors_b = [
    'Design exclusion',
    'No/ less risk products/ processes or activities [not for QMS, EMS & OHSMS]',
    'Already certified in other schemes',
    'Combined, joint or integrated audits',
    'Significant proportion of staff carry out a similar simple low risk functions, and/ or staff include a number of people who work "off location" e.g. sales persons, drivers, service personnel, etc.',
    'Prior Knowledge of the client management system:',
    'Maturity of Management system:',
    'Clients preparedness for Certification',
    'Very small site for number of personnel',
    'High level of automation',
    'Any other_',
];
$na_b = ['Clients preparedness for Certification', 'Very small site for number of personnel', 'High level of automation', 'Any other_'];

// Helper: get Yes/No for an annexure factor
function f02_annexure_yn($annexure, $key, $is_yes_default = false) {
    if (!is_array($annexure)) return [false, false];
    $val = $annexure[$key] ?? null;
    return [$val === 'Yes', $val === 'No'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; }
    body {
        font-family: Arial, sans-serif;
        font-size: 10pt;
        color: #000;
        line-height: 1.3;
        margin: 15px 15px 5px 15px;
    }
    @page { size: A4; margin: 10mm 13mm 10mm 13mm; }
    .page-break { page-break-before: always; }

    /* ── Header ── */
    .header-table { width: 100%; border: 1.5px solid #aaa; border-collapse: collapse; margin-bottom: 0; }
    .header-table td { padding: 4px 6px; vertical-align: middle; }
    .header-logo   { width: 90px; text-align: center; border-right: 1.5px solid #aaa; }
    .header-logo img { width: 75px; }
    .header-right { }
    .form-title-blue { color: #00008B; font-size: 12pt; font-weight: bold; }
    .form-version-right { text-align: right; font-size: 9pt; font-weight: bold; color: #00008B; }
    .note-blue { font-size: 8pt; color: #00008B; font-style: italic; }

    /* ── Organization / Standard bar ── */
    table.info-bar { width: 100%; border-collapse: collapse; }
    table.info-bar td { border: 1.5px solid #aaa; padding: 4px 6px; font-size: 10pt; }
    .info-lbl { color: #00008B; font-weight: bold; width: 20%; }
    .info-val { color: #00008B; font-weight: bold; }

    /* ── Generic form table ── */
    table.ft { width: 100%; border-collapse: collapse; }
    table.ft td, table.ft th { border: 1.5px solid #aaa; padding: 4px 6px; vertical-align: top; font-size: 10pt; }
    table.ft th { background: #e0e8f0; color: #00008B; font-weight: bold; text-align: center; font-size: 10pt; }
    .col-sno   { width: 5%; text-align: center; }
    .col-req   { width: 38%; }
    .col-rev   { width: 42%; }
    .col-conc  { width: 15%; text-align: center; }

    .lbl-blue  { color: #00008B; font-size: 10pt; }
    .val-black { color: #000; }
    .val-green { color: #00AA00; font-weight: bold; }
    .val-blue  { color: #00008B; }
    .val-italic { font-style: italic; color: #00008B; }

    .section-hdr-blue { background: #fff; color: #00008B; font-weight: bold; border: 1.5px solid #aaa; padding: 4px 6px; font-size: 10pt; }

    .cb { font-size: 10pt; margin-right: 3px; }

    /* ── Footer bar ── */
    .page-footer-bar { width: 100%; border-top: 1.5px solid #aaa; margin-top: 6px; font-size: 9pt; display: table; }
    .page-footer-bar .fl { display: table-cell; text-align: left;   width: 33%; padding: 2px 4px; }
    .page-footer-bar .fc { display: table-cell; text-align: center;  width: 34%; padding: 2px 4px; }
    .page-footer-bar .fr { display: table-cell; text-align: right;   width: 33%; padding: 2px 4px; color: #00008B; font-weight: bold; }
</style>
</head>
<body>

<?php
// ─── BASE64 LOGO (same as f01) ─────────────────────────────────────────────────
$logo_b64 = plugins_url('assets/images/logo.jpg', dirname(__FILE__));
?>

<!-- ════════════════════════════════════════════════════════════
     PAGE 1 — Header + Review Requirements Table (rows 1-8)
     ════════════════════════════════════════════════════════════ -->

<!-- Header -->
<table class="header-table">
    <tr>
        <td class="header-logo" rowspan="3">
          <img alt="" src="data:image/jpeg;base64,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" />
      
        </td>
        <td style="text-align:center;border-bottom:1px solid #aaa;">
            <span class="form-title-blue">Review of Application ( QMS)</span>
        </td>
    </tr>
    <tr>
        <td style="text-align:right;border-bottom:1px solid #aaa;padding:2px 6px;">
            <span class="form-version-right">F-02 QMS (Version 4.00, 17.12.2020)</span>
        </td>
    </tr>
    <tr>
        <td class="note-blue" style="padding:4px 6px;">
            Note to user: The requirements must not be altered. The text in blue italics is only the guidance and should be deleted on completion of the review. Information should be taken from duly filled in Form&nbsp;&nbsp;&nbsp;&nbsp;F-01 received from client. Conclusion in the last column refers whether or not the requirement have been met.
        </td>
    </tr>
</table>

<!-- Org / Standard / Tech-area info bar -->
<table class="info-bar" style="margin-top:0;border-top:none;">
    <tr>
        <td class="info-lbl" style="border-top:none;">Organization</td>
        <td class="info-val" style="border-top:none;"><?= $org ?></td>
    </tr>
    <tr>
        <td class="info-lbl">Standard(s)</td>
        <td><?= $cert_scheme ?></td>
    </tr>
    <tr>
        <td class="info-lbl">Technical area</td>
        <td class="val-black"><strong><?= $tech_area ?></strong></td>
    </tr>
</table>

<?php
// ── Requirements review table ──────────────────────────────────────────────
// The 9 fixed requirement rows with static text + dynamic review col + conclusion col
$req_rows = [
    [
        'req' => 'If the information about the applicant organization and its management system in application and supporting documents is sufficient for the conduct of the audit and developing the audit programme?',
        'review_key'  => 0,
        'static_lines' => [
            'The application is found completely filled with the information on desired scope of certification, certification standard etc. along with attachments.',
            'GMCSPL has received the application form F-01 on :',
        ],
        'show_date' => true,
    ],
    [
        'req' => 'If the requirements for certification are clearly defined and documented, and have been provided to the applicant organization?',
        'review_key' => 1,
    ],
    [
        'req' => 'If any known difference in understanding between GMCSPL and the applicant organization is resolved?',
        'review_key' => 2,
    ],
    [
        'req' => 'If GMCSPL has the required personnel competent in the Technical area and in the geographical area?',
        'review_key' => 3,
    ],
    [
        'req' => 'If the certification scheme and scope applied by the organisation falls under the accreditation granted to GMCSPL?',
        'review_key' => 4,
        'show_tech_area' => true,
        'show_accred_cb' => true,
    ],
    [
        'req' => 'Checked Location(s) of the applicant organization\'s operations, number of sites etc.?',
        'review_key' => 5,
    ],
    [
        'req' => 'If interpreters are required?',
        'review_key' => 6,
    ],
    [
        'req' => 'If there is any PPE requirements for Visitors?',
        'review_key' => 7,
    ],
    [
        'req' => 'Any threats to impartiality?',
        'review_key' => 8,
    ],
];

// Pre-extract tech_review matrix rows
$tr_rows = is_array($tech_review) ? array_values($tech_review) : [];
?>

<table class="ft" style="margin-top:4px;">
    <tr>
        <th class="col-sno">S.No</th>
        <th class="col-req">Requirements</th>
        <th class="col-rev">Review</th>
        <th class="col-conc">Conclusion<br>(OK/Not OK)</th>
    </tr>
    <?php foreach ($req_rows as $i => $row):
        $rdata     = $tr_rows[$i] ?? [];
        $rev_text  = isset($rdata['reviewtext']) ? esc_html($rdata['reviewtext']) : '';
        $conclusion = isset($rdata['status'])    ? esc_html($rdata['status'])     : 'OK';
    ?>
    <tr>
        <td class="col-sno"><?= $i + 1 ?></td>
        <td class="col-req val-italic"><?= esc_html($row['req']) ?></td>
        <td class="col-rev">
            <?php if ($i === 0): ?>
                <?= $rev_text ?><br>
                <small>GMCSPL has received the application form F-01 on
                    : <span class="val-green"><?= f02v('f01_received_date', $post_id) ?></span>
                </small>
            <?php elseif ($i === 4): ?>
                <?= $rev_text ?><br>
                <small>Based on the information provided by the Organization, the Certification scope falls under Technical area as addressed above :</small>
                <span class="val-green"><strong>&nbsp;<?= $tech_area ?></strong></span><br>
                <small>Client has been informed about the issue of Certificate as below</small><br>
                <span class="cb"><?= f02cb($cert_scheme && stripos($cert_scheme, 'accredit') === false) ?></span> Accredited &nbsp;&nbsp;&nbsp;
                <span class="cb"><?= f02cb(false) ?></span> Un Accredited
            <?php else: ?>
                <?= $rev_text ?>
            <?php endif; ?>
        </td>
        <td class="col-conc val-black"><?= $conclusion ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php f02footer(1); ?>

<!-- ════════════════════════════════════════════════════════════
     PAGE 2 — Contract Review Conclusion + Audit Time Calc
     ════════════════════════════════════════════════════════════ -->
<div class="page-break"></div>

<table class="ft">
    <!-- Row 9 -->
    <tr>
        <td class="col-sno">9</td>
        <td class="col-req val-italic">Any threats to impartiality?</td>
        <?php $r9 = $tr_rows[8] ?? []; ?>
        <td class="col-rev"><?= isset($r9['reviewtext']) ? esc_html($r9['reviewtext']) : '' ?></td>
        <td class="col-conc"><?= isset($r9['status']) ? esc_html($r9['status']) : 'OK' ?></td>
    </tr>

    <!-- Conclusion of contract review -->
    <tr>
        <td colspan="2" class="lbl-blue">Conclusion of the contract review</td>
        <td colspan="2">
            <span class="cb"><?= f02cb($conclusion_val === 'Application accepted') ?></span> Application accepted<br>
            <span class="cb"><?= f02cb($conclusion_val !== 'Application accepted') ?></span> Application declined due to [reason to be defined and informed to client]
        </td>
    </tr>
</table>

<br>

<!-- Review and Audit Time Calculation heading -->
<table class="ft">
    <tr>
        <td colspan="4" class="section-hdr-blue" style="text-align:center;"><strong>Review and Audit Time Calculation</strong></td>
    </tr>

    <!-- Effective employees -->
    <tr>
        <td colspan="2" class="lbl-blue">Effective number of Employees :</td>
        <td colspan="2">
            <span class="val-italic"><?php
                // Show employee breakdown from f01 data
                $emp_group = [
                    'Top Management, Marketing, Purchase, HR',
                    'Production, QC, QA, Stores, Maintenance, IQA, MRM',
                    'Total effective Employees=',
                ];
                foreach ($emp_group as $line) {
                    echo esc_html($line) . '<br>';
                }
            ?></span>
            <strong class="val-green"><?= $eff_employees ?></strong>
        </td>
    </tr>

    <tr>
        <td colspan="2" class="lbl-blue">Management Systems(S)</td>
        <td colspan="2" class="val-italic"><em><?= $mgmt_sys ?: 'QMS' ?></em></td>
    </tr>

    <tr>
        <td colspan="2" class="lbl-blue">Risk category :</td>
        <td colspan="2" class="val-italic"><em><?= $risk_cat ?></em></td>
    </tr>

    <tr>
        <td colspan="2" class="lbl-blue" style="vertical-align:top;">
            Type and extent of ICT used if any:<br>
            Are Audit Team and Clients has competency on the usage of ICT?
        </td>
        <td colspan="2" class="val-italic"><em><?= $ict_used ?></em></td>
    </tr>

    <tr>
        <td colspan="2" class="lbl-blue" style="vertical-align:top;">
            Is there any specific Time, shifts, Season (Months of audit) required to conduct to demonstrate the full scope of organisation?
        </td>
        <td colspan="2" class="val-italic"><em><?= $shifts_season ?></em></td>
    </tr>

    <tr>
        <td colspan="2" class="lbl-blue" style="vertical-align:top;">
            Are there any activities performed by the applicant organisation in another organisation?
        </td>
        <td colspan="2" class="val-italic"><em><?= $activities_other ?></em></td>
    </tr>

    <!-- Audit-Man-days heading -->
    <tr>
        <td colspan="4" class="section-hdr-blue"><strong>Audit-Man-days Calculation</strong>:</td>
    </tr>

    <!-- Inner audit man-days table -->
    <tr>
        <td colspan="4" style="padding:0;">
            <table class="ft" style="margin:0;border:none;">
                <tr>
                    <th style="width:10%;"><!-- blank --></th>
                    <th style="width:12%;">MDs as per Table P-07</th>
                    <th style="width:14%;">Additional Time</th>
                    <th style="width:14%;font-style:italic;">Reduction In Time</th>
                    <th style="width:35%;">Justification for addition and Reduction time</th>
                    <th style="width:15%;">Actual Man Days ,MD</th>
                </tr>
                <tr>
                    <td class="val-blue"><strong>QMS</strong></td>
                    <td style="text-align:center;"><?= $mds_p07 ?></td>
                    <td style="text-align:center;"><?= $add_time ?></td>
                    <td style="text-align:center;font-style:italic;"><?= $red_time ?></td>
                    <td><?= $just_red ?></td>
                    <td style="text-align:center;color:#00AA00;font-weight:bold;"><?= $actual_md ?></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align:right;font-size:9.5pt;">SubTotal MD (ST MD) Rounded to Total :</td>
                    <td style="text-align:center;color:#00AA00;font-weight:bold;"><?= $subtotal_md ?></td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Site2 Temporary -->
    <tr>
        <td colspan="4" class="section-hdr-blue">
            <strong>Audit-Man-days Calculation</strong>: <strong>Site2@@(Temporary Site)</strong>
            <span style="float:right;color:#00008B;">N/a</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding:0;">
            <table class="ft" style="margin:0;border:none;">
                <tr>
                    <th style="width:10%;"></th>
                    <th style="width:12%;">MDs as per Table P-07</th>
                    <th style="width:14%;">Additional Time</th>
                    <th style="width:14%;font-style:italic;">Reduction In Time</th>
                    <th style="width:35%;">Justification for addition and Reduction time</th>
                    <th style="width:15%;">Actual Man Days ,MD</th>
                </tr>
                <tr>
                    <td class="val-blue"><strong>QMS</strong></td>
                    <td></td><td></td><td style="font-style:italic;"></td><td></td><td></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align:right;font-size:9.5pt;">SubTotal MD (ST MD) Rounded to Total :</td>
                    <td></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="4" style="font-size:9pt;font-style:italic;">
            (@@) add separate table for other sites if sites are more than 2.
        </td>
    </tr>

    <!-- Audit time summary -->
    <tr>
        <td class="lbl-blue">Audit Time required :</td>
        <td class="lbl-blue">Initial Mandays:</td>
        <td colspan="2" style="text-align:right;color:#00AA00;font-weight:bold;font-size:11pt;"><?= $init_md ?></td>
    </tr>
    <tr>
        <td class="lbl-blue">Re Certification=2/3 of Initial MD=</td>
        <td style="color:#00AA00;font-weight:bold;"><?= $recert_md ?></td>
        <td class="lbl-blue" style="text-align:center;">Surveillance=1/3 of initial MD=</td>
        <td style="color:#00AA00;font-weight:bold;text-align:center;"><?= $surv_md ?></td>
    </tr>
    <tr>
        <td colspan="4" style="padding:0;">
            <table class="ft" style="margin:0;border:none;">
                <tr>
                    <td rowspan="2" class="lbl-blue">Audit time to be implemented for&nbsp;&nbsp;initial audit:</td>
                    <td style="width:18%;text-align:center;">ON SITE: Stage-1=</td>
                    <td style="width:18%;color:#00AA00;font-weight:bold;text-align:center;"><?= $stage1_md ?></td>
                    <td style="width:14%;text-align:center;">Stage-2=</td>
                    <td style="color:#00AA00;font-weight:bold;text-align:center;"><?= $stage2_md ?></td>
                </tr>
                <tr>
                    <td style="text-align:center;">Off Site:(Maximum 20% of total MD)</td>
                    <td></td><td></td><td></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php f02footer(2); ?>

<!-- ════════════════════════════════════════════════════════════
     PAGE 3 — Multi-site + Proposed Audit Programme + Team + Review Sig
     ════════════════════════════════════════════════════════════ -->
<div class="page-break"></div>

<table class="ft">
    <tr>
        <td class="lbl-blue" style="width:35%;vertical-align:top;">
            Are there any different activities performed in different sites?<br>
            How many similar sites?
        </td>
        <td colspan="2"><?= $diff_activities ?></td>
    </tr>
    <tr>
        <td class="lbl-blue" style="vertical-align:top;">Are there any differences between operations of similar sites?</td>
        <td colspan="2"><?= $diff_operations ?></td>
    </tr>
    <tr>
        <td class="lbl-blue" style="vertical-align:top;" rowspan="3">No. of sites selected for audit in case of multi sites, Justification on the selection of sites</td>
        <td style="width:32.5%;">Sites: (square root one +1 in general situations)</td>
        <td class="val-italic" style="width:32.5%;">n/a</td>
    </tr>
    <tr>
        <td>Temporary Site(s):</td>
        <td><?= $temp_sites ?></td>
    </tr>
    <tr>
        <td>Justification:</td>
        <td><?= $sites_justif ?></td>
    </tr>
    <tr>
        <td class="lbl-blue" style="vertical-align:top;">Audit Time on remote auditing techniques if used.</td>
        <td>MD: (Incase remote audit time is more than 30%: Justification-<br>And is it communicated to AB)</td>
        <td class="val-italic"><?= $remote_audit ?></td>
    </tr>

    <!-- Proposed Audit Programme -->
    <tr>
        <td class="lbl-blue" rowspan="7" style="vertical-align:top;">Proposed Audit Programme</td>
        <td>Incase of Seasonal business</td>
        <td style="color:#00AA00;"><strong><?= $seasonal_biz ?: 'N/a' ?></strong></td>
    </tr>
    <tr>
        <td>Time of Audit</td>
        <td>Season Audit(Months)<br><?= $season_months ?></td>
    </tr>
    <tr>
        <td>Audit Programme</td>
        <td></td>
    </tr>
    <tr>
        <td>Initial audit to be held in :</td>
        <td style="color:#00AA00;font-weight:bold;"><?= $init_month ?></td>
    </tr>
    <tr>
        <td>1st Surveillance in :</td>
        <td style="color:#00AA00;font-weight:bold;"><?= $surv1_month ?></td>
    </tr>
    <tr>
        <td>2nd surveillance :</td>
        <td style="color:#00AA00;font-weight:bold;"><?= $surv2_month ?></td>
    </tr>
    <tr>
        <td>and Renewal in :</td>
        <td style="color:#00AA00;font-weight:bold;"><?= $renewal_month ?></td>
    </tr>

    <!-- Proposed Audit Team -->
    <tr>
        <td class="lbl-blue" rowspan="7" style="vertical-align:top;">Proposed audit team</td>
        <td>Team Leader</td>
        <td><?= $team_leader ?></td>
    </tr>
    <tr>
        <td>Auditor(s):</td>
        <td><?= $auditors ?></td>
    </tr>
    <tr>
        <td>Auditor:</td>
        <td></td>
    </tr>
    <tr>
        <td>Technical Expert:</td>
        <td><?= $tech_expert ?></td>
    </tr>
    <tr>
        <td>Team Leader under supervision if any/ Witness Auditor:</td>
        <td><?= $tl_supervisor ?></td>
    </tr>
    <tr>
        <td>Observers(s):</td>
        <td><?= $observers ?></td>
    </tr>
    <tr>
        <td>Interpreter(s):</td>
        <td><?= $interpreters ?></td>
    </tr>

    <!-- Is audit team competent -->
    <tr>
        <td class="lbl-blue" style="vertical-align:top;">Is audit team competent to evaluate the control of outsourcing processes?</td>
        <td colspan="2"><?= $outsourcing_comp ?></td>
    </tr>
</table>

<!-- Reviewer signature block -->
<table class="ft" style="margin-top:4px;">
    <tr>
        <td class="lbl-blue" style="width:25%;">Reviewed By:</td>
        <td style="width:25%;"><?= $rev1_name ?></td>
        <td style="width:20%;">Signature :</td>
        <td style="width:30%;"><?= $rev1_sign ?></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>Date :</td>
        <td style="color:#00AA00;font-weight:bold;"><?= $rev1_date ?></td>
    </tr>
    <tr>
        <td class="lbl-blue">Technical &nbsp;Reviewer if required :</td>
        <td><?= $tech_rev_name ?></td>
        <td>Signature :</td>
        <td><?= $tech_rev_sign ?></td>
    </tr>
    <tr>
        <td></td><td></td>
        <td>Date :</td>
        <td><?= $tech_rev_date ?></td>
    </tr>

    <!-- Review after Stage 1 Audit Performed -->
    <tr>
        <td colspan="4" class="section-hdr-blue"><strong>Review after Stage 1 Audit Performed:</strong></td>
    </tr>
    <tr>
        <td class="lbl-blue">Review of Stage1 Audit details:</td>
        <td colspan="3"><?= $s1_stage1_detail ?></td>
    </tr>
    <tr>
        <td class="lbl-blue">Remarks or Conclusions, if any</td>
        <td colspan="3" style="color:#00AA00;font-weight:bold;"><?= $s1_remarks ?></td>
    </tr>
</table>

<?php f02footer(3); ?>

<!-- ════════════════════════════════════════════════════════════
     PAGE 4 — Review after Stage1 + Surveillance + Re-Cert
     ════════════════════════════════════════════════════════════ -->
<div class="page-break"></div>

<!-- Review after collecting info before Stage 1 Audit -->
<?php
$review_sections = [
    [
        'title'        => 'Review after Collecting the information before plan the Stage 1 Audit',
        'changes'      => $ras1_changes,
        'complaints'   => $ras1_complaints,
        'audit_chg'    => $ras1_audit_changes,
        'new_prog'     => $ras1_new_prog,
        'rev_by'       => $ras1_rev_by,
        'rev_sign'     => $ras1_rev_sign,
        'rev_date'     => $ras1_rev_date,
        'tech_sign'    => $ras1_tech_sign,
        'tech_date'    => $ras1_tech_date,
    ],
    [
        'title'        => 'Review after Collecting the information before plan the Surveillance Audit',
        'changes'      => $surv_changes,
        'complaints'   => $surv_complaints,
        'audit_chg'    => $surv_audit_chg,
        'new_prog'     => $surv_new_prog,
        'rev_by'       => $surv_rev_by,
        'rev_sign'     => $surv_rev_sign,
        'rev_date'     => $surv_rev_date,
        'tech_sign'    => $surv_tech_sign,
        'tech_date'    => $surv_tech_date,
    ],
    [
        'title'        => 'Review after Collecting the information before plan the Re Certification Audit',
        'changes'      => $recert_changes,
        'complaints'   => $recert_complaints,
        'audit_chg'    => $recert_audit_chg,
        'new_prog'     => $recert_new_prog,
        'rev_by'       => '',
        'rev_sign'     => '',
        'rev_date'     => '',
        'tech_sign'    => '',
        'tech_date'    => '',
    ],
];
foreach ($review_sections as $sec): ?>
<table class="ft" style="margin-top:6px;">
    <tr>
        <td colspan="4" class="section-hdr-blue"><strong><?= esc_html($sec['title']) ?></strong></td>
    </tr>
    <tr>
        <td class="lbl-blue" style="width:35%;">Review of Changes:</td>
        <td colspan="3"><?= $sec['changes'] ?></td>
    </tr>
    <tr>
        <td class="lbl-blue">Review action taken on complaints if any</td>
        <td colspan="3"><?= $sec['complaints'] ?></td>
    </tr>
    <tr>
        <td class="lbl-blue" style="vertical-align:top;">
            Any Changes proposed on audit team,<br>
            Methodologies, technologies used,<br>
            effective no. of man power, Audit man days.
        </td>
        <td colspan="3"><?= $sec['audit_chg'] ?></td>
    </tr>
    <tr>
        <td class="lbl-blue">New Audit Programme:</td>
        <td colspan="3"><?= $sec['new_prog'] ?></td>
    </tr>
    <tr>
        <td class="lbl-blue">Reviewed By:</td>
        <td style="width:18%;"><?= $sec['rev_by'] ?></td>
        <td style="width:20%;">Signature :</td>
        <td><?= $sec['rev_sign'] ?></td>
    </tr>
    <tr>
        <td></td><td></td>
        <td>Date :</td>
        <td style="color:#00AA00;font-weight:bold;"><?= $sec['rev_date'] ?></td>
    </tr>
    <tr>
        <td class="lbl-blue">Technical &nbsp;Reviewer if required :</td>
        <td></td>
        <td>Signature :</td>
        <td><?= $sec['tech_sign'] ?></td>
    </tr>
    <tr>
        <td></td><td></td>
        <td>Date :</td>
        <td><?= $sec['tech_date'] ?></td>
    </tr>
</table>
<?php endforeach; ?>

<?php f02footer(4); ?>

<!-- ════════════════════════════════════════════════════════════
     PAGE 5 — Annexure -1, Audit Time calculation sheet
     ════════════════════════════════════════════════════════════ -->
<div class="page-break"></div>

<table class="ft">
    <tr>
        <td colspan="4" class="section-hdr-blue" style="text-align:center;font-size:11pt;">
            <strong>Annexure -1, Audit Time calculation sheet</strong>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="font-size:9.5pt;">
            This sheet is applicable to all the schemes for the reduction/ increase in the man-days. The starting Audit Time [T] is the time defined in Tables in procedure P-07.
        </td>
    </tr>
    <tr>
        <td colspan="4" style="font-size:9.5pt;border-top:none;">
            [Kindly note that each factor carries a weight-age of not more than 10%. The total reduction in man-days can't be more than 30%]
        </td>
    </tr>

    <!-- Section A Header -->
    <tr>
        <td colspan="3" class="section-hdr-blue"><strong>A. Factors which may require additional time</strong></td>
        <td class="section-hdr-blue" style="text-align:center;"><strong>Applicable [Y/N]</strong></td>
    </tr>
    <?php foreach ($factors_a as $factor):
        $fkey = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $factor));
        $is_yes = ($annexure[$fkey] ?? '') === 'Yes';
        $is_no  = ($annexure[$fkey] ?? '') !== 'Yes';
    ?>
    <tr>
        <td colspan="3" class="lbl-blue"><?= esc_html($factor) ?></td>
        <td style="text-align:center;">
            <span class="cb"><?= f02cb($is_yes) ?></span> Yes &nbsp;&nbsp;
            <span class="cb"><?= f02cb($is_no) ?></span> No
        </td>
    </tr>
    <?php endforeach; ?>

    <!-- Section B Header -->
    <tr>
        <td colspan="3" class="section-hdr-blue"><strong>B. Factors which may require less time</strong></td>
        <td class="section-hdr-blue"></td>
    </tr>
    <?php foreach ($factors_b as $factor):
        $fkey   = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $factor));
        $is_na  = in_array($factor, $na_b);
        $is_yes = !$is_na && ($annexure[$fkey] ?? '') === 'Yes';
        $is_no  = !$is_na && ($annexure[$fkey] ?? '') !== 'Yes';
    ?>
    <tr>
        <td colspan="3" class="lbl-blue"><?= esc_html($factor) ?></td>
        <td style="text-align:center;">
            <?php if ($is_na): ?>
                <strong>N/A</strong>
            <?php else: ?>
                <span class="cb"><?= f02cb($is_yes) ?></span> Yes &nbsp;&nbsp;
                <span class="cb"><?= f02cb($is_no) ?></span> No
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php f02footer(5); ?>

</body>
</html>
