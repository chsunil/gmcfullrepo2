<?php
/**
 * QMS – F-21s1 Surveillance Year 1 Audit Report
 * ACF Group: group_6974d4071dadf
 * Uses same field names as qms-f11.php (prefix_name=0 clones share meta keys).
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

if ( ! function_exists('f21s1_val') ) {
    function f21s1_val( $v, $fallback = '-' ) {
        if ( $v === null || $v === '' || $v === false ) return $fallback;
        if ( is_array($v) ) {
            foreach ( ['display_name','label','value'] as $k ) {
                if ( ! empty($v[$k]) && is_string($v[$k]) ) return esc_html($v[$k]);
            }
            $flat = array_filter( array_map( fn($i) => is_string($i) ? $i : '', $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fallback;
        }
        return esc_html( (string) $v );
    }
}
if ( ! function_exists('f21s1_date') ) {
    function f21s1_date( $v, $fallback = '-' ) {
        if ( ! $v ) return $fallback;
        if ( preg_match('/^\d{4}-\d{2}-\d{2}/', $v) ) return date('d/m/Y', strtotime($v));
        return esc_html($v);
    }
}

// ── Top-level fields ──────────────────────────────────────────────────────────
$org_raw = get_field('organization_name', $post_id);
if ( is_array($org_raw) ) {
    $org = $org_raw['organization_name'] ?? reset($org_raw) ?? '';
} elseif ( $org_raw ) {
    $org = (string) $org_raw;
} else {
    $org = get_post_field('post_title', $post_id);
}
$org = esc_html((string) $org);

$addr_grp = get_field('f11address', $post_id) ?: get_field('address', $post_id) ?: [];
$address  = is_array($addr_grp) ? ($addr_grp['head_office'] ?? '') : (string) $addr_grp;

$audit_ref = f21s1_val( get_field('audit_refno', $post_id) ?: get_field('proposal_ref_no', $post_id) );

$mgr_rep_raw = get_field('f11management_representative', $post_id) ?: get_field('f01contact_person', $post_id);
$mgr_rep = is_array($mgr_rep_raw)
    ? esc_html($mgr_rep_raw['contact_person_name'] ?? reset($mgr_rep_raw) ?? '-')
    : f21s1_val($mgr_rep_raw);

$top_mgmt_grp = get_field('f11top_management', $post_id) ?: get_field('contact_person', $post_id);
$top_mgmt = is_array($top_mgmt_grp)
    ? esc_html($top_mgmt_grp['top_management'] ?? $top_mgmt_grp['contact_person_name'] ?? reset($top_mgmt_grp) ?? '-')
    : f21s1_val($top_mgmt_grp);

$standard    = f21s1_val( get_field('f11audit_criteria_standard', $post_id) ?: get_field('cert_scheme', $post_id) );
$exclusions  = f21s1_val( get_field('f11exclusions', $post_id) ?: get_field('exclusions_only_for_iso_9001', $post_id) );
$audit_objectives = get_field('f11audit_objectives', $post_id) ?: 'To evaluate the compliance of the management system with the requirements of the standard.';
$audit_scope = f21s1_val( get_field('f11audit_scope_confirmed', $post_id) ?: get_field('scope_of_certification', $post_id) );

$audit_sites_raw = get_field('audit_sites', $post_id);
if ( ! $audit_sites_raw ) {
    $audit_sites_raw = is_array($addr_grp) ? ($addr_grp['head_office'] ?? '') : (string) $addr_grp;
}
$audit_sites = f21s1_val($audit_sites_raw, '-');

$audit_date = f21s1_date( get_field('f11dates_of_audit', $post_id) );
$tech_code  = f21s1_val( get_field('technical_code', $post_id) ?: get_field('technical_code_area', $post_id) );
$audit_team = get_field('audit_team', $post_id) ?: get_field('f05_audit_team', $post_id) ?: [];
$recs       = get_field('recommendations', $post_id) ?: [];
$stage2_date = f21s1_date( get_field('stage2_planned_on', $post_id) );

$la_group = get_field('lead_auditor', $post_id);
if ( is_array($la_group) ) {
    $la_raw  = $la_group['lead_auditor'] ?? null;
    $la_name = is_array($la_raw) ? f21s1_val($la_raw) : esc_html((string)($la_raw ?: '-'));
    $la_sig  = esc_html($la_group['signature'] ?? '-');
} else {
    $la_name = esc_html((string)($la_group ?: '-'));
    $la_sig  = '-';
}
if ( ! $la_name ) $la_name = '-';

$review_report  = get_field('review_of_stage_1_audit_report', $post_id) ?: '-';
$reviewer_name  = esc_html( get_field('reviewer', $post_id) ?: '-' );
$review_date    = f21s1_date( get_field('review_date', $post_id) );
$sb_important   = esc_html( get_field('important_points_for_planning_forthcoming_audit_on-siteict_if_any', $post_id) ?: '-' );

// ── Section A ─────────────────────────────────────────────────────────────────
$sec_a = get_field('section_a', $post_id) ?: [];
$brief_profile  = $sec_a['Brief_Profile_of_the_Organization'] ?? '';
$products       = $sec_a['products_services:'] ?? '';
$major_customers= $sec_a['major_customers:'] ?? '';
$achievements   = $sec_a['major_achievements'] ?? '';
$mgmt_docs      = $sec_a["client's_management_system_documentation"] ?? '';
$location       = $sec_a["client's_location_and_site-specific_conditions"] ?? $address ?? '';
$processes_raw  = $sec_a['client_processes'] ?? '';
$processes      = is_array($processes_raw) ? implode(', ', array_filter(array_map(fn($i) => is_string($i) ? $i : '', $processes_raw))) : (string) $processes_raw;
$other_notes    = $sec_a['other'] ?? '';
$working_hours  = $sec_a['working_hours:'] ?? '';
$shifts         = $sec_a['no_of_shifts:'] ?? '';
$employees      = $sec_a['No_of_Employees:'] ?? '';
$machinery_raw  = $sec_a['machinery_equipments_servers_systems:'] ?? '';
$machinery      = is_array($machinery_raw) ? implode(', ', array_filter(array_map(fn($r) => is_string($r) ? $r : '', (array)$r))) : (string)$machinery_raw;
$objectives_tgts = $sec_a['organization_objectives_and_targets'] ?? '';
$statutory_raw  = $sec_a['applicable_statutory_and_regulatory_requirements'] ?? '';
$statutory      = is_array($statutory_raw) ? f21s1_val($statutory_raw) : esc_html((string)$statutory_raw);
$complaints     = $sec_a['customer_complaints_if_any:'] ?? 'No significant complaints received.';
$risks          = $sec_a['risks_and_opportunities'] ?? '';
$awareness      = $sec_a['awareness'] ?? '';
$outsourcing_raw = $sec_a['outsourcing'] ?? [];
if ( is_array($outsourcing_raw) && isset($outsourcing_raw[0]) ) {
    $outsourcing = implode('; ', array_filter(array_map(fn($r) => is_array($r) ? trim(($r['process'] ?? '')) : (string)$r, $outsourcing_raw)));
} else {
    $outsourcing = is_string($outsourcing_raw) ? $outsourcing_raw : '';
}
$ia_key  = 'status_of_internal_audits_along_with_effectiveness_of_corrective_and_preventive_actions:';
$ia_grp  = $sec_a[$ia_key] ?? [];
$ia_date = f21s1_date( is_array($ia_grp) ? ($ia_grp['internal_audit_date'] ?? '') : '' );
$ia_freq = esc_html( is_array($ia_grp) ? ($ia_grp['frequency'] ?? '-') : '-' );
$ia_ncs  = esc_html( is_array($ia_grp) ? ($ia_grp['no_of_non_conformities'] ?? '-') : '-' );

$mr_grp     = $sec_a['status_of_management_review'] ?? [];
$mr_agenda  = esc_html( is_array($mr_grp) ? ($mr_grp['all_the_agenda_points_like_complaints_feedbacks'] ?? '-') : '-' );
$mr_ia_date = f21s1_date( is_array($mr_grp) ? ($mr_grp['date_of_internal_audit'] ?? '') : '' );
$mr_mrm_date= f21s1_date( is_array($mr_grp) ? ($mr_grp['date_of_management_review'] ?? '') : '' );

$employees_verified = $sec_a['no_of_employees_scope_exclusions_as_per_application'] ?? 'Yes';
$eff_emp_raw = $sec_a['effective_number_of_employees'] ?? '';
$eff_emp     = is_array($eff_emp_raw) ? f21s1_val($eff_emp_raw) : esc_html((string)$eff_emp_raw);
$sa_scope    = esc_html( is_array($sec_a['scope'] ?? '') ? f21s1_val($sec_a['scope']) : (string)($sec_a['scope'] ?? '') );
$sa_excl     = esc_html( is_array($sec_a['exclusions'] ?? '') ? f21s1_val($sec_a['exclusions']) : (string)($sec_a['exclusions'] ?? '') );
$sa_justif   = esc_html( is_array($sec_a['justification_for_exclusions'] ?? '') ? f21s1_val($sec_a['justification_for_exclusions']) : (string)($sec_a['justification_for_exclusions'] ?? '') );
$areas_concern = esc_html( $sec_a['areas_of_concernimprovements'] ?? '-' );
$strong_pts  = esc_html( $sec_a['strong_points'] ?? '-' );
$ict_raw     = $sec_a['ict_used_and_their_effectiveness_and_comments_is_any'] ?? 'N/A';
$ict_info    = is_array($ict_raw) ? f21s1_val($ict_raw) : esc_html((string)$ict_raw);

$changes_grp = $sec_a['any_changes_observed'] ?? [];
$chg_name    = esc_html( is_array($changes_grp) ? ($changes_grp['name'] ?? 'Nil') : 'Nil' );
$chg_addr    = esc_html( is_array($changes_grp) ? ($changes_grp['address'] ?? 'Nil') : 'Nil' );
$chg_scope   = esc_html( is_array($changes_grp) ? ($changes_grp['scope'] ?? 'Nil') : 'Nil' );
$chg_manpwr  = esc_html( is_array($changes_grp) ? ($changes_grp['manpower'] ?? 'Nil') : 'Nil' );

$dev_grp     = $sec_a['deviations_changes_unresolved_issues_if_yes_specify'] ?? [];
$dev_plan    = esc_html( is_array($dev_grp) ? ($dev_grp['was_there_any_deviation_from_the_audit_plan'] ?? '-') : '-' );
$dev_issues  = esc_html( is_array($dev_grp) ? ($dev_grp['Were_there_any_significant_issues_impacting_the_audit_program'] ?? '-') : '-' );
$dev_changes = esc_html( is_array($dev_grp) ? ($dev_grp['Were_there_any_significant_changes_that_have_affected_the_management_system_of_the_client_since_the_last_audit_took_place'] ?? '-') : '-' );
$dev_unreslv = esc_html( is_array($dev_grp) ? ($dev_grp['Any_un_resolved_issues_identified'] ?? '-') : '-' );

// ── Section B ─────────────────────────────────────────────────────────────────
$sec_b        = get_field('section_b', $post_id) ?: [];
$sb_concerns  = esc_html( $sec_b['areas_of_concern_that_could_be_classified_as_nonconformity_during_the_stage_2_audit'] ?? '-' );
$sb_info      = esc_html( $sec_b['client_to_provide_following_information_and_records_for_detailed_examination_during_stage_2'] ?? '-' );
$sb_capability= esc_html( $sec_b['capability_of_the_ms_to_meet_applicable_requirements_and_expected_outcomes'] ?? '-' );
$sb_followings= esc_html( $sec_b['the_followings_need_to_be_addressed_along_with_the_issues_identified_in_the_document_review_report'] ?? '-' );
$sb_obj_raw   = $sec_b['status_of_audit_objectives'] ?? 'Fulfilled';
$sb_obj_status= is_array($sb_obj_raw) ? f21s1_val($sb_obj_raw) : esc_html((string)$sb_obj_raw);
$sb_comments  = esc_html( $sec_b['comments'] ?? '-' );
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 12mm 10mm 14mm 10mm; }
body { font-family: Arial, sans-serif; font-size: 9.5px; color: #000; margin: 0; padding: 0; line-height: 1.4; }
table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
th, td { border: 1px solid #333; padding: 4px 5px; vertical-align: top; }
th { font-weight: bold; background: #f0f0f0; text-align: left; }
.no-border td, .no-border th { border: none; background: transparent; }
.center { text-align: center; }
.pagebreak { page-break-before: always; }
.h-logo { border: none; text-align: center; vertical-align: middle; }
.h-title { text-align: center; font-size: 13px; font-weight: bold; vertical-align: middle; }
.h-sub { font-size: 9px; color: #333; }
.lbl { font-weight: bold; background: #f5f5f5; width: 22%; }
.val { width: 28%; }
.sec-hdr { background: #d9d9d9; font-weight: bold; font-size: 10px; }
.sn-td { text-align: center; width: 4%; }
.footer {
    position: fixed; bottom: 0; left: 0; right: 0;
    font-size: 7.5px; text-align: center; border-top: 1px solid #999; padding-top: 2px; color: #333;
}
</style>
</head>
<body>

<?php if ( $LOGO ) : ?>
<table style="margin-top:60px;">
  <tr>
    <td class="h-logo no-border" style="border:none; text-align:center;">
      <img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:70px; width:auto;" />
    </td>
  </tr>
</table>
<?php endif; ?>

<table>
  <tr>
    <td colspan="2" class="h-title" style="border:none; text-align:center;">
      SURVEILLANCE YEAR 1 – AUDIT REPORT
    </td>
  </tr>
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
    <td><?= esc_html($address) ?></td>
  </tr>
  <tr>
    <td class="lbl">Site</td>
    <td><?= $audit_sites ?></td>
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

<div class="footer">
  <em>Disclaimer:</em> The Auditing is based on a sampling process. GLOBAL MANAGEMENT CERTIFICATION SERVICES PRIVATE LIMITED &nbsp;|&nbsp;
  Flat No.402, Plot No.410, Matrusri Nagar, Miyapur, Hyderabad-500 049 &nbsp;|&nbsp; Tel: 040-48559001 &nbsp;|&nbsp; info@mcsglobal.in
</div>

<div class="pagebreak"></div>

<!-- Audit Team -->
<table>
  <tr><td colspan="2" class="sec-hdr">Audit Team</td></tr>
  <?php
  $team_by_role = [];
  foreach ( (array)$audit_team as $row ) {
      $role    = is_array($row) ? ($row['f05_team_role'] ?? $row['role'] ?? '') : '';
      $uid_val = is_array($row) ? ($row['f05_team_name'] ?? $row['name'] ?? null) : null;
      $nm = '';
      if ( is_array($uid_val) && isset($uid_val['display_name']) ) {
          $nm = $uid_val['display_name'];
      } elseif ( is_numeric($uid_val) && (int)$uid_val > 0 ) {
          $u  = get_userdata((int)$uid_val);
          $nm = $u ? $u->display_name : '';
      } elseif ( is_string($uid_val) ) {
          $nm = $uid_val;
      }
      $team_by_role[$role][] = $nm;
  }
  $team_roles = [
      'Lead Auditor'     => 'Lead Auditor',
      'Auditor(s)'       => 'Auditor(s)',
      'Technical Expert' => 'Technical Expert',
      'Observer(s)'      => 'Observer(s)',
      'Interpreter(s)'   => 'Interpreter(s)',
  ];
  foreach ( $team_roles as $rk => $rl ) :
      $names = [];
      foreach ( $team_by_role as $r => $ns ) {
          if ( stripos($r, $rk) !== false || stripos($rk, $r) !== false ) $names = array_merge($names, $ns);
      }
      echo '<tr><td class="lbl" style="width:45%">' . esc_html($rl) . '</td><td>' . esc_html(implode(', ', array_filter($names)) ?: '-') . '</td></tr>';
  endforeach;
  ?>
</table>

<!-- Section A -->
<h4 style="margin:6px 0 3px;">Section A: General</h4>
<table>
  <thead>
    <tr><th class="sn-td">S.N.</th><th>CLIENT INFORMATION</th></tr>
  </thead>
  <tbody>
    <tr>
      <td class="sn-td" rowspan="4">1</td>
      <td><strong>Brief Profile:</strong><br><?= nl2br(esc_html($brief_profile ?: '-')) ?></td>
    </tr>
    <tr>
      <td><strong>Products / Services:</strong><br><?= nl2br(esc_html(is_array($products) ? implode(', ', array_filter(array_map(fn($v) => is_string($v) ? $v : '', $products))) : ($products ?: '-'))) ?></td>
    </tr>
    <tr>
      <td><strong>Major Customers:</strong><br><?= nl2br(esc_html($major_customers ?: '-')) ?></td>
    </tr>
    <tr>
      <td><strong>Major Achievements:</strong><br><?= nl2br(esc_html($achievements ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">2</td>
      <td><strong>Management System Documentation:</strong><br><?= nl2br(esc_html($mgmt_docs ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">3</td>
      <td>
        <strong>Location and Site-Specific Conditions &amp; Processes:</strong><br>
        <?= is_array($location) ? f21s1_val($location) : esc_html((string)$location) ?: esc_html($address) ?>
        <?php if ( $processes ) echo '<br>' . nl2br(esc_html($processes)); ?>
        <?php if ( $other_notes ) echo '<br>' . nl2br(esc_html($other_notes)); ?>
        <br><strong>Working Hours:</strong> <?= esc_html($working_hours ?: '-') ?> &nbsp;
        <strong>No. of Shifts:</strong> <?= esc_html($shifts ?: '-') ?> &nbsp;
        <strong>No. of Employees:</strong> <?= esc_html($employees ?: '-') ?><br>
        <strong>Exclusions:</strong> <?= esc_html($sa_excl ?: $exclusions) ?><br>
        <strong>Scope:</strong> <?= esc_html($sa_scope ?: $audit_scope) ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">4</td>
      <td><strong>Machinery / Equipments, Servers, Systems:</strong><br><?= nl2br(esc_html($machinery ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">5</td>
      <td><strong>Organization Objectives and Targets:</strong><br><?= nl2br(esc_html($objectives_tgts ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">6</td>
      <td><strong>Applicable Statutory and Regulatory Requirements:</strong><br><?= $statutory ?: '-' ?></td>
    </tr>
    <tr>
      <td class="sn-td">7</td>
      <td><strong>Customer Complaints (if any):</strong><br><?= nl2br(esc_html($complaints)) ?></td>
    </tr>
    <tr>
      <td class="sn-td">8</td>
      <td><strong>Risks and Opportunities:</strong><br><?= nl2br(esc_html($risks ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">9</td>
      <td><strong>Awareness:</strong><br><?= nl2br(esc_html($awareness ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">10</td>
      <td><strong>Outsourcing:</strong><br><?= nl2br(esc_html($outsourcing ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">11</td>
      <td>
        <strong>Status of Internal Audits:</strong><br>
        <strong>Date:</strong> <?= $ia_date ?> &nbsp;
        <strong>Frequency:</strong> <?= $ia_freq ?> &nbsp;
        <strong>No. of NCs:</strong> <?= nl2br($ia_ncs) ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">12</td>
      <td>
        <strong>Status of Management Review:</strong><br>
        <?= nl2br($mr_agenda) ?><br>
        <strong>Date of IA:</strong> <?= $mr_ia_date ?> &nbsp;
        <strong>Date of MRM:</strong> <?= $mr_mrm_date ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">13</td>
      <td>
        <strong>No. of Employees, Scope, Exclusions as per Application:</strong><br>
        Verified: <strong><?= esc_html($employees_verified) ?></strong><br>
        <strong>Effective Employees:</strong> <?= $eff_emp ?: '-' ?><br>
        <strong>Scope:</strong> <?= esc_html($sa_scope ?: $audit_scope) ?><br>
        <strong>Exclusions:</strong> <?= esc_html($sa_excl ?: $exclusions) ?><br>
        <strong>Justification:</strong> <?= esc_html($sa_justif ?: '-') ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">14</td>
      <td><strong>Areas of Concern / Improvements:</strong><br><?= nl2br($areas_concern) ?></td>
    </tr>
    <tr>
      <td class="sn-td">15</td>
      <td><strong>Strong Points:</strong><br><?= nl2br($strong_pts) ?></td>
    </tr>
    <tr>
      <td class="sn-td">16</td>
      <td><strong>ICT Used and Their Effectiveness:</strong><br><?= nl2br($ict_info) ?></td>
    </tr>
    <tr>
      <td class="sn-td">17</td>
      <td>
        <strong>Any Changes Observed:</strong><br>
        <strong>Name:</strong> <?= $chg_name ?> &nbsp;
        <strong>Address:</strong> <?= $chg_addr ?> &nbsp;
        <strong>Scope:</strong> <?= $chg_scope ?> &nbsp;
        <strong>Manpower:</strong> <?= $chg_manpwr ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">18</td>
      <td>
        <strong>Deviations / Changes / Unresolved Issues:</strong><br>
        <strong>Deviation from plan?</strong> <?= $dev_plan ?><br>
        <strong>Significant issues impacting programme?</strong> <?= $dev_issues ?><br>
        <strong>Significant changes to management system?</strong> <?= $dev_changes ?><br>
        <strong>Unresolved issues?</strong> <?= $dev_unreslv ?>
      </td>
    </tr>
  </tbody>
</table>

<!-- Section B -->
<h4 style="margin:6px 0 3px;">Section B:</h4>
<table>
  <thead>
    <tr><th class="sn-td">S.N.</th><th>Area / Question</th><th style="width:40%">Notes</th></tr>
  </thead>
  <tbody>
    <tr>
      <td class="sn-td">1</td>
      <td>Areas of concern that could be classified as nonconformity during the surveillance audit?</td>
      <td><?= nl2br($sb_concerns) ?></td>
    </tr>
    <tr>
      <td class="sn-td">2</td>
      <td>Client to provide following information and records for detailed examination</td>
      <td><?= nl2br($sb_info) ?></td>
    </tr>
    <tr>
      <td class="sn-td">3</td>
      <td>Capability of the MS to meet applicable requirements and expected outcomes</td>
      <td><?= nl2br($sb_capability) ?></td>
    </tr>
    <tr>
      <td class="sn-td">4</td>
      <td>The followings need to be addressed along with the issues identified in the document review report</td>
      <td><?= nl2br($sb_followings) ?></td>
    </tr>
    <tr>
      <td class="sn-td">5</td>
      <td>Status of Audit Objectives</td>
      <td>
        <?= $sb_obj_status ?>
        <?php if ( $sb_comments && $sb_comments !== '-' ) echo '<br>Comments: ' . $sb_comments; ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">6</td>
      <td>Important points for planning forthcoming audit (on-site / ICT) if any</td>
      <td><?= nl2br($sb_important) ?></td>
    </tr>
  </tbody>
</table>

<!-- Recommendations -->
<table>
  <tr><td colspan="2" class="sec-hdr">Recommendations</td></tr>
  <?php
  $rec_options = [
      'System is ready for stage 2',
      'Re-audit requires to verify the compliance of the identified points',
      'Areas of concern need to be addressed by management',
  ];
  foreach ( $rec_options as $opt ) :
      $checked = in_array($opt, (array)$recs) ? '[X]' : '[ ]';
  ?>
  <tr>
    <td style="width:6%; text-align:center; font-family:monospace; font-size:11px;"><?= $checked ?></td>
    <td><?= esc_html($opt) ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<table>
  <tr>
    <td class="lbl" style="width:25%">Stage 2 Planned On</td>
    <td><?= $stage2_date ?></td>
  </tr>
</table>

<table>
  <tr><td colspan="4" class="sec-hdr">Lead Auditor</td></tr>
  <tr>
    <td class="lbl" style="width:20%">Name</td>
    <td style="width:30%"><?= $la_name ?></td>
    <td class="lbl" style="width:20%">Signature</td>
    <td><?= $la_sig ?></td>
  </tr>
</table>

<!-- Review -->
<table>
  <tr><td colspan="4" class="sec-hdr">Review of Surveillance Audit Report</td></tr>
  <tr>
    <td colspan="4">
      Based on the audit findings, the next surveillance audit is recommended to proceed as planned.<br><br>
      <?= nl2br(esc_html($review_report)) ?>
    </td>
  </tr>
  <tr>
    <td class="lbl" style="width:20%">Lead Auditor</td>
    <td style="width:30%"><?= $la_name ?></td>
    <td class="lbl" style="width:20%">Reviewer</td>
    <td><?= $reviewer_name ?></td>
  </tr>
  <tr>
    <td class="lbl">Signature</td>
    <td>-</td>
    <td class="lbl">Date</td>
    <td><?= $review_date ?></td>
  </tr>
</table>

<p style="font-size:8.5px; margin:4px 0;"><strong>F-21s1 &nbsp;|&nbsp; QMS Surveillance Year 1 &nbsp;|&nbsp; Version 1.00</strong></p>

</body>
</html>
