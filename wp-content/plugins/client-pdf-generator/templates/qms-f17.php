<?php

/**
 * QMS – F-17 On-going Surveillance Plan
 * Template for displaying surveillance audit schedule and methodology
 * ACF Group: group_6885b9609179d
 */

if (!defined('ABSPATH')) exit;

function qms17_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : '-';
}

// Fetch ACF fields
$org_name           = qms17_field('organization_name', $post_id);
$ref_no             = qms17_field('ref_no', $post_id);
$certificate_date   = qms17_field('certificate_date', $post_id);
$surveillance_freq  = qms17_field('surveillance_frequency', $post_id);
$surveillance_plan  = qms17_field('surveillance_plan', $post_id);

?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        h1, h2, h3 {
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 18px;
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }

        h2 {
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
        }

        h3 {
            font-size: 12px;
            margin: 15px 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #4a90e2;
        }

        .section-title {
            background: #e8e8e8;
            font-weight: bold;
            padding: 8px;
            margin-top: 15px;
            border: 1px solid #999;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #999;
            padding: 7px;
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
            background: #f9f9f9;
        }

        .info-box {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 10px;
            margin: 10px 0;
            font-size: 10px;
        }

        .highlight {
            background: #fff3cd;
            padding: 8px;
            margin: 8px 0;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <h1>ON-GOING SURVEILLANCE PLAN</h1>
    <h2>F-17 (QMS Certification)</h2>

    <!-- Certificate Information -->
    <div class="section-title">CERTIFICATE INFORMATION</div>
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
            <td class="label">Certificate Issue Date</td>
            <td><?= esc_html($certificate_date) ?></td>
        </tr>
        <tr>
            <td class="label">Surveillance Frequency</td>
            <td><?= esc_html($surveillance_freq) ?></td>
        </tr>
    </table>

    <!-- Surveillance Overview -->
    <h3>Surveillance Audit Overview</h3>
    <div class="info-box">
        <p>
            This surveillance plan outlines the schedule and methodology for on-going surveillance audits 
            to ensure continued compliance with ISO 9001:2015 requirements. Surveillance audits are scheduled 
            at regular intervals to verify that the Quality Management System is maintained and continues to be effective.
        </p>
    </div>

    <!-- Surveillance Schedule -->
    <div class="section-title">SURVEILLANCE AUDIT SCHEDULE</div>
    <table>
        <tr>
            <th>Audit Number</th>
            <th>Audit Type</th>
            <th>Scheduled Date</th>
            <th>Frequency</th>
            <th>Audit Duration (Days)</th>
        </tr>
        <tr>
            <td>Surveillance 1</td>
            <td>Full System Audit</td>
            <td>Year 1 (±2 months)</td>
            <td>Annual</td>
            <td>1-2 days</td>
        </tr>
        <tr>
            <td>Surveillance 2</td>
            <td>Full System Audit</td>
            <td>Year 2 (±2 months)</td>
            <td>Annual</td>
            <td>1-2 days</td>
        </tr>
        <tr>
            <td>Re-certification</td>
            <td>Full System Audit</td>
            <td>Year 3 (±2 months)</td>
            <td>Every 3 Years</td>
            <td>2-3 days</td>
        </tr>
    </table>

    <!-- Audit Scope & Methodology -->
    <h3>Audit Scope & Methodology</h3>
    <div class="highlight">
        <strong>Scope:</strong> The surveillance audit will cover all processes and functions to ensure continued compliance with ISO 9001:2015 and the scope of certification.
    </div>
    <div class="highlight">
        <strong>Methodology:</strong> Combination of document review, site visits, interviews with personnel, and verification of corrective actions from previous audits.
    </div>

    <!-- Key Focus Areas -->
    <h3>Key Focus Areas for Surveillance</h3>
    <table>
        <tr>
            <th>Focus Area</th>
            <th>Audit Criteria</th>
        </tr>
        <tr>
            <td>Management Commitment</td>
            <td>Leadership involvement, resource allocation, policy review</td>
        </tr>
        <tr>
            <td>Risk Assessment</td>
            <td>Risk identification, mitigation measures, effectiveness</td>
        </tr>
        <tr>
            <td>Quality Objectives</td>
            <td>Goal setting, monitoring, achievement, communication</td>
        </tr>
        <tr>
            <td>Operational Control</td>
            <td>Process compliance, documentation, effectiveness</td>
        </tr>
        <tr>
            <td>Corrective Actions</td>
            <td>Follow-up on non-conformities, preventive measures</td>
        </tr>
        <tr>
            <td>Management Review</td>
            <td>Regular review, improvement decisions</td>
        </tr>
    </table>

    <!-- Corrective Action Monitoring -->
    <h3>Corrective Action Monitoring</h3>
    <div class="info-box">
        <p>
            All non-conformities and observations raised during surveillance audits must be addressed 
            within agreed timelines. Follow-up actions will be verified in subsequent audit visits.
        </p>
    </div>

    <!-- Contact & Escalation -->
    <div class="section-title">CONTACT & ESCALATION</div>
    <table>
        <tr>
            <td class="label">Primary Audit Contact</td>
            <td>To be provided by GMCSPL</td>
        </tr>
        <tr>
            <td class="label">Escalation Point</td>
            <td>Certification Manager, GMCSPL</td>
        </tr>
        <tr>
            <td class="label">For Queries</td>
            <td>certifications@gmcspl.com | +91-XXXX-XXXX-XXX</td>
        </tr>
    </table>

    <!-- Footer -->
    <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #666; page-break-inside: avoid;">
        <hr style="border: none; border-top: 1px solid #ccc; margin: 10px 0;">
        <p>On-going Surveillance Plan | QMS Certification | Generated by GMCSPL</p>
    </div>
</body>
</html>
