<?php

/**
 * QMS – F-15 Correspondence & Communication Details
 * Template for displaying updated correspondence and communication contact information
 * ACF Group: group_6932f2a846e82
 */

if (!defined('ABSPATH')) exit;

function qms15_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : '-';
}

// Fetch ACF fields from the "new_field" group
$new_field_group = get_field('new_field', $post_id);

// Extract individual fields from the group
$company_name = isset($new_field_group['company_name:']) ? $new_field_group['company_name:'] : '-';
$correspondence_address = isset($new_field_group['correspondence_address']) ? $new_field_group['correspondence_address'] : '-';
$contact_person = isset($new_field_group['contact_person']) ? $new_field_group['contact_person'] : '-';
$preferred_communication = isset($new_field_group['preferred_mode_of_communication']) ? $new_field_group['preferred_mode_of_communication'] : array();

?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
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

        .info-box {
            background: #fffacd;
            border: 1px solid #f0e68c;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 11px;
            line-height: 1.5;
        }

        .checkbox-list {
            margin: 5px 0;
        }

        .checkbox-item {
            margin: 4px 0;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <h1>CORRESPONDENCE & COMMUNICATION DETAILS</h1>
    <h2>F-15 (QMS Certification)</h2>

    <!-- Information Box -->
    <div class="info-box">
        <strong>Important Note:</strong><br/>
        This form captures updated information for correspondence and communication purposes only, 
        to ensure that you receive relevant information about Audit &amp; Certification at the right place &amp; at the right time. 
        Please return the filled information by fax/scan/post.
    </div>

    <!-- Organization & Contact Information -->
    <div class="section-title">CORRESPONDENCE & CONTACT INFORMATION</div>
    <table>
        <tr>
            <td class="label">Company Name</td>
            <td><?= esc_html($company_name) ?></td>
        </tr>
        <tr>
            <td class="label">Correspondence Address</td>
            <td><?= nl2br(esc_html($correspondence_address)) ?></td>
        </tr>
        <tr>
            <td class="label">Contact Person</td>
            <td><?= esc_html($contact_person) ?></td>
        </tr>
    </table>

    <!-- Preferred Mode of Communication -->
    <div class="section-title">PREFERRED MODE OF COMMUNICATION</div>
    <table>
        <tr>
            <td class="label">Preferred Communication Method(s)</td>
            <td>
                <div class="checkbox-list">
                    <?php if (!empty($preferred_communication) && is_array($preferred_communication)) : ?>
                        <?php foreach ($preferred_communication as $method) : ?>
                            <div class="checkbox-item">
                                ✓ <?= esc_html($method) ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="checkbox-item">-</div>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- Summary -->
    <div class="section-title">DOCUMENT INFORMATION</div>
    <table>
        <tr>
            <th>Item</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>Form Type</td>
            <td>F-15: Correspondence &amp; Communication Details</td>
        </tr>
        <tr>
            <td>Document Date</td>
            <td><?= current_time('d-m-Y') ?></td>
        </tr>
        <tr>
            <td>Purpose</td>
            <td>Update contact and communication preferences</td>
        </tr>
    </table>

    <!-- Instructions -->
    <div style="margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; font-size: 10px;">
        <strong>Instructions:</strong><br/>
        • Ensure all contact information is current and accurate<br/>
        • Select preferred communication methods for correspondence<br/>
        • Return updated form within 7 days of receipt<br/>
        • Contact GMCSPL for any clarifications
    </div>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666; page-break-inside: avoid;">
        <hr style="border: none; border-top: 1px solid #ccc; margin: 10px 0;">
        <p>Correspondence &amp; Communication Details | QMS Certification | Generated by GMCSPL</p>
    </div>
</body>
</html>
