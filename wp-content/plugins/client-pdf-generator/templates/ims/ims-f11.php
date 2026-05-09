<?php
/**
 * IMS – F-11: Stage-1 Audit Report (Modernized)
 * ACF Group: group_ims_f11
 * Integrated version for ISO 9001, 14001, 45001
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$general = get_field( 'general_info', $post_id );
$audit_date = $general['audit_date'] ?? '';

$readiness = get_field( 'ims_readiness', $post_id ) ?: [];
$spec_readiness = get_field( 'standard_readiness', $post_id ) ?: [];
$recommendation = get_field( 'f11_recommendation', $post_id );

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 15mm; }
    body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #333; margin: 0; padding: 0; }
    
    .header { text-align: center; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px; }
    .header h1 { font-size: 18pt; margin: 5px 0; color: #2c3e50; font-weight: bold; }
    .header p { margin: 2px 0; color: #7f8c8d; font-size: 10pt; text-transform: uppercase; letter-spacing: 1px; }

    .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: #fdfdfd; }
    .meta-table td { padding: 8px 12px; border: 1px solid #dee2e6; }
    .meta-table .label { background: #f8f9fa; font-weight: bold; width: 30%; color: #495057; }

    h2 { font-size: 13pt; border-left: 5px solid #3498db; padding-left: 12px; margin-top: 25px; color: #2c3e50; background: #f0f4f8; padding-top: 5px; padding-bottom: 5px; }

    table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    table.data-table th { background: #2c3e50; color: #ffffff; text-align: left; padding: 10px; font-weight: bold; border: 1px solid #2c3e50; font-size: 9pt; }
    table.data-table td { border: 1px solid #dee2e6; padding: 12px 10px; vertical-align: top; font-size: 9.5pt; }

    .recommendation-box { margin-top: 30px; padding: 20px; border: 2px solid #2c3e50; background: #eff6ff; text-align: center; border-radius: 4px; }
    .recommendation-box .title { font-size: 10pt; color: #1e40af; margin-bottom: 10px; display: block; text-transform: uppercase; }
    .recommendation-box .decision { font-size: 14pt; color: #1e3a8a; font-weight: bold; }

    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8.5pt; color: #94a3b8; padding-top: 10px; border-top: 1px solid #e2e8f0; }
</style>
</head>
<body>

<div class="header">
    <h1>STAGE-1 AUDIT REPORT</h1>
    <p>Integrated Management System (9001, 14001, 45001)</p>
</div>

<table class="meta-table">
    <tr>
        <td class="label">Organization</td>
        <td><strong><?= $org ?></strong></td>
    </tr>
    <tr>
        <td class="label">Audit Date</td>
        <td><?= esc_html($audit_date) ?></td>
    </tr>
</table>

<h2>1. System Integration Verification</h2>
<table class="data-table">
    <thead>
        <tr>
            <th width="40%">Integrated Element</th>
            <th width="60%">Verification Status / Readiness</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Policy, Strategy & Objectives</td>
            <td><?= ucfirst(str_replace('_', ' ', $readiness['eval_policy'] ?? '-')) ?></td>
        </tr>
        <tr>
            <td>Internal Audit System (Integrated)</td>
            <td><?= ucfirst(str_replace('_', ' ', $readiness['eval_ia'] ?? '-')) ?></td>
        </tr>
        <tr>
            <td>Management Review Process (Integrated)</td>
            <td><?= ucfirst(str_replace('_', ' ', $readiness['eval_mrm'] ?? '-')) ?></td>
        </tr>
    </tbody>
</table>

<h2>2. Environmental Aspects & OH&S Hazards</h2>
<table class="data-table">
    <thead>
        <tr>
            <th width="40%">Assessment Area</th>
            <th width="60%">Technical Review Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Aspects & Impacts (ISO 14001)</strong></td>
            <td><?= !empty($spec_readiness['ems_aspects_eval']) ? nl2br(esc_html($spec_readiness['ems_aspects_eval'])) : '<span style="color:#94a3b8 italic">Review details not recorded.</span>' ?></td>
        </tr>
        <tr>
            <td><strong>Hazard ID & Risk Assessment (ISO 45001)</strong></td>
            <td><?= !empty($spec_readiness['ohs_hira_eval']) ? nl2br(esc_html($spec_readiness['ohs_hira_eval'])) : '<span style="color:#94a3b8 italic">Review details not recorded.</span>' ?></td>
        </tr>
    </tbody>
</table>

<div class="recommendation-box">
    <span class="title">Audit Conclusion & Recommendation</span>
    <span class="decision"><?= !empty($recommendation) ? strtoupper(str_replace('_', ' ', $recommendation)) : 'PENDING FINAL DECISION' ?></span>
</div>

<div style="margin-top: 50px;">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="border: none; width: 50%;">
                <div style="border-top: 1px solid #334155; padding-top: 8px; width: 220px;">
                    <strong>Lead Auditor Signature</strong>
                </div>
            </td>
            <td style="border: none; text-align: right;">
                <div style="border-top: 1px solid #334155; padding-top: 8px; width: 220px; margin-left: auto;">
                    <strong>Client Representative</strong>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    GMC Services Private Limited | F-11 Version 4.0 | Integrated Track | Generated: <?= date('d/m/Y') ?>
</div>

</body>
</html>
