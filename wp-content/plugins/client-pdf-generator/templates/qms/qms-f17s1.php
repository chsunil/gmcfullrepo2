<?php
/**
 * QMS – F-17s1 On-Going Surveillance Plan (Surveillance Year 1)
 * ACF Group: group_69758381afa73
 *
 * Seamless clone fields (prefix_name=0) — source meta keys:
 *   f17s1organization → organization_name
 *   f17s1ref_no       → proposal_ref_no
 *   f17s1standard     → cert_scheme
 *   f17s1location     → head_office
 *
 * Group: f17s1planned_monthyear  — stage_i, stage_2, 1st_Surveillance, 2nd_Surveillance, Re-Certification
 * Group: f17s1executed_monthyear — same sub-field structure
 * Matrix: f17s1area_process (matrix_flexible) — status (radio), Off1st (text)
 * Textarea: f17s1remarks
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

if ( ! function_exists('f17s1_val') ) {
    function f17s1_val( $v, $fb = '-' ) {
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

// ── Header fields ─────────────────────────────────────────────────────────────
$org_raw  = get_post_meta( $post_id, 'organization_name', true );
if ( ! $org_raw ) $org_raw = function_exists('gmc_get_organization_name')
    ? gmc_get_organization_name( $post_id )
    : get_post_field( 'post_title', $post_id );
$org      = esc_html( (string) $org_raw );

$ref_no   = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '' );
$standard = esc_html( get_post_meta( $post_id, 'cert_scheme', true ) ?: '-' );
$location = esc_html( get_post_meta( $post_id, 'head_office', true ) ?: '-' );

// ── Planned (Month/Year) ───────────────────────────────────────────────────────
$plan_grp    = get_field( 'f17s1planned_monthyear', $post_id ) ?: [];
$plan_s1     = f17s1_val( $plan_grp['stage_i']         ?? '' );
$plan_s2     = f17s1_val( $plan_grp['stage_2']         ?? '' );
$plan_surv1  = f17s1_val( $plan_grp['1st_Surveillance'] ?? '' );
$plan_surv2  = f17s1_val( $plan_grp['2nd_Surveillance'] ?? '' );
$plan_recert = f17s1_val( $plan_grp['Re-Certification'] ?? '' );

// ── Executed (Month/Year) ──────────────────────────────────────────────────────
$exec_grp    = get_field( 'f17s1executed_monthyear', $post_id ) ?: [];
$exec_s1     = f17s1_val( $exec_grp['stage_i']         ?? '' );
$exec_s2     = f17s1_val( $exec_grp['stage_2']         ?? '' );
$exec_surv1  = f17s1_val( $exec_grp['1st_Surveillance'] ?? '' );
$exec_surv2  = f17s1_val( $exec_grp['2nd_Surveillance'] ?? '' );
$exec_recert = f17s1_val( $exec_grp['Re-Certification'] ?? '' );

// ── Area/Process matrix ────────────────────────────────────────────────────────
$matrix  = get_field( 'f17s1area_process', $post_id );
$remarks = esc_html( get_field( 'f17s1remarks', $post_id ) ?: '' );
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 12mm 10mm 12mm 10mm; }
body  { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
th, td { border: 1px solid #555; padding: 3px 4px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 8px; text-transform: uppercase; }
.no-border { border: none !important; background: transparent !important; }
.lbl  { background: #f2f2f2; font-weight: bold; white-space: nowrap; width: 28%; }
.title-row th { background: #c6c6c6; font-size: 11px; text-transform: uppercase; text-align: center; }
.section-hdr td { background: #c6c6c6; font-weight: bold; font-size: 9px; text-transform: uppercase; }
.req-list { font-size: 8px; color: #333; border: 1px solid #ccc; padding: 4px 8px; background: #fafafa; margin-bottom: 5px; line-height: 1.6; }
.center { text-align: center; }
.note-box { font-size: 7.5px; color: #444; border: 1px solid #ccc; padding: 3px 6px; background: #fafafa; margin-top: 4px; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 8px; }
</style>
</head>
<body>

<!-- Header -->
<table style="margin-bottom:4px;">
    <tr>
        <?php if ( $LOGO ) : ?>
        <td class="no-border" style="width:13%; text-align:center; vertical-align:middle;">
            <img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:50px; width:auto;" />
        </td>
        <?php endif; ?>
        <th colspan="<?= $LOGO ? 2 : 3 ?>" class="title-row no-border">On-Going Surveillance Plan</th>
        <td class="no-border" style="width:22%; font-size:8px; vertical-align:top; padding-top:2px;">
            <strong>F-17s1 (Version 2.00, 20.03.2016)</strong><br>QMS Surveillance Year 1
        </td>
    </tr>
</table>

<!-- Details -->
<table style="margin-bottom:4px;">
    <tr>
        <td class="lbl">Ref. No.</td>
        <td><?= $ref_no ?: '&nbsp;' ?></td>
        <td class="lbl">Organization</td>
        <td style="font-weight:bold;"><?= $org ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Standard</td>
        <td><?= $standard ?></td>
        <td class="lbl">Location</td>
        <td><?= $location ?></td>
    </tr>
</table>

<!-- Surveillance requirements -->
<div class="req-list">
    <strong>Each surveillance audit programme shall include at least the following:</strong><br>
    a) Internal audits and management review<br>
    b) A review of actions taken on nonconformities identified during previous audit<br>
    c) Treatment of complaints<br>
    d) Effectiveness of the management system with regard to achieving the client's objectives<br>
    e) Progress of planned activities aimed at continual improvement<br>
    f) Continuing operational control<br>
    g) Review of any changes<br>
    h) Use of GLOBAL MCS and Accreditation body marks and/or any reference to certification
</div>

<!-- Planned / Executed dates -->
<table style="margin-bottom:5px;">
    <thead>
        <tr>
            <th style="width:28%;"></th>
            <th>Stage I</th>
            <th>Stage II</th>
            <th>1st Surveillance</th>
            <th>2nd Surveillance</th>
            <th>Re-Certification</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="lbl">Planned (Month/Year)</td>
            <td class="center"><?= $plan_s1 ?: '&nbsp;' ?></td>
            <td class="center"><?= $plan_s2 ?: '&nbsp;' ?></td>
            <td class="center"><?= $plan_surv1 ?: '&nbsp;' ?></td>
            <td class="center"><?= $plan_surv2 ?: '&nbsp;' ?></td>
            <td class="center"><?= $plan_recert ?: '&nbsp;' ?></td>
        </tr>
        <tr>
            <td class="lbl">Executed (Month/Year)</td>
            <td class="center"><?= $exec_s1 ?: '&nbsp;' ?></td>
            <td class="center"><?= $exec_s2 ?: '&nbsp;' ?></td>
            <td class="center"><?= $exec_surv1 ?: '&nbsp;' ?></td>
            <td class="center"><?= $exec_surv2 ?: '&nbsp;' ?></td>
            <td class="center"><?= $exec_recert ?: '&nbsp;' ?></td>
        </tr>
    </tbody>
</table>

<!-- Area / Process matrix -->
<table>
    <tr class="section-hdr"><td colspan="3">Area / Process<br><small style="font-weight:normal; text-transform:none;">(&#187;) Planned / No. of Non-conformity found during audit &nbsp; <em>Example: &#187; / -2</em></small></td></tr>
    <thead>
        <tr>
            <th style="width:50%;">Area / Process</th>
            <th style="width:25%;">Applicable [Y/N]</th>
            <th style="width:25%;">Off 1st</th>
        </tr>
    </thead>
    <tbody>
    <?php if ( is_array($matrix) && ! empty($matrix) ) :
        foreach ( $matrix as $row_label => $cols ) :
            $applicable = is_array($cols) ? esc_html( (string)( $cols['status'] ?? '' ) ) : '';
            $off1st     = is_array($cols) ? esc_html( (string)( $cols['Off1st']  ?? '' ) ) : '';
    ?>
        <tr>
            <td><?= esc_html($row_label) ?></td>
            <td class="center"><?= $applicable ?: '&nbsp;' ?></td>
            <td class="center"><?= $off1st ?: '&nbsp;' ?></td>
        </tr>
    <?php endforeach;
    else : ?>
        <tr><td colspan="3" class="no-data">Area/process data not entered.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Remarks -->
<?php if ( $remarks ) : ?>
<table style="margin-top:4px;">
    <tr>
        <td class="lbl" style="width:18%;">Remarks</td>
        <td><?= nl2br($remarks) ?></td>
    </tr>
</table>
<?php endif; ?>

<div class="note-box">
    &raquo; Note 1. In case of change, lead auditor should rewrite and add his/her signature<br>
    &raquo; Note 2. Major: +, Minor: &minus; (Example: 3 Major: +3, 4 Minor: -4)<br>
    &raquo; Note 3. Please tick the processes to be audited during the next audit.
</div>

<!-- Signature -->
<table style="margin-top:16px;">
    <tr>
        <td style="border:none; width:50%; padding-top:22px;">Lead Auditor: ___________________________</td>
        <td style="border:none; width:50%; text-align:right; padding-top:22px;">Date: ___________________________</td>
    </tr>
</table>

</body>
</html>
