<?php
/**
 * QMS – F-19 Checklist for Certification Decision
 * ACF Group: group_f19_full
 * Key fields: clones of org, address, standard, scope + many check groups
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

if ( ! function_exists('f19_val') ) {
    function f19_val( $v, $fb = '-' ) {
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
function f19_radio( $v ) {
    if ( $v === null || $v === '' ) return '—';
    return esc_html( (string) $v );
}
function f19_bool( $v ) {
    if ( $v === true  || $v === 1 || $v === '1' ) return 'Yes';
    if ( $v === false || $v === 0 || $v === '0' ) return 'No';
    return '—';
}
function f19_date( $v ) {
    if ( ! $v ) return '-';
    return preg_match('/^\d{4}-\d{2}-\d{2}/', $v) ? date('d/m/Y', strtotime($v)) : esc_html($v);
}

// Top-level fields
$org_raw = get_field( 'organization_name', $post_id ) ?: get_field( 'organization', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : ( is_array($org_raw) ? f19_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );
$standard = f19_val( get_field( 'standard', $post_id ) );
$scope    = f19_val( get_field( 'scope_of_certification', $post_id ) );

// Groups
$app_form   = get_field( 'application_form', $post_id );
$rev_app    = get_field( 'review_of_application', $post_id );
$proposal   = get_field( 'proposal', $post_id );
$ap_stage1  = get_field( 'audit_plan_stage1', $post_id );
$s1_report  = get_field( 'stage1_report', $post_id );
$ap_stage2  = get_field( 'audit_plan_stage2', $post_id );
$sch        = get_field( 'assessment_schedule', $post_id );
$s2_report  = get_field( 'stage2_report', $post_id );
$ncs        = get_field( 'ncs', $post_id );
$surv_plan  = get_field( 'surveillance_plan', $post_id );
$conf       = get_field( 'confidentiality', $post_id );
$att_sheet  = get_field( 'attendance_sheet', $post_id );
$f25        = get_field( 'checklist_f25', $post_id );
$legal      = get_field( 'legal_compliance', $post_id );
$prev_cycle = get_field( 'previous_cycle_review', $post_id );
$other_pts  = get_field( 'other_points', $post_id );
$reviews    = get_field( 'reviews', $post_id );
$cert_dec   = get_field( 'certification_decision', $post_id );
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
.lbl2 { background: #f2f2f2; font-weight: bold; width: 55%; }
.h-logo { border: none; text-align: center; }
.section-title { background: #c6c6c6; font-weight: bold; padding: 4px 6px; margin: 8px 0 3px 0; border: 1px solid #555; font-size: 9px; text-transform: uppercase; }
.sub-title { background: #e8e8e8; font-weight: bold; padding: 3px 5px; margin: 4px 0 2px 0; border: 1px solid #999; font-size: 9px; }
</style>
</head>
<body>

<?php if ( $LOGO ) : ?>
<table style="margin-bottom:4px;"><tr><td class="h-logo" style="border:none;"><img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:70px; width:auto;" /></td></tr></table>
<?php endif; ?>

<h1>Checklist for Certification Decision</h1>
<h2>F-19 &nbsp;|&nbsp; QMS Certification &nbsp;|&nbsp; Version 1.00</h2>

<div class="section-title">Organisation</div>
<table>
    <tr><td class="lbl">Organization</td><td><?= $org ?></td></tr>
    <tr><td class="lbl">Standard</td><td><?= $standard ?></td></tr>
    <tr><td class="lbl">Scope of Certification</td><td><?= $scope ?></td></tr>
</table>

<div class="section-title">Application Form</div>
<table>
    <tr><td class="lbl">Completely Filled</td><td><?= f19_radio( $app_form['completely_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Date</td><td><?= f19_date( $app_form['date'] ?? '' ) ?></td></tr>
</table>

<div class="section-title">Review of Application</div>
<table>
    <?php if ( is_array($rev_app) ) :
        // Render each sub-group row
        $sub_labels = [
            ['Completely Filled', 'completely_filled', 'radio'],
            ['Mandays correctly applied (P-07)', 'mandays_are_correctly_applied_as_per_p-07', 'radio'],
            ['NACE Code is correct', 'nace_code_is_correct', 'radio'],
            ['GMCS accredited in NACE Code', 'gmcs_is_accredited_in_nace_code', 'radio'],
            ['If transfer, F-26 attached', 'if_transfer_f-26_is_attached_with_last_report_certificate', 'bool'],
            ['If recertification, last cert valid', 'if_recertification_last_certificate_is_valid', 'bool'],
        ];
        foreach ( $sub_labels as $row ) :
            $val = null;
            foreach ( $rev_app as $subgrp ) {
                if ( is_array($subgrp) && array_key_exists($row[1], $subgrp) ) { $val = $subgrp[$row[1]]; break; }
            }
    ?>
        <tr>
            <td class="lbl"><?= esc_html($row[0]) ?></td>
            <td><?= $row[2] === 'bool' ? f19_bool($val) : f19_radio($val) ?></td>
        </tr>
    <?php endforeach; endif; ?>
</table>

<div class="section-title">Proposal</div>
<table>
    <tr><td class="lbl">Date of Offer OK</td><td><?= f19_radio( is_array($proposal) ? ($proposal['date_of_offer_is_ok'] ?? null) : null ) ?></td></tr>
    <tr><td class="lbl">Scope is Clear</td><td><?= f19_radio( is_array($proposal) ? ($proposal['scope_is_clear'] ?? null) : null ) ?></td></tr>
</table>

<div class="section-title">Audit Plan – Stage 1</div>
<table>
    <?php $s1p = is_array($ap_stage1) ? $ap_stage1 : []; ?>
    <tr><td class="lbl">Completely Filled</td><td><?= f19_radio( $s1p['completely_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Auditors Registered by GMCSPL</td><td><?= f19_val( $s1p['auditors_registered'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Mandays Correct</td><td><?= esc_html( (string)($s1p['mandays_correct'] ?? '-') ) ?></td></tr>
    <tr><td class="lbl">Scope Correct</td><td><?= f19_radio( $s1p['scope_correct'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Date of Audit Correct</td><td><?= f19_val( $s1p['date_of_audit'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Audit Plan ≥ 3 Days in Advance</td><td><?= f19_radio( $s1p['plan_in_advance'] ?? null ) ?></td></tr>
</table>

<div class="section-title">Stage 1 Report</div>
<table>
    <?php $s1r = is_array($s1_report) ? $s1_report : []; ?>
    <tr><td class="lbl">Completely Filled</td><td><?= f19_radio( $s1r['completely_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Internal Audit Date OK</td><td><?= f19_radio( $s1r['internal_audit_date'] ?? null ) ?></td></tr>
    <tr><td class="lbl">MRM Date</td><td><?= f19_date( $s1r['mrm_date'] ?? '' ) ?></td></tr>
</table>

<div class="section-title">Audit Plan – Stage 2</div>
<table>
    <?php $s2p = is_array($ap_stage2) ? $ap_stage2 : []; ?>
    <tr><td class="lbl">Completely Filled</td><td><?= f19_radio( $s2p['completely_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Team Members Registered by GMCSPL</td><td><?= f19_val( $s2p['team_members'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Date of Audit Correct</td><td><?= f19_date( $s2p['date_of_audit'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">Audit Plan ≥ 3 Days in Advance</td><td><?= f19_radio( $s2p['plan_in_advance'] ?? null ) ?></td></tr>
</table>

<div class="section-title">Assessment Schedule</div>
<table>
    <?php $sc = is_array($sch) ? $sch : []; ?>
    <tr><td class="lbl">Audit Team Same as Plan</td><td><?= f19_radio( $sc['team_same'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Dates Correct</td><td><?= f19_date( $sc['dates_correct'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">Mandays as per Plan</td><td><?= esc_html( (string)($sc['mandays_as_plan'] ?? '-') ) ?></td></tr>
    <tr><td class="lbl">One Man-day ≥ 8 Hours</td><td><?= f19_radio( $sc['manday_hours'] ?? null ) ?></td></tr>
</table>

<div class="section-title">Stage 2 Report</div>
<table>
    <?php $s2r = is_array($s2_report) ? $s2_report : []; ?>
    <tr><td class="lbl">Completely Filled</td><td><?= f19_radio( $s2r['completely_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Signed and Correctly Dated</td><td><?= f19_date( $s2r['signed_correct_date'] ?? '' ) ?></td></tr>
</table>

<div class="section-title">NCs</div>
<table>
    <?php $nc = is_array($ncs) ? $ncs : []; ?>
    <tr><td class="lbl">Dates Correct</td><td><?= f19_date( $nc['dates_correct'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">Previous NC Closed</td><td><?= f19_radio( $nc['previous_nc_closed'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Completely Filled</td><td><?= f19_radio( $nc['completely_filled'] ?? null ) ?></td></tr>
</table>

<div class="section-title">Ongoing Surveillance Plan</div>
<table>
    <?php $sp = is_array($surv_plan) ? $surv_plan : []; ?>
    <tr><td class="lbl">Completely Filled</td><td><?= f19_radio( $sp['completely_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Processes as in Assessment Schedule</td><td><?= f19_radio( $sp['processes_ok'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Next Assessment Date</td><td><?= f19_date( $sp['next_assessment_date'] ?? '' ) ?></td></tr>
</table>

<div class="section-title">Confidentiality &amp; No COI</div>
<table>
    <?php $cf = is_array($conf) ? $conf : []; ?>
    <tr><td class="lbl">Signed by Each Audit Team Member</td><td><?= f19_radio( $cf['signed_by_team'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Dates Before Participation</td><td><?= f19_date( $cf['dates_before_audit'] ?? '' ) ?></td></tr>
</table>

<div class="section-title">Attendance Sheet</div>
<table>
    <?php $as = is_array($att_sheet) ? $att_sheet : []; ?>
    <tr><td class="lbl">Stage 1 Date</td><td><?= f19_date( $as['stage1_date'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">Stage 2 Date</td><td><?= f19_date( $as['stage2_date'] ?? '' ) ?></td></tr>
</table>

<div class="section-title">Checklist F-25</div>
<table>
    <?php $f25d = is_array($f25) ? $f25 : []; ?>
    <tr><td class="lbl">Properly Filled</td><td><?= f19_radio( $f25d['properly_filled'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Internal Audit Date</td><td><?= f19_date( $f25d['internal_audit_date'] ?? '' ) ?></td></tr>
    <tr><td class="lbl">MRM Date</td><td><?= f19_date( $f25d['mrm_date'] ?? '' ) ?></td></tr>
</table>

<div class="section-title">Legal Compliance Criteria</div>
<table>
    <?php $lc = is_array($legal) ? $legal : []; ?>
    <tr><td class="lbl">Organization Achieved Legal Compliance</td><td><?= f19_radio( $lc['compliance_achieved'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Audit Report Addresses Compliance Statement</td><td><?= f19_radio( $lc['report_statement'] ?? null ) ?></td></tr>
</table>

<div class="section-title">Review of Previous Cycle Performance</div>
<table>
    <tr><td class="lbl">Comments Addressed</td><td><?= f19_val( is_array($prev_cycle) ? ($prev_cycle['comments_addressed'] ?? null) : null ) ?></td></tr>
</table>

<div class="section-title">Any Other Points</div>
<table>
    <tr><td class="lbl">Details</td><td><?= f19_val( is_array($other_pts) ? ($other_pts['details'] ?? null) : null ) ?></td></tr>
</table>

<div class="section-title">Review Dates &amp; Remarks</div>
<table>
    <?php $rv = is_array($reviews) ? $reviews : []; ?>
    <tr><td class="lbl">1st Review Date</td><td><?= f19_date( $rv['review1_date'] ?? '' ) ?></td><td class="lbl">Remarks</td><td><?= f19_val( $rv['review1_remarks'] ?? null ) ?></td></tr>
    <tr><td class="lbl">2nd Review Date</td><td><?= f19_date( $rv['review2_date'] ?? '' ) ?></td><td class="lbl">Remarks</td><td><?= f19_val( $rv['review2_remarks'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Checked By</td><td><?= f19_val( $rv['checked_by'] ?? null ) ?></td><td class="lbl">Checked Date</td><td><?= f19_date( $rv['checked_date'] ?? '' ) ?></td></tr>
</table>

<div class="section-title">Certification Decision</div>
<table>
    <?php $cd = is_array($cert_dec) ? $cert_dec : []; ?>
    <tr><td class="lbl">Person(s) of Certification Decision</td><td><?= f19_val( $cd['decision_person'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Lead Auditor(s)</td><td><?= f19_val( $cd['lead_auditors'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Standard</td><td><?= f19_val( $cd['standard'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Scope</td><td><?= f19_val( $cd['scope'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Any Issues/Ambiguity</td><td><?= f19_radio( $cd['issues_ambiguity'] ?? null ) ?></td></tr>
    <tr><td class="lbl">No. of Major NCs</td><td><?= esc_html( (string)($cd['major_ncs'] ?? '-') ) ?></td></tr>
    <tr><td class="lbl">No. of Minor NCs</td><td><?= esc_html( (string)($cd['minor_ncs'] ?? '-') ) ?></td></tr>
    <tr><td class="lbl">Effectiveness of Corrections</td><td><?= f19_val( $cd['effectiveness'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Decision on Certification</td><td><?= f19_val( $cd['decision_final'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Justification</td><td><?= f19_val( $cd['justification'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Signature</td><td><?= f19_val( $cd['signature'] ?? null ) ?></td></tr>
    <tr><td class="lbl">Date of Certification Decision</td><td><?= f19_date( $cd['cert_decision_date'] ?? '' ) ?></td></tr>
</table>

</body>
</html>
