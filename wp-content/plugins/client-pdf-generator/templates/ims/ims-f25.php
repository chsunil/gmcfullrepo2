<?php
/**
 * IMS – F-25 Assessment Check List
 * ACF Group: group_ims_f25
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$ref_no    = get_field('refno', $post_id) ?: get_field('proposal_ref_no', $post_id) ?: '-';
$standard  = get_field('cert_scheme', $post_id) ?: '-';

$matrix = get_field( 'assessment_check_list', $post_id ) ?: [];

$logo_b64 = 'data:image/jpeg;base64,...'; // Omitted
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { size: A4 landscape; margin: 10mm 10mm; }
    body { font-family: Arial, sans-serif; font-size: 8.5px; color: #333; line-height: 1.2; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    th, td { border: 1px solid #555; padding: 4px; vertical-align: middle; text-align: left; }
    th { background: #eaeff2; font-weight: bold; text-align: center; font-size: 8.5px; }
    
    .lbl { background: #f8f9fa; font-weight: bold; width: 22%; }
    .center { text-align: center; }
</style>
</head>
<body>

<table style="border:none;">
    <tr>
        <td style="border:none; width:15%;"><img src="data:image/jpeg;base64,...Logo..." alt="Logo" style="max-height:45px;"></td>
        <td style="border:none; text-align:center;">
            <h1 style="margin:0; font-size:14pt;">Assessment Check List</h1>
            <div style="font-size:9pt;">IMS (9001, 14001 & 45001)</div>
        </td>
        <td style="border:none; width:15%; text-align:right; font-size:7.5px;">F-25<br>Ver 5.00</td>
    </tr>
</table>

<table>
    <tr><td class="lbl">Organization</td><td colspan="3" style="font-weight:bold;"><?= $org ?></td><td class="lbl">Ref. No.</td><td><?= $ref_no ?></td></tr>
    <tr><td class="lbl">Standard</td><td><?= $standard ?></td><td class="lbl">&nbsp;</td><td>&nbsp;</td><td class="lbl">&nbsp;</td><td>&nbsp;</td></tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width:25%;">IMS Requirement / Clause</th>
            <th style="width:25%;">Evidence / Records (Initial)</th>
            <th style="width:25%;">Surv-1</th>
            <th style="width:25%;">Surv-2</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($matrix)) : ?>
            <?php foreach ($matrix as $row => $cols) : ?>
            <tr>
                <td class="lbl"><?= esc_html($row) ?></td>
                <td><?= nl2br(esc_html($cols['initial'] ?? '')) ?></td>
                <td><?= nl2br(esc_html($cols['s1'] ?? '')) ?></td>
                <td><?= nl2br(esc_html($cols['s2'] ?? '')) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="4" class="center">No checklist data entered.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
