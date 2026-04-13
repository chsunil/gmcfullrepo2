<?php
/**
 * QMS – F-07 Certification Assessment Plan
 * ACF Group: group_qms_f07
 *
 * get_field('quality_system_requirements') returns:
 *   [
 *     '4.1 Understanding the Organization...' => [
 *         'Top Management' => 'p',
 *         'Purchase'       => 'p',
 *         'HR'             => 'r',
 *         ...
 *     ],
 *     '4.2 ...' => [ ... ],
 *     ...
 *   ]
 * Outer key  = ISO clause label (first column)
 * Inner keys = process/dept column names
 * Inner vals = 'p', 'r', or ''
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$ref_raw = get_field('refno', $post_id) ?: get_field('proposal_ref_no', $post_id) ?: '';
if ( is_array($ref_raw) ) {
    $ref_raw = get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '';
}
$ref_no = esc_html( $ref_raw );

$matrix = get_field( 'quality_system_requirements', $post_id );
if ( ! is_array($matrix) ) $matrix = [];

// Column names come from the inner array's keys of the first row
$proc_cols = ! empty($matrix) ? array_keys( reset($matrix) ) : [];
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 10mm 8mm 10mm 8mm; }
body  { font-family: Arial, sans-serif; font-size: 8px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
th, td { border: 1px solid #555; padding: 2px 3px; vertical-align: middle; text-align: center; }
.no-border { border: none !important; }
.lbl  { text-align: left; font-weight: bold; background: #f2f2f2; white-space: nowrap; }
.title-row th { background: #c6c6c6; font-size: 11px; text-transform: uppercase; text-align: center; }

/* ── Rotated column header — no position:absolute (breaks dompdf) ─────────── */
.rot-th {
    height: 80px;
    vertical-align: bottom;
    padding: 0 1px 2px 1px;
    overflow: hidden;
}
.rot-label {
    display: block;
    white-space: nowrap;
    font-size: 7.5px;
    font-weight: bold;
    
    line-height: 1;
    margin-top: 70px;
    /* transform: rotate(-90deg); */
    transform-origin: left bottom;
}

/* ── Clause rows ──────────────────────────────────────────────────────────── */
.clause         { text-align: left; padding-left: 4px; font-size: 7.5px; }
.clause-section { background: #e8e8e8; font-weight: bold; text-align: left;
                  padding-left: 4px; font-size: 8px; }
.val-P { font-weight: bold;  font-size: 8px; }
.val-R { color: #444; font-size: 8px; }
.val-  { color: #ccc; font-size: 8px; }

.legend { font-size: 7px; color: #444; border: 1px solid #bbb;
          padding: 3px 5px; margin-bottom: 4px; background: #fafafa; }
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
        <th colspan="<?= $LOGO ? 2 : 3 ?>" class="title-row no-border">Certification Assessment Plan</th>
        <td class="no-border" style="width:20%; font-size:8px; vertical-align:top; padding-top:2px;">
            <strong>F-07 (Version 2.00, 20.03.2016)</strong><br>QMS
        </td>
    </tr>
</table>

<!-- Org + Ref -->
<table style="margin-bottom:4px;">
    <tr>
        <td class="lbl" style="width:22%;">Organization Name</td>
        <td style="text-align:left; font-weight:bold;  width:48%;"><?= $org ?></td>
        <td class="lbl" style="width:10%;">Ref. No.</td>
        <td style="text-align:left;  width:20%;"><?= $ref_no ?></td>
    </tr>
</table>

<!-- Legend -->
<div class="legend">
    <strong>P</strong> = Primary area / process — assessment and assessor notes to be completed &nbsp;&nbsp;
    <strong>R</strong> = Significantly relevant area &nbsp;&nbsp;
    <strong>Blank</strong> = No or insignificantly relevant area
</div>

<?php if ( ! empty($matrix) && ! empty($proc_cols) ) : ?>
<table>
    <thead>
        <tr>
            <th style="width:35%; text-align:left; vertical-align:bottom; font-size:8px; padding:2px 4px;">
                Quality System Requirements
            </th>
            <?php foreach ( $proc_cols as $col ) : ?>
            <th class="rot-th">
                <div class="rot-label"><?= esc_html($col) ?></div>
            </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $matrix as $clause_name => $row_values ) :
            $clause = esc_html( trim( $clause_name ) );
            if ( $clause === '' ) continue;
            $is_section = preg_match('/^\d+\s*$/', $clause) ||
                          preg_match('/^(Context|Leadership|Planning|Support|Operation|Performance|Improvement)/i', $clause);
        ?>
        <tr>
            <td class="<?= $is_section ? 'clause-section' : 'clause' ?>"><?= $clause ?></td>
            <?php foreach ( $proc_cols as $col ) :
                $val = strtoupper( trim( $row_values[$col] ?? '' ) );
                $cls = $val !== '' ? 'val-' . $val[0] : 'val-';
            ?>
            <td class="<?= $cls ?>"><?= esc_html($val) ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else : ?>
<table>
    <tr>
        <td style="text-align:center; color:#888; font-style:italic; padding:20px;">
            No assessment matrix data entered yet.
        </td>
    </tr>
</table>
<?php endif; ?>

<table style="margin-top:12px;">
    <tr>
        <td style="border:none; font-size:8px; width:50%;">Auditor: ___________________________</td>
        <td style="border:none; font-size:8px; width:50%; text-align:right;">Date: ___________________________</td>
    </tr>
</table>

</body>
</html>
