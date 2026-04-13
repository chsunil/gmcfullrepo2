<?php
/**
 * QMS – F-13a Attendance Sheet (Stage-2 / Surveillance)
 * ACF Group: group_6885acad944c2
 *
 * Clone fields (prefix_name=0) — source meta keys:
 *   f13adate             → field_0023          → stage2_audit_surveillance_audit_date_initial
 *   f13aorganization_name→ field_org_name       → organization_name
 *   f13aRef_No           → field_69b4128404509  → f03proposal_ref_no
 *
 * Repeater:
 *   f13aattendance_sheet (field_6885acad97488)
 *     → sno, name, designation_&_department, opening_meeting, closing_meeting
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

// ── Helpers ──────────────────────────────────────────────────────────────────
if ( ! function_exists('f13a_val') ) {
    function f13a_val( $v, $fallback = '-' ) {
        if ( $v === null || $v === '' || $v === false ) return $fallback;
        if ( is_array($v) ) {
            if ( isset($v['display_name']) ) return esc_html( $v['display_name'] );
            if ( isset($v['label']) )        return esc_html( $v['label'] );
            $flat = array_filter( array_map( fn($i) => is_string($i) ? $i : '', $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fallback;
        }
        return esc_html( (string) $v );
    }
}

// ── Fields ───────────────────────────────────────────────────────────────────
// Organization Name
$org_raw = get_post_meta( $post_id, 'organization_name', true );
if ( ! $org_raw ) {
    $org_raw = function_exists('gmc_get_organization_name')
        ? gmc_get_organization_name( $post_id )
        : get_post_field( 'post_title', $post_id );
}
$org = esc_html( (string) $org_raw );

// Ref No — source: f03proposal_ref_no
$ref_no = esc_html( get_post_meta( $post_id, 'f03proposal_ref_no', true ) ?: '' );
if ( ! $ref_no ) $ref_no = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '-' );

// Date — field_0023 → stage2_audit_surveillance_audit_date_initial
$date_raw = get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_initial', true );
$date = function_exists('gmc_format_date') ? gmc_format_date( $date_raw ) : esc_html( $date_raw ?: '-' );

// Attendance repeater — use field key for reliability
$rows = get_field( 'field_6885acad97488', $post_id );
if ( ! is_array($rows) ) $rows = [];
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 12mm 10mm 12mm 10mm; }
body  { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
th, td { border: 1px solid #555; padding: 5px 6px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 9px; text-transform: uppercase; }
.no-border { border: none !important; background: transparent !important; }
.lbl  { background: #f2f2f2; font-weight: bold; width: 22%; white-space: nowrap; }
.title-cell { text-align: center; vertical-align: middle; }
.section-hdr td { background: #c6c6c6; font-weight: bold; font-size: 9px; text-transform: uppercase; }
.center { text-align: center; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 12px; }
</style>
</head>
<body>

<!-- Header -->
<table style="margin-bottom:6px;">
    <tr>
        <?php if ( $LOGO ) : ?>
        <td class="no-border" style="width:13%; text-align:center; vertical-align:middle;">
            <img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:50px; width:auto;" />
        </td>
        <?php endif; ?>
        <td class="no-border title-cell" colspan="<?= $LOGO ? 1 : 2 ?>">
            <strong style="font-size:14px; text-transform:uppercase; letter-spacing:1px;">Attendance Sheet</strong><br>
            <span style="font-size:10px; color:#444;">F-13a &nbsp;|&nbsp; QMS Certification &nbsp;|&nbsp; Version 1.00</span>
        </td>
        <td class="no-border" style="width:22%; font-size:8px; vertical-align:top; padding-top:2px;">
            <strong>F-13a (Version 1.00)</strong><br>QMS
        </td>
    </tr>
</table>

<!-- Audit Details -->
<table style="margin-bottom:6px;">
    <tr class="section-hdr"><td colspan="4">Audit Details</td></tr>
    <tr>
        <td class="lbl">Organization Name</td>
        <td colspan="3"><?= $org ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Ref No.</td>
        <td><?= $ref_no ?></td>
        <td class="lbl">Date</td>
        <td><?= $date ?></td>
    </tr>
</table>

<!-- Attendance Record -->
<table>
    <thead>
        <tr class="section-hdr"><td colspan="5">Attendance Record</td></tr>
        <tr>
            <th style="width:6%;">S.No</th>
            <th style="width:28%;">Name</th>
            <th style="width:28%;">Designation &amp; Department</th>
            <th style="width:19%;">Opening Meeting</th>
            <th style="width:19%;">Closing Meeting</th>
        </tr>
    </thead>
    <tbody>
    <?php if ( ! empty($rows) ) : ?>
        <?php foreach ( $rows as $row ) :
            $sno      = isset($row['sno'])                      ? esc_html($row['sno']) : '';
            $name     = isset($row['name'])                     ? esc_html($row['name']) : '-';
            $desig    = isset($row['designation_&_department']) ? esc_html($row['designation_&_department']) : '-';
            $open_mt  = isset($row['opening_meeting'])          ? esc_html($row['opening_meeting']) : '-';
            $close_mt = isset($row['closing_meeting'])          ? esc_html($row['closing_meeting']) : '-';
        ?>
        <tr>
            <td class="center"><?= $sno ?></td>
            <td><?= $name ?></td>
            <td><?= $desig ?></td>
            <td class="center"><?= $open_mt ?></td>
            <td class="center"><?= $close_mt ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="5" class="no-data">No attendance records entered yet.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Signature Row -->
<table style="margin-top:20px;">
    <tr>
        <td style="width:50%; height:50px; vertical-align:bottom; text-align:center;">
            <strong>Lead Auditor</strong><br>
            Signature: _____________________ &nbsp; Date: ___________
        </td>
        <td style="width:50%; height:50px; vertical-align:bottom; text-align:center;">
            <strong>Management Representative</strong><br>
            Signature: _____________________ &nbsp; Date: ___________
        </td>
    </tr>
</table>

</body>
</html>
