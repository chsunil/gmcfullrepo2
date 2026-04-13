<?php
/**
 * IMS – F-17: Closing Meeting Minutes
 * ACF Group: group_ims_f17
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();
$prefix  = $args['prefix']  ?? ''; // For Surveillance S1/S2

// ── Helpers ───────────────────────────────────────────────────────────────────
$field_pfx = $prefix ? $prefix . '_' : '';

$org_raw = get_field( $field_pfx . 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$meeting = get_field( $field_pfx . 'meeting_details', $post_id );
$ncs_9   = get_field( $field_pfx . 'ncs_9001', $post_id ) ?: [];
$ncs_14  = get_field( $field_pfx . 'ncs_14001', $post_id ) ?: [];
$ncs_45  = get_field( $field_pfx . 'ncs_45001', $post_id ) ?: [];
$conclusion = get_field( $field_pfx . 'conclusion', $post_id );

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 15mm; }
    body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #333; margin: 0; padding: 0; }
    
    .header { text-align: center; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px; }
    .header h1 { font-size: 18pt; margin: 5px 0; color: #2c3e50; text-transform: uppercase; }
    .header p { margin: 2px 0; color: #7f8c8d; font-size: 10pt; }

    .meta-box { background: #f8f9fa; border: 1px solid #b0bec5; padding: 10px; margin-bottom: 20px; border-radius: 4px; }
    .meta-box b { color: #2c3e50; }

    h2 { font-size: 12pt; background: #eee; padding: 5px 10px; margin-top: 20px; color: #333; border-radius: 3px; }

    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #2c3e50; color: #ffffff; text-align: left; padding: 8px; font-weight: bold; border: 1px solid #2c3e50; font-size: 9pt; }
    td { border: 1px solid #b0bec5; padding: 8px; vertical-align: top; font-size: 9pt; }

    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #7f8c8d; padding-top: 10px; border-top: 1px solid #eee; }
</style>
</head>
<body>

<div class="header">
    <h1>Closing Meeting Minutes</h1>
    <p>Integrated Management System (ISO 9001, 14001, 45001)</p>
</div>

<div class="meta-box">
    <b>Organization:</b> <?= $org ?> | <b>Date:</b> <?= esc_html($meeting['date'] ?? '-') ?> | <b>Venue:</b> <?= esc_html($meeting['venue'] ?? '-') ?>
</div>

<!-- ISO 9001 -->
<h2>ISO 9001:2015 - Findings</h2>
<?php if ( empty($ncs_9) ): ?>
    <p style="padding-left: 10px; color: #7f8c8d;">No non-conformities identified.</p>
<?php else: ?>
    <table>
        <thead>
            <tr><th width="15%">NC No</th><th width="15%">Clause</th><th width="70%">Description</th></tr>
        </thead>
        <tbody>
            <?php foreach( $ncs_9 as $nc ): ?>
                <tr><td><?= esc_html($nc['no']) ?></td><td><?= esc_html($nc['clause']) ?></td><td><?= nl2br(esc_html($nc['desc'])) ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- ISO 14001 -->
<h2>ISO 14001:2015 - Findings</h2>
<?php if ( empty($ncs_14) ): ?>
    <p style="padding-left: 10px; color: #7f8c8d;">No non-conformities identified.</p>
<?php else: ?>
    <table>
        <thead>
            <tr><th width="15%">NC No</th><th width="15%">Clause</th><th width="70%">Description</th></tr>
        </thead>
        <tbody>
            <?php foreach( $ncs_14 as $nc ): ?>
                <tr><td><?= esc_html($nc['no']) ?></td><td><?= esc_html($nc['clause']) ?></td><td><?= nl2br(esc_html($nc['desc'])) ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- ISO 45001 -->
<h2>ISO 45001:2018 - Findings</h2>
<?php if ( empty($ncs_45) ): ?>
    <p style="padding-left: 10px; color: #7f8c8d;">No non-conformities identified.</p>
<?php else: ?>
    <table>
        <thead>
            <tr><th width="15%">NC No</th><th width="15%">Clause</th><th width="70%">Description</th></tr>
        </thead>
        <tbody>
            <?php foreach( $ncs_45 as $nc ): ?>
                <tr><td><?= esc_html($nc['no']) ?></td><td><?= esc_html($nc['clause']) ?></td><td><?= nl2br(esc_html($nc['desc'])) ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2>Conclusion</h2>
<div style="border: 1px dashed #b0bec5; padding: 15px; background: #fff;">
    <?= nl2br(esc_html($conclusion ?: 'The audit was conducted as per the integrated audit plan. The management system was found to be effective.')) ?>
</div>

<div class="footer">
    GMC Services Private Limited | F-17 Version 4.0 | Generated on <?= date('d/m/Y') ?>
</div>

</body>
</html>
