<?php
/**
 * IMS – F-10: Non-Conformity Report
 * ACF Group: group_ims_f10
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$rows = get_field( 'ims_nc_rows', $post_id ) ?: [];

$logo_b64 = 'data:image/jpeg;base64,...'; // Omitted
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
    td { border: 1px solid #b0bec5; padding: 10px; vertical-align: top; font-size: 9pt; }

    .nc-block { margin-bottom: 30px; page-break-inside: avoid; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #7f8c8d; padding-top: 10px; border-top: 1px solid #eee; }
</style>
</head>
<body>

<div class="header">
    <h1>Non-Conformity Report (NCR)</h1>
    <p>Integrated Management System (ISO 9001, 14001, 45001)</p>
</div>

<div class="org-box">
    <b>Organization:</b> <?= $org ?>
</div>

<?php if ( empty($rows) ): ?>
    <div style="padding: 20px; text-align: center; border: 1px dashed #b0bec5; color: #7f8c8d;">
        No non-conformities were identified during this audit.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th width="10%">No.</th>
                <th width="15%">Standard/Clause</th>
                <th width="35%">Details of Finding</th>
                <th width="40%">Correction / Root Cause</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $rows as $row ): ?>
                <tr>
                    <td><b><?= esc_html($row['nc_no'] ?: '-') ?></b><br><small><?= esc_html($row['date'] ?: '') ?></small></td>
                    <td><?= esc_html($row['clause_no'] ?: '-') ?></td>
                    <td><?= nl2br(esc_html($row['finding_details'] ?: '-')) ?></td>
                    <td><?= nl2br(esc_html($row['correction'] ?: '-')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div style="margin-top: 40px; font-size: 9pt;">
    <table style="border: none;">
        <tr>
            <td style="border: none; width: 50%;">
                <div style="border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; width: 200px;">
                    <b>Auditor Signature</b>
                </div>
            </td>
            <td style="border: none; text-align: right;">
                <div style="border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; width: 200px; margin-left: auto;">
                    <b>Client Acknowledgment</b>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    GMC Services Private Limited | F-10 Version 4.00 | Generated on <?= date('d/m/Y') ?>
</div>

</body>
</html>
