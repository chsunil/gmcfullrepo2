<?php

/**
 * QMS – F-08a Audit Schedule (Sheet 2)
 * Template for displaying audit schedule details - stage 2
 * ACF Group: group_6884e410c0ca6
 */

if (!defined('ABSPATH')) exit;

function qms08a_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : '-';
}

// Fetch ACF fields
$org_name           = qms08a_field('organization_name', $post_id);
$ref_no             = qms08a_field('Ref_No:', $post_id);
$location           = qms08a_field('location', $post_id);
$audit_date         = qms08a_field('issue_date', $post_id);

?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }

        h1, h2 {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }

        h2 {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .section-title {
            background: #f2f2f2;
            font-weight: bold;
            padding: 8px;
            margin-top: 15px;
            border: 1px solid #999;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        .label {
            width: 35%;
            font-weight: bold;
            background: #f9f9f9;
        }

        .info-note {
            background: #e8f4f8;
            border-left: 4px solid #4a90e2;
            padding: 10px;
            margin: 10px 0;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <h1>AUDIT SCHEDULE - STAGE 2</h1>
    <h2>F-08a (QMS Certification)</h2>

    <!-- Information Note -->
    <div class="info-note">
        <strong>Stage 2 Audit Schedule</strong><br/>
        This document outlines the scheduled dates, locations, and audit team assignments for the Stage 2 certification audit.
    </div>

    <!-- Audit Details -->
    <div class="section-title">AUDIT SCHEDULE INFORMATION</div>
    <table>
        <tr>
            <td class="label">Organization Name</td>
            <td><?= esc_html($org_name) ?></td>
        </tr>
        <tr>
            <td class="label">Reference No.</td>
            <td><?= esc_html($ref_no) ?></td>
        </tr>
        <tr>
            <td class="label">Audit Location</td>
            <td><?= esc_html($location) ?></td>
        </tr>
        <tr>
            <td class="label">Scheduled Audit Date</td>
            <td><?= esc_html($audit_date) ?></td>
        </tr>
    </table>

    <!-- Audit Team -->
    <div class="section-title">AUDIT TEAM ASSIGNMENT</div>
    <table>
        <tr>
            <th>Team Member Name</th>
            <th>Role</th>
            <th>Assigned Areas</th>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; color: #999;">Audit team details will be populated from client data</td>
        </tr>
    </table>

    <!-- Audit Scope -->
    <div class="section-title">AUDIT SCOPE</div>
    <table>
        <tr>
            <td style="padding: 12px;">
                <strong>Scope Coverage:</strong><br/>
                Stage 2 audits will include comprehensive review of QMS implementation, controls, and evidence of effectiveness 
                across all defined scope areas. Auditors will assess compliance with applicable ISO 9001 requirements.
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666; page-break-inside: avoid;">
        <hr style="border: none; border-top: 1px solid #ccc; margin: 10px 0;">
        <p>This document is auto-generated. Actual audit schedule is subject to confirmation by GMCSPL.</p>
    </div>
</body>
</html>
