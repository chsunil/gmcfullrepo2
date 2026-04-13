<?php
/**
 * QMS – F-10 Non-Conformity Report
 * ACF Group: group_696f7f4276d24
 *
 * Fields:
 *   f10_nc_rows (repeater):
 *     date, nc_no, category_, clause_no, requirement,
 *     finding_details, correction, root_cause_analysis,
 *     corrective_action_plan,
 *     corrective_action_initiated_by (user array), corrective_action_initiated_date,
 *     corrective_action_reviewed_by (user array), corrective_action_reviewed_date,
 *     effectiveness_of_corrective_action_during_subsequent_audit (user array)
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$ref_no  = esc_html( get_field('proposal_ref_no', $post_id) ?: '' );

$nc_rows = get_field( 'f10_nc_rows', $post_id );
if ( ! is_array($nc_rows) ) $nc_rows = [];
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 landscape; margin: 10mm 8mm 10mm 8mm; }
body  { font-family: Arial, sans-serif; font-size: 8px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
th, td { border: 1px solid #555; padding: 2px 3px; vertical-align: top; text-align: left; }
.no-border { border: none !important; }
.lbl  { font-weight: bold; background: #f2f2f2; white-space: nowrap; }
.title-row th { background: #c6c6c6; font-size: 11px; text-transform: uppercase; text-align: center; }
.col-hdr { background: #e0e0e0; font-weight: bold; font-size: 7.5px; text-align: center; vertical-align: middle; }
.note-box { font-size: 7px; color: #555; border: 1px solid #ccc; padding: 3px 5px; margin-bottom: 4px; background: #fafafa; }
</style>
</head>
<body>

<!-- Header -->
<table style="margin-bottom:4px;">
    <tr>
        <?php if ( $LOGO ) : ?>
        <td class="no-border" style="width:13%; text-align:center; vertical-align:middle;">
            <img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:50px; width:auto;" />
        </td>
        <?php endif; ?>
        <th colspan="<?= $LOGO ? 2 : 3 ?>" class="title-row no-border" style="text-align: center;">Non-Conformity Report</th>
        <td class="no-border" style="width:20%; font-size:8px; vertical-align:top; padding-top:2px;">
            <strong>F-10 (Version 2.00, 20.03.2016)</strong><br>QMS
        </td>
    </tr>
</table>

<!-- Org + Ref -->
<table style="margin-bottom:4px;">
    <tr>
        <td class="lbl" style="width:20%;">Organization Name</td>
        <td style="text-align:left; font-weight:bold;  width:50%;"><?= $org ?></td>
        <td class="lbl" style="width:10%;">Ref. No.</td>
        <td style="text-align:left;  width:20%;"><?= $ref_no ?></td>
    </tr>
</table>

<!-- Note -->
<div class="note-box">
    Organizations may use root cause analysis with the following 5WHY tool or other appropriate tools for nonconformities identified.
</div>

<!-- NC Table -->
<table>
    <thead>
        <tr>
            <th class="col-hdr" style="width:5%;">Date</th>
            <th class="col-hdr" style="width:5%;">NC No</th>
            <th class="col-hdr" style="width:5%;">Category</th>
            <th class="col-hdr" style="width:6%;">Clause No</th>
            <th class="col-hdr" style="width:12%;">Requirement</th>
            <th class="col-hdr" style="width:13%;">Finding Details</th>
            <th class="col-hdr" style="width:10%;">Correction</th>
            <th class="col-hdr" style="width:10%;">Root Cause Analysis</th>
            <th class="col-hdr" style="width:11%;">Corrective Action Plan</th>
            <th class="col-hdr" style="width:9%;">Initiated By / Date</th>
            <th class="col-hdr" style="width:9%;">Reviewed By / Date</th>
            <th class="col-hdr" style="width:5%;">Effectiveness</th>
        </tr>
    </thead>
    <tbody>
    <?php if ( ! empty($nc_rows) ) : ?>
        <?php foreach ( $nc_rows as $row ) :
            $date      = esc_html( $row['date'] ?? '' );
            $nc_no     = esc_html( $row['nc_no'] ?? '' );
            $cat       = esc_html( $row['category_'] ?? '' );
            $clause    = esc_html( $row['clause_no'] ?? '' );
            $req       = esc_html( $row['requirement'] ?? '' );
            $finding   = esc_html( $row['finding_details'] ?? '' );
            $corr      = esc_html( $row['correction'] ?? '' );
            $root      = esc_html( $row['root_cause_analysis'] ?? '' );
            $cap         = esc_html( $row['corrective_action_plan'] ?? '' );
            $target_date = esc_html( $row['target_date'] ?? '' );
            $init_by   = esc_html( $row['corrective_action_initiated_by']['display_name'] ?? '' );
            $init_date = esc_html( $row['corrective_action_initiated_date'] ?? '' );
            $rev_by    = esc_html( $row['corrective_action_reviewed_by']['display_name'] ?? '' );
            $rev_date  = esc_html( $row['corrective_action_reviewed_date'] ?? '' );
            $effect    = esc_html( $row['effectiveness_of_corrective_action_during_subsequent_audit']['display_name'] ?? '' );
        ?>
        <tr>
            <td><?= $date ?></td>
            <td><?= $nc_no ?></td>
            <td><?= $cat ?></td>
            <td><?= $clause ?></td>
            <td><?= nl2br($req) ?></td>
            <td><?= nl2br($finding) ?></td>
            <td><?= nl2br($corr) ?></td>
            <td><?= nl2br($root) ?></td>
            <td><?= nl2br($cap) ?><?= $target_date ? "<br><small>Target: {$target_date}</small>" : '' ?></td>
            <td><?= $init_by ?><br><?= $init_date ?></td>
            <td><?= $rev_by ?><br><?= $rev_date ?></td>
            <td><?= $effect ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="12" style="text-align:center; color:#888; font-style:italic; padding:16px;">
                No non-conformities recorded.
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
