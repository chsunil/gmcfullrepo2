<?php
/**
 * QMS – F-13 Attendance Sheet
 * ACF Group: group_qms_f13  (verified against acf-export-2026-03-14.json)
 *
 * Top-level fields (inside unnamed seamless group):
 *   organization_name  — clone of field_org_name       (text, prefix_name=0)
 *   f13Ref_No          — clone of field_qms_f05_7      (f05gmcspl_ref_no, prefix_name=0)
 *   f13Date            — clone of field_0016            (date_picker, prefix_name=0)
 *
 * Repeater:
 *   ATTENDANCE_SHEET → sno, name, designation_&_department, opening_meeting, closing_meeting
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
if ( ! function_exists('f13_val') ) {
    function f13_val( $v, $fallback = '-' ) {
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

// ── Fields ────────────────────────────────────────────────────────────────────
// Organization Name — clone shares meta key with draft field_org_name; fallback to post title
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html( $org_raw )
         : ( is_array($org_raw) ? f13_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );

// Ref No — clone of f05gmcspl_ref_no (F05 Audit Team Allocation)
$ref_no  = f13_val( get_field( 'f03proposal_ref_no', $post_id ) );

// Date — clone of field_0016 (date_picker, returns d/m/Y or Y-m-d)
$date_raw = get_field( 'stage1_audit_initial', $post_id );
if ( $date_raw && preg_match('/^\d{4}-\d{2}-\d{2}/', $date_raw) ) {
    $date = date( 'd/m/Y', strtotime($date_raw) );
} else {
    $date = $date_raw ? esc_html($date_raw) : '-';
}

// Attendance repeater
$rows = get_field( 'ATTENDANCE_SHEET', $post_id );
if ( ! is_array($rows) ) $rows = [];

function f05cb( $checked = false ) {
    return $checked ? '[X]' : '[  ]';
}
$stage = get_field( 'f05audit_stage', $post_id );
$is_stage1 = ( $stage === 'stage1' );
$is_stage2 = ( $stage === 'stage2' );
$is_recert = ( $stage === 'recertification' );
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body {
    font-family: Arial, sans-serif;
    font-size: 10px;
    color: #000;
    margin: 0;
    padding: 10px;
}
h1 {
    text-align: center;
    font-size: 14px;
    font-weight: bold;
    margin: 0 0 2px 0;
    text-transform: uppercase;
    letter-spacing: 1px;
}
h2 {
    text-align: center;
    font-size: 10px;
    margin: 0 0 12px 0;
    color: #444;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}
th, td {
    border: 1px solid #555;
    padding: 5px 6px;
    vertical-align: top;
    text-align: left;
}
th {
    background: #d9d9d9;
    font-weight: bold;
    text-align: center;
    font-size: 9px;
    text-transform: uppercase;
}
.lbl {
    background: #f2f2f2;
    font-weight: bold;
    width: 22%;
    white-space: nowrap;
}
.h-logo  { border: none; text-align: center; vertical-align: middle; }
.h-title { text-align: center; font-size: 13px; font-weight: bold; vertical-align: middle; }
.section-title {
    background: #c6c6c6;
    font-weight: bold;
    padding: 5px 7px;
    margin: 12px 0 4px 0;
    border: 1px solid #555;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.center { text-align: center; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 12px; }
</style>
</head>
<body>

<!-- Logo -->
<table style="margin-bottom:6px;border:none;">
    <tr>
        <td class="logo-cell">
           <img alt="" src="data:image/jpeg;base64,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" />
        </td>
        <td class="title-cell">
           <h1>Attendance Sheet</h1>
           <h2>F-13 &nbsp;|&nbsp; QMS Certification &nbsp;|&nbsp; Version 1.00</h2>
        </td>
        <td class="cb-cell">
            <div class="cb-list">
                <div><?= f05cb( $is_stage1 ) ?> Stage-1</div>
                <div><?= f05cb( $is_stage2 ) ?> Stage-2</div>
                <div><?= f05cb( $is_recert ) ?> Re Certification</div>
                <div><?= f05cb( $is_surv )   ?> Surveillance Audit</div>
            </div>
        </td>
    </tr>
</table>

<!-- Title -->



<!-- Header Info -->
<div class="section-title">Audit Details</div>
<table>
    <tr>
        <td class="lbl">Organization Name</td>
        <td colspan="3"><?= $org ?></td>
    </tr>
    <tr>
        <td class="lbl">Ref No.</td>
        <td><?= $ref_no ?></td>
        <td class="lbl">Date</td>
        <td><?= $date ?></td>
    </tr>
</table>

<!-- Attendance Table -->
<div class="section-title">Attendance Record</div>
<table>
    <thead>
        <tr>
            <th style="width:6%">S.No</th>
            <th style="width:28%">Name</th>
            <th style="width:28%">Designation &amp; Department</th>
            <th style="width:19%">Opening Meeting</th>
            <th style="width:19%">Closing Meeting</th>
        </tr>
    </thead>
    <tbody>
    <?php if ( ! empty($rows) ) : ?>
        <?php foreach ( $rows as $row ) :
            $sno        = isset($row['sno'])                      ? esc_html($row['sno']) : '';
            $name       = isset($row['name'])                     ? esc_html($row['name']) : '-';
            $desig      = isset($row['designation_&_department']) ? esc_html($row['designation_&_department']) : '-';
            $open_mt    = isset($row['opening_meeting'])          ? esc_html($row['opening_meeting']) : '-';
            $close_mt   = isset($row['closing_meeting'])          ? esc_html($row['closing_meeting']) : '-';
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
