<?php

/**
 * QMS – F-13a Attendance Sheet
 * Template for displaying audit team attendance and participation
 * ACF Group: group_6885acad944c2
 */

if (!defined('ABSPATH')) exit;

function qms13a_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : '-';
}

// Fetch ACF fields
$org_name           = qms13a_field('organization_name', $post_id);
$ref_no             = qms13a_field('ref_no', $post_id);
$audit_date         = qms13a_field('audit_date', $post_id);
$audit_stage        = qms13a_field('audit_stage', $post_id);
$attendance_data    = qms13a_field('attendance_data', $post_id);

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
            font-size: 17px;
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
            padding: 7px;
            margin-top: 15px;
            border: 1px solid #999;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px;
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

        .present {
            text-align: center;
            background: #d4edda;
        }

        .absent {
            text-align: center;
            background: #f8d7da;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <h1>ATTENDANCE SHEET</h1>
    <h2>F-13a (QMS Certification)</h2>

    <!-- Audit Information -->
    <div class="section-title">AUDIT INFORMATION</div>
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
            <td class="label">Audit Date</td>
            <td><?= esc_html($audit_date) ?></td>
        </tr>
        <tr>
            <td class="label">Audit Stage</td>
            <td><?= esc_html($audit_stage) ?></td>
        </tr>
    </table>

    <!-- Attendance Table -->
    <div class="section-title">ATTENDEE ATTENDANCE RECORD</div>
    <table>
        <tr>
            <th>Name</th>
            <th>Role/Department</th>
            <th>Day 1</th>
            <th>Day 2</th>
            <th>Day 3</th>
            <th>Signature</th>
        </tr>
        <?php if (!empty($attendance_data) && is_array($attendance_data)) : ?>
            <?php foreach ($attendance_data as $attendee) : ?>
                <tr>
                    <td><?= isset($attendee['name']) ? esc_html($attendee['name']) : '-' ?></td>
                    <td><?= isset($attendee['role']) ? esc_html($attendee['role']) : '-' ?></td>
                    <td class="<?= isset($attendee['day1']) && $attendee['day1'] ? 'present' : '' ?>">
                        <?= isset($attendee['day1']) && $attendee['day1'] ? '✓' : '-' ?>
                    </td>
                    <td class="<?= isset($attendee['day2']) && $attendee['day2'] ? 'present' : '' ?>">
                        <?= isset($attendee['day2']) && $attendee['day2'] ? '✓' : '-' ?>
                    </td>
                    <td class="<?= isset($attendee['day3']) && $attendee['day3'] ? 'present' : '' ?>">
                        <?= isset($attendee['day3']) && $attendee['day3'] ? '✓' : '-' ?>
                    </td>
                    <td>_________</td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #999;">No attendance data recorded</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Instructions -->
    <div style="margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; font-size: 10px;">
        <strong>Instructions:</strong><br/>
        • All attendees must sign the attendance sheet daily<br/>
        • Mark attendance for each day of the audit<br/>
        • Contact the audit lead for any absence or modification
    </div>

    <!-- Footer -->
    <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #666; page-break-inside: avoid;">
        <hr style="border: none; border-top: 1px solid #ccc; margin: 10px 0;">
        <p>Attendance Sheet | QMS Certification Audit | Generated by GMCSPL</p>
    </div>
</body>
</html>
