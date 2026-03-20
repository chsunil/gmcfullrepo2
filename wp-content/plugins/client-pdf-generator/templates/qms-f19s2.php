<?php
/**
 * QMS – F-19s2 Checklist for Certification Decision (Surveillance Year 2)
 * ACF Group: group_b133a3da4e9d
 * All fields use s2_ prefix.
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

if ( ! function_exists('f19s2_val') ) {
    function f19s2_val( $v, $fb = '-' ) {
        if ( $v === null || $v === '' || $v === false ) return $fb;
        if ( is_array($v) ) {
            foreach ( ['display_name','label','name','value'] as $k ) {
                if ( ! empty($v[$k]) && is_string($v[$k]) ) return esc_html($v[$k]);
            }
            $flat = array_filter( array_map( fn($i) => is_string($i) ? trim($i) : '', $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fb;
        }
        return esc_html( (string) $v );
    }
}
function f19s2_radio( $v ) { return ( $v === null || $v === '' ) ? '—' : esc_html( (string) $v ); }
function f19s2_bool( $v ) {
    if ( $v === true  || $v === 1 || $v === '1' ) return 'Yes';
    if ( $v === false || $v === 0 || $v === '0' ) return 'No';
    return '—';
}
function f19s2_date( $v ) {
    if ( ! $v ) return '-';
    return preg_match('/^\d{4}-\d{2}-\d{2}/', $v) ? date('d/m/Y', strtotime($v)) : esc_html($v);
}

$org_raw = get_field( 's2_organization_name', $post_id ) ?: get_field( 's2_organization', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : ( is_array($org_raw) ? f19s2_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );
$standard = f19s2_val( get_field( 's2_standard', $post_id ) );
$scope    = f19s2_val( get_field( 's2_scope_of_certification', $post_id ) );

$app_form  = get_field( 's2_application_form', $post_id );
$rev_app   = get_field( 's2_review_of_application', $post_id );
$proposal  = get_field( 's2_proposal', $post_id );
$ap_stage1 = get_field( 's2_audit_plan_stage1', $post_id );
$s1_report = get_field( 's2_stage1_report', $post_id );
$ap_stage2 = get_field( 's2_audit_plan_stage2', $post_id );
$sch       = get_field( 's2_assessment_schedule', $post_id );
$s2_report = get_field( 's2_stage2_report', $post_id );
$ncs       = get_field( 's2_ncs', $post_id );
$surv_plan = get_field( 's2_surveillance_plan', $post_id );
$conf      = get_field( 's2_confidentiality', $post_id );
$att_sheet = get_field( 's2_attendance_sheet', $post_id );
$f25       = get_field( 's2_checklist_f25', $post_id );
$legal     = get_field( 's2_legal_compliance', $post_id );
$prev_cycle= get_field( 's2_previous_cycle_review', $post_id );
$other_pts = get_field( 's2_other_points', $post_id );
$reviews   = get_field( 's2_reviews', $post_id );
$cert_dec  = get_field( 's2_certification_decision', $post_id );
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 0; padding: 8px; }
h1 { text-align: center; font-size: 13px; font-weight: bold; margin: 0 0 2px 0; text-transform: uppercase; }
h2 { text-align: center; font-size: 9px; margin: 0 0 10px 0; color: #444; }
table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
th, td { border: 1px solid #555; padding: 4px 5px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; font-size: 8px; text-transform: uppercase; }
.lbl { background: #f2f2f2; font-weight: bold; width: 40%; }
.h-logo { border: none; text-align: center; }
.section-title { background: #c6c6c6; font-weight: bold; padding: 4px 6px; margin: 8px 0 3px 0; border: 1px solid #555; font-size: 9px; text-transform: uppercase; }
</style>
</head>
<body>
<?php if ( $LOGO ) : ?>
<table style="margin-bottom:4px;"><tr><td class="h-logo" style="border:none;"><img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:70px; width:auto;" /></td></tr></table>
<?php endif; ?>
<h1>Checklist for Certification Decision</h1>
<h2>F-19s2 &nbsp;|&nbsp; QMS Surveillance Year 2 &nbsp;|&nbsp; Version 1.00</h2>

<div class="section-title">Organisation</div>
<table>
    <tr><td class="lbl">Organization</td><td><?= $org ?></td></tr>
    <tr><td class="lbl">Standard</td><td><?= $standard ?></td></tr>
    <tr><td class="lbl">Scope of Certification</td><td><?= $scope ?></td></tr>
</table>

<div class="section-title">Application Form</div>
<table>
    <tr><td class="lbl">Completely Filled</td><td><?= f19s2_radio( is_array($app_form) ? ($app_form['s2_completely_filled'] ?? null) : null ) ?></td></tr>
    <tr><td class="lbl">Date</td><td><?= f19s2_date( is_array($app_form) ? ($app_form['s2_date'] ?? '') : '' ) ?></td></tr>
</table>

<div class="section-title">Review of Application</div>
<table>
    <?php if ( is_array($rev_app) ) :
        $sub_labels = [
            ['Completely Filled', 's2_completely_filled', 'radio'],
            ['Mandays correctly applied (P-07)', 's2_mandays_are_correctly_applied_as_per_p-07', 'radio'],
            ['NACE Code is correct', 's2_nace_code_is_correct', 'radio'],
            ['GMCS accredited in NACE Code', 's2_gmcs_is_accredited_in_nace_code', 'radio'],
            ['If transfer, F-26 attached', 's2_if_transfer_f-26_is_attached_with_last_report_certificate', 'bool'],
            ['If recertification, last cert valid', 's2_if_recertification_last_certificate_is_valid', 'bool'],
        ];
        foreach ( $sub_labels as $row ) :
            $val = null;
            foreach ( $rev_app as $subgrp ) {
                if ( is_array($subgrp) && array_key_exists($row[1], $subgrp) ) { $val = $subgrp[$row[1]]; break; }
            } ?>
        <tr><td class="lbl"><?= esc_html($row[0]) ?></td><td><?= $row[2] === 'bool' ? f19s2_bool($val) : f19s2_radio($val) ?></td></tr>
    <?php endforeach; endif; ?>
</table>

<div class="section-title">Proposal</div>
<table>
    <tr><td class="lbl">Date of Offer OK</td><td><?= f19s2_radio( is_array($proposal) ? ($proposal['s2_date_of_offer_is_ok'] ?? null) : null ) ?></td></tr>
    <tr><td class="lbl">Scope is Clear</td><td><?= f19s2_radio( is_array($proposal) ? ($proposal['s2_scope_is_clear'] ?? null) : null ) ?></td></tr>
</table>

<div class="section-title">Audit Plan – Stage 1</div>
<table>
    <?php $s1p = is_array($ap_stage1) ? $ap_stage1 : []; ?>
    <tr><td class="lbl">Completely Filled</td><td><?= f19s2_radio( $s1p['s2_completely_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Auditors Registered</td><td><?= f19s2_val( $s1p['s2_auditors_registered'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Mandays Correct</td><td><?= esc_html( (string)($s1p['s2_mandays_correct'] ?? '-') ) ?></td></tr>
    <tr><td class="lbl">Scope Correct</td><td><?= f19s2_radio( $s1p['s2_scope_correct'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Date of Audit Correct</td><td><?= f19s2_val( $s1p['s2_date_of_audit'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Plan ≥ 3 Days in Advance</td><td><?= f19s2_radio( $s1p['s2_plan_in_advance'] ?? null ) ?></td></tr>
</table>

<div class="section-title">Stage 1 Report / Stage 2 Plan &amp; Report / NCs</div>
<table>
    <?php $s1r = is_array($s1_report) ? $s1_report : []; $s2p = is_array($ap_stage2) ? $ap_stage2 : [];
          $sc = is_array($sch) ? $sch : []; $s2r = is_array($s2_report) ? $s2_report : []; $nc = is_array($ncs) ? $ncs : []; ?>
    <tr><td class="lbl">Stage 1 Completely Filled</td><td><?= f19s2_radio( $s1r['s2_completely_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Stage 2 Team Registered</td><td><?= f19s2_val( $s2p['s2_team_members'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Stage 2 Date of Audit</td><td><?= f19s2_date( $s2p['s2_date_of_audit'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">Assessment Team Same</td><td><?= f19s2_radio( $sc['s2_team_same'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Stage 2 Report Signed &amp; Dated</td><td><?= f19s2_date( $s2r['s2_signed_correct_date'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">Previous NC Closed</td><td><?= f19s2_radio( $nc['s2_previous_nc_closed'] ?? null ) ?></td></tr>
</table>

<div class="section-title">Surveillance Plan / Confidentiality / Attendance / F-25</div>
<table>
    <?php $sp = is_array($surv_plan) ? $surv_plan : []; $cf = is_array($conf) ? $conf : [];
          $as = is_array($att_sheet) ? $att_sheet : []; $f25d = is_array($f25) ? $f25 : []; ?>
    <tr><td class="lbl">Surveillance Plan Filled</td><td><?= f19s2_radio( $sp['s2_completely_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Next Assessment Date</td><td><?= f19s2_date( $sp['s2_next_assessment_date'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">Confidentiality Signed</td><td><?= f19s2_radio( $cf['s2_signed_by_team'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Attendance Stage 1 Date</td><td><?= f19s2_date( $as['s2_stage1_date'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">Attendance Stage 2 Date</td><td><?= f19s2_date( $as['s2_stage2_date'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">F-25 Properly Filled</td><td><?= f19s2_radio( $f25d['s2_properly_filled'] ?? null ) ?></td></tr>
</table>

<div class="section-title">Certification Decision</div>
<table>
    <?php $cd = is_array($cert_dec) ? $cert_dec : []; ?>
    <tr><td class="lbl">Person(s)</td><td><?= f19s2_val( $cd['s2_decision_person'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Lead Auditor(s)</td><td><?= f19s2_val( $cd['s2_lead_auditors'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Decision on Certification</td><td><?= f19s2_val( $cd['s2_decision_final'] ?? null ) ?></td></tr>
    <tr><td class="lbl">No. of Major NCs</td><td><?= esc_html( (string)($cd['s2_major_ncs'] ?? '-') ) ?></td></tr>
    <tr><td class="lbl">No. of Minor NCs</td><td><?= esc_html( (string)($cd['s2_minor_ncs'] ?? '-') ) ?></td></tr>
    <tr><td class="lbl">Signature</td><td><?= f19s2_val( $cd['s2_signature'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Date of Decision</td><td><?= f19s2_date( $cd['s2_cert_decision_date'] ?? '' ) ?></td></tr>
</table>
</body>
</html>
