<?php
/**
 * QMS – F-24s1 Customer Feedback Form (Surveillance Year 1)
 * ACF Group: group_69758615cba4d
 * Fields: organization, location, audit_type, feedback_f24, suggestions, cert_plans, name, evaluation_result, reviewed_by
 * (Field names identical to F-24; shared meta keys via clone prefix_name=0)
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

if ( ! function_exists('f24s1_val') ) {
    function f24s1_val( $v, $fb = '-' ) {
        if ( $v === null || $v === '' || $v === false ) return $fb;
        if ( is_array($v) ) {
            foreach ( ['display_name','label','name','value'] as $k ) {
                if ( ! empty($v[$k]) && is_string($v[$k]) ) return esc_html($v[$k]);
            }
            $flat = array_filter( array_map( fn($i) => is_string($i) ? trim($i) : '', $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fb;
        }
        return esc_html( (string) $v );
    }
}

$org_raw = get_field( 'organization', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : ( is_array($org_raw) ? f24s1_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );

$location    = f24s1_val( get_field( 'location', $post_id ) );
$audit_type  = f24s1_val( get_field( 'audit_type', $post_id ) );
$suggestions = f24s1_val( get_field( 'suggestions_or_any_other_points:', $post_id ) );
$cert_plans  = f24s1_val( get_field( 'do_you_have_any_plan_for_certification_in_any_other_systems_if_yes_please_inform:', $post_id ) );
$client_name = f24s1_val( get_field( 'name', $post_id ) );
$eval_result = f24s1_val( get_field( 'evaluation_result', $post_id ) );

$rev = get_field( 'reviewed_by', $post_id );
$rev_name = is_array($rev) ? f24s1_val( $rev['reviewed_by'] ?? '' ) : '-';
$rev_sig  = is_array($rev) ? f24s1_val( $rev['signature'] ?? '' ) : '-';
$rev_cmt  = is_array($rev) ? f24s1_val( $rev['comment'] ?? '' ) : '-';

$matrix = get_field( 'feedback_f24', $post_id );
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
<?php if ( is_array($matrix) && ! empty($matrix) ) :
    $first = reset($matrix); $cols = is_array($first) ? array_keys($first) : [];
?>
<table>
    <?php if ( $cols ) : ?><thead><tr><?php foreach ($cols as $c) : ?><th><?= esc_html($c) ?></th><?php endforeach; ?></tr></thead><?php endif; ?>
    <tbody><?php foreach ( $matrix as $mrow ) : ?><tr><?php foreach ( (array) $mrow as $cell ) : ?><td><?= esc_html( (string) $cell ) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
</table>
<?php else : ?>
<p style="color:#888; font-style:italic; text-align:center; padding:8px;">Feedback matrix data not available.</p>
<?php endif; ?>

<div class="section-title">Comments</div>
<table>
    <tr><td class="lbl">Suggestions / Other Points</td><td><?= $suggestions ?></td></tr>
    <tr><td class="lbl">Other Certification Plans</td><td><?= $cert_plans ?></td></tr>
    <tr><td class="lbl">Client Name</td><td><?= $client_name ?></td></tr>
</table>

<div class="section-title">For GMCSPL Office Use Only</div>
<table>
    <tr><td class="lbl office-bg">Evaluation Result</td><td class="office-bg"><?= $eval_result ?></td></tr>
    <tr><td class="lbl office-bg">Reviewed By</td><td class="office-bg"><?= $rev_name ?></td></tr>
    <tr><td class="lbl office-bg">Signature</td><td class="office-bg"><?= $rev_sig ?></td></tr>
    <tr><td class="lbl office-bg">Comment</td><td class="office-bg"><?= $rev_cmt ?></td></tr>
</table>

</body>
</html>
