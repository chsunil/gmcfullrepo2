<?php

/**
 * Script to generate FE templates from FE-17 to FE-2250
 * Run this script once to create all missing templates
 */

if (!defined('ABSPATH')) exit;

// Define known FE templates with their titles and field groups (from TODO_ACF_template_mapping.md)
$known_fe_templates = [
    '17' => ['title' => 'Action Plan', 'group' => 'group_field_group_607b8'],
    '18' => ['title' => 'Corrective Action Plan', 'group' => 'group_field_group_607b9'],
    '19' => ['title' => 'Preventive Action Plan', 'group' => 'group_field_group_607ba'],
    '20' => ['title' => 'Audit Report', 'group' => 'group_field_group_607bb'],
    '21' => ['title' => 'Non-Conformance Report', 'group' => 'group_field_group_607bc'],
    '22' => ['title' => 'Audit Plan', 'group' => 'group_field_group_607bd'],
];

// Generate the full list from FE-17 to FE-2250
$fe_templates = [];
$start_hex = 0x607b8; // For FE-17
for ($i = 17; $i <= 2250; $i++) {
    $group_key = 'group_field_group_' . dechex($start_hex + ($i - 17));
    $title = isset($known_fe_templates[$i]) ? $known_fe_templates[$i]['title'] : 'QMS Template FE-' . $i;
    $fe_templates[$i] = ['title' => $title, 'group' => $group_key];
}

// Function to generate template content
function generate_fe_template($fe_number, $title, $group_key) {
    $template = "<?php

/**
 * FE-{$fe_number} {$title} Template
 * This file generates the {$title} PDF
 * Data is pulled via get_field()
 * ACF Group: {$group_key}
 */

if (!defined('ABSPATH')) exit;

// Helper fallback function
function fe{$fe_number}_field(\$key, \$post_id) {
    \$val = get_field(\$key, \$post_id);
    return !empty(\$val) ? \$val : '-';
}

// Pulling all required fields (placeholder fields, to be updated based on ACF)
// TODO: Update these fields based on actual ACF field group {$group_key}
\$field1 = fe{$fe_number}_field('field1', \$post_id);
\$field2 = fe{$fe_number}_field('field2', \$post_id);
\$field3 = fe{$fe_number}_field('field3', \$post_id);

?>
<html>

<head>
    <meta charset=\"UTF-8\">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
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
            padding: 6px;
            margin-top: 15px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .label {
            width: 30%;
            font-weight: bold;
            background: #f9f9f9;
        }
    </style>

<body>
    <!-- HEADER -->
    <h1>" . strtoupper($title) . "</h1>
    <h2>FE-{$fe_number} (Version 1.00)</h2>

    <!-- CONTENT SECTION -->
    <div class=\"section-title\">" . strtoupper($title) . " DETAILS</div>
    <table>
        <tr>
            <td class=\"label\">Field 1</td>
            <td><?=\$field1 ?></td>
        </tr>
        <tr>
            <td class=\"label\">Field 2</td>
            <td><?=\$field2 ?></td>
        </tr>
        <tr>
            <td class=\"label\">Field 3</td>
            <td><?=\$field3 ?></td>
        </tr>
    </table>
</body>
</html>";

    return $template;
}

// Generate templates
$templates_dir = __DIR__ . '/templates/';
if (!is_dir($templates_dir)) {
    mkdir($templates_dir, 0755, true);
}

foreach ($fe_templates as $number => $data) {
    $filename = "fe-{$number}.php";
    $filepath = $templates_dir . $filename;

    if (!file_exists($filepath)) {
        $content = generate_fe_template($number, $data['title'], $data['group']);
        file_put_contents($filepath, $content);
        echo "Generated: {$filename}\n";
    } else {
        echo "Skipped (exists): {$filename}\n";
    }
}

echo "Template generation complete!\n";
echo "Note: These are placeholder templates. Update the fields based on actual ACF field groups.\n";
