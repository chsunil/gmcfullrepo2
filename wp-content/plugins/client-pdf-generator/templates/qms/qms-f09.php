<?php
/**
 * QMS – F-09 Stage-2 / Surveillance Audit Report
 * ACF Group: group_68850e57b5024
 *
 * Clone fields (prefix_name=0) — all seamless, data stored under source meta key:
 *   f09client                   → field_org_name          → organization_name
 *   f09adress                   → field_68173ed29add4     → head_office
 *   f09site                     → field_68173ed2a08cc     → (main_operative_site)
 *   f09Audit_Ref_No             → field_69b4128404509     → (audit ref no)
 *   f09management_representative→ field_68173ed2aa379     → contact_person group
 *   f09top_management           → field_68173ed351a54     → top_management
 *   f09audit_criteria_standard  → field_qms_f05a_6        → cert_scheme
 *   f09exclusion__iso_9001      → field_6817433c24058     → exclusions_only_for_iso_9001
 *   f09audit_scope_confirmed    → field_qms_f11_audit_scope_confirmed → scope_of_certification
 *   f09functional_unitsprocesses_audited → field_68480952e7d39
 *   f09audit_sites              → field_68850e8db8da2     → head_office (chains to f09adress)
 *   f09dates_of_audit           → field_0023              → stage2_audit_surveillance_audit_date_initial
 *   f09technical_code           → field_67fe8e52fc7e0     → technical_code
 *   f09audit_team               → field_6883436eb352e     → proposed_audit_team
 *   brief_profile_of_the_org... → field_qms_f11_a_answer
 *   f09evaluation_of_internal_audits → field_686042ea0ff55
 *   MRM_Date                    → field_688517e459c14 (circular — read from mrm_date_initial)
 *   tentative_date              → field_0024              → stage2_audit_surveillance_audit_date_surv1
 *   team_leader                 → field_audit_team_leader
 *
 * Audit Objectives textarea has name="" — unreadable via get_field(); hardcode default text.
 */

if ( ! defined('ABSPATH') ) exit;

