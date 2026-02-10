<?php
/**
 * QMS – F-10 Non-Conformity Report (PORTRAIT – HTML TEMPLATE)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Expected variables (same as F25):
 * - $post_id
 */

// --------------------------------------------------
// Pull ACF data
// --------------------------------------------------
$nc_rows = get_field('f10_non_conformity_report', $post_id);
if (!is_array($nc_rows)) {
    $nc_rows = [];
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>F-10 Non-Conformity Report</title>

    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 10px;
            color: #000;
        }
        h1 {
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }
        th {
            background: #f2f2f2;
            text-align: center;
        }
        .no-data {
            text-align: center;
            padding: 10px;
        }
    </style>
</head>

<body>

<h1>F-10 – NON-CONFORMITY REPORT</h1>

<table>
    <thead>
        <tr>
            <th width="6%">Date</th>
            <th width="6%">NC No</th>
            <th width="6%">Category</th>
            <th width="6%">Clause</th>
            <th width="12%">Requirement</th>
            <th width="14%">Finding Details</th>
            <th width="10%">Correction</th>
            <th width="10%">Root Cause</th>
            <th width="10%">Corrective Action Plan</th>
            <th width="10%">Initiated By / Date</th>
            <th width="10%">Reviewed By / Date</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!empty($nc_rows)) : ?>

        <?php foreach ($nc_rows as $row) :

            $date       = $row['date'] ?? '';
            $nc_no      = $row['nc_no'] ?? '';
            $category   = $row['category_'] ?? '';
            $clause     = $row['clause_no'] ?? '';
            $req        = $row['requirement'] ?? '';
            $finding    = $row['finding_details'] ?? '';
            $correction = $row['correction'] ?? '';
            $root       = $row['root_cause_analysis'] ?? '';
            $cap        = $row['corrective_action_plan'] ?? '';

            $init_by    = $row['corrective_action_initiated_by']['display_name'] ?? '';
            $init_date  = $row['corrective_action_initiated_date'] ?? '';

            $rev_by     = $row['corrective_action_reviewed_by']['display_name'] ?? '';
            $rev_date   = $row['corrective_action_reviewed_date'] ?? '';
        ?>
            <tr>
                <td><?php echo esc_html($date); ?></td>
                <td><?php echo esc_html($nc_no); ?></td>
                <td><?php echo esc_html($category); ?></td>
                <td><?php echo esc_html($clause); ?></td>
                <td><?php echo nl2br(esc_html($req)); ?></td>
                <td><?php echo nl2br(esc_html($finding)); ?></td>
                <td><?php echo nl2br(esc_html($correction)); ?></td>
                <td><?php echo nl2br(esc_html($root)); ?></td>
                <td><?php echo nl2br(esc_html($cap)); ?></td>
                <td>
                    <?php echo esc_html($init_by); ?><br>
                    <?php echo esc_html($init_date); ?>
                </td>
                <td>
                    <?php echo esc_html($rev_by); ?><br>
                    <?php echo esc_html($rev_date); ?>
                </td>
            </tr>
        <?php endforeach; ?>

    <?php else : ?>
        <tr>
            <td colspan="11" class="no-data">No Non-Conformities Recorded</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
