<?php
/**
 * QMS – F-19 S2 Checklist for Certification Decision (Surveillance Year 2)
 * ACF Group: group_b133a3da4e9d
 *
 * Header clones (prefix_name=0) — source meta keys:
 *   organization_name, head_office, cert_scheme, scope_of_certification, proposal_ref_no
 *
 * ACF group fields (get_field):
 *   f19s2application_form, f19s2review_of_application, f19s2proposal,
 *   f19s2surveillance_audit-1_audit_plan, f19s2stage1_report,
 *   f19s2assessment_schedule, f19s2ncs, f19s2surveillance_plan,
 *   f19s2confidentiality, f19s2surveillance_attendance_sheet,
 *   f19s2checklist_f25, f19s2legal_compliance, f19s2previous_cycle_review,
 *   f19s2f29s1details (textarea), f19s2reviews, f19s2certification_decision
 *
 * Sub-fields are unprefixed (only top-level names carry f19s2 prefix).
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

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
$app_form   = get_field( 'f19s2application_form',                $post_id ) ?: [];
$rev_app    = get_field( 'f19s2review_of_application',           $post_id ) ?: [];
$proposal   = get_field( 'f19s2proposal',                        $post_id ) ?: [];
$ap_surv1   = get_field( 'f19s2surveillance_audit-1_audit_plan', $post_id ) ?: [];
$s1_report  = get_field( 'f19s2stage1_report',                   $post_id ) ?: [];
$sch        = get_field( 'f19s2assessment_schedule',             $post_id ) ?: [];
$ncs        = get_field( 'f19s2ncs',                             $post_id ) ?: [];
$surv_plan  = get_field( 'f19s2surveillance_plan',               $post_id ) ?: [];
$conf       = get_field( 'f19s2confidentiality',                 $post_id ) ?: [];
$att_sheet  = get_field( 'f19s2surveillance_attendance_sheet',   $post_id ) ?: [];
$f25        = get_field( 'f19s2checklist_f25',                   $post_id ) ?: [];
$legal      = get_field( 'f19s2legal_compliance',                $post_id ) ?: [];
$prev_cycle = get_field( 'f19s2previous_cycle_review',           $post_id ) ?: [];
$f29details = esc_html( get_field( 'f19s2f29s1details',          $post_id ) ?: '' );
$reviews    = get_field( 'f19s2reviews',                         $post_id ) ?: [];
$cert_dec   = get_field( 'f19s2certification_decision',          $post_id ) ?: [];

// ── Checklist sections ─────────────────────────────────────────────────────────
$sections = [

    // 1. Application Form
    [
        'doc'  => 'Application Form',
        'rows' => [
            ['Completely filled', f19_radio( $app_form['completely_filled'] ?? null )],
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
                f19_nested( $rev_app, 'if_transfer_f-26_is_attached_with_last_report_certificate', 'val' )],
            ['If recertification, last certificate is valid',
                f19_nested( $rev_app, 'if_recertification_last_certificate_is_valid', 'val' )],
        ],
    ],

    // 3. Proposal
    [
        'doc'  => 'Proposal',
        'rows' => [
            ['Date of offer is ok', f19_val(   $proposal['date_of_offer_is_ok'] ?? null )],
            ['Scope is clear',      f19_radio( $proposal['scope_is_clear']       ?? null )],
        ],
    ],

    // 4. Surveillance Audit-1 Audit Plan
    [
        'doc'  => 'Surveillance Audit-1 Audit Plan',
        'rows' => [
            ['Completely filled',
                f19_radio( $ap_surv1['completely_filled']                            ?? null )],
            ['Auditors are registered by GMCSPL',
                f19_val(   $ap_surv1['auditors_registered']                          ?? null )],
            ['Mandays are correct',
                esc_html( (string)( $ap_surv1['mandays_correct'] ?? '' ) ) ?: '&nbsp;'],
            ['Scope is correct',
                f19_radio( $ap_surv1['scope_correct']                                ?? null )],
            ['Date of audit is correct',
                f19_date(  $ap_surv1['date_of_audit']                                ?? '' )],
            ['Date of audit plan is at least 3 days in advance',
                f19_val(   $ap_surv1['date_of_audit_plan_is_at_least_3_days_in_advance'] ?? null )],
        ],
    ],

    // 5. Stage 1 Report
    [
        'doc'  => 'Stage 1 Report',
        'rows' => [
            ['Completely filled',
                f19_radio( $s1_report['completely_filled']   ?? null )],
            ['Date is correct',
                f19_date(  $s1_report['date_correct']        ?? '' )],
            ['Internal audit date is ok',
                f19_radio( $s1_report['internal_audit_date'] ?? null )],
            ['MRM date is ok',
                f19_radio( $s1_report['mrm_date']            ?? null )],
        ],
    ],

    // 6. Assessment Schedule
    [
        'doc'  => 'Assessment Schedule',
        'rows' => [
            ['Audit team is same as in plan',
                f19_radio( $sch['team_same']     ?? null )],
            ['Dates are correct',
                f19_radio( $sch['dates_correct'] ?? null )],
            ['Mandays are as per audit plan',
                esc_html( (string)( $sch['mandays_as_plan'] ?? '' ) ) ?: '&nbsp;'],
            ['One man-day is of at least 8 hours',
                f19_radio( $sch['manday_hours']  ?? null )],
        ],
    ],

    // 7. NCs
    [
        'doc'  => 'NCs',
        'rows' => [
            ['Dates are correct',
                f19_radio( $ncs['dates_correctx']      ?? null )],
            ['Previous NC if any, closed',
                f19_radio( $ncs['previous_nc_closed'] ?? null )],
            ['Completely filled',
                f19_radio( $ncs['completely_filled']  ?? null )],
        ],
    ],

    // 8. On-going Surveillance Plan
    [
        'doc'  => 'On-going Surveillance Plan',
        'rows' => [
            ['Completely filled',
                f19_radio( $surv_plan['completely_filled']    ?? null )],
            ['Processes as in Assessment schedule',
                f19_radio( $surv_plan['processes_ok']         ?? null )],
            ['Next assessment date',
                f19_date(  $surv_plan['next_assessment_date'] ?? '' )],
        ],
    ],

    // 9. Confidentiality & No COI
    [
        'doc'  => 'Confidentiality &amp; No COI',
        'rows' => [
            ['Signed by each of the audit team members',
                f19_radio( $conf['signed_by_team']     ?? null )],
            ['Dates of sign is before the participation in audit',
                f19_date(  $conf['dates_before_audit'] ?? '' )],
        ],
    ],

    // 10. Surveillance Attendance Sheet
    [
        'doc'  => 'Surveillance Attendance Sheet',
        'rows' => [
            ['Correct dated',
                f19_radio( $att_sheet['correct_dated_q']                  ?? null )],
            ['Date',
                f19_date(  $att_sheet['surveillance_attendance_sheet_date'] ?? '' )],
        ],
    ],

    // 11. Checklist F-25
    [
        'doc'  => 'Checklist F-25',
        'rows' => [
            ['Properly filled',
                f19_radio( $f25['properly_filled'] ?? null )],
            ['Internal audit date provided',
                f19_date( is_array($f25['internal_audit_date'] ?? null)
                    ? ( $f25['internal_audit_date']['internal_audit'] ?? '' )
                    : '' )],
            ['MRM date provided',
                f19_date( is_array($f25['internal_audit_date'] ?? null)
                    ? ( $f25['internal_audit_date']['mrm_date'] ?? '' )
                    : '' )],
        ],
    ],

    // 12. Legal Compliance Criteria
    [
        'doc'  => 'Legal Compliance Criteria',
        'rows' => [
            ['Is the organization achieved legal compliance with QMS requirements?',
                f19_radio( $legal['compliance_achieved'] ?? null )],
            ['Is the Audit report addressing about statement on legal compliance?',
                f19_radio( $legal['report_statement']    ?? null )],
        ],
    ],

    // 13. Review of previous cycle performance
    [
        'doc'  => 'Review of previous cycle performance',
        'rows' => [
            ['Comments on re-certification performance of the previous cycle addressed by the auditors',
                f19_val( $prev_cycle['comments_addressed'] ?? null )],
        ],
    ],
];

// ── Review block ───────────────────────────────────────────────────────────────
$rv       = is_array($reviews) ? $reviews : [];
$rv1_date = f19_date( $rv['review1_date']    ?? '' );
$rv1_rem  = esc_html( (string)( $rv['review1_remarks'] ?? '' ) );
$rv2_date = f19_date( $rv['review2_date']    ?? '' );
$rv2_rem  = esc_html( (string)( $rv['review2_remarks'] ?? '' ) );
$rv2_rdy  = f19_bool( $rv['review2_ready']   ?? null );
$rv2_fup  = f19_bool( $rv['review2_followup'] ?? null );
$chk_by   = f19_val(  $rv['checked_by']      ?? null );
$chk_date = f19_date( $rv['checked_date']    ?? '' );

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
.doc  { vertical-align: middle; font-weight: bold; width: 22%; }
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
            Checklist for Certification Decision — Surveillance 2<br>
            <span style="font-size:8px; font-weight:normal;">To be completed by designated staff, except Certification Decision</span>
        </th>
        <td class="no-border" style="width:22%; font-size:7.5px; vertical-align:top; padding-top:2px;">
            <strong>F-19 S2 (Version 4.00, 17.03.2023)</strong><br>QMS Certification
        </td>
    </tr>
</table>

<!-- Details -->
<table style="margin-bottom:3px;">
    <tr>
        <td class="lbl" style="width:22%;">Ref No.</td>
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
        <td><?= $standard ?></td>
        <td class="lbl" style="width:22%;">Scope of Certification</td>
        <td><?= $scope ?></td>
    </tr>
</table>

<!-- Checklist Table -->
<table>
    <thead>
        <tr>
            <th class="sno">S.No</th>
            <th class="doc">Document</th>
            <th style="width:51%;">Check Point</th>
            <th style="width:22%;">Comments</th>
        </tr>
    </thead>
    <tbody>
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
    </tbody>
</table>

<?php if ( $f29details ) : ?>
<!-- F-29 Details -->
<table style="margin-top:4px;">
    <tr>
        <td class="lbl" style="width:22%;">Details (F-29)</td>
        <td><?= nl2br( $f29details ) ?></td>
    </tr>
</table>
<?php endif; ?>

<!-- Review Section -->
<table style="margin-top:4px;">
    <tr class="sec-hdr"><td colspan="4">Review</td></tr>
    <tr>
        <td class="lbl" style="width:22%; white-space:nowrap;">1st Review Date</td>
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
        <td class="lbl" style="white-space:nowrap;">2nd Review Date</td>
        <td colspan="3"><?= $rv2_date ?></td>
    </tr>
    <tr>
        <td colspan="4" style="border-top:none; padding:2px 4px; font-size:8px;">
            File ready for GM review: <?= $rv2_rdy ?>
            &nbsp;&nbsp;&nbsp;
            Follow up required: <?= $rv2_fup ?>
        </td>
    </tr>
    <tr>
        <td class="lbl" style="white-space:nowrap;">Remarks</td>
        <td colspan="3"><?= $rv2_rem ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl" style="width:22%;">Checked By</td>
        <td style="width:28%;"><?= $chk_by ?: '&nbsp;' ?></td>
        <td class="lbl" style="width:22%;">Date</td>
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
        <td class="cd-lbl">Any issues / ambiguity of information on 1 to 13 of above table</td>
        <td><?= f19_val( $cd['issues_ambiguity'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">No. of Major Nonconformities (NCs)</td>
        <td><?= esc_html( (string)( $cd['major_ncs'] ?? '' ) ) ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Effectiveness of Correction &amp; CA taken on Major NCs</td>
        <td><?= f19_val( $cd['major_nc_effectiveness'] ?? null ) ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">No. of Minor Nonconformities (NCs)</td>
        <td><?= esc_html( (string)( $cd['minor_ncs'] ?? '' ) ) ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="cd-lbl">Effectiveness of Correction &amp; CA taken on Minor NCs</td>
        <td><?= f19_val( $cd['minor_nc_effectiveness'] ?? null ) ?></td>
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
