<?php
/**
 * QMS – F-17s1 On Going Surveillance Plan (Year 1)
 * ACF Group: group_69758381afa73
 * Fields:
 *   organization   — clone of field_org_name
 *   ref_no         — clone of field_68554bdf55898
 *   standard       — clone of field_standard_applied
 *   location       — clone of field_68173ed29add4
 *   planned_monthyear (group): stage_i, stage_2, 1st_Surveillance, 2nd_Surveillance, Re-Certification
 *   Executed_monthyear_copy (group): same sub-fields
 *   area_process   — matrix_flexible
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

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

$org_raw  = get_field( 'organization', $post_id );
$org      = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
          : ( is_array($org_raw) ? f17s1_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );
$ref_no   = f17s1_val( get_field( 'ref_no', $post_id ) );
$standard = f17s1_val( get_field( 'standard', $post_id ) );
$location = f17s1_val( get_field( 'location', $post_id ) );

$planned  = get_field( 'planned_monthyear', $post_id );
$executed = get_field( 'Executed_monthyear_copy', $post_id );
$matrix   = get_field( 'area_process', $post_id );

$plan_labels = ['Stage I' => 'stage_i', 'Stage 2' => 'stage_2', '1st Surveillance' => '1st_Surveillance', '2nd Surveillance' => '2nd_Surveillance', 'Re-Certification' => 'Re-Certification'];
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 10px; }
h1 { text-align: center; font-size: 14px; font-weight: bold; margin: 0 0 2px 0; text-transform: uppercase; }
h2 { text-align: center; font-size: 10px; margin: 0 0 12px 0; color: #444; }
table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
th, td { border: 1px solid #555; padding: 4px 6px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 9px; text-transform: uppercase; }
.lbl { background: #f2f2f2; font-weight: bold; width: 25%; }
.h-logo { border: none; text-align: center; }
.section-title { background: #c6c6c6; font-weight: bold; padding: 4px 7px; margin: 10px 0 3px 0; border: 1px solid #555; font-size: 10px; text-transform: uppercase; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 10px; }
</style>
</head>
<body>

<?php if ( $LOGO ) : ?>
<table style="margin-bottom:4px;"><tr><td class="h-logo" style="border:none;"><img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:70px; width:auto;" /></td></tr></table>
<?php endif; ?>

<h1>On Going Surveillance Plan</h1>
<h2>F-17s1 &nbsp;|&nbsp; QMS Surveillance Year 1 &nbsp;|&nbsp; Version 1.00</h2>

<div class="section-title">Organisation Details</div>
<table>
    <tr><td class="lbl">Organization</td><td><?= $org ?></td><td class="lbl">Ref. No.</td><td><?= $ref_no ?></td></tr>
    <tr><td class="lbl">Standard</td><td><?= $standard ?></td><td class="lbl">Location</td><td><?= $location ?></td></tr>
</table>

<div class="section-title">Planned vs Executed (Month/Year)</div>
<table>
    <thead>
        <tr>
            <th style="width:30%;">Stage</th>
            <th style="width:35%;">Planned (Month/Year)</th>
            <th style="width:35%;">Executed (Month/Year)</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ( $plan_labels as $label => $key ) : ?>
        <tr>
            <td><?= esc_html($label) ?></td>
            <td><?= is_array($planned) ? f17s1_val( $planned[$key] ?? '' ) : '-' ?></td>
            <td><?= is_array($executed) ? f17s1_val( $executed[$key] ?? '' ) : '-' ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="section-title">Area / Process (Surveillance Programme)</div>
<?php if ( is_array($matrix) && ! empty($matrix) ) :
    $first = reset($matrix); $cols = is_array($first) ? array_keys($first) : [];
?>
<table>
    <?php if ( $cols ) : ?><thead><tr><?php foreach ($cols as $c) : ?><th><?= esc_html($c) ?></th><?php endforeach; ?></tr></thead><?php endif; ?>
    <tbody><?php foreach ( $matrix as $mrow ) : ?><tr><?php foreach ( (array) $mrow as $cell ) : ?><td><?= esc_html( (string) $cell ) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
</table>
<?php else : ?>
<p class="no-data">Surveillance plan matrix data not available.</p>
<?php endif; ?>

<table style="margin-top:20px;">
    <tr>
        <td style="border:none; padding-top:25px; width:50%;">Lead Auditor: ___________________________</td>
        <td style="border:none; padding-top:25px; width:50%; text-align:right;">Date: ___________________________</td>
    </tr>
</table>

</body>
</html>
