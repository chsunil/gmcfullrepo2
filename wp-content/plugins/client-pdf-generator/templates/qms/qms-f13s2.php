<?php
/**
 * QMS – F-13s2 Attendance Sheet (Surveillance Year 2)
 * ACF Group: group_d958093fc637
 * Fields (seamless group, prefix_name=0):
 *   organization_name — clone of field_org_name
 *   f13Ref_No         — clone of field_qms_f05_7
 *   f13Date           — clone of field_0016 (date_picker)
 * Repeater: ATTENDANCE_SHEET → sno, name, designation_&_department, opening_meeting, closing_meeting
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

if ( ! function_exists('f13s2_val') ) {
    function f13s2_val( $v, $fb = '-' ) {
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

$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : ( is_array($org_raw) ? f13s2_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );

// f13Ref_No is a seamless clone of field_qms_f05_7 → proposal_ref_no
$ref_no   = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '-' );
// f13Date — use actual Surv-1 audit date
$date_raw = get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_surv2', true );
$date     = $date_raw ? ( function_exists('gmc_format_date') ? gmc_format_date($date_raw) : esc_html($date_raw) ) : '-';

$rows = get_field( 'f13s2ATTENDANCE_SHEET', $post_id );
if ( ! is_array($rows) ) $rows = [];
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 10px; }
h1 { text-align: center; font-size: 14px; font-weight: bold; margin: 0 0 2px 0; text-transform: uppercase; }
h2 { text-align: center; font-size: 10px; margin: 0 0 12px 0; color: #444; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
th, td { border: 1px solid #555; padding: 5px 6px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 9px; text-transform: uppercase; }
.lbl { background: #f2f2f2; font-weight: bold; width: 22%; white-space: nowrap; }
.h-logo { border: none; text-align: center; vertical-align: middle; }
.section-title { background: #c6c6c6; font-weight: bold; padding: 5px 7px; margin: 12px 0 4px 0; border: 1px solid #555; font-size: 10px; text-transform: uppercase; }
.center { text-align: center; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 12px; }
</style>
</head>
<body>

<?php if ( $LOGO ) : ?>
<table style="margin-bottom:4px;">
  <tr><td class="h-logo" style="border:none;"><img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:80px; width:auto;" /></td></tr>
</table>
<?php endif; ?>

<h1>Attendance Sheet</h1>
<h2>F-13s2 &nbsp;|&nbsp; QMS Surveillance Year 2 &nbsp;|&nbsp; Version 1.00</h2>

<div class="section-title">Audit Details</div>
<table>
    <tr><td class="lbl">Organization Name</td><td><?= $org ?></td></tr>
    <tr><td class="lbl">Ref No.</td><td><?= $ref_no ?></td></tr>
    <tr><td class="lbl">Date</td><td><?= $date ?></td></tr>
</table>

<div class="section-title">Attendance Record</div>
<table>
    
        <tr>
            <th style="width:6%;">S.No</th>
            <th style="width:28%;">Name</th>
            <th style="width:28%;">Designation &amp; Department</th>
            <th style="width:19%;">Opening Meeting</th>
            <th style="width:19%;">Closing Meeting</th>
        </tr>
    
    
    <?php if ( $rows ) :
        foreach ( $rows as $r ) : ?>
        <tr>
            <td class="center"><?= esc_html( $r['sno'] ?? '' ) ?></td>
            <td><?= esc_html( $r['name'] ?? '' ) ?></td>
            <td><?= esc_html( $r['designation_&_department'] ?? '' ) ?></td>
            <td class="center"><?= esc_html( $r['opening_meeting'] ?? '' ) ?></td>
            <td class="center"><?= esc_html( $r['closing_meeting'] ?? '' ) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="5" class="no-data">No attendance records.</td></tr>
    <?php endif; ?>
    
</table>

<table style="margin-top:20px;">
    <tr>
        <td style="width:50%; border:none; padding-top:30px;">Lead Auditor: ___________________________</td>
        <td style="width:50%; border:none; padding-top:30px; text-align:right;">Signature: ___________________________</td>
    </tr>
</table>

</body>
</html>
