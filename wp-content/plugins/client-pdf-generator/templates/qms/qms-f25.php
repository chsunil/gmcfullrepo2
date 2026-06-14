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

// --------------------------------------------------
// Which stage column(s) to print — set via query var by the AJAX handler
// (user ticks "Print in PDF" beside Initial/Surv-1/Surv-2 column headers)
// Empty / missing = print all 3 (default, matches old behaviour)
// --------------------------------------------------
$all_stage_cols = [
    'initial_certification' => 'Initial Certification',
    'surveillance_1'        => 'Surveillance-1',
    'surveillance_2'        => 'Surveillance-2',
];
$selected_keys = get_query_var('cpdf_print_stages', []);
if (empty($selected_keys) || !is_array($selected_keys)) {
    $selected_keys = array_keys($all_stage_cols);
}
$print_cols = array_intersect_key($all_stage_cols, array_flip($selected_keys));
if (empty($print_cols)) {
    $print_cols = $all_stage_cols; // safety fallback — never print an empty table
}
$single_mode     = (count($print_cols) === 1);
$data_col_count  = count($print_cols);
$data_col_width  = round((100 - 18 - 25) / $data_col_count, 2) . '%';
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
            <?php foreach ($print_cols as $col_key => $col_label) : ?>
                <th class="col" style="width:<?php echo esc_attr($data_col_width); ?>;">
                    <?php echo $single_mode ? esc_html('Evidences / Records – ' . $col_label) : esc_html($col_label); ?>
                </th>
            <?php endforeach; ?>
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

        ?>
            <tr>
                <td class="req"><?php echo esc_html($requirement); ?></td>
                <td class="guidance"><?php echo esc_html($guidance); ?></td>
                <?php foreach ($print_cols as $col_key => $col_label) : ?>
                    <td style="width:<?php echo esc_attr($data_col_width); ?>;"><?php echo nl2br(esc_html($row[$col_key] ?? '')); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>

    <?php else : ?>
        <tr>
            <td colspan="<?php echo esc_attr(2 + $data_col_count); ?>" style="text-align:center;">No data available</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
