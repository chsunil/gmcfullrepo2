<?php
/**
 * QMS – F-24s1 Customer Feedback Form (Surveillance Year 1)
 * ACF Group: group_69758615cba4d
 *
 * Seamless clones (prefix_name=0) — read via source meta key:
 *   f24s1organization → organization_name
 *   f24s1location     → address_on_site  (field_address_on_site)
 *   f24s1name         → top_management   (field_67d5ac7dd1e5c)
 *
 * Own fields (get_field by f24s1 name):
 *   f24s1audit_type, f24s1feedback (matrix_flexible),
 *   f24s1suggestions (textarea), f24s1other_certification_plan (text),
 *   f24s1designation (text), f24s1date (date_picker),
 *   f24s1evaluation_result (radio), f24s1reviewed_by (group)
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

// ── Seamless clones — read via source meta key ───────────────────────────────
$org      = esc_html( gmc_get_organization_name($post_id) );
$location = esc_html( get_post_meta($post_id, 'address_on_site', true) ?: '-' );

// ── Own fields ───────────────────────────────────────────────────────────────
$audit_type   = esc_html( get_field('f24s1audit_type', $post_id) ?: '-' );
$suggestions  = esc_html( get_field('f24s1suggestions', $post_id) ?: '' );
$cert_plans   = esc_html( get_field('f24s1other_certification_plan', $post_id) ?: '' );
$client_name  = esc_html( get_post_meta($post_id, 'top_management', true) ?: '' );
$client_desig = esc_html( get_field('f24s1designation', $post_id) ?: '' );
$client_date  = gmc_format_date( get_field('f24s1date', $post_id) );
$eval_result  = esc_html( get_field('f24s1evaluation_result', $post_id) ?: '' );

$rev      = get_field('f24s1reviewed_by', $post_id) ?: [];
$rev_name = esc_html( $rev['reviewed_by'] ?? '' );
$rev_sig  = esc_html( $rev['signature']   ?? '' );
$rev_cmt  = esc_html( $rev['comment']     ?? '' );

$matrix = get_field('f24s1feedback', $post_id);
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 10px; }
h1 { text-align: center; font-size: 14px; font-weight: bold; margin: 0 0 2px 0; text-transform: uppercase; }
h2 { text-align: center; font-size: 10px; margin: 0 0 12px 0; color: #444; }
table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
th, td { border: 1px solid #555; padding: 5px 6px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 9px; text-transform: uppercase; }
.lbl { background: #f2f2f2; font-weight: bold; width: 35%; white-space: nowrap; }
.h-logo { border: none; text-align: center; vertical-align: middle; }
.section-title { background: #c6c6c6; font-weight: bold; padding: 5px 7px; margin: 12px 0 4px 0; border: 1px solid #555; font-size: 10px; text-transform: uppercase; }
.office-bg { background: #fffde7; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 12px; }
</style>
</head>
<body>

<?php if ( $LOGO ) : ?>
<table style="margin-bottom:4px;"><tr><td class="h-logo" style="border:none;"><img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:80px; width:auto;" /></td></tr></table>
<?php endif; ?>

<h1>Customer Feedback Form</h1>
<h2>F-24s1 &nbsp;|&nbsp; QMS Surveillance Year 1 &nbsp;|&nbsp; Version 1.00</h2>

<div class="section-title">Audit Information</div>
<table>
    <tr><td class="lbl">Organization</td><td><?= $org ?></td></tr>
    <tr><td class="lbl">Location</td><td><?= $location ?></td></tr>
    <tr><td class="lbl">Audit Type</td><td><?= $audit_type ?></td></tr>
</table>

<div class="section-title">Feedback</div>
<?php

if ( is_array($matrix) && ! empty($matrix) ) : ?>
    <table>
        <tr>
            <th>Question</th>
            <th>Response</th>
        </tr>
        <?php foreach ( $matrix as $question => $data ) : 
            $status = isset($data['status']) ? $data['status'] : '';
        ?>
            <tr>
                <td><?php echo esc_html($question); ?></td>
                <td><?php echo esc_html($status); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else : ?>
    <p style="color:#888; font-style:italic; text-align:center; padding:8px;">
        Feedback matrix data not available.
    </p>
<?php endif; ?>

<div class="section-title">Comments</div>
<table>
    <tr><td class="lbl">Suggestions / Other Points</td><td><?= $suggestions ?></td></tr>
    <tr><td class="lbl">Other Certification Plans</td><td><?= $cert_plans ?></td></tr>
    <tr><td class="lbl">Client Name</td><td><?= $client_name ?></td></tr>
    <?php if ( $client_desig ) : ?>
    <tr><td class="lbl">Designation</td><td><?= esc_html($client_desig) ?></td></tr>
    <?php endif; ?>
    <tr><td class="lbl">Date</td><td><?= $client_date ?: '&nbsp;' ?></td></tr>
</table>

<div class="section-title">For GMCSPL Office Use Only</div>
<p style="font-size:9px; color:#555; margin:2px 0 4px 0; font-style:italic;">Each question carries five marks.</p>
<table>
    <tr><td class="lbl office-bg">Evaluation Result</td><td class="office-bg"><?= $eval_result ?></td></tr>
    <tr><td class="lbl office-bg">Reviewed By</td><td class="office-bg"><?= $rev_name ?></td></tr>
    <tr><td class="lbl office-bg">Signature</td><td class="office-bg"><?= $rev_sig ?></td></tr>
    <tr><td class="lbl office-bg">Comment</td><td class="office-bg"><?= $rev_cmt ?></td></tr>
</table>

</body>
</html>
