<?php
/**
 * QMS – F-19 S1 Checklist for Certification Decision (Surveillance 1)
 * ACF Group: group_6975847c4afca
 * Field names identical to original F-19 (no prefix — seamless clones share source meta keys).
 *
 * Header clones (prefix_name=0) — source meta keys:
 *   organization_name, head_office, cert_scheme, scope_of_certification, proposal_ref_no
 *
 * ACF group fields (get_field):
 *   application_form, review_of_application, proposal, audit_plan_stage1, stage1_report,
 *   audit_plan_stage2, assessment_schedule, stage2_report, ncs, surveillance_plan,
 *   confidentiality, attendance_sheet, checklist_f25, legal_compliance,
 *   previous_cycle_review, other_points, reviews, certification_decision
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

// ── Helpers (shared with f19.php — guarded by function_exists) ─────────────────
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
if ( ! function_exists('f19_radio') ) {
    function f19_radio( $v ) {
        return ( $v === null || $v === '' ) ? '&nbsp;' : esc_html( (string) $v );
    }
}
if ( ! function_exists('f19_bool') ) {
    function f19_bool( $v ) {
        if ( $v === true  || $v === 1 || $v === '1' ) return 'Yes';
        if ( $v === false || $v === 0 || $v === '0' ) return 'No';
        return '&nbsp;';
    }
}
if ( ! function_exists('f19_date') ) {
    function f19_date( $v ) {
        if ( ! $v ) return '&nbsp;';
        if ( is_array($v) ) {
            $found = '';
            foreach ( $v as $item ) {
                if ( is_string($item) && $item !== '' ) { $found = $item; break; }
            }
            if ( $found === '' ) return '&nbsp;';
            $v = $found;
        }
        if ( function_exists('gmc_format_date') ) return gmc_format_date($v);
        return preg_match('/^\d{4}-\d{2}-\d{2}/', $v) ? date('d/m/Y', strtotime($v)) : esc_html($v);
    }
}
if ( ! function_exists('f19_nested') ) {
    function f19_nested( $arr, $key, $type = 'radio' ) {
        if ( ! is_array($arr) ) return '&nbsp;';
        $v = null;
        if ( array_key_exists($key, $arr) ) {
            $v = $arr[$key];
        } else {
            foreach ( $arr as $subgrp ) {
                if ( is_array($subgrp) && array_key_exists($key, $subgrp) ) {
                    $v = $subgrp[$key]; break;
                }
            }
        }
        if ( $v === null ) return '&nbsp;';
        switch ( $type ) {
            case 'bool':  return f19_bool($v);
            case 'date':  return f19_date($v);
            case 'val':   return f19_val($v);
            default:      return f19_radio($v);
        }
    }
}

// ── Header fields ──────────────────────────────────────────────────────────────
$org_raw = get_post_meta( $post_id, 'organization_name', true );
if ( ! $org_raw ) $org_raw = function_exists('gmc_get_organization_name')
    ? gmc_get_organization_name( $post_id )
    : get_post_field( 'post_title', $post_id );
$org = esc_html( (string) $org_raw );

$address  = esc_html( get_post_meta( $post_id, 'head_office', true ) ?: '-' );
$standard = esc_html( get_post_meta( $post_id, 'cert_scheme', true )
    ?: get_post_meta( $post_id, 'standard_applied', true ) ?: '-' );
$scope    = esc_html( get_post_meta( $post_id, 'scope_of_certification', true ) ?: '-' );
$ref_no   = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '-' );

// ── ACF groups ─────────────────────────────────────────────────────────────────
$app_form   = get_field( 'application_form',       $post_id ) ?: [];
$rev_app    = get_field( 'review_of_application',  $post_id ) ?: [];
$proposal   = get_field( 'proposal',               $post_id ) ?: [];
$ap_stage1  = get_field( 'audit_plan_stage1',      $post_id ) ?: [];
$s1_report  = get_field( 'stage1_report',          $post_id ) ?: [];
$ap_stage2  = get_field( 'audit_plan_stage2',      $post_id ) ?: [];
$sch        = get_field( 'assessment_schedule',    $post_id ) ?: [];
$s2_report  = get_field( 'stage2_report',          $post_id ) ?: [];
$ncs        = get_field( 'ncs',                    $post_id ) ?: [];
$surv_plan  = get_field( 'surveillance_plan',      $post_id ) ?: [];
$conf       = get_field( 'confidentiality',        $post_id ) ?: [];
$att_sheet  = get_field( 'attendance_sheet',       $post_id ) ?: [];
$f25        = get_field( 'checklist_f25',          $post_id ) ?: [];
$legal      = get_field( 'legal_compliance',       $post_id ) ?: [];
$prev_cycle = get_field( 'previous_cycle_review',  $post_id ) ?: [];
$other_pts  = get_field( 'other_points',           $post_id ) ?: [];
$reviews    = get_field( 'reviews',                $post_id ) ?: [];
$cert_dec   = get_field( 'certification_decision', $post_id ) ?: [];

// ── Checklist sections ─────────────────────────────────────────────────────────
$sections = [

    // 1. Application Form
    [
        'doc'  => 'Application Form',
        'rows' => [
            ['Completely filled',  f19_radio( $app_form['completely_filled'] ?? null )],
            ['Date',               f19_date(  $app_form['date'] ?? '' )],
        ],
    ],

    // 2. Review of Application
    [
        'doc'  => 'Review of application',
        'rows' => [
            ['Completely filled',
                f19_nested( $rev_app, 'completely_filled', 'radio' )],
            ['Mandays are correctly applied as per P-07',
                f19_nested( $rev_app, 'mandays_are_correctly_applied_as_per_p-07', 'radio' )],
            ['NACE Code is correct',
                f19_nested( $rev_app, 'nace_code_is_correct', 'radio' )],
            ['GMCS is accredited in NACE Code',
                f19_nested( $rev_app, 'gmcs_is_accredited_in_nace_code', 'radio' )],
            ['Date of review is ok',
                f19_nested( $rev_app, 'date_of_review_is_ok', 'date' )],
            ['If transfer, F-26 is attached with last report / certificate',
                f19_nested( $rev_app, 'if_transfer_f-26_is_attached_with_last_report_certificate', 'bool' )],
            ['If recertification, last certificate is valid',
                f19_nested( $rev_app, 'if_recertification_last_certificate_is_valid', 'bool' )],
        ],
    ],

    // 3. Proposal
    [
        'doc'  => 'Proposal',
        'rows' => [
            ['Date of offer is ok', f19_date(  $proposal['date_of_offer_is_ok'] ?? '' )],
            ['Scope is clear',      f19_radio( $proposal['scope_is_clear']       ?? null )],
        ],
    ],

    // 4. Surveillance Audit-1 Audit Plan
    [
        'doc'  => 'Surveillance Audit-1 Audit Plan',
        'rows' => [
            ['Completely filled',
                f19_radio( $ap_stage1['completely_filled']         ?? null )],
            ['Auditors are registered by GMCSPL',
                f19_val(   $ap_stage1['auditors_registered']       ?? null )],
            ['Mandays are correct',
                esc_html( (string)( $ap_stage1['mandays_correct'] ?? '' ) ) ?: '&nbsp;'],
            ['Scope is correct',
                f19_radio( $ap_stage1['scope_correct']             ?? null )],
            ['Date of audit is correct',
                f19_date(  $ap_stage1['date_of_audit']             ?? '' )],
            ['Date of audit plan is at least 3 days in advance',
                f19_radio( $ap_stage1['plan_in_advance']           ?? null )],
            ['Person with NACE Code',
                f19_val(   $ap_stage2['person_with_nace']          ?? null )],
        ],
    ],

    // 5. Surveillance Audit-1 Report
    [
        'doc'  => 'Surveillance Audit-1 report',
        'rows' => [
            ['Completely filled',
                f19_radio( $s1_report['completely_filled']   ?? null )],
            ['Date is correct',
                f19_date(  $s1_report['date_correct']        ?? '' )],
            ['Internal audit date is ok',
                f19_date(  $s1_report['internal_audit_date'] ?? '' )],
            ['MRM date is ok',
                f19_date(  $s1_report['mrm_date']            ?? '' )],
        ],
    ],

    // 6. Assessment Schedule
    [
        'doc'  => 'Assessment schedule',
        'rows' => [
            ['Audit team is same as in plan',
                f19_radio( $sch['team_same']        ?? null )],
            ['Dates are correct',
                f19_date(  $sch['dates_correct']    ?? '' )],
            ['Mandays are as per audit plan',
                esc_html( (string)( $sch['mandays_as_plan'] ?? '' ) ) ?: '&nbsp;'],
            ['One man-day is of at least 8 hours',
                f19_radio( $sch['manday_hours']     ?? null )],
        ],
    ],

    // 7. NCs
    [
        'doc'  => 'NCs',
        'rows' => [
            ['Dates are correct',
                f19_date(  $ncs['dates_correct']      ?? '' )],
            ['Previous NC if any, closed',
                f19_radio( $ncs['previous_nc_closed'] ?? null )],
            ['Completely filled',
                f19_radio( $ncs['completely_filled']  ?? null )],
        ],
    ],

    // 10. On going surveillance plan
    [
        'doc'  => 'On going surveillance plan',
        'rows' => [
            ['Completely filled',
                f19_radio( $surv_plan['completely_filled']    ?? null )],
            ['Processes as in Assessment schedule',
                f19_radio( $surv_plan['processes_ok']         ?? null )],
            ['Next assessment date',
                f19_date(  $surv_plan['next_assessment_date'] ?? '' )],
        ],
    ],

    // 11. Confidentiality & No COI
    [
        'doc'  => 'Confidentiality &amp; No COI',
        'rows' => [
            ['Signed by each of the audit team members',
                f19_radio( $conf['signed_by_team']    ?? null )],
            ['Dates of sign is before the participation in audit',
                f19_date(  $conf['dates_before_audit'] ?? '' )],
        ],
    ],

    // 10. Surveillance Attendance Sheet
    [
        'doc'  => 'Surveillance Attendance sheet',
        'rows' => [
            ['Correct dated', f19_date( $att_sheet['stage1_date'] ?? '' )],
        ],
    ],

    // 13. Checklist F-25
    [
        'doc'  => 'Checklist F-25',
        'rows' => [
            ['Properly filled',
                f19_radio( $f25['properly_filled']     ?? null )],
            ['Internal audit and MRM dates provided — Internal Audit',
                f19_date(  $f25['internal_audit_date'] ?? '' )],
            ['Internal audit and MRM dates provided — MRM',
                f19_date(  $f25['mrm_date']            ?? '' )],
        ],
    ],

    // 14. Legal Compliance Criteria
    [
        'doc'  => 'Legal Compliance Criteria for Certification decision',
        'rows' => [
            ['Is the organization achieved legal compliance with QMS requirements?',
                f19_radio( $legal['compliance_achieved'] ?? null )],
            ['Is the Audit report addressing about statement on legal compliance?',
                f19_radio( $legal['report_statement']    ?? null )],
        ],
    ],

    // 15. Review of previous cycle performance
    [
        'doc'  => 'Review of previous cycle performance',
        'rows' => [
            ['Comments on re-certification performance of the previous cycle addressed by the auditors',
                f19_val( $prev_cycle['comments_addressed'] ?? null )],
        ],
    ],

    // 16. Any other points
    [
        'doc'  => 'Any other points',
        'rows' => [
            ['Details', f19_val( $other_pts['details'] ?? null )],
        ],
    ],
];

// ── Review block ───────────────────────────────────────────────────────────────
$rv       = is_array($reviews) ? $reviews : [];
$rv1_date = f19_date( $rv['review1_date']    ?? '' );
$rv1_rem  = esc_html( (string)( $rv['review1_remarks'] ?? '' ) );
$rv2_date = f19_date( $rv['review2_date']    ?? '' );
$rv2_rem  = esc_html( (string)( $rv['review2_remarks'] ?? '' ) );
$chk_by   = esc_html( (string)( $rv['checked_by']   ?? '' ) );
$chk_date = f19_date( $rv['checked_date'] ?? '' );

// ── Certification Decision ─────────────────────────────────────────────────────
$cd = is_array($cert_dec) ? $cert_dec : [];
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 10mm 8mm; }
body  { font-family: Arial, sans-serif; font-size: 8px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
th, td { border: 1px solid #555; padding: 2px 4px; vertical-align: middle; text-align: left; }
th { background: #d9d9d9; font-weight: bold; font-size: 7.5px; text-transform: uppercase; text-align: center; }
.no-border { border: none !important; background: transparent !important; }
.title-row th { background: #c6c6c6; font-size: 11px; text-transform: uppercase; text-align: center; }
.lbl  { background: #f2f2f2; font-weight: bold; white-space: nowrap; }
.lbl2 { background: #f2f2f2; font-weight: bold; }
.sec-hdr td { background: #c6c6c6; font-weight: bold; font-size: 8px; text-transform: uppercase; }
.sno  { text-align: center; vertical-align: middle; font-weight: bold; width: 5%; }
.doc  { vertical-align: middle; font-weight: bold; width: 20%; }
.val  { text-align: center; vertical-align: middle; width: 22%; }
.cd-lbl { background: #f2f2f2; font-weight: bold; width: 55%; }
</style>
</head>
<body>

<!-- Header -->
<table style="margin-bottom:3px;">
    <tr>
        <?php if ( $LOGO ) : ?>
        <td class="no-border" style="width:12%; text-align:center; vertical-align:middle;">
            <img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:45px; width:auto;" />
        </td>
        <?php endif; ?>
        <th colspan="<?= $LOGO ? 2 : 3 ?>" class="title-row no-border">
            Checklist for Certification Decision — Surveillance 1<br>
            <span style="font-size:8px; font-weight:normal;">To be completed by designated staff, except Certification Decision</span>
        </th>
        <td class="no-border" style="width:22%; font-size:7.5px; vertical-align:top; padding-top:2px;">
            <strong>F-19 S1 (Version 4.00, 17.03.2023)</strong><br>QMS Certification
        </td>
    </tr>
</table>

<!-- Details -->
<table style="margin-bottom:3px;">
    <tr>
        <td class="lbl" style="width:5%;">Ref No.</td>
        <td colspan="3"><?= $ref_no ?></td>
    </tr>
    <tr>
        <td class="lbl">Organization</td>
        <td colspan="3" style="font-weight:bold;"><?= $org ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Address</td>
        <td colspan="3"><?= $address ?></td>
    </tr>
    <tr>
        <td class="lbl">Standard</td>
        <td style="width:34%;"><?= $standard ?></td>
        <td class="lbl" style="width:16%;">&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Scope of Certification</td>
        <td colspan="3"><?= $scope ?></td>
    </tr>
</table>

<!-- Checklist Table -->
<table>
    
        <tr>
            <th class="sno">S.No</th>
            <th class="doc">Document</th>
            <th style="width:53%;">Check Point</th>
            <th style="width:22%;">Comments</th>
        </tr>
    
    
    <?php foreach ( $sections as $idx => $sec ) :
        $sno      = $idx + 1;
        $rowcount = count($sec['rows']);
        foreach ( $sec['rows'] as $ri => $row ) :
    ?>
        <tr>
            <?php if ( $ri === 0 ) : ?>
            <td class="sno" rowspan="<?= $rowcount ?>"><?= $sno ?></td>
            <td class="doc" rowspan="<?= $rowcount ?>"><?= $sec['doc'] ?></td>
            <?php endif; ?>
            <td><?= $row[0] ?></td>
            <td class="val"><?= $row[1] ?></td>
        </tr>
    <?php endforeach; endforeach; ?>
    
</table>

<!-- Review Section -->
<table style="margin-top:4px;">
    <tr class="sec-hdr"><td colspan="4">Review</td></tr>
    <tr>
        <td class="lbl" style="width:20%; white-space:nowrap;">1st Review / Date</td>
        <td colspan="3"><?= $rv1_date ?></td>
    </tr>
    <tr>
        <td colspan="4" style="border-top:none; padding:2px 4px; font-size:8px;">
            &#9744; File is ready for the review by MD / Review Team
            &nbsp;&nbsp;&nbsp;
            &#9744; Follow up is required
        </td>
    </tr>
    <tr>
        <td class="lbl" style="white-space:nowrap;">Remarks</td>
        <td colspan="3"><?= $rv1_rem ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl" style="white-space:nowrap;">2nd Review / Date</td>
        <td colspan="3"><?= $rv2_date ?></td>
    </tr>
    <tr>
        <td colspan="4" style="border-top:none; padding:2px 4px; font-size:8px;">
            &#9744; File is ready for the review by GM
            &nbsp;&nbsp;&nbsp;
            &#9744; Follow up is required
        </td>
    </tr>
    <tr>
        <td class="lbl" style="white-space:nowrap;">Remarks</td>
        <td colspan="3"><?= $rv2_rem ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl" style="width:20%;">Checked By</td>
        <td style="width:30%;"><?= $chk_by ?: '&nbsp;' ?></td>
        <td class="lbl" style="width:20%;">Date</td>
        <td><?= $chk_date ?></td>
    </tr>
</table>

<!-- Certification Decision -->
<table style="margin-top:4px;">
    <tr class="sec-hdr"><td colspan="2">Certification Decision</td></tr>
    <tr>
        <td class="cd-lbl">Person(s) of Certification Decision</td>
        <td><?= f19_val( $cd['decision_person'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Lead Auditor / Auditors</td>
        <td><?= f19_val( $cd['lead_auditors'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Standard</td>
        <td><?= f19_val( $cd['standard'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Scope</td>
        <td><?= f19_val( $cd['scope'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Any issues / ambiguity of information on 1 to 16 of above table</td>
        <td><?= f19_radio( $cd['issues_ambiguity'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">No. of Major Nonconformities (NCs)</td>
        <td><?= esc_html( (string)( $cd['major_ncs'] ?? '' ) ) ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Effectiveness of Correction &amp; CA taken on Major NCs</td>
        <td><?= f19_val( $cd['effectiveness'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">No. of Minor Nonconformities (NCs)</td>
        <td><?= esc_html( (string)( $cd['minor_ncs'] ?? '' ) ) ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Decision on Certification</td>
        <td><?= f19_val( $cd['decision_final'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Justification in case of decision is not in line with auditor's recommendation</td>
        <td><?= f19_val( $cd['justification'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Signature of person(s) of Certification Decision</td>
        <td><?= f19_val( $cd['signature'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Date of Certification Decision</td>
        <td><?= f19_date( $cd['cert_decision_date'] ?? '' ) ?></td>
    </tr>
</table>

</body>
</html>
