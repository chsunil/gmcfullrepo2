<?php
/**
 * QMS – F-16s1 Audit Programme (Surveillance Year 1)
 * ACF Group: group_6974d719ef57b
 *
 * Seamless clone fields (prefix_name=0) — source meta keys:
 *   f16s1ref_no        → proposal_ref_no
 *   f16s1organization  → organization_name
 *   f16s1standard      → cert_scheme
 *   f16s1technical_area → technical_code_area
 *
 * Repeater: f16s1schedule_table (min:4)
 *   row_type (select) | stage_i (date F-Y) | stage_ii (date F-Y) | surv_1 (date F-Y) | surv_2 (date F-Y) | re_certification (date F-Y) | remarks (text)
 *
 * Matrix: f16s1AUDIT_PROGRAMME (matrix_flexible)
 *   observation=Stage I, remarks=Stage II, 1stsurveillance, 2ndsurveillance, re_certification, re_certification_remarks
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

// ── Header fields ─────────────────────────────────────────────────────────────
$org_raw = get_post_meta( $post_id, 'organization_name', true );
if ( ! $org_raw ) $org_raw = function_exists('gmc_get_organization_name')
    ? gmc_get_organization_name( $post_id )
    : get_post_field( 'post_title', $post_id );
$org = esc_html( (string) $org_raw );

$ref_no    = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '' );
$standard  = esc_html( get_post_meta( $post_id, 'cert_scheme', true ) ?: '-' );
$tech_area = esc_html( get_post_meta( $post_id, 'technical_code_area', true ) ?: '-' );

// ── Schedule repeater ─────────────────────────────────────────────────────────
$schedule_rows = get_field( 'f16s1schedule_table', $post_id ) ?: [];

// ── Matrix ────────────────────────────────────────────────────────────────────
$matrix = get_field( 'f16s1AUDIT_PROGRAMME', $post_id );
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 landscape; margin: 10mm; }
body  { font-family: Arial, sans-serif; font-size: 8.5px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
th, td { border: 1px solid #555; padding: 2px 4px; vertical-align: middle; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 8px; text-transform: uppercase; }
.no-border { border: none !important; background: transparent !important; }
.lbl  { background: #f2f2f2; font-weight: bold; white-space: nowrap; }
.title-row th { background: #c6c6c6; font-size: 11px; text-transform: none; text-align: center; font-weight: bold; }
.section-hdr td { background: #c6c6c6; font-weight: bold; font-size: 8.5px; text-transform: uppercase; }
.legend-row td { background: #f9f9f9; font-size: 7.5px; font-style: italic; }
.center { text-align: center; }
.note-box { font-size: 7.5px; color: #444; border: 1px solid #ccc; padding: 3px 6px; background: #fafafa; margin-top: 4px; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 8px; }
</style>
</head>
<body>

<!-- Header -->
<table style="margin-bottom:3px;">
    <tr>
        <?php if ( $LOGO ) : ?>
        <td class="no-border" style="width:10%; text-align:center; vertical-align:middle;">
            <img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:45px; width:auto;" />
        </td>
        <?php endif; ?>
        <th colspan="<?= $LOGO ? 2 : 3 ?>" class="title-row no-border">AUDIT PROGRAMME</th>
        <td class="no-border" style="width:18%; font-size:7.5px; vertical-align:top; padding-top:2px;">
            <strong>F-16s1 (Version 2.00, 20.03.2016)</strong><br>QMS Surveillance Year 1
        </td>
    </tr>
</table>

<!-- Details -->
<table style="margin-bottom:3px;">
    <tr>
        <td class="lbl" style="width:16%;">Ref. No.</td>
        <td colspan="3" style="width:34%;"><?= $ref_no ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Organization</td>
        <td colspan="3" style="font-weight:bold;"><?= $org ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Standard</td>
        <td style="width:34%;"><?= $standard ?></td>
        <td class="lbl" style="width:16%;">Technical Area</td>
        <td><?= $tech_area ?></td>
    </tr>
</table>

<!-- Planned / Conducted / ICT / Critical Security -->
<table style="margin-bottom:3px;">
    <thead>
        <tr>
            <th style="width:22%;"></th>
            <th>Stage I</th>
            <th>Stage II</th>
            <th>1st Surveillance</th>
            <th>2nd Surveillance</th>
            <th>Re-Certification</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
    <?php if ( ! empty($schedule_rows) ) :
        foreach ( $schedule_rows as $row ) :
            $rt  = esc_html( $row['row_type'] ?? '' );
            $s1  = esc_html( $row['stage_i'] ?? '' );
            $s2  = esc_html( $row['stage_ii'] ?? '' );
            $sv1 = esc_html( $row['surv_1'] ?? '' );
            $sv2 = esc_html( $row['surv_2'] ?? '' );
            $rc  = esc_html( $row['re_certification'] ?? '' );
            $rmk = esc_html( $row['remarks'] ?? '' );
    ?>
        <tr>
            <td class="lbl"><?= $rt ?: '&nbsp;' ?></td>
            <td class="center"><?= $s1 ?: '&nbsp;' ?></td>
            <td class="center"><?= $s2 ?: '&nbsp;' ?></td>
            <td class="center"><?= $sv1 ?: '&nbsp;' ?></td>
            <td class="center"><?= $sv2 ?: '&nbsp;' ?></td>
            <td class="center"><?= $rc ?: '&nbsp;' ?></td>
            <td><?= $rmk ?: '&nbsp;' ?></td>
        </tr>
    <?php endforeach;
    else : ?>
        <tr>
            <td class="lbl">Planned</td>
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">Conducted</td>
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">ICT Details if any</td>
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">Critical Security Controls if any</td>
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Area / Process matrix -->
<table>
    <tr class="section-hdr"><td colspan="7">Area / Process</td></tr>
    <tr class="legend-row">
        <td colspan="7">(&raquo;) Planned / No. of Non-conformity found during audit &nbsp;&nbsp; <em>(Example: &raquo; / -2)</em></td>
    </tr>
    
        <tr>
            <th style="width:25%;">Area / Process</th>
            <th>Stage I</th>
            <th>Stage II</th>
            <th>1st Surveillance</th>
            <th>2nd Surveillance</th>
            <th>Re-Certification</th>
            <th>Remarks</th>
        </tr>
    
    
    <?php if ( is_array($matrix) && ! empty($matrix) ) :
        foreach ( $matrix as $row_label => $cols ) :
            $c1 = is_array($cols) ? esc_html( (string)( $cols['observation']              ?? '' ) ) : '';
            $c2 = is_array($cols) ? esc_html( (string)( $cols['remarks']                  ?? '' ) ) : '';
            $c3 = is_array($cols) ? esc_html( (string)( $cols['1stsurveillance']          ?? '' ) ) : '';
            $c4 = is_array($cols) ? esc_html( (string)( $cols['2ndsurveillance']          ?? '' ) ) : '';
            $c5 = is_array($cols) ? esc_html( (string)( $cols['re_certification']         ?? '' ) ) : '';
            $c6 = is_array($cols) ? esc_html( (string)( $cols['re_certification_remarks'] ?? '' ) ) : '';
    ?>
        <tr>
            <td><?= esc_html($row_label) ?></td>
            <td class="center"><?= $c1 ?: '&nbsp;' ?></td>
            <td class="center"><?= $c2 ?: '&nbsp;' ?></td>
            <td class="center"><?= $c3 ?: '&nbsp;' ?></td>
            <td class="center"><?= $c4 ?: '&nbsp;' ?></td>
            <td class="center"><?= $c5 ?: '&nbsp;' ?></td>
            <td class="center"><?= $c6 ?: '&nbsp;' ?></td>
        </tr>
    <?php endforeach;
    else : ?>
        <tr><td colspan="7" class="no-data">Audit programme matrix data not entered.</td></tr>
    <?php endif; ?>
    <tr>
        <td class="lbl">Number of NC&#39;s</td>
        <td class="center">&nbsp;</td><td class="center">&nbsp;</td>
        <td class="center">&nbsp;</td><td class="center">&nbsp;</td>
        <td class="center">&nbsp;</td><td class="center">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Number of effective personnel</td>
        <td class="center">&nbsp;</td><td class="center">&nbsp;</td>
        <td class="center">&nbsp;</td><td class="center">&nbsp;</td>
        <td class="center">&nbsp;</td><td class="center">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">No of Sites Covered</td>
        <td class="center">&nbsp;</td><td class="center">&nbsp;</td>
        <td class="center">&nbsp;</td><td class="center">&nbsp;</td>
        <td class="center">&nbsp;</td><td class="center">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Audit Team Proposed</td>
        <td colspan="6">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Audit Team Actual</td>
        <td colspan="6">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Review of actions taken for notification of adverse events</td>
        <td colspan="6">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Impact on surveillance man-day</td>
        <td colspan="6">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Prepared by Team Leader</td>
        <td colspan="6">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Remarks</td>
        <td colspan="6">&nbsp;</td>
    </tr>
    
</table>

<div class="note-box">
    &raquo; Note 1. In case of change, lead auditor should rewrite and add his/her signature<br>
    &raquo; Note 2. Major: +, Minor: &minus; (Example: 3 Major: +3, 4 Minor: -4)<br>
    &raquo; Note 3. Please tick the processes to be audited during the next audit.
</div>

</body>
</html>
