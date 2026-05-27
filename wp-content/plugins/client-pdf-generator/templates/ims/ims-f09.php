<?php
/**
 * IMS – F-09 Stage-2 / Surveillance Audit Report
 * ACF Group: group_ims_f09
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
if ( ! function_exists('ims_f09v') ) {
    function ims_f09v( $key, $post_id, $fallback = '-' ) {
        $v = get_field( $key, $post_id );
        if ( empty($v) ) return $fallback;
        if ( is_array($v) ) {
            if ( isset($v['display_name']) ) return esc_html( $v['display_name'] );
            $flat = array_filter( array_map( fn($i) => is_string($i) ? $i : ( $i['label'] ?? $i['value'] ?? '' ), $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fallback;
        }
        return esc_html( $v );
    }
}

// ── Prefix Logic ─────────────────────────────────────────────────────────────
$prefix = $args['prefix'] ?? ''; // e.g. 'f09s1'

// ── Fields ────────────────────────────────────────────────────────────────────
$org        = ims_f09v($prefix . 'organization_name', $post_id);
$ref_no     = ims_f09v($prefix . 'Audit_Ref_No', $post_id);
$standard   = ims_f09v($prefix . 'standards', $post_id);
$objectives = get_field($prefix . 'audit_objectives', $post_id) ?: "Determination of conformity of IMS...";
$positive   = get_field($prefix . 'positive_features', $post_id) ?: '-';
$integ_lvl  = get_field('f09_level_of_integration', $post_id) ?: get_post_meta($post_id, 'level_of_integration', true) ?: '100';

$conclusion = get_field($prefix . 'conclusion_and_recommendation', $post_id) ?: 'Recommended for Grant of Certification';

$logo_b64 = 'data:image/jpeg;base64,...'; // Omitted
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 20mm 15mm; }
    body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #333; margin: 0; padding: 0; }
    
    .cover { text-align: center; margin-top: 50px; }
    .cover h1 { font-size: 24pt; margin-bottom: 20px; color: #2c3e50; }
    .cover h2 { font-size: 16pt; color: #7f8c8d; margin-bottom: 40px; }
    
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    th, td { border: 1px solid #b0bec5; padding: 8px 12px; vertical-align: top; }
    th { background: #f8f9fa; text-align: left; width: 30%; font-weight: bold; color: #2c3e50; }

    .section-title { background: #2c3e50; color: #fff; padding: 10px; font-weight: bold; margin-top: 30px; font-size: 12pt; border-radius: 4px; }
    .footer { position: fixed; bottom: 10px; width: 100%; text-align: center; font-size: 8pt; color: #7f8c8d; }
</style>
</head>
<body>

<div class="cover">
    <div style="margin-bottom: 30px;">
        <img src="data:image/jpeg;base64,...Logo..." alt="Logo" style="max-width:120px;">
    </div>
    <h1>AUDIT REPORT</h1>
    <h2>IMS (QMS, EMS & OH&SMS)</h2>
    
    <table style="width: 80%; margin: 0 auto;">
        <tr><th>Organization</th><td><?= $org ?></td></tr>
        <tr><th>Audit Ref No.</th><td><?= $ref_no ?></td></tr>
        <tr><th>Audit Standard</th><td><?= $standard ?></td></tr>
        <tr><th>Level of Integration</th><td><?= $integ_lvl ?>%</td></tr>
    </table>
</div>

<div class="section-title">1. Audit Objectives</div>
<div style="padding: 15px; background: #fdfdfd; border: 1px solid #b0bec5; border-top: none;">
    <?= nl2br(esc_html($objectives)) ?>
</div>

<div class="section-title">2. Positive Features</div>
<div style="padding: 15px; background: #fdfdfd; border: 1px solid #b0bec5; border-top: none;">
    <?= nl2br(esc_html($positive)) ?>
</div>

<div class="section-title">3. Conclusion & Recommendation</div>
<div style="padding: 15px; background: #fdfdfd; border: 1px solid #b0bec5; border-top: none;">
    <p>Based on the audit evidence collected, the audit team concludes that the management system is: </p>
    <div style="font-weight: bold; font-size: 11pt; color: #27ae60; border: 2px solid #27ae60; display: inline-block; padding: 10px; margin-top: 10px;">
        <?= $conclusion ?>
    </div>
</div>

<div class="footer">
    GMC Services Private Limited | F-09 Version 5.00
</div>

</body>
</html>
