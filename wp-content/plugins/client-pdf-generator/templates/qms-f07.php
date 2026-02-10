<?php

/**
 * QMS – F-07 Certification Assessment Plan
 * Template for displaying certification assessment plan details
 * ACF Group: group_qms_f07
 */

if (!defined('ABSPATH')) exit;

function qms07_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : '-';
}

// Fetch ACF fields
$org_name           = qms07_field('organization_name', $post_id);
$ref_no             = qms07_field('refno', $post_id);
$quality_req        = qms07_field('quality_system_requirements', $post_id);

?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
        }

        h1, h2 {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        h2 {
            font-size: 12px;
            margin-bottom: 15px;
        }

        .section-title {
            background: #e8e8e8;
            font-weight: bold;
            padding: 6px;
            margin-top: 12px;
            border: 1px solid #999;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #999;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .label {
            width: 30%;
            font-weight: bold;
            background: #fafafa;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <h1>CERTIFICATION ASSESSMENT PLAN</h1>
    <h2>F-07 (QMS Certification)</h2>

    <!-- Organization Info -->
    <div class="section-title">ORGANIZATION INFORMATION</div>
    <table>
        <tr>
            <td class="label">Organization Name</td>
            <td><?= esc_html($org_name) ?></td>
        </tr>
        <tr>
            <td class="label">Reference No.</td>
            <td><?= esc_html($ref_no) ?></td>
        </tr>
    </table>

    <!-- Quality System Requirements Matrix -->
    <div class="section-title">QUALITY SYSTEM REQUIREMENTS ASSESSMENT</div>
    <table>
        <tr>
            <th>Requirement</th>
            <th>Assessment Scope</th>
            <th>Notes</th>
        </tr>
        <tr>
            <td colspan="3" style="background: #f0f0f0; padding: 8px;">
                <strong>Assessment Matrix:</strong><br/>
                <small>P = Primary area/process for assessment | R = Significantly relevant area | Blank = Not relevant</small>
            </td>
        </tr>
        <?php if (!empty($quality_req) && is_array($quality_req)) : ?>
            <?php foreach ($quality_req as $index => $row) : ?>
                <tr>
                    <td><?= isset($row[0]) ? esc_html($row[0]) : '-' ?></td>
                    <td><?= isset($row[1]) ? esc_html($row[1]) : '-' ?></td>
                    <td><?= isset($row[2]) ? esc_html($row[2]) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="3">No assessment data available</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; font-size: 9px; color: #666; page-break-inside: avoid;">
        <hr style="border: none; border-top: 1px solid #ccc; margin: 10px 0;">
        <p>QMS Certification Assessment Plan - Generated automatically by GMCSPL</p>
    </div>
</body>
</html>
