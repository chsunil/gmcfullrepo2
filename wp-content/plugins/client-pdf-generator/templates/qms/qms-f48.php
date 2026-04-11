<?php
/**
 * QMS – F-48 Checklist for Completion of Reports
 * ACF Group: group_qms_f48
 *
 * Clone fields (prefix_name=0) — source meta keys:
 *   f48_org          → organization_name
 *   f48_standard     → cert_scheme
 *   f48_file_ref_no  → proposal_ref_no
 *
 * Matrix: f48_checklist (matrix_flexible)
 *   Rows starting with ## are section headers (rendered as grey spanning rows)
 *   Columns: ic_rc, year1, year2, year3, year4, year5, review
 *
 * Other: f48_remarks (textarea), f48_verified_by (text)
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

// ── Header fields ─────────────────────────────────────────────────────────────
$org_raw = get_post_meta( $post_id, 'organization_name', true );
if ( ! $org_raw ) $org_raw = function_exists('gmc_get_organization_name')
    ? gmc_get_organization_name( $post_id )
    : get_post_field( 'post_title', $post_id );
$org = esc_html( (string) $org_raw );

$standard    = esc_html( get_post_meta( $post_id, 'cert_scheme', true )
    ?: get_post_meta( $post_id, 'standard_applied', true ) ?: '-' );
$file_ref_no = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '-' );

// ── Checklist matrix ──────────────────────────────────────────────────────────
$matrix      = get_field( 'f48_checklist', $post_id );
$remarks     = esc_html( get_field( 'f48_remarks',     $post_id ) ?: '' );
$verified_by = esc_html( get_field( 'f48_verified_by', $post_id ) ?: '' );

$col_keys = ['ic_rc', 'year1', 'year2', 'year3', 'year4', 'year5', 'review'];
$col_labels = ['IC / RC', 'Surveillance 1', 'Surveillance 2', 'Surveillance 3', 'Surveillance 4', 'Surveillance 5', 'Review (Office Use)'];
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 10mm 8mm; }
body  { font-family: Arial, sans-serif; font-size: 8.5px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
th, td { border: 1px solid #555; padding: 2px 4px; vertical-align: middle; text-align: center; }
th { background: #d9d9d9; font-weight: bold; font-size: 8px; text-transform: uppercase; }
.no-border { border: none !important; background: transparent !important; }
.title-row th { background: #c6c6c6; font-size: 11px; text-transform: uppercase; text-align: center; }
.lbl { background: #f2f2f2; font-weight: bold; text-align: left; white-space: nowrap; }
.doc-label { text-align: left; padding-left: 5px; }
.section-hdr { background: #c6c6c6; font-weight: bold; text-align: left; font-size: 8.5px; text-transform: uppercase; padding-left: 4px; }
.chk { font-size: 11px; font-weight: bold; }
.note { font-size: 7.5px; color: #555; padding: 3px 0; }
</style>
</head>
<body>

<!-- Header -->
<table style="margin-bottom:3px;">
    <tr>
        <?php if ( $LOGO ) : ?>
        <td class="no-border" style="width:12%; text-align:center; vertical-align:middle;">
            <img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:45px; width:auto;" />
        </td>
        <?php endif; ?>
        <th colspan="<?= $LOGO ? 2 : 3 ?>" class="title-row no-border">
            Checklist for Completion of Reports
        </th>
        <td class="no-border" style="width:22%; font-size:7.5px; vertical-align:top; padding-top:2px;">
            <strong>F-48 (Version 2.00, 20.03.2016)</strong><br>QMS
        </td>
    </tr>
</table>

<!-- Details -->
<table style="margin-bottom:3px;">
    <tr>
        <td class="lbl" style="width:16%;">Organization</td>
        <td colspan="3" style="text-align:left; font-weight:bold;"><?= $org ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Standard</td>
        <td style="text-align:left; width:34%;"><?= $standard ?></td>
        <td class="lbl" style="width:16%;">File Ref No.</td>
        <td style="text-align:left;"><?= $file_ref_no ?></td>
    </tr>
</table>

<!-- Checklist matrix -->
<table>
    <thead>
        <tr>
            <th style="width:38%; text-align:left; padding-left:4px;">Document</th>
            <?php foreach ( $col_labels as $lbl ) : ?>
            <th style="width:<?= round(62/7,1) ?>%;"><?= $lbl ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
    <?php if ( is_array($matrix) && ! empty($matrix) ) :
        foreach ( $matrix as $row_label => $cols ) :
            $is_header = ( strpos($row_label, '##') === 0 );
            if ( $is_header ) :
                $display = esc_html( ltrim( substr($row_label, 2) ) );
    ?>
        <tr>
            <td class="section-hdr" colspan="8"><?= $display ?></td>
        </tr>
    <?php   else :
                $cells = is_array($cols) ? $cols : [];
    ?>
        <tr>
            <td class="doc-label"><?= esc_html($row_label) ?></td>
            <?php foreach ( $col_keys as $ck ) : ?>
            <td class="chk"><?= esc_html( (string)( $cells[$ck] ?? '' ) ) ?: '&nbsp;' ?></td>
            <?php endforeach; ?>
        </tr>
    <?php   endif;
        endforeach;
    else : ?>
        <tr><td colspan="8" style="text-align:center; color:#888; font-style:italic; padding:8px;">Checklist data not entered.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php if ( $remarks ) : ?>
<table style="margin-top:3px;">
    <tr>
        <td class="lbl" style="width:14%; white-space:nowrap;">Remarks</td>
        <td style="text-align:left;"><?= nl2br($remarks) ?></td>
    </tr>
</table>
<?php endif; ?>

<table style="margin-top:10px;">
    <tr>
        <td style="border:none; width:55%; padding-top:18px;">
            Verified By: <strong><?= $verified_by ?: '___________________________' ?></strong>
        </td>
        <td style="border:none; width:45%; text-align:right; padding-top:18px;">
            Date: ___________________________
        </td>
    </tr>
</table>

<p class="note">Note: &#10003; means the document was attached and blank means it was not.</p>

</body>
</html>
