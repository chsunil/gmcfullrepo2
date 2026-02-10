<?php

/**
 * QMS – F-12 Scope of Certification
 * Template for displaying scope of certification details
 * ACF Group: group_68851d87c8150
 */

if (!defined('ABSPATH')) exit;

function qms12_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : '-';
}

// Fetch ACF fields
$org_name           = qms12_field('organization_name', $post_id);
$ref_no             = qms12_field('ref_no', $post_id);
$scope_statement    = qms12_field('scope_statement', $post_id);
$scope_limits       = qms12_field('scope_limitations', $post_id);
$exclusions         = qms12_field('exclusions', $post_id);

?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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

        .scope-box {
            background: #ecf0f1;
            border-left: 4px solid #3498db;
            padding: 12px;
            margin: 10px 0;
            line-height: 1.6;
        }

        ul {
            margin: 8px 0;
            padding-left: 20px;
        }

        li {
            margin: 4px 0;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <h1>SCOPE OF CERTIFICATION</h1>
    <h2>F-12 (QMS Certification)</h2>

    <!-- Organization Information -->
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

    <!-- Scope Statement -->
    <div class="section-title">SCOPE STATEMENT</div>
    <div class="scope-box">
        <?php if (!empty($scope_statement)) : ?>
            <?= wp_kses_post($scope_statement) ?>
        <?php else : ?>
            <p>No scope statement provided.</p>
        <?php endif; ?>
    </div>

    <!-- Scope Limitations -->
    <div class="section-title">SCOPE LIMITATIONS</div>
    <div class="scope-box">
        <?php if (!empty($scope_limits)) : ?>
            <?= wp_kses_post($scope_limits) ?>
        <?php else : ?>
            <p>No limitations specified.</p>
        <?php endif; ?>
    </div>

    <!-- Exclusions -->
    <div class="section-title">EXCLUSIONS</div>
    <?php if (!empty($exclusions)) : ?>
        <table>
            <tr>
                <th>Exclusion Clause</th>
                <th>Justification</th>
            </tr>
            <?php if (is_array($exclusions)) : ?>
                <?php foreach ($exclusions as $exclusion) : ?>
                    <tr>
                        <td><?= isset($exclusion['clause']) ? esc_html($exclusion['clause']) : '-' ?></td>
                        <td><?= isset($exclusion['justification']) ? esc_html($exclusion['justification']) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="2"><?= esc_html($exclusions) ?></td>
                </tr>
            <?php endif; ?>
        </table>
    <?php else : ?>
        <table>
            <tr>
                <td style="text-align: center; color: #999;">No exclusions documented.</td>
            </tr>
        </table>
    <?php endif; ?>

    <!-- Certification Details -->
    <div class="section-title">CERTIFICATION DETAILS</div>
    <table>
        <tr>
            <th>Item</th>
            <th>Description</th>
        </tr>
        <tr>
            <td><strong>Certification Scheme</strong></td>
            <td>ISO 9001:2015</td>
        </tr>
        <tr>
            <td><strong>Certificate Type</strong></td>
            <td>QMS (Quality Management System)</td>
        </tr>
        <tr>
            <td><strong>Approval Date</strong></td>
            <td><?= current_time('d-m-Y') ?></td>
        </tr>
    </table>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666; page-break-inside: avoid;">
        <hr style="border: none; border-top: 1px solid #ccc; margin: 10px 0;">
        <p>Scope of Certification Document | Generated by GMCSPL Certifications</p>
    </div>
</body>
</html>
