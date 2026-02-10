<?php

/**
 * QMS – F-05a Audit Team Allocation Plan (Supplemental)
 * Template for displaying audit team allocation details
 * ACF Group: group_687a08899558e
 */

if (!defined('ABSPATH')) exit;

// Helper function to safely retrieve field values
function qms05a_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : '-';
}

// Fetch ACF fields
$org_name           = qms05a_field('test', $post_id); // Organization name (cloned field)
$gmcspl_ref_no      = qms05a_field('gmcspl_ref_no', $post_id);
$stage              = qms05a_field('stagef05a', $post_id);
$scope_cert         = qms05a_field('scope_of_certification', $post_id);

?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
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

        .message-box {
            background: #fffacd;
            border: 1px solid #f0e68c;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <h1>AUDIT TEAM ALLOCATION PLAN (SUPPLEMENTAL)</h1>
    <h2>F-05a (QMS Certification)</h2>

    <!-- Message Box -->
    <div class="message-box">
        <strong>Note:</strong> Please find attached the audit plan for the planned audit as per the Audit Programme. 
        If you have any conflict of interest with any of the audit team members or any modification required in the audit plan, 
        please inform within 2 working days or else audit team and plan shall be considered accepted. 
        Any matter may be appealed in accordance with GMCSPL procedure "Appeals & Complaints (P-06)".
    </div>

    <!-- General Information -->
    <div class="section-title">GENERAL INFORMATION</div>
    <table>
        <tr>
            <td class="label">Organization Name</td>
            <td><?= esc_html($org_name) ?></td>
        </tr>
        <tr>
            <td class="label">GMCSPL Reference No.</td>
            <td><?= esc_html($gmcspl_ref_no) ?></td>
        </tr>
        <tr>
            <td class="label">Audit Stage</td>
            <td><?= esc_html($stage) ?></td>
        </tr>
        <tr>
            <td class="label">Scope of Certification</td>
            <td><?= esc_html($scope_cert) ?></td>
        </tr>
    </table>

    <!-- Footer -->
    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666;">
        <p>This document is generated automatically. For queries, contact GMCSPL Certifications Team.</p>
    </div>
</body>
</html>
