<?php
/**
 * IMS – F-24: Customer Feedback Form
 * ACF Group: group_ims_f24
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();
$prefix  = $args['prefix']  ?? '';

$field_pfx = $prefix ? $prefix . '_' : '';

$org_raw = get_field( $field_pfx . 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$matrix  = get_field( $field_pfx . 'feedback_matrix', $post_id );
$overall = get_field( $field_pfx . 'overall_satisfaction', $post_id );

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
    
    .meta-box { background: #f8f9fa; border: 1px solid #b0bec5; padding: 10px; margin-bottom: 20px; border-radius: 4px; }

    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #2c3e50; color: #ffffff; text-align: left; padding: 10px; font-weight: bold; border: 1px solid #2c3e50; font-size: 9pt; }
    td { border: 1px solid #b0bec5; padding: 10px; vertical-align: middle; font-size: 9pt; }

    .rating-cell { text-align: center; font-weight: bold; font-size: 11pt; color: #2c3e50; }

    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #7f8c8d; padding-top: 10px; border-top: 1px solid #eee; }
</style>
</head>
<body>

<div class="header">
    <h1>Customer Feedback Form</h1>
    <p>Integrated Management System (ISO 9001, 14001, 45001)</p>
</div>

<div class="meta-box">
    <b>Organization:</b> <?= $org ?>
</div>

<p>We value your feedback. Please rate our audit services on a scale of 1 to 5 (5 being Excellent).</p>

<table>
    <thead>
        <tr>
            <th width="40%">Performance Indicator</th>
            <th width="20%" style="text-align:center;">Rating (1-5)</th>
            <th width="40%">Comments</th>
        </tr>
    </thead>
    <tbody>
        <?php if ( !empty($matrix['rows']) ): ?>
            <?php foreach ( $matrix['rows'] as $idx => $label ): ?>
                <tr>
                    <td><?= esc_html($label) ?></td>
                    <td class="rating-cell"><?= esc_html($matrix['data'][$idx][0] ?? '-') ?></td>
                    <td><?= esc_html($matrix['data'][$idx][1] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div style="margin-top: 20px;">
    <b>Overall Satisfaction / Additional Comments:</b>
    <div style="border: 1px solid #b0bec5; padding: 15px; margin-top: 5px; min-height: 80px;">
        <?= nl2br(esc_html($overall ?: '-')) ?>
    </div>
</div>

<div style="margin-top: 40px; text-align: right; padding-right: 20px;">
    <div style="border-top: 1px solid #333; width: 220px; float: right; padding-top: 5px; text-align: center;">
        <b>Client Signature & Date</b>
    </div>
</div>

<div class="footer">
    GMC Services Private Limited | F-24 Version 4.0 | Generated on <?= date('d/m/Y') ?>
</div>

</body>
</html>
