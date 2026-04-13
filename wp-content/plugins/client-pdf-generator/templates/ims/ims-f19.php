<?php
/**
 * IMS – F-19 Checklist for Certification Decision
 * ACF Group: group_ims_f19
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Prefix Logic ─────────────────────────────────────────────────────────────
$prefix = $args['prefix'] ?? ''; // e.g. 'f19s1'

// ── Helpers ───────────────────────────────────────────────────────────────────
$org_raw = get_field( $prefix . 'organization_name', $post_id ) ?: get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$ref_no    = get_field($prefix . 'refno', $post_id) ?: get_field($prefix . 'proposal_ref_no', $post_id) ?: get_field('proposal_ref_no', $post_id) ?: '-';
$standard  = get_field($prefix . 'standards', $post_id) ?: get_field('cert_scheme', $post_id) ?: '-';
$address   = get_field($prefix . 'head_office', $post_id) ?: get_field('head_office', $post_id) ?: '-';

$matrix    = get_field( $prefix . 'f19_decision_table', $post_id ) ?: [];
$decision  = get_field( $prefix . 'final_certification_decision', $post_id ) ?: '-';

$logo_b64 = 'data:image/jpeg;base64,...'; // Omitted
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { size: A4 portrait; margin: 12mm 10mm; }
    body { font-family: Arial, sans-serif; font-size: 8.5px; color: #333; line-height: 1.4; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th, td { border: 1px solid #444; padding: 6px; vertical-align: middle; text-align: left; }
    th { background: #eaeff2; font-weight: bold; text-align: center; font-size: 9px; }
    
    .lbl { background: #f8f9fa; font-weight: bold; width: 25%; }
    .sec-hdr { background: #2c3e50; color: #fff; font-weight: bold; font-size: 10px; padding: 5px; margin-top: 15px; }

    .center { text-align: center; }
    .decision-val { font-weight: bold; font-size: 11pt; color: #d32f2f; }
</style>
</head>
<body>

<div style="border-bottom: 2px solid #2c3e50; padding-bottom: 5px; margin-bottom: 15px;">
    <table style="border:none; margin-bottom:0;">
        <tr>
            <td style="border:none; width:15%;"><img src="data:image/jpeg;base64,...Logo..." alt="Logo" style="max-height:50px;"></td>
            <td style="border:none; text-align:center;">
                <h1 style="margin:0; font-size:16pt;">Checklist for Certification Decision</h1>
                <div style="font-size:10px;">Integrated Management System (9001, 14001 & 45001)</div>
            </td>
            <td style="border:none; width:15%; text-align:right; font-size:8px;">F-19<br>Ver 5.00</td>
        </tr>
    </table>
</div>

<table>
    <tr><td class="lbl">Organization</td><td colspan="3" style="font-weight:bold;"><?= $org ?></td></tr>
    <tr><td class="lbl">Ref No.</td><td><?= $ref_no ?></td><td class="lbl">Standard</td><td><?= $standard ?></td></tr>
    <tr><td class="lbl">Address</td><td colspan="3"><?= $address ?></td></tr>
</table>

<div class="sec-hdr">1. Verification Summary</div>
<table>
    <thead>
        <tr>
            <th style="width:50%;">Review Item</th>
            <th style="width:15%;">Verified</th>
            <th style="width:35%;">Remarks</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($matrix)) : ?>
            <?php foreach ($matrix as $row => $cols) : ?>
            <tr>
                <td><?= esc_html($row) ?></td>
                <td class="center"><?= esc_html($cols['verified'] ?? '-') ?></td>
                <td><?= esc_html($cols['remarks'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="3" class="center">No verification data available.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="sec-hdr">2. Final Certification Decision</div>
<div style="border: 1px solid #444; padding: 15px; background: #fff;">
    <p>Decision on Grant of Certification: </p>
    <div class="decision-val"><?= $decision ?></div>
</div>

<div style="margin-top: 30px;">
    <table style="border:none;">
        <tr>
            <td style="border:none; width:50%;">
                __________________________<br>
                <strong>Certification Manager</strong>
            </td>
            <td style="border:none; text-align:right;">
                Date: ____________________
            </td>
        </tr>
    </table>
</div>

</body>
</html>
