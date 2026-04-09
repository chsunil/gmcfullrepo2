<?php
/**
 * QMS – F-24 Customer Feedback Form
 * ACF Group: group_692abe113eded
 *
 * Clone fields (prefix_name=0) — source meta keys:
 *   f24organization  → organization_name
 *   f24location      → address_on_site
 *   f24name          → top_management
 *   f24designation   → designation:
 *
 * Other fields (own meta keys, direct get_field):
 *   f24audit_type                                     → radio
 *   feedback_f24                                      → matrix_flexible (col: status Yes/No)
 *   f24suggestions_or_any_other_points:               → text
 *   f24do_you_have_any_plan_for_certification_...:    → text
 *   f24evaluation_result                              → select
 *   f24reviewed_by                                    → group (reviewed_by, signature, comment)
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

$location   = esc_html( get_post_meta( $post_id, 'address_on_site', true ) ?: '-' );
$audit_type = esc_html( get_field( 'f24audit_type', $post_id ) ?: '-' );

// ── Feedback matrix ───────────────────────────────────────────────────────────
$matrix = get_field( 'feedback_f24', $post_id );

// ── Comments section ──────────────────────────────────────────────────────────
$suggestions = esc_html( get_field( 'f24suggestions_or_any_other_points:', $post_id ) ?: '' );
$cert_plans  = esc_html( get_field( 'f24do_you_have_any_plan_for_certification_in_any_other_systems_if_yes_please_inform:', $post_id ) ?: '' );
$client_name = esc_html( get_post_meta( $post_id, 'top_management', true ) ?: '' );
$designation = esc_html( get_post_meta( $post_id, 'designation:', true ) ?: '' );

// ── Office use ────────────────────────────────────────────────────────────────
$eval_result = esc_html( get_field( 'f24evaluation_result', $post_id ) ?: '' );
$rev         = get_field( 'f24reviewed_by', $post_id );
$rev_name    = is_array($rev) ? esc_html( (string)( $rev['reviewed_by'] ?? '' ) ) : '';
$rev_sig     = is_array($rev) ? esc_html( (string)( $rev['signature']   ?? '' ) ) : '';
$rev_cmt     = is_array($rev) ? esc_html( (string)( $rev['comment']     ?? '' ) ) : '';
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 12mm 10mm; }
body  { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
th, td { border: 1px solid #555; padding: 3px 5px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 8px; text-transform: uppercase; }
.no-border { border: none !important; background: transparent !important; }
.lbl  { background: #f2f2f2; font-weight: bold; white-space: nowrap; width: 55%; }
.lbl-narrow { background: #f2f2f2; font-weight: bold; white-space: nowrap; width: 35%; }
.title-row th { background: #c6c6c6; font-size: 12px; text-transform: uppercase; text-align: center; }
.section-hdr td { background: #c6c6c6; font-weight: bold; font-size: 9px; text-transform: uppercase; }
.office-bg td { background: #fffde7; }
.center { text-align: center; }
.intro-box { font-size: 8px; color: #444; border: 1px solid #ccc; padding: 4px 8px; background: #fafafa; margin-bottom: 5px; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 8px; }
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
        <th colspan="<?= $LOGO ? 2 : 3 ?>" class="title-row no-border">Customer Feedback Form</th>
        <td class="no-border" style="width:22%; font-size:8px; vertical-align:top; padding-top:2px;">
            <strong>F-24 (Version 1.00)</strong><br>QMS Certification
        </td>
    </tr>
</table>

<!-- Intro message -->
<div class="intro-box">
    Your feedback is most important for us for improving our certification process. Kindly provide your feedback to GM- Certification, Mr. Rajesh (admin@mcsglobal.in). In case, GM- Certification is part of assessment team, feedback to be sent to Managing Director (ksn@mcsglobal.in)
</div>

<!-- Audit Information -->
<table style="margin-bottom:5px;">
    <tr class="section-hdr"><td colspan="2">Audit Information</td></tr>
    <tr><td class="lbl-narrow">Organization</td><td style="font-weight:bold;"><?= $org ?: '&nbsp;' ?></td></tr>
    <tr><td class="lbl-narrow">Location</td><td><?= $location ?></td></tr>
    <tr><td class="lbl-narrow">Audit Type</td><td><?= $audit_type ?></td></tr>
</table>

<!-- Feedback Matrix -->
<table>
    <tr class="section-hdr"><td colspan="2">Feedback</td></tr>
    <thead>
        <tr>
            <th style="width:78%; text-align:left;">Question</th>
            <th style="width:22%;">Yes / No</th>
        </tr>
    </thead>
    <tbody>
    <?php if ( is_array($matrix) && ! empty($matrix) ) :
        foreach ( $matrix as $row_label => $cols ) :
            $status = is_array($cols) ? esc_html( (string)( $cols['status'] ?? '' ) ) : '';
    ?>
        <tr>
            <td><?= esc_html($row_label) ?></td>
            <td class="center"><?= $status ?: '&nbsp;' ?></td>
        </tr>
    <?php endforeach;
    else : ?>
        <tr><td colspan="2" class="no-data">Feedback data not entered.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Comments -->
<table style="margin-bottom:5px;">
    <tr class="section-hdr"><td colspan="2">Comments</td></tr>
    <tr>
        <td class="lbl">Suggestions or any other points</td>
        <td><?= $suggestions ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Do you have any plan for certification in any other systems? If yes, please inform</td>
        <td><?= $cert_plans ?: '&nbsp;' ?></td>
    </tr>
    <tr><td class="lbl-narrow">Name</td><td><?= $client_name ?: '&nbsp;' ?></td></tr>
    <tr><td class="lbl-narrow">Designation</td><td><?= $designation ?: '&nbsp;' ?></td></tr>
</table>

<!-- For GMCSPL Office Use Only -->
<table>
    <tr class="section-hdr"><td colspan="2">For GMCSPL Office Use Only &nbsp;<span style="font-weight:normal; font-size:8px;">(Each question carries five marks)</span></td></tr>
    <tr class="office-bg"><td class="lbl-narrow">Evaluation Result</td><td><?= $eval_result ?: '&nbsp;' ?></td></tr>
    <tr class="office-bg"><td class="lbl-narrow">Reviewed By</td><td><?= $rev_name ?: '&nbsp;' ?></td></tr>
    <tr class="office-bg"><td class="lbl-narrow">Signature</td><td><?= $rev_sig ?: '&nbsp;' ?></td></tr>
    <tr class="office-bg"><td class="lbl-narrow">Comment</td><td><?= $rev_cmt ?: '&nbsp;' ?></td></tr>
</table>

</body>
</html>
