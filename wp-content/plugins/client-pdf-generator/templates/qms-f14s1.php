<?php
/**
 * QMS F-14 Conflict of Interest Declaration Template
 * This file generates the F-14 PDF form.
 * Data is pulled via get_field() from ACF.
 */

if (!defined('ABSPATH')) exit;

// Helper fallback function
function qms14_field($key, $post_id, $fallback = '-') {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : $fallback;
}

// Get current post ID if not passed in args
$post_id = $args['post_id'] ?? get_the_ID();

// --- Fields for F-14 ---
// NOTE: Please ensure these ACF field names match your setup for the F-14 field group.
$organization_name = qms14_field('organization_name', $post_id);
$auditor_name      = qms14_field('f14_auditor_name', $post_id, '____________________');
$declaration_date  = qms14_field('f14_declaration_date', $post_id, date('d-m-Y'));

// Example declaration text. You can make this an ACF field if it needs to be dynamic.
$declaration_text = "I hereby declare that I have no conflict of interest in conducting the audit for the aforementioned organization. I confirm that I have not provided any consultancy, internal audit services, or had any financial or personal relationship with the client that could compromise my impartiality within the last two years.";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>F-14 Conflict of Interest Declaration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 16px;
            margin: 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 12px;
            margin: 3px 0;
            font-weight: normal;
        }

        .form-info {
            text-align: right;
            font-size: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .label-cell {
            background-color: #f2f2f2;
            font-weight: bold;
            width: 30%;
        }

        .declaration-box {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 20px;
            background-color: #fafafa;
        }

        .signature-section {
            margin-top: 50px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Conflict of Interest Declaration</h1>
        <h2>F-14 QMS (Version 1.00, 02.08.2025)</h2>
    </div>

    <table>
        <tr>
            <td class="label-cell">Organization Name:</td>
            <td><?php echo esc_html($organization_name); ?></td>
        </tr>
        <tr>
            <td class="label-cell">Auditor/Personnel Name:</td>
            <td><?php echo esc_html($auditor_name); ?></td>
        </tr>
        <tr>
            <td class="label-cell">Date of Declaration:</td>
            <td><?php echo esc_html($declaration_date); ?></td>
        </tr>
    </table>

    <div class="declaration-box">
        <h3>Declaration</h3>
        <p><?php echo esc_html($declaration_text); ?></p>
    </div>

    <div class="signature-section">
        <div class="signature-line">
            Signature
        </div>
    </div>
</body>
</html>