// ── Helpers ───────────────────────────────────────────────────────────────────
if ( ! function_exists('f09v') ) {
    function f09v( $v, $fallback = '-' ) {
        if ( $v === null || $v === '' || $v === false ) return $fallback;
        if ( is_array($v) ) {
            if ( isset($v['display_name']) ) return esc_html( $v['display_name'] );
            if ( isset($v['label']) )        return esc_html( $v['label'] );
            if ( isset($v['value']) )        return esc_html( $v['value'] );
            $flat = array_filter( array_map( fn($i) => is_string($i) ? $i : '', $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fallback;
        }
        return esc_html( (string) $v );
    }
}
if ( ! function_exists('f09date') ) {
    function f09date( $v, $fallback = '-' ) {
        if ( ! $v ) return $fallback;
        if ( function_exists('gmc_format_date') ) return gmc_format_date( $v );
        if ( preg_match('/^\d{8}$/', $v) ) {
            $v = substr($v,0,4).'-'.substr($v,4,2).'-'.substr($v,6,2);
        }
        if ( preg_match('/^\d{4}-\d{2}-\d{2}/', $v) ) {
            return date( 'd/m/Y', strtotime($v) );
        }
        return esc_html( $v );
    }
}
if ( ! function_exists('f09radio') ) {
    // Renders radio options with [X] / [ ]
    function f09radio( $options, $current, $layout = 'horizontal' ) {
        $out = [];
        foreach ( $options as $val => $label ) {
            $checked = ( (string)$current === (string)$val ) ? '[X]' : '[ ]';
            $out[] = '<span style="font-family:monospace;">' . $checked . '</span> ' . esc_html($label);
        }
        $sep = $layout === 'horizontal' ? ' &nbsp; ' : '<br>';
        return implode( $sep, $out );
    }
}

// ── Logo ──────────────────────────────────────────────────────────────────────
$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

// ── TOP-LEVEL FIELDS ─────────────────────────────────────────────────────────
// Organization Name
$org_raw = function_exists('gmc_get_organization_name')
    ? gmc_get_organization_name( $post_id )
    : get_field( 'organization_name', $post_id );
if ( is_array($org_raw) ) $org_raw = $org_raw['organization_name'] ?? reset($org_raw) ?? '';
if ( ! $org_raw ) $org_raw = get_post_field( 'post_title', $post_id );
$org = esc_html( (string)$org_raw );

// Address — seamless clone of field_68173ed29add4 (head_office)
$addr_raw = get_field( 'f09adress', $post_id );
$address  = is_array($addr_raw)
    ? esc_html( $addr_raw['head_office'] ?? reset($addr_raw) ?? '-' )
    : f09v( $addr_raw ?: get_post_meta( $post_id, 'head_office', true ) );

// Site — seamless clone of field_68173ed2a08cc → meta key: other_sites
$site_raw = get_field( 'f09site', $post_id );
$site     = is_array($site_raw)
    ? f09v( reset($site_raw) )
    : f09v( $site_raw ?: get_post_meta( $post_id, 'other_sites', true ) );

// Audit Ref No. — seamless clone of field_69b4128404509 → meta key: f03proposal_ref_no
$audit_ref = f09v(
    get_post_meta( $post_id, 'f03proposal_ref_no', true )
    ?: get_field( 'f09Audit_Ref_No', $post_id )
    ?: get_post_meta( $post_id, 'proposal_ref_no', true )
);

// Stage — select: stage-2 / Re Certification
$f09stage = get_field( 'f09stage', $post_id ) ?: '';

// Management Representative — seamless clone of contact_person group
$mgr_rep_raw = get_field( 'f09management_representative', $post_id )
               ?: get_field( 'f01contact_person', $post_id );
$mgr_rep = is_array($mgr_rep_raw)
    ? esc_html( $mgr_rep_raw['contact_person_name'] ?? reset($mgr_rep_raw) ?? '-' )
    : f09v( $mgr_rep_raw );

// Top Management — seamless clone of field_68173ed351a54 → meta key: contact_person_top_management
$top_mgmt_raw = get_field( 'f09top_management', $post_id );
$top_mgmt = is_array($top_mgmt_raw)
    ? esc_html( $top_mgmt_raw['contact_person_top_management'] ?? $top_mgmt_raw['top_management'] ?? reset($top_mgmt_raw) ?? '-' )
    : f09v( $top_mgmt_raw ?: get_post_meta( $post_id, 'contact_person_top_management', true ) );

// Audit Criteria / Standard — seamless clone of field_qms_f05a_6 → meta key: audit_criteria
$standard = f09v(
    get_post_meta( $post_id, 'audit_criteria', true )
    ?: get_field( 'f09audit_criteria_standard', $post_id )
    ?: get_post_meta( $post_id, 'cert_scheme', true )
);

// Exclusion (ISO 9001) — seamless clone of field_6817433c24058 → meta key: exclusions_only_for_iso_9001
$exclusions = f09v(
    get_post_meta( $post_id, 'exclusions_only_for_iso_9001', true )
    ?: get_field( 'f09exclusion__iso_9001', $post_id )
);

// Audit Objectives — field has name="" so not readable via get_field(); use default
$audit_objectives = "* Determination of the conformity of the client's management system, or parts of it, with audit criteria;\n"
    . "* Evaluation of the ability of the management system to ensure that the client organization meets applicable statutory, regulatory and contractual requirements;\n"
    . "* Evaluation of the effectiveness of the management system to ensure that the client organization is continually meeting its specified objectives;\n"
    . "* Identification of areas for potential improvement of the management system.\n"
    . "* To Verify the organization compliance and effective maintenance of QMS in accordance with ISO 9001:2015\n"
    . "* To confirm client adheres to its own policies, objectives and procedures.\n"
    . "* Collective information and evidence about conformity to all requirements of the standard.\n"
    . "* Performance monitoring, measuring, reporting and reviewing against key performance objectives and targets wrt management system standard.";

// Audit Scope [confirmed] — seamless clone of field_qms_f11_audit_scope_confirmed → meta key: f11audit_scope_confirmed
$audit_scope = f09v(
    get_post_meta( $post_id, 'f11audit_scope_confirmed', true )
    ?: get_field( 'f09audit_scope_confirmed', $post_id )
    ?: get_post_meta( $post_id, 'scope_of_certification', true )
);

// Functional Units / Processes Audited — direct textarea field, meta key: Process_Operations_site_specific_
$func_units = f09v( get_post_meta( $post_id, 'Process_Operations_site_specific_', true ) );

// Audit Site[s] — chains to head_office
$audit_sites_raw = get_field( 'f09audit_sites', $post_id );
$audit_sites = is_array($audit_sites_raw)
    ? esc_html( $audit_sites_raw['head_office'] ?? reset($audit_sites_raw) ?? '-' )
    : f09v( $audit_sites_raw ?: get_post_meta( $post_id, 'head_office', true ) );

// Date[s] of Audit — seamless clone of field_0023 → stage2_audit_surveillance_audit_date_initial
$audit_date_raw = get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_initial', true );
$audit_date = f09date( $audit_date_raw );

// Technical Code — seamless clone of field_67fe8e52fc7e0 → meta key: review_application_technical_code_area
$tech_code = f09v(
    get_post_meta( $post_id, 'review_application_technical_code_area', true )
    ?: get_field( 'f09technical_code', $post_id )
);

// Audit Team — seamless clone of proposed_audit_team (repeater)
$audit_team = get_field( 'f09audit_team', $post_id )
              ?: get_field( 'proposed_audit_team', $post_id )
              ?: get_field( 'f05_audit_team', $post_id )
              ?: [];

// ── REPORT SECTION FIELDS ────────────────────────────────────────────────────
// Brief Profile — seamless clone of field_qms_f11_a_answer → meta key: Brief_Profile_of_the_Organization
$brief_profile = f09v(
    get_post_meta( $post_id, 'Brief_Profile_of_the_Organization', true )
    ?: get_field( 'brief_profile_of_the_organization_including__main_products_services__and_customers', $post_id ),
    ''
);

// Positive Features
$positive_features = esc_html( get_field( 'f09positive_features', $post_id ) ?: '-' );

// Review of Action Taken on NCs from previous audit
$review_action = esc_html( get_field(
    'f09review_of_action_taken_on_identified_nonconformities_during_previous_auditactions_initiated_for_the_observations_addressed_in_stage_1_audit______if_applicable',
    $post_id
) ?: '-' );

// Comparison with previous audits
$comparison = esc_html( get_field( 'comparison_with_the_results_of_previous_audits', $post_id ) ?: '-' );

// Evaluation of Internal Audits — group with 3 sub-fields (seamless clone of field_686042ea0ff55)
$ia_grp  = get_field( 'f09evaluation_of_internal_audits', $post_id ) ?: [];
if ( is_string($ia_grp) ) {
    // Edge case: returned as flat string
    $ia_general = esc_html( $ia_grp );
    $ia_date = $ia_freq = $ia_ncs = '';
} else {
    $ia_general = '';
    $ia_date_raw = $ia_grp['internal_audit_date'] ?? '';
    $ia_date = f09date( is_array($ia_date_raw) ? '' : $ia_date_raw );
    $ia_freq = esc_html( $ia_grp['frequency'] ?? '-' );
    $ia_ncs  = esc_html( $ia_grp['no_of_non_conformities'] ?? '-' );
}

// Evaluation of Management Review
$eval_mgmt_review = esc_html( get_field( 'evaluation_of_management_review', $post_id ) ?: '-' );

// MRM Date — circular clone; read from audit dates meta directly
$mrm_date_raw = get_post_meta( $post_id, 'mrm_date_initial', true );
$mrm_date = f09date( $mrm_date_raw );

// Usage of Certification Documents and Logos
$cert_usage = esc_html( get_field( 'usage_of_certification_documents_and_logos', $post_id ) ?: '-' );

// Review of Any Changes
$review_changes = esc_html( get_field( 'review_of_any_changes', $post_id ) ?: '-' );

// Continuing Operational Controls
$oper_controls = esc_html( get_field( 'continuing_operational_controls', $post_id ) ?: '-' );

// Customer Complaints, Appeals, etc.
$customer_complaints = esc_html( get_field(
    'customer_complaints_appeals_investigation_and_corrective_&_preventive_actions_taken_if_any',
    $post_id
) ?: '-' );

// Observations / areas for potential improvement
$observations = esc_html( get_field( 'observations_areas_for_potential_improvement', $post_id ) ?: '-' );

// Deviations / issues
$deviation       = esc_html( get_field( 'any_devation_from_audit_plan_with_reasons', $post_id ) ?: '-' );
$sig_issues_prog = esc_html( get_field( 'significant_issues_impacting_the_audit_program', $post_id ) ?: '-' );
$sig_issues_ms   = esc_html( get_field( 'significant_issues_affecting_management_system_since_last_audit_took_place', $post_id ) ?: '-' );
$unresolved      = esc_html( get_field( 'any_unresolved_issues_if_any', $post_id ) ?: '-' );

// Risk assessment procedures
$risk_procedures = esc_html( get_field( 'are_the_procedures_employed_in_risk_assessment_are_sound_and_properly_implemented', $post_id ) ?: '-' );

// Summary of document review
$doc_review_summary = esc_html( get_field( 'summary_of_document_review_adequacy_of_internal_organization_and_procedures_adopted_by_the_client', $post_id ) ?: '-' );

// Planned activities for continual improvement
$planned_activities = esc_html( get_field( 'any_planned_activities_for_continual_improvement', $post_id ) ?: '-' );

// Capability of management system
$ms_capability = esc_html( get_field( 'capability_of_management_system_to_meet_applicable_requirements_and_exceed_outcomes', $post_id ) ?: '-' );

// ── CONCLUSION FIELDS ────────────────────────────────────────────────────────
// Nonconformities (radio: Major / Minor / Nil)
$nc_type = get_field( 'nonconformities_identified__during_this_assessment:', $post_id ) ?: '';
$nc_nos  = esc_html( get_field( 'nos', $post_id ) ?: '-' );

// Follow-up Measures
$followup    = esc_html( get_field( 'follow-up_measures_if_required', $post_id ) ?: '-' );
$add_reqs    = esc_html( get_field( 'additional_requirements_if_any_to_be_provided_during_subsequent_audits', $post_id ) ?: '-' );
$imp_points  = esc_html( get_field( 'important_points_for_planning_forthcoming_audit_on-siteict', $post_id ) ?: '-' );

// Overall Comment
$overall_comment = esc_html( get_field( 'overall_comment_on_the_compliance_of_the_system_to_the_requirements_of_the_standard_for_meeting_organization_policies_and_objectives', $post_id ) ?: '-' );
$ms_performance  = esc_html( get_field( 'performance_of_the_management_system_over_the_period_of_certification_and_review_of_previous_surveillance_audit_reports', $post_id ) ?: '-' );
$level_integ     = esc_html( get_field( 'level_of_integration', $post_id ) ?: '-' );

// Radio fields
$scope_appropriate  = get_field( 'appropriateness_of_certification_scope', $post_id ) ?: '';
$fulfill_objectives = get_field( 'Fulfillment_of_Audit_Objectives', $post_id ) ?: '';
$next_assessment    = get_field( 'the_next_assessment', $post_id ) ?: '';
$audit_type         = get_field( 'audit_type', $post_id ) ?: '';
$conclusion         = get_field( 'conclusion_and_recommendation', $post_id ) ?: '';

// Tentative Date (seamless clone of field_0024 → stage2_audit_surveillance_audit_date_surv1)
$tentative_date_raw = get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_surv1', true );
$tentative_date = f09date( $tentative_date_raw );

// Sign-off
$remarks     = esc_html( get_field( 'remarks', $post_id ) ?: '-' );
// Team Leader — seamless clone of field_audit_team_leader → meta key: audit_team_leader
$team_ldr_raw = get_post_meta( $post_id, 'audit_team_leader', true )
                ?: get_field( 'team_leader', $post_id );
$team_leader  = is_array($team_ldr_raw)
    ? esc_html( $team_ldr_raw['display_name'] ?? $team_ldr_raw['audit_team_leader'] ?? reset($team_ldr_raw) ?? '-' )
    : f09v( $team_ldr_raw );
$signature   = esc_html( get_field( 'siganture', $post_id ) ?: '-' );

// Audit type / title for page heading
$report_title = 'STAGE-2 / SURVEILLANCE AUDIT REPORT';
if ( $audit_type === 'Re Certification' ) $report_title = 'RE-CERTIFICATION AUDIT REPORT';
elseif ( $audit_type === 'Surveillance' )  $report_title = 'SURVEILLANCE AUDIT REPORT';
elseif ( $audit_type === 'Stage -2' )      $report_title = 'STAGE-2 AUDIT REPORT';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
  @page { margin: 12mm 10mm 14mm 10mm; }
  body  { font-family: Arial, sans-serif; font-size: 9.5px; color: #000; margin: 0; padding: 0; line-height: 1.4; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
  th, td { border: 1px solid #333; padding: 4px 5px; vertical-align: top; }
  th { font-weight: bold; background: #f0f0f0; text-align: left; }
  .no-border td, .no-border th { border: none; background: transparent; }
  .center { text-align: center; }
  .pagebreak { page-break-before: always; }
  .h-logo  { border: none; text-align: center; vertical-align: middle; }
  .h-title { text-align: center; font-size: 13px; font-weight: bold; vertical-align: middle; }
  .h-right { text-align: right; font-size: 9px; vertical-align: bottom; white-space: nowrap; }
  .lbl  { font-weight: bold; background: #f5f5f5; width: 22%; }
  .val  { width: 28%; }
  .sec-hdr { background: #d9d9d9; font-weight: bold; font-size: 10px; }
  .sn-td   { text-align: center; width: 4%; }
  .footer  {
    position: fixed; bottom: 0; left: 0; right: 0;
    font-size: 7.5px; text-align: center; border-top: 1px solid #999; padding-top: 2px; color: #333;
  }
</style>
</head>
<body>

<!-- ══ PAGE 1: COVER HEADER ══════════════════════════════════════════════════ -->
<table style="margin-top:80px">
  <tr>
    <?php if ( $LOGO ) : ?>
    <td class="h-logo no-border" style="width:15%">
      <img src="<?= $LOGO ?>" alt="Logo" style="max-width:80px;max-height:70px;">
    </td>
    <?php endif; ?>
    <td class="h-title no-border"><?= esc_html( $report_title ) ?></td>
  </tr>
</table>

<table>
  <tr>
    <td class="lbl">Audit Ref No.</td>
    <td><?= $audit_ref ?></td>
  </tr>
  <tr>
    <td class="lbl">Client</td>
    <td><?= $org ?></td>
  </tr>
  <tr>
    <td class="lbl">Address</td>
    <td><?= $address ?></td>
  </tr>
  <tr>
    <td class="lbl">Site</td>
    <td><?= $site ?></td>
  </tr>
</table>

<table>
  <tr><td colspan="6" class="sec-hdr">Audit Details</td></tr>
  <tr>
    <td class="lbl">Management Representative</td>
    <td><?= $mgr_rep ?></td>
    <td class="lbl">Top Management</td>
    <td colspan="3"><?= $top_mgmt ?></td>
  </tr>
  <tr>
    <td class="lbl">Audit Criteria [Standard]</td>
    <td colspan="2"><?= $standard ?></td>
    <td class="lbl">Exclusion (ISO 9001)</td>
    <td colspan="2"><?= $exclusions ?></td>
  </tr>
  <tr>
    <td class="lbl">Audit Objectives</td>
    <td colspan="5"><?= nl2br(esc_html($audit_objectives)) ?></td>
  </tr>
  <tr>
    <td class="lbl">Audit Scope [Confirmed]</td>
    <td colspan="5"><?= $audit_scope ?></td>
  </tr>
  <tr>
    <td class="lbl">Functional Units / Processes Audited</td>
    <td colspan="5"><?= $func_units ?></td>
  </tr>
  <tr>
    <td class="lbl">Audit Site[s]</td>
    <td colspan="2"><?= $audit_sites ?></td>
    <td class="lbl">Date[s] of Audit</td>
    <td colspan="2"><?= $audit_date ?></td>
  </tr>
  <tr>
    <td class="lbl">Technical Code</td>
    <td colspan="5"><?= $tech_code ?></td>
  </tr>
</table>

<!-- Fixed footer on all pages -->
<div class="footer">
  GLOBAL MANAGEMENT CERTIFICATION SERVICES PRIVATE LIMITED &nbsp;|&nbsp;
  Flat No.402, Plot No.410, Matrusri Nagar, Miyapur, Hyderabad-500 049, India &nbsp;|&nbsp;
  Tel: 040-48559001 &nbsp;|&nbsp; E.mail: info@mcsglobal.in &nbsp;|&nbsp; Website: www.mcsglobal.in
</div>

<div class="pagebreak"></div>

<!-- ══ PAGE 2: AUDIT TEAM ═════════════════════════════════════════════════════ -->
<table>
  <tr><td colspan="2" class="sec-hdr">Audit Team</td></tr>
  <?php
  // Build role map from repeater
  $team_by_role = [];
  foreach ( (array)$audit_team as $row ) {
      $role    = is_array($row) ? ( $row['f05_team_role'] ?? $row['role'] ?? '' ) : '';
      $uid_val = is_array($row) ? ( $row['f05_team_name'] ?? $row['name'] ?? null ) : null;
      $nm = '';
      if ( is_array($uid_val) && isset($uid_val['display_name']) ) {
          $nm = $uid_val['display_name'];
      } elseif ( is_numeric($uid_val) && (int)$uid_val > 0 ) {
          $u  = get_userdata( (int)$uid_val );
          $nm = $u ? $u->display_name : '';
      } elseif ( is_string($uid_val) ) {
          $nm = $uid_val;
      }
      $team_by_role[ $role ][] = $nm;
  }
  $team_roles = [
      'Lead Auditor'                          => 'Lead Auditor',
      'Auditor(s)'                            => 'Auditor(s)',
      'Auditor'                               => 'Auditor',
      'Technical Expert'                      => 'Technical Expert',
      'Team Leader under supervision / Witness Auditor' => 'Team Leader under supervision / Witness Auditor',
      'Observer(s)'                           => 'Observer(s)',
      'Interpreter(s)'                        => 'Interpreter(s)',
      'Auditor (ICT)'                         => 'Auditor (ICT)',
  ];
  foreach ( $team_roles as $role_key => $role_label ) :
      $names = [];
      foreach ( $team_by_role as $r => $ns ) {
          if ( stripos($r, $role_key) !== false || stripos($role_key, $r) !== false ) {
              $names = array_merge($names, $ns);
          }
      }
      $names_str = implode(', ', array_filter($names)) ?: '-';
  ?>
  <tr>
    <td class="lbl" style="width:45%"><?= esc_html($role_label) ?></td>
    <td><?= esc_html($names_str) ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<!-- ══ SECTION A: GENERAL INFORMATION ═══════════════════════════════════════ -->
<h4 style="margin:6px 0 3px;">Section A: General Information</h4>
<table>
  <thead>
    <tr>
      <th class="sn-td">S.N.</th>
      <th>CLIENT INFORMATION</th>
    </tr>
  </thead>
  <tbody>

    <tr>
      <td class="sn-td">1</td>
      <td>
        <strong>Brief Profile of the Organization (Including main products / services and customers):</strong><br>
        <?= nl2br( $brief_profile ?: '-' ) ?>
      </td>
    </tr>

    <tr>
      <td class="sn-td">2</td>
      <td><strong>Positive Features:</strong><br><?= nl2br($positive_features) ?></td>
    </tr>

    <tr>
      <td class="sn-td">3</td>
      <td>
        <strong>Review of Action Taken on Identified Nonconformities during Previous Audit / Actions initiated for observations addressed in Stage-1 Audit (If Applicable):</strong><br>
        <?= nl2br($review_action) ?>
      </td>
    </tr>

    <tr>
      <td class="sn-td">4</td>
      <td><strong>Comparison with the results of Previous Audits:</strong><br><?= nl2br($comparison) ?></td>
    </tr>

    <tr>
      <td class="sn-td">5</td>
      <td>
        <strong>Evaluation of Internal Audits:</strong>
        <?php if ( $ia_general ) : ?><br><?= nl2br($ia_general) ?><?php endif; ?>
        <table style="margin-top:4px; width:auto; min-width:60%;">
          <tr>
            <td style="border:1px solid #999; padding:3px 5px; font-weight:bold; white-space:nowrap;">Internal Audit Date</td>
            <td style="border:1px solid #999; padding:3px 5px;"><?= $ia_date ?></td>
          </tr>
          <tr>
            <td style="border:1px solid #999; padding:3px 5px; font-weight:bold;">Frequency</td>
            <td style="border:1px solid #999; padding:3px 5px;"><?= $ia_freq ?></td>
          </tr>
          <tr>
            <td style="border:1px solid #999; padding:3px 5px; font-weight:bold; white-space:nowrap;">No. of Non-Conformities</td>
            <td style="border:1px solid #999; padding:3px 5px;"><?= nl2br($ia_ncs) ?></td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td class="sn-td">6</td>
      <td>
        <strong>Evaluation of Management Review:</strong><br><?= nl2br($eval_mgmt_review) ?><br>
        <strong>MRM Date:</strong> <?= $mrm_date ?>
      </td>
    </tr>

    <tr>
      <td class="sn-td">7</td>
      <td><strong>Usage of Certification Documents and Logos:</strong><br><?= nl2br($cert_usage) ?></td>
    </tr>

    <tr>
      <td class="sn-td">8</td>
      <td><strong>Review of Any Changes:</strong><br><?= nl2br($review_changes) ?></td>
    </tr>

    <tr>
      <td class="sn-td">9</td>
      <td><strong>Continuing Operational Controls:</strong><br><?= nl2br($oper_controls) ?></td>
    </tr>

    <tr>
      <td class="sn-td">10</td>
      <td><strong>Customer Complaints, Appeals, Investigation and Corrective &amp; Preventive Actions Taken (if any):</strong><br><?= nl2br($customer_complaints) ?></td>
    </tr>

    <tr>
      <td class="sn-td">11</td>
      <td><strong>Observations / Areas for Potential Improvement:</strong><br><?= nl2br($observations) ?></td>
    </tr>

    <tr>
      <td class="sn-td">12</td>
      <td>
        <strong>Any Deviation from Audit Plan with Reasons:</strong><br><?= nl2br($deviation) ?><br><br>
        <strong>Significant Issues Impacting the Audit Programme:</strong><br><?= nl2br($sig_issues_prog) ?><br><br>
        <strong>Significant Issues Affecting the Management System since Last Audit:</strong><br><?= nl2br($sig_issues_ms) ?><br><br>
        <strong>Any Unresolved Issues, if any:</strong><br><?= nl2br($unresolved) ?>
      </td>
    </tr>

    <tr>
      <td class="sn-td">13</td>
      <td><strong>Are the Procedures Employed in Risk Assessment Sound and Properly Implemented:</strong><br><?= nl2br($risk_procedures) ?></td>
    </tr>

    <tr>
      <td class="sn-td">14</td>
      <td><strong>Summary of Document Review, Adequacy of Internal Organization and Procedures Adopted by the Client:</strong><br><?= nl2br($doc_review_summary) ?></td>
    </tr>

    <tr>
      <td class="sn-td">15</td>
      <td><strong>Any Planned Activities for Continual Improvement:</strong><br><?= nl2br($planned_activities) ?></td>
    </tr>

    <tr>
      <td class="sn-td">16</td>
      <td><strong>Capability of the Management System to Meet Applicable Requirements and Exceed Outcomes:</strong><br><?= nl2br($ms_capability) ?></td>
    </tr>

  </tbody>
</table>

<div class="pagebreak"></div>

<!-- ══ SECTION B: ASSESSMENT STATUS ═════════════════════════════════════════ -->
<h4 style="margin:6px 0 3px;">Section B: Assessment Status &amp; Conclusions</h4>
<table>
  <tbody>

    <tr>
      <td class="lbl" style="width:35%">Nonconformities Identified During This Assessment</td>
      <td><?= f09radio(['Major'=>'Major','Minor'=>'Minor','Nil'=>'Nil'], $nc_type, 'horizontal') ?></td>
    </tr>
    <?php if ( $nc_type && $nc_type !== 'Nil' ) : ?>
    <tr>
      <td class="lbl">No's</td>
      <td><?= $nc_nos ?></td>
    </tr>
    <?php endif; ?>

    <tr>
      <td class="lbl">Follow-up Measures, if Required</td>
      <td><?= nl2br($followup) ?></td>
    </tr>

    <tr>
      <td class="lbl">Additional Requirements (if any) to be Provided During Subsequent Audits</td>
      <td><?= nl2br($add_reqs) ?></td>
    </tr>

    <tr>
      <td class="lbl">Important Points for Planning Forthcoming Audit (on-site / ICT)</td>
      <td><?= nl2br($imp_points) ?></td>
    </tr>

    <tr>
      <td class="lbl">Overall Comment on the Compliance of the System to Requirements of the Standard</td>
      <td><?= nl2br($overall_comment) ?></td>
    </tr>

    <tr>
      <td class="lbl">Performance of the Management System over the Period of Certification</td>
      <td><?= nl2br($ms_performance) ?></td>
    </tr>

    <tr>
      <td class="lbl">Level of Integration</td>
      <td><?= nl2br($level_integ) ?></td>
    </tr>

    <tr>
      <td class="lbl">Appropriateness of Certification Scope</td>
      <td><?= f09radio(['Confirmed'=>'Confirmed','Not Confirmed'=>'Not Confirmed'], $scope_appropriate) ?></td>
    </tr>

    <tr>
      <td class="lbl">Fulfillment of Audit Objectives</td>
      <td><?= f09radio(['Confirmed'=>'Confirmed','Not Confirmed'=>'Not Confirmed'], $fulfill_objectives) ?></td>
    </tr>

    <tr>
      <td class="lbl">Audit Type</td>
      <td><?= f09radio(['Stage -2'=>'Stage-2','Surveillance'=>'Surveillance','Re Certification'=>'Re Certification'], $audit_type) ?></td>
    </tr>

    <tr>
      <td class="lbl">The Next Assessment</td>
      <td>
        <?= f09radio(['Surveillance-1'=>'Surveillance-1','Surveillance-2'=>'Surveillance-2','Re Certification'=>'Re Certification'], $next_assessment, 'vertical') ?><br>
        <strong>Tentative Date:</strong> <?= $tentative_date ?>
      </td>
    </tr>

  </tbody>
</table>

<!-- ══ CONCLUSION & RECOMMENDATION ══════════════════════════════════════════ -->
<table>
  <tr><td colspan="2" class="sec-hdr">Conclusion and Recommendation</td></tr>
  <tr>
    <td colspan="2" style="font-size:9px; background:#f9f9f9;">
      Based on the information and audit evidences gathered, the audit team concluded to:
    </td>
  </tr>
  <?php
  $conc_options = [
      'Recommended for Grant of Certification' => 'Recommended for Grant of Certification',
      'Recommend for follow up assessment'     => 'Recommend for follow up assessment',
      'Recommend for full assessment again'    => 'Recommend for full assessment again',
  ];
  foreach ( $conc_options as $val => $label ) :
      $checked = ( (string)$conclusion === (string)$val ) ? '[X]' : '[ ]';
  ?>
  <tr>
    <td style="width:6%; text-align:center; font-family:monospace; font-size:11px;"><?= $checked ?></td>
    <td><?= esc_html($label) ?></td>
  </tr>
  <?php endforeach;
  // If "other" choice was saved (not one of the 3 standard options)
  if ( $conclusion && ! isset($conc_options[$conclusion]) ) : ?>
  <tr>
    <td style="width:6%; text-align:center; font-family:monospace; font-size:11px;">[X]</td>
    <td><?= esc_html($conclusion) ?></td>
  </tr>
  <?php endif; ?>
</table>

<!-- ══ SIGN-OFF ═══════════════════════════════════════════════════════════════ -->
<table>
  <tr><td colspan="4" class="sec-hdr">Lead Auditor / Team Leader</td></tr>
  <tr>
    <td class="lbl" style="width:20%">Team Leader</td>
    <td style="width:30%"><?= $team_leader ?></td>
    <td class="lbl" style="width:20%">Signature</td>
    <td><?= $signature ?></td>
  </tr>
</table>

<table>
  <tr>
    <td class="lbl" style="width:22%">Remarks</td>
    <td colspan="3"><?= nl2br($remarks) ?></td>
  </tr>
  <tr>
    <td colspan="4" style="font-size:8.5px; background:#f9f9f9; font-style:italic;">
      I pledge that the report and records will remain confidential and will not be shared with any other person or organization and abide with the confidentiality and no conflict of interest agreement signed.
    </td>
  </tr>
</table>

</body>
</html>
