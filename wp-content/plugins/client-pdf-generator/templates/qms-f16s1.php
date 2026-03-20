<?php
/**
 * QMS – F-16s1 Audit Programme (Surveillance Year 1)
 * ACF Group: group_6974d719ef57b
 * Fields (seamless group):
 *   Organization  — clone of field_org_name
 *   ref_no        — clone of field_68554bdf55898
 *   (unnamed)     — clone of field_standard_applied
 *   technical_area — clone of field_qms_f02_technical_area
 *   AUDIT_PROGRAMMEF16 — matrix_flexible
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

if ( ! function_exists('f16s1_val') ) {
    function f16s1_val( $v, $fb = '-' ) {
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

$org_raw       = get_field( 'Organization', $post_id );
$org           = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
               : ( is_array($org_raw) ? f16s1_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );
$ref_no        = f16s1_val( get_field( 'ref_no', $post_id ) );
$standard      = f16s1_val( get_field( 'standard_applied', $post_id ) );
$tech_area     = f16s1_val( get_field( 'technical_area', $post_id ) );
$matrix        = get_field( 'AUDIT_PROGRAMMEF16', $post_id );
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 landscape; margin: 12mm; }
body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 8px; }
h1 { text-align: center; font-size: 14px; font-weight: bold; margin: 0 0 2px 0; text-transform: uppercase; }
h2 { text-align: center; font-size: 10px; margin: 0 0 10px 0; color: #444; }
table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
th, td { border: 1px solid #555; padding: 4px 5px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 9px; text-transform: uppercase; }
.lbl { background: #f2f2f2; font-weight: bold; width: 25%; }
.h-logo { border: none; text-align: center; }
.section-title { background: #c6c6c6; font-weight: bold; padding: 4px 6px; margin: 10px 0 3px 0; border: 1px solid #555; font-size: 10px; text-transform: uppercase; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 12px; }
</style>
</head>
<body>

<?php if ( $LOGO ) : ?>
<table style="margin-bottom:4px;"><tr><td class="h-logo" style="border:none;"><img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:70px; width:auto;" /></td></tr></table>
<?php endif; ?>

<h1>Audit Programme</h1>
<h2>F-16s1 &nbsp;|&nbsp; QMS Surveillance Year 1 &nbsp;|&nbsp; Version 1.00</h2>

<div class="section-title">Details</div>
<table>
    <tr><td class="lbl">Organization</td><td><?= $org ?></td><td class="lbl">Ref No.</td><td><?= $ref_no ?></td></tr>
    <tr><td class="lbl">Standard</td><td><?= $standard ?></td><td class="lbl">Technical Area</td><td><?= $tech_area ?></td></tr>
</table>

<div class="section-title">Audit Programme</div>
<?php if ( is_array($matrix) && ! empty($matrix) ) :
    $first = reset($matrix); $cols = is_array($first) ? array_keys($first) : [];
?>
<table>
    <?php if ( $cols ) : ?><thead><tr><?php foreach ($cols as $c) : ?><th><?= esc_html($c) ?></th><?php endforeach; ?></tr></thead><?php endif; ?>
    <tbody><?php foreach ( $matrix as $mrow ) : ?><tr><?php foreach ( (array) $mrow as $cell ) : ?><td><?= esc_html( (string) $cell ) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
</table>
<?php else : ?>
<p class="no-data">Audit programme matrix data not available.</p>
<?php endif; ?>

<table style="margin-top:20px;">
    <tr>
        <td style="border:none; padding-top:25px; width:50%;">Lead Auditor: ___________________________</td>
        <td style="border:none; padding-top:25px; width:50%; text-align:right;">Date: ___________________________</td>
    </tr>
</table>

</body>
</html>
