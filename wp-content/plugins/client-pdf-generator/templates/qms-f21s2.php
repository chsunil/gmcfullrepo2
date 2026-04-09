<?php
/**
 * QMS – F-21s1 Surveillance Year 2 Audit Report
 * ACF Group: group_65cf5ee61153
 *
 * Seamless clones (prefix_name=0) — read via source meta key:
 *   f21s2organization_name      → organization_name
 *   f21s2address                → head_office  (clones address group field_6996ae3512093)
 *   f21s2audit_ref_no           → proposal_ref_no  (field_68554bdf55898)
 *   f21s2management_representative → contact_person_name  (field_68173ed2aa379)
 *   f21s2top_management         → get_field('field_6974d40899d31')
 *   f21s2audit_criteria_standard → cert_scheme  (field_68173ed2b0218)
 *   f21s2exclusions             → exclusions_only_for_iso_9001  (field_6817433c24058)
 *   f21s2audit_scope_confirmed  → scope_of_certification  (field_68173ed2a657a)
 *   f21s2functional_units       → get_field('field_68480952e7d39')
 *   f21s2dates_of_audit         → stage2_audit_surveillance_audit_date_surv2 / certification_decision_date_surv2
 *   f21s2technical_code         → technical_code_area  (field_67fe8e52fc7e0)
 *   f21s2audit_team             → audit_team_allocation_plan repeater  (field_6970b191d4bbc)
 *   f21s2mrm_date               → mrm_date_surv2  (field_0033)
 *
 * Own fields (get_field by name):
 *   f21s2audit_objectives (textarea)
 *   f21s2brief_profile_of_the_organization_including__main_products_services__and_customers (textarea)
 *   f21s2positive_features (textarea)
 *   f21s2review_of_stage_1_audit_report (textarea)
 *   f21s2evaluation_of_internal_audits (group):
 *       f21s2verified (text), internal_audit_date (clone field_0030=internal_audit_date_surv2),
 *       frequency (clone field_6860433f0ff57), no_of_non_conformities (text)
 *   f21s2evaluation_of_management_review (text)
 *   f21s2usage_of_certification_documents_and_logos (text)
 *   f21s2review_of_any_changes (text)
 *   f21s2continuing_operational_controls (text)
 *   f21s2customer_complients_and_corrective_action_taken (text)
 *   f21s2observations_areas_for_potential_improvement (text)
 *   f21s2any_devation_from_audit_plan_with_reasons (text)
 *   f21s2significant_issues_impacting_the_audit_program (text)
 *   f21s2significant_issues_affecting_management_system_since_last_audit_took_place (text)
 *   f21s2any_unresolved_issues_if_any (text)
 *   f21s2any_planned_activities_for_continual_improvement (text)
 *   f21s2capability_of_management_system_to_meet_applicable_requirments_and_exceed_outcomes (text)
 *   f21s2nonconformities_identified_during_this_assessment (text)
 *   f21s2follow-up_measures_if_required (text)
 *   f21s2appropriateness_of_certification_scope (radio: Confirmed/Not Confirmed)
 *   f21s2fulfillment_of_audit_objectives (radio: Confirmed/Not Confirmed)
 *   f21s2additional_requirments_if_any (text)
 *   overall_comment_on_the_compliance_... (text, no f21s2 prefix)
 *   performance_of_the_management_system_... (text, no f21s2 prefix)
 *   f21s2next_assessment (radio: Surveillance-1 / Surveillance-2 / Re Certification)
 *   f21s2stage2_planned_on (date_picker)
 *   f21s2recommendations (checkbox)
 *   f21s2remarks (textarea)
 *   f21s2lead_auditor (group): lead_auditor (user, return_format=array), signature (text)
 *   f21s2attachments (checkbox)
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

// ── Seamless clone fields (read via source meta key) ─────────────────────────
$org     = esc_html( gmc_get_organization_name($post_id) );
$address = esc_html( get_post_meta($post_id, 'head_office', true) ?: '' );
$audit_ref = esc_html( get_post_meta($post_id, 'proposal_ref_no', true) ?: '' );
$standard  = esc_html( get_post_meta($post_id, 'cert_scheme', true) ?: '-' );
$exclusions= esc_html( get_post_meta($post_id, 'exclusions_only_for_iso_9001', true) ?: '-' );
$audit_scope = esc_html( get_post_meta($post_id, 'scope_of_certification', true) ?: '-' );
$tech_code   = esc_html( get_post_meta($post_id, 'technical_code_area', true) ?: '-' );

// Management representative (seamless clone of f01contact_person group)
$mgr_rep_raw = get_post_meta($post_id, 'contact_person_name', true)
    ?: get_post_meta($post_id, 'f01contact_person', true);
if ( is_array($mgr_rep_raw) ) {
    $mgr_rep = esc_html( implode(', ', array_filter(array_map('strval', $mgr_rep_raw))) ?: '' );
} else {
    $mgr_rep = esc_html( (string)$mgr_rep_raw ?: '-' );
}

// Top management (clone of field_6974d40899d31)
$top_mgmt_raw = get_field('field_6974d40899d31', $post_id);
if ( is_array($top_mgmt_raw) ) {
    $top_mgmt = esc_html( $top_mgmt_raw['display_name'] ?? implode(', ', array_filter(array_map('strval', $top_mgmt_raw))) );
} else {
    $top_mgmt = esc_html( (string)$top_mgmt_raw ?: '-' );
}

// Functional units (clone of field_68480952e7d39)
$func_raw = get_field('field_68480952e7d39', $post_id)
    ?: get_post_meta($post_id, 'functional_units_processes_audited', true);
$func_units = is_array($func_raw)
    ? esc_html( implode(', ', array_filter(array_map('strval', $func_raw))) )
    : esc_html( (string)$func_raw ?: '' );

// Dates of audit (surv1 audit date from audit_dates, falls back to cert decision date)
$audit_date = gmc_format_date(
    get_post_meta($post_id, 'stage2_audit_surveillance_audit_date_surv2', true)
    ?: get_post_meta($post_id, 'certification_decision_date_surv2', true)
);

// Audit team repeater (clone of field_6970b191d4bbc = audit_team_allocation_plan)
$audit_team = get_field('field_b62810cd7c51', $post_id) ?: [];

// MRM date (clone of field_0033 = mrm_date_surv2)
$mrm_date = gmc_format_date( get_post_meta($post_id, 'mrm_date_surv2', true) );

// ── Evaluation of Internal Audits (group) ────────────────────────────────────
$ia_grp      = get_field('f21s2evaluation_of_internal_audits', $post_id) ?: [];
$ia_verified = esc_html( $ia_grp['f21s2verified'] ?? '' );
$ia_freq     = esc_html( $ia_grp['frequency'] ?? '' );
$ia_ncs      = esc_html( $ia_grp['no_of_non_conformities'] ?? '' );
$ia_date     = gmc_format_date( get_post_meta($post_id, 'internal_audit_date_surv2', true) );

// ── Flat own fields ──────────────────────────────────────────────────────────
$audit_objectives = get_field('f21s2audit_objectives', $post_id) ?: '';
$brief_profile    = get_field('f21s2brief_profile_of_the_organization_including__main_products_services__and_customers', $post_id) ?: '';
$positive_features= get_field('f21s2positive_features', $post_id) ?: '';
$review_of_ncs    = get_field('f21s2review_of_stage_1_audit_report', $post_id) ?: '';
$eval_mgmt_review = esc_html( get_field('f21s2evaluation_of_management_review', $post_id) ?: '' );
$logo_usage       = esc_html( get_field('f21s2usage_of_certification_documents_and_logos', $post_id) ?: '' );
$changes_text     = esc_html( get_field('f21s2review_of_any_changes', $post_id) ?: '' );
$cont_op_ctrl     = esc_html( get_field('f21s2continuing_operational_controls', $post_id) ?: '' );
$complaints       = esc_html( get_field('f21s2customer_complients_and_corrective_action_taken', $post_id) ?: '' );
$observations     = esc_html( get_field('f21s2observations_areas_for_potential_improvement', $post_id) ?: '' );
$dev_plan         = esc_html( get_field('f21s2any_devation_from_audit_plan_with_reasons', $post_id) ?: '' );
$dev_issues       = esc_html( get_field('f21s2significant_issues_impacting_the_audit_program', $post_id) ?: '' );
$dev_changes      = esc_html( get_field('f21s2significant_issues_affecting_management_system_since_last_audit_took_place', $post_id) ?: '' );
$dev_unreslv      = esc_html( get_field('f21s2any_unresolved_issues_if_any', $post_id) ?: '' );
$planned_act      = esc_html( get_field('f21s2any_planned_activities_for_continual_improvement', $post_id) ?: '' );
$sb_capability    = esc_html( get_field('f21s2capability_of_management_system_to_meet_applicable_requirments_and_exceed_outcomes', $post_id) ?: '' );
$sb_ncs_dur       = esc_html( get_field('f21s2nonconformities_identified_during_this_assessment', $post_id) ?: '' );
$sb_followup      = esc_html( get_field('f21s2follow-up_measures_if_required', $post_id) ?: '' );
$approp_scope     = get_field('f21s2appropriateness_of_certification_scope', $post_id) ?: '';
$fulfill_obj      = get_field('f21s2fulfillment_of_audit_objectives', $post_id) ?: '';
$add_req          = esc_html( get_field('f21s2additional_requirments_if_any', $post_id) ?: '' );

// These two fields have no f21s2 prefix in the JSON
$overall_cmt = esc_html( get_field('overall_comment_on_the_compliance_of_the_system_to_the_requirements_of_the_standard_for_meeting_organization_policies_and_objectives', $post_id) ?: '' );
$perf_cycle  = esc_html( get_field('performance_of_the_management_system_over_the_period_of_certification_and_review_of_previous_audit_reports', $post_id) ?: '' );

// ── Conclusion fields ────────────────────────────────────────────────────────
$next_assmnt   = get_field('f21s2next_assessment', $post_id) ?: '';
$tentative_date= gmc_format_date( get_field('f21s2stage2_planned_on', $post_id) );
$recs          = get_field('f21s2recommendations', $post_id) ?: [];
$remarks_text  = esc_html( get_field('f21s2remarks', $post_id) ?: '' );

// ── Lead Auditor group ───────────────────────────────────────────────────────
$la_grp  = get_field('f21s2lead_auditor', $post_id) ?: [];
$la_user = $la_grp['lead_auditor'] ?? null;  // return_format=array
if ( is_array($la_user) ) {
    $la_name = esc_html( $la_user['display_name'] ?? '' );
} elseif ( is_numeric($la_user) && (int)$la_user > 0 ) {
    $u = get_userdata((int)$la_user);
    $la_name = $u ? esc_html($u->display_name) : '';
} else {
    $la_name = esc_html( (string)($la_user ?: '') );
}
$la_sig = esc_html( $la_grp['signature'] ?? '' );

// Attachments checkbox
$attachments = get_field('f21s2attachments', $post_id) ?: [];

// ── Build audit team by role (from repeater) ─────────────────────────────────
$lead_name   = '';
$co_auditors = [];
$auditors    = [];
foreach ( (array)$audit_team as $row ) {
    if ( ! is_array($row) ) continue;
    $role    = $row['f05_team_role'] ?? $row['role'] ?? '';
    $uid_val = $row['f05_team_name'] ?? $row['name'] ?? null;
    $nm = '';
    if ( is_array($uid_val) && isset($uid_val['display_name']) ) {
        $nm = $uid_val['display_name'];
    } elseif ( is_numeric($uid_val) && (int)$uid_val > 0 ) {
        $u  = get_userdata((int)$uid_val);
        $nm = $u ? $u->display_name : '';
    } elseif ( is_string($uid_val) ) {
        $nm = $uid_val;
    }
    if ( ! $nm ) continue;
    if ( stripos($role, 'lead') !== false ) {
        $lead_name = $nm;
    } elseif ( stripos($role, 'co') !== false ) {
        $co_auditors[] = $nm;
    } else {
        $auditors[] = $nm;
    }
}
if ( ! $lead_name ) $lead_name = $la_name;
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 12mm 10mm 12mm 10mm; }
body { font-family: Arial, sans-serif; font-size: 9.5px; color: #000; margin: 0; padding: 0; line-height: 1.4; }
table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
th, td { border: 1px solid #333; padding: 4px 5px; vertical-align: top; }
th { font-weight: bold; background: #f0f0f0; text-align: left; }
.no-border { border: none !important; background: transparent !important; }
.center { text-align: center; }
.lbl { font-weight: bold; background: #f5f5f5; width: 30%; white-space: nowrap; }
.lbl-sm { font-weight: bold; background: #f5f5f5; width: 22%; }
.sec-hdr { background: #d9d9d9; font-weight: bold; font-size: 10px; text-transform: uppercase; }
.h-title { text-align: center; font-size: 13px; font-weight: bold; }
.chk { font-family: monospace; font-size: 11px; }
</style>
</head>
<body>

<!-- ====== HEADER ====== -->
<?php if ($LOGO) : ?>
<table style="margin-bottom:4px;">
  <tr>
    <td class="no-border" style="text-align:center;">
      <img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:65px; width:auto;" />
    </td>
  </tr>
</table>
<?php endif; ?>

<table>
  <tr>
    <td colspan="4" class="h-title" style="border:none; text-align:center; padding:4px 0;">
      Audit Report
    </td>
  </tr>
  <tr>
    <td class="lbl-sm">Client</td>
    <td colspan="3" style="font-weight:bold;"><?= $org ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Address</td>
    <td colspan="3"><?= $address ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Audit Ref No.</td>
    <td><?= $audit_ref ?></td>
    <td class="lbl-sm">Type</td>
    <td>
      <span class="chk"><?= ($next_assmnt === 'Surveillance-1') ? '[X]' : '[ ]' ?></span> Surveillance 1 &nbsp;
      <span class="chk">[ ]</span> Re-certification
    </td>
  </tr>
</table>

<p style="font-size:8px; color:#555; font-style:italic; margin:4px 0;">
  <em>Disclaimer: The Auditing is based on a sampling process of the available information. GLOBAL MANAGEMENT CERTIFICATION SERVICES PRIVATE LIMITED | Flat No.402, Plot No.410, Matrusri Nagar, Miyapur, Hyderabad-500 049 | Tel: 040-48559001 | E-mail: info@mcsglobal.in | www.mcsglobal.in</em>
</p>
<p style="font-size:8px; color:#555; font-style:italic; margin:2px 0;">
  F-21 Version 1.00 &nbsp;|&nbsp; QMS Surveillance Year 2
</p>

<!-- ====== AUDIT DETAILS ====== -->
<table>
  <tr><td colspan="4" class="sec-hdr">Audit Details</td></tr>
  <tr>
    <td class="lbl-sm">Management Representative</td>
    <td><?= $mgr_rep ?></td>
    <td class="lbl-sm">Top Management</td>
    <td><?= $top_mgmt ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Audit Criteria [Standard]</td>
    <td><?= $standard ?></td>
    <td class="lbl-sm">Exclusion (ISO 9001)</td>
    <td><?= $exclusions ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Audit Objectives</td>
    <td colspan="3"><?= nl2br(esc_html($audit_objectives)) ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Audit Scope [Confirmed]</td>
    <td colspan="3"><?= $audit_scope ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Functional Units / Processes Audited</td>
    <td colspan="3"><?= $func_units ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Audit Site[s]</td>
    <td><?= $address ?></td>
    <td class="lbl-sm">Date[s] of Audit</td>
    <td><?= $audit_date ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Technical Code</td>
    <td colspan="3"><?= $tech_code ?></td>
  </tr>
</table>

<table>
  <tr><td colspan="2" class="sec-hdr">Audit Team</td></tr>
  <tr>
    <td class="lbl-sm">Team Leader</td>
    <td><?= esc_html($lead_name ?: '-') ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Co Auditor</td>
    <td><?= esc_html(implode(', ', $co_auditors) ?: '&nbsp;') ?></td>
  </tr>
  <tr>
    <td class="lbl-sm">Auditor</td>
    <td><?= esc_html(implode(', ', $auditors) ?: '&nbsp;') ?></td>
  </tr>
</table>

<!-- ====== MAIN BODY ====== -->
<table>
  <tr>
    <td class="lbl">Brief Profile of the organization including main products/services and customers</td>
    <td><?= nl2br(esc_html($brief_profile)) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Positive features</td>
    <td><?= nl2br(esc_html($positive_features)) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Review of Action Taken on Identified nonconformities during previous Audit (If Applicable)</td>
    <td><?= nl2br(esc_html($review_of_ncs)) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Evaluation of Internal Audits</td>
    <td>
      <?php if ($ia_verified) echo nl2br(esc_html($ia_verified)) . '<br>'; ?>
      <?php if ($ia_freq)     echo '<strong>Frequency:</strong> ' . nl2br(esc_html($ia_freq)) . '<br>'; ?>
      <strong>Internal Audit Date:</strong> <?= $ia_date ?: '&nbsp;' ?><br>
      <strong>No of Non conformities:</strong> <?= $ia_ncs ?: '&nbsp;' ?>
    </td>
  </tr>
  <tr>
    <td class="lbl">Evaluation of Management Review</td>
    <td><?= nl2br($eval_mgmt_review) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">MRM Date</td>
    <td><?= $mrm_date ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Usage of Certification Documents and Logos</td>
    <td><?= nl2br($logo_usage) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Review of any changes</td>
    <td><?= nl2br($changes_text) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Continuing operational control</td>
    <td><?= nl2br($cont_op_ctrl) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Customer complaints and corrective action taken</td>
    <td><?= nl2br($complaints) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Observations / areas for potential improvement</td>
    <td><?= nl2br($observations) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Any Deviation from Audit plan with reasons</td>
    <td><?= nl2br($dev_plan) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Significant issues impacting the audit program</td>
    <td><?= nl2br($dev_issues) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Significant issues affecting management system since last audit took place</td>
    <td><?= nl2br($dev_changes) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Any Unresolved Issues, if any</td>
    <td><?= nl2br($dev_unreslv) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Any Planned Activities for continual improvement</td>
    <td><?= nl2br($planned_act) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Capability of management System to meet applicable requirements and exceed outcomes</td>
    <td><?= nl2br($sb_capability) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Nonconformities identified during this assessment</td>
    <td><?= nl2br($sb_ncs_dur) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Follow-up measures, if required</td>
    <td><?= nl2br($sb_followup) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Appropriateness of Certification Scope</td>
    <td>
      <span class="chk"><?= ($approp_scope === 'Confirmed') ? '[X]' : '[ ]' ?></span> Confirmed &nbsp;
      <span class="chk"><?= ($approp_scope === 'Not Confirmed') ? '[X]' : '[ ]' ?></span> Not Confirmed
    </td>
  </tr>
  <tr>
    <td class="lbl">Fulfillment of Audit Objectives</td>
    <td>
      <span class="chk"><?= ($fulfill_obj === 'Confirmed') ? '[X]' : '[ ]' ?></span> Confirmed &nbsp;
      <span class="chk"><?= ($fulfill_obj === 'Not Confirmed') ? '[X]' : '[ ]' ?></span> Not Confirmed
    </td>
  </tr>
  <tr>
    <td class="lbl">Additional Requirements if any</td>
    <td><?= nl2br($add_req) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Overall Comment on the compliance of the system, to the requirements of the standard for meeting organization policies and objectives</td>
    <td><?= nl2br($overall_cmt) ?: '&nbsp;' ?></td>
  </tr>
  <tr>
    <td class="lbl">Performance of the management system over the period of certification and review of previous audit reports</td>
    <td><?= nl2br($perf_cycle) ?: '&nbsp;' ?></td>
  </tr>
</table>

<!-- ====== CONCLUSION ====== -->
<table>
  <tr><td colspan="2" class="sec-hdr">The Next Assessment</td></tr>
  <tr>
    <td colspan="2">
      <span class="chk"><?= ($next_assmnt === 'Surveillance-1') ? '[X]' : '[ ]' ?></span> Surveillance-1 &nbsp;&nbsp;
      <span class="chk"><?= ($next_assmnt === 'Surveillance-2') ? '[X]' : '[ ]' ?></span> Surveillance-2 &nbsp;&nbsp;
      <span class="chk"><?= ($next_assmnt === 'Re Certification') ? '[X]' : '[ ]' ?></span> Re Certification
    </td>
  </tr>
  <tr>
    <td class="lbl-sm">Tentative Date:</td>
    <td><?= $tentative_date ?: '&nbsp;' ?></td>
  </tr>
</table>

<?php
$conclusion_options = [
    'recommend_continuation'   => 'Recommend for Continuation of Certification',
    'recommend_followup'       => 'Recommend for follow up assessment',
    'recommend_full_assessment'=> 'Recommend for full assessment again',
];
?>
<table>
  <tr><td colspan="2" class="sec-hdr">Conclusion and Recommendation</td></tr>
  <?php foreach ( $conclusion_options as $key => $label ) :
      $checked = in_array($key, (array)$recs) ? '[X]' : '[ ]';
  ?>
  <tr>
    <td style="width:5%; text-align:center;" class="chk"><?= $checked ?></td>
    <td><?= esc_html($label) ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<table>
  <tr>
    <td class="lbl-sm">Remarks:</td>
    <td><?= nl2br($remarks_text) ?: '&nbsp;' ?></td>
  </tr>
</table>

<p style="font-size:8.5px; color:#333; margin:6px 0; font-style:italic;">
  I pledge that the report and records will remain confidential and will not be shared with any other person or organization and abide with the confidentiality and no conflict of interest agreement signed.
</p>

<table>
  <tr><td colspan="4" class="sec-hdr">Audit Team Leader</td></tr>
  <tr>
    <td class="lbl-sm">Name</td>
    <td><?= esc_html($lead_name ?: '-') ?></td>
    <td class="lbl-sm">Signature</td>
    <td><?= $la_sig ?: '&nbsp;' ?></td>
  </tr>
</table>

<!-- ====== ATTACHMENTS ====== -->
<?php
$attach_list = [
    'AUDIT TEAM ALLOCATION PLAN [F-05]' => 'Audit Team Allocation Plan [F-05]',
    'Audit schedule [F-08]'             => 'Audit Schedule [F-08]',
    'Non Conformity Reports [F-10]'     => 'Non Conformity Reports [F-10]',
    'Attendance Sheet [F-13]'           => 'Attendance Sheet [F-13]',
    'Confidentiality [ F-14]'           => 'Confidentiality [F-14]',
    'Assessment Checklist [F-25]'       => 'Assessment Checklist [F-25]',
    'Audit Program [F-16]'              => 'Audit Program [F-16]',
];
$attach_pairs = array_chunk(array_keys($attach_list), 2, true);
?>
<table>
  <tr><td colspan="4" class="sec-hdr">Attachments</td></tr>
  <?php foreach ( $attach_pairs as $pair ) :
      $keys = array_keys($pair);
  ?>
  <tr>
    <?php foreach ( $keys as $k ) :
        $chk = in_array($k, (array)$attachments) ? '[X]' : '[ ]';
    ?>
    <td style="width:5%; text-align:center;" class="chk"><?= $chk ?></td>
    <td><?= esc_html($attach_list[$k]) ?></td>
    <?php endforeach; ?>
    <?php if ( count($keys) < 2 ) : ?><td colspan="2">&nbsp;</td><?php endif; ?>
  </tr>
  <?php endforeach; ?>
</table>

</body>
</html>
