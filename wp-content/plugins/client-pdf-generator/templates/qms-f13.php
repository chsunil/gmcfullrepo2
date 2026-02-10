<?php
/**
 * QMS – F-13 Corrective Action Request (PORTRAIT – HTML TEMPLATE)
 * Data is pulled via get_field()
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Expected variables:
 * - $post_id
 */

// --------------------------------------------------
// Helper function (check if already defined)
// --------------------------------------------------
if (!function_exists('qms13_field')) {
    function qms13_field($key, $post_id) {
        $val = get_field($key, $post_id);
        return !empty($val) ? $val : '-';
    }
}

// --------------------------------------------------
// Pull ACF data
// --------------------------------------------------
$org_name = qms13_field('organization_name', $post_id);

// F-13 Corrective Action Request Repeater
$car_rows = get_field('f13_corrective_action_request', $post_id);
if (!is_array($car_rows)) {
    $car_rows = [];
}

// Audit details
$audit_type     = qms13_field('f13_audit_type', $post_id);
$audit_date     = qms13_field('f13_audit_date', $post_id);
$lead_auditor   = qms13_field('f13_lead_auditor', $post_id);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>F-13 Corrective Action Request</title>

    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 10px;
            color: #000;
        }
        h1 {
            text-align: center;
            font-size: 14px;
            margin-bottom: 5px;
        }
        h2 {
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
        }
        .header-info {
            margin-bottom: 15px;
        }
        .header-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-info td {
            border: 1px solid #000;
            padding: 4px;
        }
        .header-info .label {
            width: 20%;
            font-weight: bold;
            background: #f2f2f2;
        }
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }
        .main-table th {
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .section-title {
            background: #d9d9d9;
            font-weight: bold;
            padding: 6px;
            margin-top: 15px;
            border: 1px solid #000;
            text-align: center;
        }
        .no-data {
            text-align: center;
            padding: 10px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .signature-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            height: 60px;
            vertical-align: bottom;
        }
    </style>
</head>

<body>

<h1>CORRECTIVE ACTION REQUEST</h1>
<h2>F-13 QMS (Version 4.00)</h2>

<!-- Header Info -->
<div class="header-info">
    <table>
        <tr>
            <td class="label">Organization Name</td>
            <td colspan="3"><?php echo esc_html($org_name); ?></td>
        </tr>
        <tr>
            <td class="label">Audit Type</td>
            <td><?php echo esc_html($audit_type); ?></td>
            <td class="label">Audit Date</td>
            <td><?php echo esc_html($audit_date); ?></td>
        </tr>
        <tr>
            <td class="label">Lead Auditor</td>
            <td colspan="3"><?php echo esc_html($lead_auditor); ?></td>
        </tr>
    </table>
</div>

<!-- Corrective Action Request Table -->
<div class="section-title">CORRECTIVE ACTION REQUESTS</div>

<table class="main-table">
    <thead>
        <tr>
            <th width="6%">CAR No</th>
            <th width="8%">Date Issued</th>
            <th width="8%">Category</th>
            <th width="8%">Clause No</th>
            <th width="15%">Requirement</th>
            <th width="15%">Non-Conformity Details</th>
            <th width="15%">Proposed Corrective Action</th>
            <th width="8%">Target Date</th>
            <th width="8%">Completion Date</th>
            <th width="9%">Status</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!empty($car_rows)) : ?>

        <?php foreach ($car_rows as $row) :

            $car_no         = $row['car_no'] ?? '';
            $date_issued    = $row['date_issued'] ?? '';
            $category       = $row['category'] ?? '';
            $clause_no      = $row['clause_no'] ?? '';
            $requirement    = $row['requirement'] ?? '';
            $nc_details     = $row['non_conformity_details'] ?? '';
            $corrective_action = $row['proposed_corrective_action'] ?? '';
            $target_date    = $row['target_date'] ?? '';
            $completion_date = $row['completion_date'] ?? '';
            $status         = $row['status'] ?? '';
        ?>
            <tr>
                <td><?php echo esc_html($car_no); ?></td>
                <td><?php echo esc_html($date_issued); ?></td>
                <td><?php echo esc_html($category); ?></td>
                <td><?php echo esc_html($clause_no); ?></td>
                <td><?php echo nl2br(esc_html($requirement)); ?></td>
                <td><?php echo nl2br(esc_html($nc_details)); ?></td>
                <td><?php echo nl2br(esc_html($corrective_action)); ?></td>
                <td><?php echo esc_html($target_date); ?></td>
                <td><?php echo esc_html($completion_date); ?></td>
                <td><?php echo esc_html($status); ?></td>
            </tr>
        <?php endforeach; ?>

    <?php else : ?>
        <tr>
            <td colspan="10" class="no-data">No Corrective Action Requests Recorded</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Signature Section -->
<table class="signature-table">
    <tr>
        <td width="25%">
            <strong>Prepared By (Auditor)</strong><br><br><br>
            _______________________<br>
            Date: _______________
        </td>
        <td width="25%">
            <strong>Accepted By (Auditee)</strong><br><br><br>
            _______________________<br>
            Date: _______________
        </td>
        <td width="25%">
            <strong>Verified By</strong><br><br><br>
            _______________________<br>
            Date: _______________
        </td>
        <td width="25%">
            <strong>Closed By</strong><br><br><br>
            _______________________<br>
            Date: _______________
        </td>
    </tr>
</table>

</body>
</html>
