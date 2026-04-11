<?php
/**
 * QMS – F-11 Stage-1 Audit Report
 * ACF Group: group_qms_f11  (verified against acf-export-2026-03-14.json)
 */
if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ──────────────────────────────────────────────────────────────────
if ( ! function_exists('f11_val') ) {
    function f11_val( $v, $fallback = '-' ) {
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
if ( ! function_exists('f11_date') ) {
    function f11_date( $v, $fallback = '-' ) {
        if ( ! $v ) return $fallback;
        // ACF date_picker may return d/m/Y already or Y-m-d
        if ( preg_match('/^\d{4}-\d{2}-\d{2}/', $v) ) {
            return date( 'd/m/Y', strtotime($v) );
        }
        return esc_html( $v );
    }
}

// ── Logo ─────────────────────────────────────────────────────────────────────
$logo_b64 = 'data:image/jpeg;base64,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';

// ── TOP-LEVEL FIELDS ──────────────────────────────────────────────────────────
// Organization Name — post title is most reliable; ACF field may be empty clone
$org_raw = get_field( 'organization_name', $post_id );
if ( is_array($org_raw) ) {
    $org = $org_raw['organization_name'] ?? reset($org_raw) ?? '';
} elseif ( $org_raw ) {
    $org = (string) $org_raw;
} else {
    $org = '';
}
if ( ! $org ) $org = get_post_field( 'post_title', $post_id );
$org = esc_html( (string)$org );

// Address — clone field f11address (clones 'address' group)
$addr_grp = get_field( 'f11address', $post_id ) ?: get_field( 'address', $post_id ) ?: [];
$address  = is_array($addr_grp) ? ( $addr_grp['head_office'] ?? '' ) : (string) $addr_grp;

// Audit Ref No — clone field audit_refno; fallback to proposal_ref_no
$audit_ref = f11_val( get_field( 'audit_refno', $post_id ) ?: get_field( 'proposal_ref_no', $post_id ) );

// Management Representative — clone field f11management_representative; fallback to f01contact_person group
$mgr_rep_raw = get_field( 'f11management_representative', $post_id ) ?: get_field( 'f01contact_person', $post_id );
$mgr_rep = is_array($mgr_rep_raw)
    ? esc_html( $mgr_rep_raw['contact_person_name'] ?? reset($mgr_rep_raw) ?? '-' )
    : f11_val( $mgr_rep_raw );

// Top Management — clone field f11top_management; fallback to contact_person group
$top_mgmt_grp = get_field( 'f11top_management', $post_id ) ?: get_field( 'contact_person', $post_id );
$top_mgmt = is_array($top_mgmt_grp)
    ? esc_html( $top_mgmt_grp['top_management'] ?? $top_mgmt_grp['contact_person_name'] ?? reset($top_mgmt_grp) ?? '-' )
    : f11_val( $top_mgmt_grp );

// Audit Criteria / Standard — clone field f11audit_criteria_standard; fallback to cert_scheme
$standard = f11_val( get_field( 'f11audit_criteria_standard', $post_id ) ?: get_field( 'cert_scheme', $post_id ) );

// Exclusions — clone field f11exclusions; fallback to exclusions_only_for_iso_9001
$exclusions = f11_val( get_field( 'f11exclusions', $post_id ) ?: get_field( 'exclusions_only_for_iso_9001', $post_id ) );

// Audit Objectives — textarea: f11audit_objectives
$audit_objectives = get_field( 'f11audit_objectives', $post_id )
    ?: "* To Review Management System's Documented Information\n* To evaluate site specific conditions and readiness for Stage 2 audit\n* To Evaluate if internal audits and management reviews are planned, substantiating the readiness for stage 2\n* To Evaluate the understanding regarding requirements of the standard, in particular with respect to the identification of key performance or significant aspects, processes, objectives and operation of the management system";

// Audit Scope — clone field f11audit_scope_confirmed; fallback to scope_of_certification
$audit_scope = f11_val( get_field( 'f11audit_scope_confirmed', $post_id ) ?: get_field( 'scope_of_certification', $post_id ) );

// Audit Sites — clone field audit_sites; fallback to head_office from address group
$audit_sites_raw = get_field( 'audit_sites', $post_id );
if ( ! $audit_sites_raw ) {
    $addr_fallback = get_field( 'f11address', $post_id ) ?: get_field( 'address', $post_id ) ?: [];
    $audit_sites_raw = is_array($addr_fallback) ? ( $addr_fallback['head_office'] ?? '' ) : (string) $addr_fallback;
}
$audit_sites = f11_val( $audit_sites_raw, '-' );

// Dates of Audit — date_picker: f11dates_of_audit; fallback to stage1_audit from audit_dates
$audit_date = f11_date( get_field( 'f11dates_of_audit', $post_id ) ?: get_field( 'stage1_audit_initial', $post_id ) );

// Technical Code — clone field technical_code; fallback to technical_code_area
$tech_code = f11_val( get_field( 'technical_code', $post_id ) ?: get_field( 'technical_code_area', $post_id ) );

// Audit Team — clone field audit_team; fallback to f05_audit_team repeater
$audit_team = get_field( 'audit_team', $post_id ) ?: get_field( 'f05_audit_team', $post_id ) ?: [];

// Recommendations — checkbox: recommendations (no f11 prefix)
$recs = get_field( 'recommendations', $post_id ) ?: [];

// Stage 2 Planned On — date_picker: stage2_planned_on (no f11 prefix)
$stage2_date = f11_date( get_field( 'stage2_planned_on', $post_id ) );

// Lead Auditor group — group: lead_auditor
$la_group = get_field( 'lead_auditor', $post_id );
if ( is_array($la_group) ) {
    $la_raw  = $la_group['lead_auditor'] ?? null;   // sub-field (clone)
    $la_name = is_array($la_raw) ? f11_val($la_raw) : esc_html((string)($la_raw ?: ''));
    $la_sig  = esc_html( $la_group['signature'] ?? '-' );
} else {
    // If lead_auditor returns a string (edge case), use it directly
    $la_name = esc_html( (string)($la_group ?: '-') );
    $la_sig  = '-';
}
if ( ! $la_name ) $la_name = '-';

// Review of Stage 1 Audit Report — text
$review_report = get_field( 'review_of_stage_1_audit_report', $post_id ) ?: '-';

// Reviewer — text (simple text field, not user)
$reviewer_name = esc_html( get_field( 'reviewer', $post_id ) ?: '-' );

// Review Date — date_picker
$review_date = f11_date( get_field( 'review_date', $post_id ) );

// Important Points (TOP-LEVEL, not inside section_b)
$sb_important = esc_html( get_field( 'important_points_for_planning_forthcoming_audit_on-siteict_if_any', $post_id ) ?: '-' );

// ── SECTION A GROUP ───────────────────────────────────────────────────────────
// When section_a hasn't been saved yet, fall back to direct fields from earlier forms
$sec_a = get_field( 'section_a', $post_id ) ?: [];
// Fallback values from source fields (for clone sub-fields in section_a)
$_products_fb    = get_field( 'products_services',                     $post_id );
$_statutory_fb   = get_field( 'applicable_legal_and_statutory_requirements', $post_id );
$_outsource_fb   = get_field( 'outsourced_processes',                  $post_id ) ?: [];
$_ia_mrm_fb      = get_field( 'internal_audit_&_mrm',                 $post_id ) ?: [];
$_audit_calc_fb  = get_field( 'review_and_audit_time_calculation',    $post_id ) ?: [];
$_ict_fb         = get_field( 'Type_and_extent_of_ICT_used_if_any',   $post_id )
                   ?: ( $_audit_calc_fb['Type_and_extent_of_ICT_used_if_any'] ?? '' );
$_complaints_fb  = get_field( 'any_complaints_from_regulatory_or_from_market', $post_id );

$brief_profile   = $sec_a['Brief_Profile_of_the_Organization'] ?? '';
$products        = $sec_a['products_services:']  ?? $_products_fb ?? '';
$major_customers = $sec_a['major_customers:']    ?? '';
$achievements    = $sec_a['major_achievements']  ?? '';
$mgmt_docs       = $sec_a["client's_management_system_documentation"] ?? '';
$location        = $sec_a["client's_location_and_site-specific_conditions"] ?? $address ?? '';
$processes_raw   = $sec_a['client_processes'] ?? '';
$processes       = is_array($processes_raw) ? implode(', ', array_filter(array_map(fn($i) => is_string($i) ? $i : '', $processes_raw))) : (string)$processes_raw;
$other_notes     = $sec_a['other'] ?? '';
$working_hours   = $sec_a['working_hours:'] ?? '';
$shifts          = $sec_a['no_of_shifts:']   ?? '';
$employees       = $sec_a['No_of_Employees:'] ?? '';

// Machinery — clone inside section_a
$machinery_raw   = $sec_a['machinery_equipments_servers_systems:'] ?? '';  // trailing colon
$machinery       = is_array($machinery_raw) ? implode("\n", array_filter(array_map(fn($r) => is_string($r) ? $r : implode(', ', array_filter(array_map(fn($v) => is_string($v) ? $v : '', (array)$r))), $machinery_raw))) : (string)$machinery_raw;

$objectives_tgts = $sec_a['organization_objectives_and_targets'] ?? '';

// Statutory — clone, fallback to applicable_legal_and_statutory_requirements
$statutory_raw   = $sec_a['applicable_statutory_and_regulatory_requirements'] ?? $_statutory_fb ?? '';
$statutory       = is_array($statutory_raw) ? f11_val($statutory_raw) : esc_html((string)$statutory_raw);

$complaints      = $sec_a['customer_complaints_if_any:'] ?? $_complaints_fb ?? 'No significant complaints received from clients.';
$risks           = $sec_a['risks_and_opportunities'] ?? '';
$awareness       = $sec_a['awareness'] ?? '';

// Outsourcing — clone, fallback to outsourced_processes repeater
$outsourcing_raw = $sec_a['outsourcing'] ?: $_outsource_fb ?: [];
if ( is_array($outsourcing_raw) && isset($outsourcing_raw[0]) ) {
    $outsourcing = implode('; ', array_filter( array_map(
        fn($r) => is_array($r)
            ? trim( ($r['process'] ?? '') . ( !empty($r['suppliersub_contractor']) ? ' ('.$r['suppliersub_contractor'].')' : '' ) )
            : (string)$r,
        $outsourcing_raw
    )));
} else {
    $outsourcing = is_string($outsourcing_raw) ? $outsourcing_raw : '';
}

// Internal Audits sub-group — fallback to internal_audit_&_mrm data
$ia_key  = 'status_of_internal_audits_along_with_effectiveness_of_corrective_and_preventive_actions:';
$ia_grp  = $sec_a[ $ia_key ] ?? [];
$ia_date_raw = $ia_grp['internal_audit_date'] ?? $_ia_mrm_fb['internal_audit_date'] ?? '';
$ia_date     = f11_date( is_array($ia_date_raw) ? '' : $ia_date_raw );
$ia_freq     = esc_html( $ia_grp['frequency'] ?? '-' );
$ia_ncs      = esc_html( $ia_grp['no_of_non_conformities'] ?? '-' );

// Management Review sub-group — fallback to internal_audit_&_mrm data
$mr_grp      = $sec_a['status_of_management_review'] ?? [];
$mr_agenda   = esc_html( $mr_grp['all_the_agenda_points_like_complaints_feedbacks'] ?? '-' );
$mr_ia_raw   = $mr_grp['date_of_internal_audit'] ?? $_ia_mrm_fb['internal_audit_date'] ?? '';
$mr_ia_date  = f11_date( is_array($mr_ia_raw) ? '' : $mr_ia_raw );
$mr_mrm_raw  = $mr_grp['date_of_management_review'] ?? $_ia_mrm_fb['mrm-audit_date'] ?? '';
$mr_mrm_date = f11_date( is_array($mr_mrm_raw) ? '' : $mr_mrm_raw );

// Employee count / scope / exclusions verified (radio)
$employees_verified = $sec_a['no_of_employees_scope_exclusions_as_per_application'] ?? 'Yes';

// Effective employees — fallback to review_and_audit_time_calculation
$eff_emp_raw   = $sec_a['effective_number_of_employees']
                 ?? ( is_array($_audit_calc_fb) ? ($_audit_calc_fb['effective_number_of_employees'] ?? '') : '' );
$eff_emp       = is_array($eff_emp_raw) ? f11_val($eff_emp_raw) : esc_html((string)$eff_emp_raw);

// Note: 'scope' appears twice in section_a (same key — ACF returns the first match)
$sa_scope_raw  = $sec_a['scope'] ?? '';
$sa_scope      = is_array($sa_scope_raw) ? f11_val($sa_scope_raw) : esc_html((string)$sa_scope_raw);

$sa_excl_raw   = $sec_a['exclusions'] ?? '';
$sa_excl       = is_array($sa_excl_raw) ? f11_val($sa_excl_raw) : esc_html((string)$sa_excl_raw);

$sa_justif_raw = $sec_a['justification_for_exclusions'] ?? '';
$sa_justif     = is_array($sa_justif_raw) ? f11_val($sa_justif_raw) : esc_html((string)$sa_justif_raw);

$areas_concern = esc_html( $sec_a['areas_of_concernimprovements'] ?? '-' );
$strong_pts    = esc_html( $sec_a['strong_points'] ?? '-' );

// ICT — clone, fallback to ICT field from audit time calculation
$ict_raw  = $sec_a['ict_used_and_their_effectiveness_and_comments_is_any'] ?? $_ict_fb ?? 'N/A';
$ict_info = is_array($ict_raw) ? f11_val($ict_raw) : esc_html((string)$ict_raw);

// Any Changes Observed sub-group
$changes_grp = $sec_a['any_changes_observed'] ?? [];
$chg_name    = esc_html( $changes_grp['name']     ?? 'Nil' );
$chg_addr    = esc_html( $changes_grp['address']  ?? 'Nil' );
$chg_scope   = esc_html( $changes_grp['scope']    ?? 'Nil' );
$chg_manpwr  = esc_html( $changes_grp['manpower'] ?? 'Nil' );

// Deviations sub-group
$dev_grp     = $sec_a['deviations_changes_unresolved_issues_if_yes_specify'] ?? [];
$dev_plan    = esc_html( $dev_grp['was_there_any_deviation_from_the_audit_plan'] ?? '-' );
$dev_issues  = esc_html( $dev_grp['Were_there_any_significant_issues_impacting_the_audit_program'] ?? '-' );
$dev_changes = esc_html( $dev_grp['Were_there_any_significant_changes_that_have_affected_the_management_system_of_the_client_since_the_last_audit_took_place'] ?? '-' );
$dev_unreslv = esc_html( $dev_grp['Any_un_resolved_issues_identified'] ?? '-' );

// ── SECTION B GROUP ───────────────────────────────────────────────────────────
$sec_b        = get_field( 'section_b', $post_id ) ?: [];
$sb_concerns  = esc_html( $sec_b['areas_of_concern_that_could_be_classified_as_nonconformity_during_the_stage_2_audit'] ?? '-' );
$sb_info      = esc_html( $sec_b['client_to_provide_following_information_and_records_for_detailed_examination_during_stage_2'] ?? '-' );
$sb_capability= esc_html( $sec_b['capability_of_the_ms_to_meet_applicable_requirements_and_expected_outcomes'] ?? '-' );
$sb_followings= esc_html( $sec_b['the_followings_need_to_be_addressed_along_with_the_issues_identified_in_the_document_review_report'] ?? '-' );
$sb_obj_raw   = $sec_b['status_of_audit_objectives'] ?? 'Fulfilled';
$sb_obj_status= is_array($sb_obj_raw) ? f11_val($sb_obj_raw) : esc_html((string)$sb_obj_raw);
$sb_comments  = esc_html( $sec_b['comments'] ?? '-' );

?><!doctype html>
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
  .h-sub   { font-size: 9px; color: #333; }
  .h-right { text-align: right; font-size: 9px; vertical-align: bottom; white-space: nowrap; }
  .lbl  { font-weight: bold; background: #f5f5f5; width: 22%; }
  .val  { width: 28%; }
  .sec-hdr { background: #d9d9d9; font-weight: bold; font-size: 10px; }
  .sn-td   { text-align: center; width: 4%; }
  .green   { color: #000000; }
  .footer  {
    position: fixed; bottom: 0; left: 0; right: 0;
    font-size: 7.5px; text-align: center; border-top: 1px solid #999; padding-top: 2px; color: #333;
  }
</style>
</head>
<body>

<!-- ══ PAGE 1: COVER HEADER ══════════════════════════════════════════════════ -->
<table style="margin-top:100px">
  <tr>
    <td class="h-logo no-border">
  <img alt="" src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEA3ADcAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT/wAARCAF3AZYDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9U6KKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigApCMilooAQDAo700tkGqS6xZtqp04XMZvVi84wbvnCZxnHpmlexLaW5oUUmRS0ygooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACmsaXIpkjDHBoAyPFHiKz8K6De6tfSrDa2sTSuzHHAHT+lfn1pv7QmrRfG8+OJJGEE8ohkts8fZc4C/gOfrXp/7Z/xfF9dReCtNnzHCwk1BlPVv4U/rXykQfT86+PzLHuNVU6b+E/KeIc7nHExpUJfA7vzZ+tOh6va6/pNtqNnKJ7W5jEsbqeCD3rSXtXyN+xd8XhNaSeCdRlPmxbpbJnPVf4kz7dQK+uIyMCvpcNXWIpKaP0LLcbDH4eNaPXf1JKKTNLXWeoFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFI3Q0tIxwKAIyOK4H40fEm3+F/gW/1aRlNyF8u2jJ+/Ien4Cu6nlWKNnchUUZJJ4Ffnp+1H8WT8SPHTWlnKW0jTCYYgDw75+Zq8zHYpYak31Pnc7zGOXYVyv7z0R5Fq+q3OuardahdymW5uZGkd27sTk1UPWgL2NO28da/PZScpNs/BakpVJOcnqzQ8M+Ibzwpr1jq9hKYrq0kEiMD6dvxr9OPhd48tPiN4K07W7V1zNGBKgOfLkH3lP41+WpGOK+gP2Rfi3/whXi1tA1CYrpeqEKm48Ry9vpnpXt5Ti/Y1PZy2Z9pwzmn1Wv7Cb92R98A9KevIqJGyAe2O1SKc19ze5+z3ukOooopjCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoopKAA4zSPjFLkDvWV4i1q18PaPd6ldyCK3to2kdj2AGaTaSuyJSUIuUtkeL/ALV3xd/4QHwY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+wf8C/Etr45XxtqdnJpum28TpEs6bWnZx1A9OetfeH/CG6JnI0iyz/1wX/CtWC1jt4ljjRY0UYCoMAD6V6dXNoewnRw9Pl5tzyKOTT9tCriKnNy7DhwKetIExTulfOH1i0FooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigD/2Q==" />    
  </td>
  </tr>
 
</table>

<table>
   <tr>
    <td colspan="2" class="h-title no-border">
      STAGE-1 AUDIT REPORT<br>
    </td> 
  </tr>
  
  <tr>
    <td class="lbl">Audit Ref No.</td>
    <td><?= $audit_ref ?></td>
  </tr>
  <tr>
    <td class="lbl">Client</td>
    <td class="green"><?= $org ?></td>
  </tr>
  <tr>
    <td class="lbl">Address</td>
    <td class="green" ><?= esc_html($address) ?></td>
    </tr>
    <tr><td class="lbl">Site</td>
    <td>-</td>
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

<!-- Fixed footer on all pages -->
<div class="footer">
  <em>Disclaimer:</em> The Auditing is based on a sampling process of the available information and consequently there is an element of uncertainty which may be reflected in the Audit findings. Those relying or acting upon the Audit results and conclusions should be aware of this uncertainty. The Audit recommendations are subject to an independent review, prior to decision.<br>
  GLOBAL MANAGEMENT CERTIFICATION SERVICES PRIVATE LIMITED &nbsp;|&nbsp;
  Flat No.402, Plot No.410, Matrusri Nagar, Miyapur, Hyderabad-500 049, India &nbsp;|&nbsp;
  Tel: 040-48559001 &nbsp;|&nbsp; E.mail: info@mcsglobal.in &nbsp;|&nbsp; Website: www.mcsglobal.in
</div>

<div class="pagebreak"></div>

<!-- ══ PAGE 2: AUDIT DETAILS ═════════════════════════════════════════════════ -->
<!-- Audit Team -->
<table>
  <tr><td colspan="2" class="sec-hdr">Audit Team</td></tr>
  <?php
  // Build role map from repeater for display
  $team_by_role = [];
  foreach ( (array)$audit_team as $row ) {
      $role = is_array($row) ? ( $row['f05_team_role'] ?? $row['role'] ?? '' ) : '';
      $nm   = '';
      $uid_val = is_array($row) ? ( $row['f05_team_name'] ?? $row['name'] ?? null ) : null;
      if ( is_array($uid_val) && isset($uid_val['display_name']) ) {
          $nm = $uid_val['display_name'];
      } elseif ( is_numeric($uid_val) && (int)$uid_val > 0 ) {
          $u = get_userdata( (int)$uid_val );
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
      // Try exact match then partial match
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

<!-- ══ SECTION A ═════════════════════════════════════════════════════════════ -->
<h4 style="margin:6px 0 3px;">Section A: General</h4>
<table>
  <thead>
    <tr>
      <th class="sn-td">S.N.</th>
      <th>CLIENT INFORMATION</th>
    </tr>
  </thead>
  <tbody>

    <!-- 1. Brief Profile -->
    <tr>
      <td class="sn-td" rowspan="4">1</td>
      <td><strong>Brief Profile of the Organization:</strong><br><?= nl2br(esc_html($brief_profile ?: '-')) ?></td>
    </tr>
    <tr>
      <td><strong>Products / Services:</strong><br>
        <?php
        if ( is_array($products) ) {
            echo esc_html( implode(', ', array_filter(array_map(fn($v) => is_string($v) ? $v : '', $products))) );
        } else {
            echo nl2br(esc_html($products ?: '-'));
        }
        ?>
      </td>
    </tr>
    <tr>
      <td><strong>Major Customers:</strong><br><?= nl2br(esc_html($major_customers ?: '-')) ?></td>
    </tr>
    <tr>
      <td><strong>Major Achievements:</strong><br><?= nl2br(esc_html($achievements ?: '-')) ?></td>
    </tr>

    <!-- 2. Management System Documentation -->
    <tr>
      <td class="sn-td">2</td>
      <td>
        <strong>Client's Management System Documentation:</strong><br>
        <?= nl2br(esc_html($mgmt_docs ?: '-')) ?>
      </td>
    </tr>

    <!-- 3. Location, Site-Specific Conditions & Processes -->
    <tr>
      <td class="sn-td">3</td>
      <td>
        <strong>Client's Location and Site-Specific Conditions &amp; Processes:</strong><br>
        <?php
        $loc_txt = is_array($location) ? f11_val($location) : esc_html((string)$location);
        echo $loc_txt ?: esc_html($address);
        if ( $processes ) { echo '<br>' . nl2br(esc_html($processes)); }
        if ( $other_notes ) { echo '<br>' . nl2br(esc_html($other_notes)); }
        ?><br><br>
        <em>Preparedness for Stage 2: Information addressed in the application was verified &amp; found _______________</em><br><br>
        <strong>Working Hours:</strong> <?= esc_html($working_hours ?: '-') ?> &nbsp;
        <strong>No. of Shifts:</strong> <?= esc_html($shifts ?: '-') ?> &nbsp;
        <strong>No. of Employees:</strong> <?= esc_html($employees ?: '-') ?><br>
        <strong>Exclusions:</strong> <?= esc_html($sa_excl ?: $exclusions) ?><br>
        <strong>Scope:</strong> <?= esc_html($sa_scope ?: $audit_scope) ?>
      </td>
    </tr>

    <!-- 4. Machinery / Equipment -->
    <tr>
      <td class="sn-td">4</td>
      <td><strong>Machinery / Equipments, Servers, Systems:</strong><br><?= nl2br(esc_html($machinery ?: '-')) ?></td>
    </tr>

    <!-- 5. Organization Objectives and Targets -->
    <tr>
      <td class="sn-td">5</td>
      <td><strong>Organization Objectives and Targets:</strong><br><?= nl2br(esc_html($objectives_tgts ?: '-')) ?></td>
    </tr>

    <!-- 6. Statutory and Regulatory Requirements -->
    <tr>
      <td class="sn-td">6</td>
      <td><strong>Applicable Statutory and Regulatory Requirements:</strong><br><?= $statutory ?: '-' ?></td>
    </tr>

    <!-- 7. Customer Complaints -->
    <tr>
      <td class="sn-td">7</td>
      <td><strong>Customer Complaints (if any):</strong><br><?= nl2br(esc_html($complaints)) ?></td>
    </tr>

    <!-- 8. Risks and Opportunities -->
    <tr>
      <td class="sn-td">8</td>
      <td><strong>Risks and Opportunities:</strong><br><?= nl2br(esc_html($risks ?: '-')) ?></td>
    </tr>

    <!-- 9. Awareness -->
    <tr>
      <td class="sn-td">9</td>
      <td><strong>Awareness:</strong><br><?= nl2br(esc_html($awareness ?: '-')) ?></td>
    </tr>

    <!-- 10. Outsourcing -->
    <tr>
      <td class="sn-td">10</td>
      <td><strong>Outsourcing:</strong><br><?= nl2br(esc_html($outsourcing ?: '-')) ?></td>
    </tr>

    <!-- 11. Status of Internal Audits -->
    <tr>
      <td class="sn-td">11</td>
      <td>
        <strong>Status of Internal Audits along with Effectiveness of Corrective and Preventive Actions:</strong><br>
        <strong>Internal Audit Date:</strong> <?= $ia_date ?><br>
        <strong>Frequency:</strong> <?= $ia_freq ?><br>
        <strong>No. of Non Conformities:</strong> <?= nl2br($ia_ncs) ?>
      </td>
    </tr>

    <!-- 12. Status of Management Review -->
    <tr>
      <td class="sn-td">12</td>
      <td>
        <strong>Status of Management Review:</strong><br>
        <?= nl2br($mr_agenda) ?><br>
        <strong>Date of Internal Audit:</strong> <?= $mr_ia_date ?> &nbsp;
        <strong>Date of Management Review:</strong> <?= $mr_mrm_date ?>
      </td>
    </tr>

    <!-- 13. No of Employees, Scope, Exclusions as per application -->
    <tr>
      <td class="sn-td">13</td>
      <td>
        <strong>No. of Employees, Scope, Exclusions as per Application:</strong><br>
        Information addressed in the application was verified and found: <strong><?= esc_html($employees_verified) ?></strong><br>
        <strong>Effective No. of Employees:</strong> <?= $eff_emp ?: '-' ?><br>
        <strong>Scope:</strong> <?= esc_html($sa_scope ?: $audit_scope) ?><br>
        <strong>Exclusions:</strong> <?= esc_html($sa_excl ?: $exclusions) ?><br>
        <strong>Justification:</strong> <?= esc_html($sa_justif ?: '-') ?>
      </td>
    </tr>

    <!-- 14. Areas of Concern / Improvements -->
    <tr>
      <td class="sn-td">14</td>
      <td><strong>Areas of Concern / Improvements:</strong><br><?= nl2br($areas_concern) ?></td>
    </tr>

    <!-- 15. Strong Points -->
    <tr>
      <td class="sn-td">15</td>
      <td><strong>Strong Points:</strong><br><?= nl2br($strong_pts) ?></td>
    </tr>

    <!-- 16. ICT Used -->
    <tr>
      <td class="sn-td">16</td>
      <td><strong>ICT Used and Their Effectiveness and Comments (if any):</strong><br><?= nl2br($ict_info) ?></td>
    </tr>

    <!-- 17. Any Changes Observed -->
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

    <!-- 18. Deviations / Changes / Unresolved Issues -->
    <tr>
      <td class="sn-td">18</td>
      <td>
        <strong>Deviations / Changes / Unresolved Issues (If Yes, specify):</strong><br>
        <strong>Was there any deviation from the audit plan?</strong> <?= $dev_plan ?><br>
        <strong>Were there any significant issues impacting the audit programme?</strong> <?= $dev_issues ?><br>
        <strong>Were there any significant changes affecting the management system of the client since the last audit?</strong> <?= $dev_changes ?><br>
        <strong>Any unresolved issues identified?</strong> <?= $dev_unreslv ?>
      </td>
    </tr>

  </tbody>
</table>

<!-- ══ SECTION B ═════════════════════════════════════════════════════════════ -->
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
      <td><?= nl2br($sb_concerns) ?></td>
    </tr>
    <tr>
      <td class="sn-td">2</td>
      <td>Client to provide following information and records for detailed examination during Stage 2</td>
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
        <?php if ( $sb_comments && $sb_comments !== '-' ) : ?>
          <br>Comments: <?= $sb_comments ?>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <td class="sn-td">6</td>
      <td>Important points for planning forthcoming audit (on-site / ICT) if any</td>
      <td><?= nl2br($sb_important) ?></td>
    </tr>
  </tbody>
</table>

<!-- ══ RECOMMENDATIONS ═══════════════════════════════════════════════════════ -->
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

<!-- ══ STAGE 2 PLANNED & LEAD AUDITOR SIGN-OFF ═══════════════════════════════ -->
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

<p style="font-size:8.5px; margin:4px 0;">
  <strong>Attachments:</strong> Review of Stage 1 Audit Report
</p>

<!-- ══ REVIEW OF STAGE 1 AUDIT REPORT ════════════════════════════════════════ -->
<table>
  <tr><td colspan="4" class="sec-hdr">Review of Stage 1 Audit Report</td></tr>
  <tr>
    <td colspan="4">
      Based on the audit findings, issues addressed, data provided by Stage 1 Auditors and the Management, the
      Stage 2 audit is recommended to proceed as planned.<br><br>
      <?= nl2br(esc_html($review_report)) ?>
    </td>
  </tr>
  <tr>
    <td class="lbl" style="width:20%">Lead Auditor</td>
    <td style="width:30%"><?= $la_name ?></td>
    <td class="lbl" style="width:20%">Auditor(s)</td>
    <td>-</td>
  </tr>
  <tr>
    <td class="lbl">Technical Expert</td>
    <td>-</td>
    <td class="lbl">Reviewer</td>
    <td><?= $reviewer_name ?></td>
  </tr>
  <tr>
    <td class="lbl">Signature</td>
    <td>-</td>
    <td class="lbl">Date</td>
    <td><?= $review_date ?></td>
  </tr>
</table>

</body>
</html>
