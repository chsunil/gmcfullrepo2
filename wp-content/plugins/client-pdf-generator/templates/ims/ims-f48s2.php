<?php
/**
 * IMS – F-48: Checklist for Completion of Reports
 * ACF Group: group_ims_f48
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$matrix = get_field( 'ims_completion_matrix', $post_id );
$final_verify = get_field( 'final_verify', $post_id );

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

    .org-box { background: #f8f9fa; border: 1px solid #b0bec5; padding: 10px; margin-bottom: 20px; border-radius: 4px; }
    .org-box b { color: #2c3e50; }

    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #2c3e50; color: #ffffff; text-align: left; padding: 10px; font-weight: bold; border: 1px solid #2c3e50; font-size: 9pt; }
    td { border: 1px solid #b0bec5; padding: 8px; vertical-align: top; font-size: 9pt; }

    .check-cell { text-align: center; font-size: 12pt; font-weight: bold; color: #2c3e50; }

    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #7f8c8d; padding-top: 10px; border-top: 1px solid #eee; }
</style>
</head>
<body>

<div class="header">
    <h1>Checklist for Completion of Reports</h1>
    <p>Integrated Management System (ISO 9001, 14001, 45001)</p>
</div>

<div class="org-box">
    <b>Organization:</b> <?= $org ?>
</div>

<table>
    <thead>
        <tr>
            <th width="40%">Document / Record</th>
            <th width="20%" style="text-align:center;">Initial</th>
            <th width="20%" style="text-align:center;">Surv-1</th>
            <th width="20%" style="text-align:center;">Surv-2</th>
        </tr>
    </thead>
    <tbody>
        <?php if ( !empty($matrix['rows']) ): ?>
            <?php foreach ( $matrix['rows'] as $row_idx => $label ): ?>
                <tr>
                    <td><b><?= esc_html($label) ?></b></td>
                    <td class="check-cell"><?= esc_html($matrix['data'][$row_idx][0] ?? '') ?></td>
                    <td class="check-cell"><?= esc_html($matrix['data'][$row_idx][1] ?? '') ?></td>
                    <td class="check-cell"><?= esc_html($matrix['data'][$row_idx][2] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div style="margin-top: 30px; padding: 15px; border: 1px solid #b0bec5; background: #e8f5e9; border-radius: 4px;">
    <b>Final Verification:</b> <?= $final_verify ? '✅ Verified - All documents complete.' : '❌ Pending Verification' ?>
</div>

<div class="footer">
    GMC Services Private Limited | F-48 Version 4.0 | Generated on <?= date('d/m/Y') ?>
</div>

</body>
</html>
