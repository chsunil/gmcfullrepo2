<?php

/**
 * QMS – F-16 Audit Programme
 * Template for displaying detailed audit programme and schedule
 * ACF Group: group_6885acfbb64a3
 */

if (!defined('ABSPATH')) exit;

function qms16_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : '-';
}

// Fetch ACF fields
$org_name           = qms16_field('organization_name', $post_id);
$ref_no             = qms16_field('ref_no', $post_id);
$audit_start        = qms16_field('audit_start_date', $post_id);
$audit_end          = qms16_field('audit_end_date', $post_id);
$audit_programme    = qms16_field('audit_programme', $post_id);

?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
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
            font-size: 13px;
            margin-bottom: 20px;
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

        .programme-table {
            margin-top: 8px;
            font-size: 10px;
        }

        .time-slot {
            width: 15%;
            background: #f5f5f5;
        }

        .activity {
            width: 40%;
        }

        .responsibility {
            width: 25%;
        }

        .location {
            width: 20%;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <h1>AUDIT PROGRAMME</h1>
    <h2>F-16 (QMS Certification)</h2>

    <!-- Audit Information -->
    <div class="section-title">AUDIT PROGRAMME DETAILS</div>
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
            <td class="label">Audit Start Date</td>
            <td><?= esc_html($audit_start) ?></td>
        </tr>
        <tr>
            <td class="label">Audit End Date</td>
            <td><?= esc_html($audit_end) ?></td>
        </tr>
    </table>

    <!-- Audit Programme Schedule -->
    <div class="section-title">AUDIT SCHEDULE & ACTIVITIES</div>
    <table class="programme-table">
        <tr>
            <th class="time-slot">Time / Date</th>
            <th class="activity">Activity / Topic</th>
            <th class="responsibility">Responsibility</th>
            <th class="location">Location</th>
        </tr>
        <?php if (!empty($audit_programme) && is_array($audit_programme)) : ?>
            <?php foreach ($audit_programme as $item) : ?>
                <tr>
                    <td class="time-slot"><?= isset($item['date_time']) ? esc_html($item['date_time']) : '-' ?></td>
                    <td class="activity"><?= isset($item['activity']) ? esc_html($item['activity']) : '-' ?></td>
                    <td class="responsibility"><?= isset($item['responsible']) ? esc_html($item['responsible']) : '-' ?></td>
                    <td class="location"><?= isset($item['location']) ? esc_html($item['location']) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" style="text-align: center; color: #999;">Programme schedule to be populated</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Key Milestones -->
    <div class="section-title">KEY MILESTONES</div>
    <table>
        <tr>
            <th>Milestone</th>
            <th>Expected Date</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>Opening Meeting</td>
            <td><?= esc_html($audit_start) ?></td>
            <td>Scheduled</td>
        </tr>
        <tr>
            <td>System Audit Activities</td>
            <td>Day 1-3</td>
            <td>In Progress</td>
        </tr>
        <tr>
            <td>Site/Process Audit</td>
            <td>Day 2-3</td>
            <td>Pending</td>
        </tr>
        <tr>
            <td>Closing Meeting</td>
            <td><?= esc_html($audit_end) ?></td>
            <td>Scheduled</td>
        </tr>
    </table>

    <!-- Notes -->
    <div style="margin-top: 15px; padding: 10px; background: #f0f8ff; border-left: 4px solid #4a90e2; font-size: 10px;">
        <strong>Important Notes:</strong><br/>
        • All personnel involved in the audit process must be available as per schedule<br/>
        • Any changes to the audit programme must be communicated immediately<br/>
        • Please ensure all required documentation is available during scheduled visits
    </div>

    <!-- Footer -->
    <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #666; page-break-inside: avoid;">
        <hr style="border: none; border-top: 1px solid #ccc; margin: 10px 0;">
        <p>Audit Programme | QMS Certification | Generated by GMCSPL</p>
    </div>
</body>
</html>
