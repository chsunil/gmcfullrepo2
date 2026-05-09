<?php
/**
 * QMS – F-25 Assessment Check List (LANDSCAPE – HTML TEMPLATE)
 */

if (!defined('ABSPATH')) {
    exit;
}

// --------------------------------------------------
// Pull matrix data from DB (ACF)
// --------------------------------------------------
$matrix_data = get_field('assessment_check_list', $post_id);
if (!is_array($matrix_data)) {
    $matrix_data = [];
}

// Pull field definition to get row order + labels
$field = acf_get_field('field_qms_f25_table');
$rows = [];

if ($field && !empty($field['rows'])) {
    $rows = array_filter(array_map('trim', explode('|', $field['rows'])));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>
        /* FORCE LANDSCAPE */
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        h1 {
            text-align: center;
            margin: 0 0 10px 0;
            padding: 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .req {
            width: 18%;
        }

        .guidance {
            width: 25%;
        }

        .col {
            width: 19%;
        }
    </style>
</head>

<body>

<h1>F-25 – Assessment Check List (QMS)</h1>

<table>
    <thead>
        <tr>
            <th class="req">Requirement (ISO 9001)</th>
            <th class="guidance">Auditor Guidance</th>
            <th class="col">Initial Certification</th>
            <th class="col">Surveillance-1</th>
            <th class="col">Surveillance-2</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!empty($rows)) : ?>

        <?php foreach ($rows as $row_key) :

            // Split Requirement & Auditor Guidance
            $parts = explode('§§', $row_key, 2);
            $requirement = trim($parts[0]);
            $guidance    = isset($parts[1]) ? trim($parts[1]) : '';

            // Row data from DB
            $row = isset($matrix_data[$row_key]) && is_array($matrix_data[$row_key])
                ? $matrix_data[$row_key]
                : [];

            $initial = $row['initial_certification'] ?? '';
            $s1      = $row['surveillance_1'] ?? '';
            $s2      = $row['surveillance_2'] ?? '';
        ?>
            <tr>
                <td class="req"><?php echo esc_html($requirement); ?></td>
                <td class="guidance"><?php echo esc_html($guidance); ?></td>
                <td><?php echo nl2br(esc_html($initial)); ?></td>
                <td><?php echo nl2br(esc_html($s1)); ?></td>
                <td><?php echo nl2br(esc_html($s2)); ?></td>
            </tr>
        <?php endforeach; ?>

    <?php else : ?>
        <tr>
            <td colspan="5" style="text-align:center;">No data available</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
