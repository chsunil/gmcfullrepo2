<?php
/**
 * QMS – F-15s2 Correspondence & Communication Details (Surveillance Year 2)
 * ACF Group: group_f5e9a27b67e6
 * Fields (seamless group "s2_new_field"):
 *   s2_company_name:         — clone of field_org_name
 *   s2_correspondence_address — clone
 *   s2_contact_person        — clone
 *   s2_preferred_mode_of_communication — checkbox
 * Standalone: s2_visiting_card — image
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

if ( ! function_exists('f15s2_val') ) {
    function f15s2_val( $v, $fb = '-' ) {
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

$grp = get_field( 's2_new_field', $post_id );

$org_raw = is_array($grp) ? ($grp['s2_company_name:'] ?? null) : null;
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : ( is_array($org_raw) ? f15s2_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );

$address = f15s2_val( is_array($grp) ? ($grp['s2_correspondence_address'] ?? null) : null );
$contact = f15s2_val( is_array($grp) ? ($grp['s2_contact_person'] ?? null) : null );

$pref_mode_raw = is_array($grp) ? ($grp['s2_preferred_mode_of_communication'] ?? []) : [];
$pref_mode = is_array($pref_mode_raw) ? esc_html( implode(', ', $pref_mode_raw) ) : f15s2_val($pref_mode_raw);

$card_raw = get_field( 's2_visiting_card', $post_id );
$card_url = is_array($card_raw) ? ($card_raw['url'] ?? '') : (string)$card_raw;
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
.lbl { background: #f2f2f2; font-weight: bold; width: 35%; }
.h-logo { border: none; text-align: center; }
.section-title { background: #c6c6c6; font-weight: bold; padding: 5px 7px; margin: 12px 0 4px 0; border: 1px solid #555; font-size: 10px; text-transform: uppercase; }
</style>
</head>
<body>
<?php if ( $LOGO ) : ?>
<table style="margin-bottom:4px;"><tr><td class="h-logo" style="border:none;"><img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:80px; width:auto;" /></td></tr></table>
<?php endif; ?>
<h1>Correspondence &amp; Communication Details</h1>
<h2>F-15s2 &nbsp;|&nbsp; QMS Surveillance Year 2 &nbsp;|&nbsp; Version 1.00</h2>

<div class="section-title">Contact Information</div>
<table>
    <tr><td class="lbl">Company Name</td><td><?= $org ?></td></tr>
    <tr><td class="lbl">Correspondence Address</td><td><?= $address ?></td></tr>
    <tr><td class="lbl">Contact Person</td><td><?= $contact ?></td></tr>
    <tr><td class="lbl">Preferred Mode of Communication</td><td><?= $pref_mode ?: '-' ?></td></tr>
</table>

<?php if ( $card_url ) : ?>
<div class="section-title">Visiting Card</div>
<p><img src="<?= esc_url($card_url) ?>" style="max-width:200px; max-height:120px;" alt="Visiting Card" /></p>
<?php endif; ?>

<table style="margin-top:30px;">
    <tr>
        <td style="border:none; padding-top:25px; width:50%;">Authorized Signatory: ___________________________</td>
        <td style="border:none; padding-top:25px; width:50%; text-align:right;">Date: ___________________________</td>
    </tr>
</table>
</body>
</html>
