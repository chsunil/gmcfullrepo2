<?php
/**
 * QMS – F-11 Stage-1 Audit Report
 * ACF Group: group_qms_f11
 */
if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helper ───────────────────────────────────────────────────────────────────
if ( ! function_exists('f11v') ) {
    function f11v( $key, $post_id, $fallback = '-' ) {
        $v = get_field( $key, $post_id );
        if ( $v === null || $v === '' || $v === false ) return $fallback;
        if ( is_array($v) ) {
            if ( isset($v['display_name']) ) return esc_html( $v['display_name'] );
            $flat = array_filter( array_map( fn($i) => is_string($i) ? $i : ( $i['label'] ?? $i['value'] ?? '' ), $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fallback;
        }
        return esc_html( $v );
    }
}
if ( ! function_exists('f11nl') ) {
    function f11nl( $v ) {
        return nl2br( esc_html( $v ?: '-' ) );
    }
}

// ── Logo (same base64 as other templates) ────────────────────────────────────
$logo_b64 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2ODApLCBxdWFsaXR5ID0gODIK/9sAQwAGBAQFBAQGBQUFBgYGBwkOCQkICAkSDQ0KDhUSFhYVEhQUFxohHBcYHxkUFB0nHR8iIyUlJRYcKSwoJCshJCUk/9sAQwEGBgYJCAkRCQkRJBgUGCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQk/8AAEQgAXABkAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A+qaKKKACiiigAooooAKKKRXViQrA4ODg9DS0AFFFFABRRRQAUUUUAFFFFABRRRQAVkeK/EVv4X0O51KfB8tcRpnmRz91fz/TNa54FeD/ABQ8QXnjDxAdK0mC4u7TT3K4gQt87dGb6dB+PrXn4qu6cLLfocOYYv2FNyj8T0XqaHwq8e3EXiS4s9VnLx6rKZA7HhZz/IHp+Ar24c18tz+F/ENgoml0bUoFU7t5t3G3Hf2r3n4c+LR4r8Pxy7h9tt/3Nyvrj+LH+1/OuXA1pf/AHs8zJcXN/7PM6qiiivSPoQooooAKKKKACiiigAoooJAoA5/xx4t0rwdoc19qkjBHBjjijOJJmI+6v+Pawn/AAn/AI48VSNS2T+1LYwM+7xVK34o/gSue+JXi6TxbrS6fo1vcXUFgSuIEL75OjMcdsf1r6B8E+HItG8KWrMi/arhBcTN3LMMgflgfhXl/V3Xqrm2R9bKKyXBe0X8Weq+S/4J8+6j4M8TWDp5mhanGXbajC1dgT6cCu3+D3w68Z2GtS39/osthBLBsV5ZMMzAjBK9elff6/dGe9N3L/er1/7Liry5nqfMz4mcoOCpWvvc+e/iJ8O/GHi3XJhZaXJaRxRqhKxEGQD+L8a5o/A3xpZxCGTQ5bqIcbxGDu/Ov0Ew3rSFR3yK4qeVqE3Lmvc9ChnUqVFUuS91Z9DJ+HHhm68N+FbO2v4Ua5Cdxnav90H0Fdfmm5xS16FOnGlFQjsjshCMI8sdjI17W7XQdPe6u3CoOijksegFeaeHfH1/qmsMl5FBHp8jERAA7h6EmuKuNY1LxH4lvdN1nWHi0BFmNuqKBkJtxge/Oa5v7D4fPT7RD/vXLfrXz2Y5pSwc+SOqPfwmX1cTG7Wh7HrWq6Xo+nzXWp3EdvbqDudsda8H8GfE69tPEeoWmqXHnaf9plEJYZKjefl+melYXia28LJpzDRr5rrUGOFUrgJ9TXE+bFHGHYZBGcelcWAqyz3KalRrllF7HRjKFPLMVToJ3Ul1R9OeGNb07WjdvpVwJ4omCbwchW9K6CuX8F+HH0q1N3cqY7m5UHZjlF7flXUV9pThKEVGTuz5aUlKTcVYKKKKsgKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigD/2Q==';

// ── Header fields ─────────────────────────────────────────────────────────────
// organization_name: direct ACF field read (same as qms-f02)
$org = get_field( 'organization_name', $post_id );
if ( is_array($org) ) {
    $org = $org['display_name'] ?? reset($org) ?? '';
}
if ( empty($org) ) {
    $org = get_post_field( 'post_title', $post_id ); // post title is set to org name on ACF save
}
$org = (string) $org;

// Address: GROUP field 'address' → sub-key 'head_office' (field_68173ed29add4)
$addr_grp = get_field( 'address', $post_id ) ?: [];
$address  = $addr_grp['head_office'] ?? '';

$audit_ref       = f11v( 'proposal_ref_no', $post_id );
$lead_auditor    = get_field( 'lead_auditor', $post_id ) ?: '-';
$auditors_txt    = get_field( 'auditors', $post_id ) ?: '-';
$tech_expert     = get_field( 'technical_expert', $post_id ) ?: '-';
$witness         = get_field( 'witness_auditor', $post_id ) ?: '-';
$observers       = get_field( 'observers', $post_id ) ?: '-';
$interpreters    = get_field( 'interpreters', $post_id ) ?: '-';
$auditor_ict     = get_field( 'auditor_ict', $post_id ) ?: '-';

// Clone fields (prefix_name:0 → read by original field/group name)
// MR: f01contact_person GROUP (field_68173ed2aa379), sub-field: contact_person_name
$contact_grp     = get_field( 'f01contact_person', $post_id ) ?: [];
$mgr_rep         = $contact_grp['contact_person_name'] ?? '-';
// Top Mgmt: contact_person GROUP (field_68173ed2a2ab5), sub-field: top_management
$top_mgmt_grp    = get_field( 'contact_person', $post_id ) ?: [];
$top_mgmt        = $top_mgmt_grp['top_management'] ?? '-';
$standard        = f11v( 'cert_scheme', $post_id );                // clone of field_68173ed2b0218
$exclusions      = f11v( 'exclusions_only_for_iso_9001', $post_id );
$audit_scope     = f11v( 'scope_of_certification', $post_id );
$tech_code       = f11v( 'technical_code_area', $post_id );        // clone of field_67fe8e52fc7e0

// Audit date: clone of field_0017 → stage1_audit_initial (Ymd)
$raw_audit_date  = get_field( 'stage1_audit_initial', $post_id );
$audit_date      = $raw_audit_date ? date( 'd/m/Y', strtotime( $raw_audit_date ) ) : '-';

// Stage 2 planned: clone of field_0023 → stage2_audit_surveillance_audit_date_initial
$raw_s2 = get_field( 'stage2_audit_surveillance_audit_date_initial', $post_id );
$stage2_date = $raw_s2 ? date( 'd/m/Y', strtotime( $raw_s2 ) ) : '-';

// Audit objectives (textarea with default)
$audit_objectives = get_field( 'f11audit_objectives', $post_id )
    ?: "* To Review Management System's Documented Information\n* To evaluate site specific condition and readiness for Stage 2 audit\n* To Evaluate if internal audits and management reviews are planned, substantiating the readiness for stage 2\n* To Evaluate the understanding regarding requirements of the standard, in particular with respect to the identification of key performance or significant aspects, processes, objectives and operation of the management system";

// Audit team: f11audit_team clones field_f05_audit_team (prefix_name:0) → stored as f05_audit_team
$audit_team = get_field( 'f05_audit_team', $post_id ) ?: [];

// Recommendations (checkbox, return_format: label)
$recs = get_field( 'f11recommendations', $post_id ) ?: [];

// Review of Stage 1 report
$review_report = get_field( 'review_of_stage_1_audit_report', $post_id ) ?: '-';

// Lead Auditor group (f11lead_auditor)
// Sub-field 'lead_auditor' clones field_67cdf_assigned_employee (name: assigned_employee, prefix_name:0)
$la_group   = get_field( 'f11lead_auditor', $post_id ) ?: [];
$la_user    = $la_group['assigned_employee'] ?? null;
if ( is_array($la_user) && isset($la_user['display_name']) ) {
    $la_name = $la_user['display_name'];
} elseif ( is_numeric($la_user) && $la_user > 0 ) {
    $u = get_userdata( (int)$la_user );
    $la_name = $u ? $u->display_name : $lead_auditor;
} else {
    $la_name = $lead_auditor; // fallback to top-level text field
}
$la_sig = $la_group['signature'] ?? '-';

// Lead Auditor copy group (f11lead_auditor_copy)
$la2_group  = get_field( 'f11lead_auditor_copy', $post_id ) ?: [];
$la2_user   = $la2_group['assigned_employee'] ?? null;
if ( is_array($la2_user) && isset($la2_user['display_name']) ) {
    $la2_name = $la2_user['display_name'];
} elseif ( is_numeric($la2_user) && $la2_user > 0 ) {
    $u = get_userdata( (int)$la2_user );
    $la2_name = $u ? $u->display_name : '';
} else {
    $la2_name = '';
}
$la2_sig = $la2_group['signature'] ?? '-';

// ── Section A group ───────────────────────────────────────────────────────────
$sec_a = get_field( 'f11section_a', $post_id ) ?: [];

function f11sa( $sec_a, $key, $fallback = '-' ) {
    $v = $sec_a[ $key ] ?? '';
    if ( $v === null || $v === '' ) return $fallback;
    if ( is_array($v) ) return implode(', ', array_filter( array_map( fn($i) => is_string($i) ? $i : '', $v ) ) ) ?: $fallback;
    return esc_html( $v );
}

$brief_profile   = $sec_a['f11Brief_Profile_of_the_Organization'] ?? '';
$products        = $sec_a['products_services'] ?? '';          // clone of field_68173ed2ac4f8 (prefix_name:0)
$major_customers = $sec_a['f11major_customers:'] ?? '';        // trailing colon in name
$achievements    = $sec_a['major_achievements'] ?? '';
$mgmt_docs       = $sec_a['f11client\'s_management_system_documentation'] ?? $sec_a["f11client's_management_system_documentation"] ?? '';
$working_hours   = $sec_a['f11working_hours:'] ?? '';
$shifts          = $sec_a['f11no_of_shifts:'] ?? '';
$employees       = $sec_a['f11No_of_Employees:'] ?? '';
$objectives_tgts = $sec_a['f11organization_objectives_and_targets'] ?? '';
$complaints      = $sec_a['f11customer_complaints_if_any:'] ?? 'No significant complaints received from clients.';
$risks           = $sec_a['f11risks_and_opportunities'] ?? '';
$awareness       = $sec_a['f11awareness'] ?? '';

// Outsourcing: REPEATER outsourced_processes (field_68173ed301f40, prefix_name:0)
$outsourcing_rows = $sec_a['outsourced_processes'] ?? [];
if ( is_array($outsourcing_rows) && ! empty($outsourcing_rows) ) {
    $outsourcing = implode('; ', array_map(
        fn($r) => trim( ($r['process'] ?? '') . ( !empty($r['suppliersub_contractor']) ? ' (' . $r['suppliersub_contractor'] . ')' : '' ) ),
        $outsourcing_rows
    ));
} else {
    $outsourcing = '';
}

// Statutory: clone of field_6817458a2405a → applicable_legal_and_statutory_requirements
$statutory       = $sec_a['applicable_legal_and_statutory_requirements'] ?? '';

// Machinery: clone of field_688bb0509b4e1 → 20:_Major_Machinery_Equipmentsmain_operative_site
$machinery       = $sec_a['20:_Major_Machinery_Equipmentsmain_operative_site'] ?? '';

// ICT: clone of field_67fe92f174030 → Type_and_extent_of_ICT_used_if_any
$ict_info        = $sec_a['Type_and_extent_of_ICT_used_if_any'] ?? 'N/A';

// Internal Audit Status group
$ia_key = 'f1status_of_internal_audits_along_with_effectiveness_of_corrective_and_preventive_actions:';
$ia_grp  = $sec_a[ $ia_key ] ?? [];
// internal_audit_date clones field_68173ed42faaf (date_picker, return_format d/m/Y → already formatted)
$ia_date = $ia_grp['internal_audit_date'] ?: '-';
$ia_freq = $ia_grp['f11frequency'] ?? '-';
$ia_ncs  = $ia_grp['f11no_of_non_conformities'] ?? '-';

// Management Review Status group
$mr_grp  = $sec_a['f11status_of_management_review'] ?? [];
$mr_agenda = $mr_grp['f11all_the_agenda_points_like_complaints_feedbacks'] ?? '-';
$mr_ia_raw = $mr_grp['internal_audit_date_initial'] ?? ( $mr_grp['f11date_of_internal_audit'] ?? '' ); // clone field_0029
$mr_ia_date = $mr_ia_raw ? date( 'd/m/Y', strtotime( $mr_ia_raw ) ) : '-';
$mr_mrm_raw = $mr_grp['mrm_date_initial'] ?? ( $mr_grp['f1date_of_management_review'] ?? '' ); // clone field_0032
$mr_mrm_date = $mr_mrm_raw ? date( 'd/m/Y', strtotime( $mr_mrm_raw ) ) : '-';

// No of Employees, scope, exclusions verified radio
$employees_verified = $sec_a['f11no_of_employees_scope_exclusions_as_per_application'] ?? 'Yes';

// Effective employees: clone of field_67fe924e034f3 → effective_number_of_employees
$eff_emp = $sec_a['effective_number_of_employees'] ?? '';

// Section A scope/exclusions/justification
$sa_scope   = $sec_a['scope_of_certification'] ?? '';
$sa_excl    = $sec_a['exclusions_only_for_iso_9001'] ?? '';
// Justification: clone of field_681743c024059 → exclusions_only_for_iso_9002_Justification
$sa_justif  = $sec_a['exclusions_only_for_iso_9002_Justification'] ?? '';
$areas_concern = $sec_a['f11areas_of_concernimprovements'] ?? '-';
$strong_pts = $sec_a['strong_points'] ?? '-';

// Any Changes Observed group
$changes_grp = $sec_a['f1any_changes_observed'] ?? [];
$chg_name    = $changes_grp['name'] ?? 'Nil';
$chg_addr    = $changes_grp['address'] ?? 'Nil';
$chg_scope   = $changes_grp['scope'] ?? 'Nil';
$chg_manpwr  = $changes_grp['manpower'] ?? 'Nil';

// Deviations group
$dev_grp     = $sec_a['f11deviations_changes_unresolved_issues_if_yes_specify'] ?? [];
$dev_plan    = $dev_grp['was_there_any_deviation_from_the_audit_plan'] ?? '-';
$dev_issues  = $dev_grp['Were_there_any_significant_issues_impacting_the_audit_program'] ?? '-';
$dev_changes = $dev_grp['Were_there_any_significant_changes_that_have_affected_the_management_system_of_the_client_since_the_last_audit_took_place'] ?? '-';
$dev_unreslv = $dev_grp['Any_un_resolved_issues_identified'] ?? '-';

// ── Section B group ───────────────────────────────────────────────────────────
$sec_b = get_field( 'f11section_b', $post_id ) ?: [];
$sb_concerns  = $sec_b['areas_of_concern_that_could_be_classified_as_nonconformity_during_the_stage_2_audit'] ?? '-';
$sb_info      = $sec_b['client_to_provide_following_information_and_records_for_detailed_examination_during_stage_2'] ?? '-';
$sb_capability= $sec_b['capability_of_the_ms_to_meet_applicable_requirements_and_expected_outcomes'] ?? '-';
$sb_followings= $sec_b['the_followings_need_to_be_addressed_along_with_the_issues_identified_in_the_document_review_report'] ?? '-';
$sb_obj_status= $sec_b['status_of_audit_objectives'] ?? 'Fulfilled';
$sb_comments  = $sec_b['comments'] ?? '-';
$sb_important = $sec_b['important_points_for_planning_forthcoming_audit_on-siteict_if_any'] ?? '-';

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

  /* header */
  .h-logo { width: 12%; border: none; text-align: center; vertical-align: middle; }
  .h-logo img { max-width: 70px; max-height: 60px; }
  .h-title { text-align: center; font-size: 13px; font-weight: bold; vertical-align: middle; }
  .h-sub   { font-size: 9px; color: #333; }
  .h-right { text-align: right; font-size: 9px; vertical-align: bottom; white-space: nowrap; }

  .lbl { font-weight: bold; background: #f5f5f5; width: 24%; }
  .val { width: 26%; }
  .sec-hdr { background: #d9d9d9; font-weight: bold; font-size: 10px; }
  .sn-td { text-align: center; width: 4%; }
  .green  { color: #006400; }

  .footer {
    position: fixed; bottom: 0; left: 0; right: 0;
    font-size: 7.5px; text-align: center; border-top: 1px solid #999; padding-top: 2px;
    color: #333;
  }
</style>
</head>
<body>

<!-- ══ PAGE 1: HEADER ════════════════════════════════════════════════════════ -->
<table>
  <tr>
    <td class="h-logo no-border"><img src="<?= $logo_b64 ?>" alt="GMC Logo"></td>
    <td class="h-title no-border">
      STAGE-1 AUDIT REPORT<br>
      <span class="h-sub">F-11 (Version 3.00, 01.02.2020)</span>
    </td>
    <td class="h-right no-border">F-11</td>
  </tr>
</table>

<table>
  <tr>
    <td class="lbl">Client</td>
    <td class="green" colspan="3"><?= esc_html($org) ?></td>
    <td class="lbl">Audit Ref No.</td>
    <td><?= esc_html($audit_ref) ?></td>
  </tr>
  <tr>
    <td class="lbl">Address</td>
    <td class="green" colspan="5"><?= esc_html($address) ?></td>
  </tr>
</table>

<!-- Footer fixed on all pages -->
<div class="footer">
  <em>Disclaimer:</em> The Auditing is based on a sampling process of the available information and consequently there is an element of uncertainty which may be reflected in the Audit findings. Those relying or acting upon the Audit results and conclusions to be aware of this uncertainty. The Audit recommendations are subject to an independent review, prior to decision.<br>
  GLOBAL MANAGEMENT CERTIFICATION SERVICES PRIVATE LIMITED &nbsp;|&nbsp;
  Flat No.402, Plot No.410, Matrusri Nagar, Miyapur, Hyderabad-500 049, India &nbsp;|&nbsp;
  Tel: 040-48559001 &nbsp;|&nbsp; E.mail: info@mcsglobal.in &nbsp;|&nbsp; Website: www.mcsglobal.in
</div>

<div class="pagebreak"></div>

<!-- ══ PAGE 2: AUDIT INFO ════════════════════════════════════════════════════ -->
<table>
  <tr><td colspan="6" class="sec-hdr">Audit Details</td></tr>
  <tr>
    <td class="lbl">Lead Auditor</td><td><?= esc_html($lead_auditor) ?></td>
    <td class="lbl">Auditor(s)</td><td><?= esc_html($auditors_txt) ?></td>
    <td class="lbl">Technical Expert</td><td><?= esc_html($tech_expert) ?></td>
  </tr>
  <tr>
    <td class="lbl">Witness / TL Supervision</td><td><?= esc_html($witness) ?></td>
    <td class="lbl">Observer(s)</td><td><?= esc_html($observers) ?></td>
    <td class="lbl">Interpreter(s)</td><td><?= esc_html($interpreters) ?></td>
  </tr>
  <tr>
    <td class="lbl">Auditor (ICT)</td><td colspan="2"><?= esc_html($auditor_ict) ?></td>
    <td class="lbl">Date(s) of Audit</td><td colspan="2"><?= esc_html($audit_date) ?></td>
  </tr>
  <tr>
    <td class="lbl">Management Representative</td><td><?= esc_html($mgr_rep) ?></td>
    <td class="lbl">Top Management</td><td colspan="3"><?= esc_html($top_mgmt) ?></td>
  </tr>
  <tr>
    <td class="lbl">Audit Criteria [Standard]</td><td colspan="2"><?= esc_html($standard) ?></td>
    <td class="lbl">Technical Code</td><td colspan="2"><?= esc_html($tech_code) ?></td>
  </tr>
  <tr>
    <td class="lbl">Exclusions</td>
    <td colspan="5"><?= esc_html($exclusions) ?></td>
  </tr>
  <tr>
    <td class="lbl">Audit Scope [Confirmed]</td>
    <td colspan="5"><?= esc_html($audit_scope) ?></td>
  </tr>
  <tr>
    <td class="lbl">Audit Objectives</td>
    <td colspan="5"><?= nl2br(esc_html($audit_objectives)) ?></td>
  </tr>
</table>

<!-- AUDIT TEAM -->
<?php if ( ! empty($audit_team) ) : ?>
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Email / Mobile</th>
      <th>Onsite / ICT</th>
      <th>Role</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ( $audit_team as $row ) : ?>
    <tr>
      <?php
        // Resolve auditor user field
        $a_name = '';
        if ( isset($row['f05_team_name']) ) {
            $uid = is_array($row['f05_team_name']) ? ($row['f05_team_name']['ID'] ?? 0) : (int)$row['f05_team_name'];
            if ( $uid ) {
                $u = get_userdata($uid);
                $a_name = $u ? $u->display_name : '';
            } else {
                $a_name = is_array($row['f05_team_name']) ? ($row['f05_team_name']['display_name'] ?? '') : $row['f05_team_name'];
            }
        }
      ?>
      <td><?= esc_html($a_name ?: '-') ?></td>
      <td><?= esc_html($row['f05_team_mail_mobile'] ?? '-') ?></td>
      <td><?= esc_html($row['f05_team_onsite_ict'] ?? '-') ?></td>
      <td><?= esc_html($row['f05_team_role'] ?? '-') ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<!-- ══ SECTION A ════════════════════════════════════════════════════════════ -->
<h4 style="margin:6px 0 3px;">Section A: General</h4>
<table>
  <thead>
    <tr>
      <th class="sn-td">S.N.</th>
      <th>CLIENT INFORMATION</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="sn-td" rowspan="5">1</td>
      <td><strong>Brief Profile of the Organization:</strong><br><?= nl2br(esc_html($brief_profile ?: '-')) ?></td>
    </tr>
    <tr>
      <td><strong>Products / Services:</strong><br><?= nl2br(esc_html($products ?: '-')) ?></td>
    </tr>
    <tr>
      <td><strong>Major Customers:</strong><br><?= nl2br(esc_html($major_customers ?: '-')) ?></td>
    </tr>
    <tr>
      <td><strong>Major Achievements:</strong><br><?= nl2br(esc_html($achievements ?: '-')) ?></td>
    </tr>
    <tr>
      <td><strong>Client's Management System Documentation:</strong><br><?= nl2br(esc_html($mgmt_docs ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">2</td>
      <td>
        <strong>Location, Site-Specific Conditions &amp; Processes:</strong><br>
        <?= esc_html($address) ?><br><br>
        <em><strong>Preparedness for Stage 2 Audit:</strong> Verification of application details regarding scope, exclusions, number of employees and site.<br>
        "Information addressed in the application was verified &amp; found _______________"</em><br><br>
        <strong>Working Hours:</strong> <?= esc_html($working_hours ?: '-') ?><br>
        <strong>No. of Shifts:</strong> <?= esc_html($shifts ?: '-') ?><br>
        <strong>No. of Employees:</strong> <?= esc_html($employees ?: '-') ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">3</td>
      <td><strong>Machinery / Equipment, Servers, Systems:</strong><br><?= nl2br(esc_html($machinery ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">4</td>
      <td><strong>Organization Objectives and Targets:</strong><br><?= nl2br(esc_html($objectives_tgts ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">5</td>
      <td><strong>Applicable Statutory and Regulatory Requirements:</strong><br><?= nl2br(esc_html($statutory ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">6</td>
      <td><strong>Customer Complaints (if any):</strong><br><?= nl2br(esc_html($complaints)) ?></td>
    </tr>
    <tr>
      <td class="sn-td">7</td>
      <td><strong>Risks and Opportunities:</strong><br><?= nl2br(esc_html($risks ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">8</td>
      <td><strong>Awareness:</strong><br><?= nl2br(esc_html($awareness ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">9</td>
      <td><strong>Outsourcing:</strong><br><?= nl2br(esc_html($outsourcing ?: '-')) ?></td>
    </tr>
    <tr>
      <td class="sn-td">10</td>
      <td>
        <strong>Status of Internal Audits &amp; Effectiveness of Corrective and Preventive Actions:</strong><br>
        <strong>Internal Audit Date:</strong> <?= esc_html($ia_date) ?><br>
        <strong>Frequency:</strong> <?= esc_html($ia_freq) ?><br>
        <strong>Non-Conformities:</strong> <?= nl2br(esc_html($ia_ncs)) ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">11</td>
      <td>
        <strong>Status of Management Review:</strong><br>
        <strong>Agenda Covered:</strong> <?= nl2br(esc_html($mr_agenda)) ?><br>
        <strong>Date of Internal Audit:</strong> <?= esc_html($mr_ia_date) ?><br>
        <strong>Date of Management Review:</strong> <?= esc_html($mr_mrm_date) ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">12</td>
      <td>
        <strong>Employee Count, Scope and Exclusions (as per application):</strong><br>
        Verified and found: <?= esc_html($employees_verified) ?><br>
        <strong>Effective No. of Employees:</strong> <?= esc_html($eff_emp ?: '-') ?><br>
        <strong>Scope:</strong> <?= esc_html($sa_scope ?: $audit_scope) ?><br>
        <strong>Exclusions:</strong> <?= esc_html($sa_excl ?: $exclusions) ?><br>
        <strong>Justification:</strong> <?= esc_html($sa_justif ?: '-') ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">13</td>
      <td><strong>Areas of Concern / Improvements:</strong><br><?= nl2br(esc_html($areas_concern)) ?></td>
    </tr>
    <tr>
      <td class="sn-td">14</td>
      <td><strong>Strong Points:</strong><br><?= nl2br(esc_html($strong_pts)) ?></td>
    </tr>
    <tr>
      <td class="sn-td">15</td>
      <td><strong>ICT Used &amp; Effectiveness (if any):</strong><br><?= nl2br(esc_html($ict_info)) ?></td>
    </tr>
    <tr>
      <td class="sn-td">16</td>
      <td>
        <strong>Any Changes Observed:</strong><br>
        <strong>Name:</strong> <?= esc_html($chg_name) ?> &nbsp;
        <strong>Address:</strong> <?= esc_html($chg_addr) ?> &nbsp;
        <strong>Scope:</strong> <?= esc_html($chg_scope) ?> &nbsp;
        <strong>Manpower:</strong> <?= esc_html($chg_manpwr) ?>
      </td>
    </tr>
  </tbody>
</table>

<!-- ══ SECTION B ════════════════════════════════════════════════════════════ -->
<h4 style="margin:6px 0 3px;">Section B:</h4>
<table>
  <thead>
    <tr>
      <th class="sn-td">S.N.</th>
      <th>Area / Question</th>
      <th style="width:40%">Notes</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="sn-td">1</td>
      <td>Areas of concern that could be classified as nonconformity during the Stage 2 audit?</td>
      <td><?= nl2br(esc_html($sb_concerns)) ?></td>
    </tr>
    <tr>
      <td class="sn-td">2</td>
      <td>Client to provide following information and records for detailed examination during Stage 2</td>
      <td><?= nl2br(esc_html($sb_info)) ?></td>
    </tr>
    <tr>
      <td class="sn-td">3</td>
      <td>Capability of the MS to meet applicable requirements and expected outcomes</td>
      <td><?= nl2br(esc_html($sb_capability)) ?></td>
    </tr>
    <tr>
      <td class="sn-td">4</td>
      <td>The followings need to be addressed along with the issues identified in the document review report</td>
      <td><?= nl2br(esc_html($sb_followings)) ?></td>
    </tr>
    <tr>
      <td class="sn-td">5</td>
      <td>Status of Audit Objectives</td>
      <td><?= esc_html($sb_obj_status) ?><?= ($sb_obj_status !== 'Fulfilled' && $sb_comments !== '-') ? '<br>Comments: ' . esc_html($sb_comments) : '' ?></td>
    </tr>
    <tr>
      <td class="sn-td">6</td>
      <td>Was there any deviation from the audit plan?</td>
      <td><?= esc_html($dev_plan) ?></td>
    </tr>
    <tr>
      <td class="sn-td">7</td>
      <td>Were there any significant issues impacting the audit program?</td>
      <td><?= esc_html($dev_issues) ?></td>
    </tr>
    <tr>
      <td class="sn-td">8</td>
      <td>Were there any significant changes affecting the management system since the last audit?</td>
      <td><?= esc_html($dev_changes) ?></td>
    </tr>
    <tr>
      <td class="sn-td">9</td>
      <td>Any un-resolved issues identified?</td>
      <td><?= esc_html($dev_unreslv) ?></td>
    </tr>
    <tr>
      <td class="sn-td">10</td>
      <td>Important points for planning forthcoming audit (on-site / ICT) if any</td>
      <td><?= nl2br(esc_html($sb_important)) ?></td>
    </tr>
  </tbody>
</table>

<!-- ══ RECOMMENDATIONS ══════════════════════════════════════════════════════ -->
<table>
  <tr>
    <td colspan="3" class="sec-hdr">Recommendations</td>
  </tr>
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
    <td colspan="2"><?= esc_html($opt) ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<!-- ══ STAGE 2 & SIGN-OFF ════════════════════════════════════════════════════ -->
<table>
  <tr>
    <td class="lbl">Stage 2 Planned On</td>
    <td colspan="5"><?= esc_html($stage2_date) ?></td>
  </tr>
</table>

<table>
  <tr>
    <td colspan="6" class="sec-hdr">Lead Auditor</td>
  </tr>
  <tr>
    <td class="lbl" style="width:20%">Name</td>
    <td style="width:30%"><?= esc_html($la_name) ?></td>
    <td class="lbl" style="width:20%">Signature</td>
    <td><?= esc_html($la_sig) ?></td>
  </tr>
</table>

<table>
  <tr>
    <td colspan="6" class="sec-hdr">Review of Stage 1 Audit Report</td>
  </tr>
  <tr>
    <td colspan="6"><?= nl2br(esc_html($review_report)) ?></td>
  </tr>
  <?php if ( ! empty($la2_name) || $la2_sig !== '-' ) : ?>
  <tr>
    <td class="lbl" style="width:20%">Lead Auditor (Reviewer)</td>
    <td style="width:30%"><?= esc_html($la2_name) ?></td>
    <td class="lbl" style="width:20%">Signature</td>
    <td><?= esc_html($la2_sig) ?></td>
  </tr>
  <?php endif; ?>
</table>

</body>
</html>
