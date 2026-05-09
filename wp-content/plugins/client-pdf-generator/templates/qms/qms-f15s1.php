<?php
/**
 * QMS – F-15s1 Correspondence & Communication Details (Surveillance Year 1)
 * ACF Group: group_6975912587e24
 * Fields (seamless group "new_field"):
 *   company_name:            — clone of field_org_name
 *   correspondence_address   — clone of field_68173ed29add4
 *   contact_person           — clone of field_68173ed2aa379
 *   preferred_mode_of_communication — checkbox
 *   (two unnamed clone fields: field_0029, field_69759125ae2a3)
 * Standalone:
 *   visiting_card            — image
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

if ( ! function_exists('f15s1_val') ) {
    function f15s1_val( $v, $fb = '-' ) {
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

// Fetch the seamless group (new_field)
$grp = get_field( 'new_field', $post_id );

$org_raw = null;
if ( is_array($grp) ) {
    $org_raw = $grp['company_name:'] ?? null;
}
$org = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
     : ( is_array($org_raw) ? f15s1_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );

$address = '';
if ( is_array($grp) ) {
    $addr_raw = $grp['correspondence_address'] ?? null;
    $address  = is_string($addr_raw) ? esc_html($addr_raw)
              : ( is_array($addr_raw) ? f15s1_val($addr_raw) : '' );
}
// Fallback to get_post_meta for head_office if group read failed
if ( ! $address ) {
    $address = esc_html( get_post_meta( $post_id, 'head_office', true ) ?: '' );
}

// Contact person group (seamless clone of f01contact_person → sub-fields)
$contact_data = [];
if ( is_array($grp) ) {
    $cp_raw = $grp['contact_person'] ?? null;
    if ( is_array($cp_raw) ) {
        $contact_data = $cp_raw;
    }
}
// Fallback: read directly
if ( empty($contact_data) ) {
    $contact_data = get_field( 'f01contact_person', $post_id ) ?: [];
}
$contact_name  = esc_html( $contact_data['contact_person_name'] ?? '' );
$contact_pos   = esc_html( $contact_data['contact_position']    ?? '' );
$contact_mob   = esc_html( $contact_data['contact_mobile']      ?? '' );
$contact_email = esc_html( $contact_data['contact_email']       ?? '' );
$contact_tel   = esc_html( $contact_data['tel']                 ?? '' );
$contact_fax   = esc_html( $contact_data['fax']                 ?? '' );
$contact_web   = esc_html( $contact_data['website']             ?? '' );

// Date — surv1 audit date
$form_date_raw = get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_surv1', true );
$form_date     = $form_date_raw
    ? ( function_exists('gmc_format_date') ? gmc_format_date($form_date_raw) : esc_html($form_date_raw) )
    : '';

// Preferred mode of communication (checkbox — returns array of selected values)
$pref_mode_raw = is_array($grp) ? ($grp['preferred_mode_of_communication'] ?? []) : [];
$pref_mode = is_array($pref_mode_raw) ? esc_html( implode(', ', $pref_mode_raw) ) : f15s1_val($pref_mode_raw);

// Visiting card image
$card_raw = get_field( 'visiting_card', $post_id );
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
<h2>F-15s1 &nbsp;|&nbsp; QMS Surveillance Year 1 &nbsp;|&nbsp; Version 1.00</h2>

<div class="section-title">Contact Information</div>
<table>
    <tr><td class="lbl">Company Name</td><td><?= $org ?></td></tr>
    <tr><td class="lbl">Correspondence Address</td><td><?= $address ?: '-' ?></td></tr>
    <tr><td class="lbl">Contact Person</td><td><?= $contact_name ?: '-' ?></td></tr>
    <?php if ( $contact_pos ) : ?>
    <tr><td class="lbl">Designation</td><td><?= $contact_pos ?></td></tr>
    <?php endif; ?>
    <?php if ( $contact_mob ) : ?>
    <tr><td class="lbl">Mobile</td><td><?= $contact_mob ?></td></tr>
    <?php endif; ?>
    <?php if ( $contact_email ) : ?>
    <tr><td class="lbl">Email</td><td><?= $contact_email ?></td></tr>
    <?php endif; ?>
    <?php if ( $contact_tel ) : ?>
    <tr><td class="lbl">Landline No.</td><td><?= $contact_tel ?></td></tr>
    <?php endif; ?>
    <?php if ( $contact_fax ) : ?>
    <tr><td class="lbl">Fax No.</td><td><?= $contact_fax ?></td></tr>
    <?php endif; ?>
    <?php if ( $contact_web ) : ?>
    <tr><td class="lbl">Website</td><td><?= $contact_web ?></td></tr>
    <?php endif; ?>
    <tr><td class="lbl">Preferred Mode of Communication</td><td><?= $pref_mode ?: '-' ?></td></tr>
</table>

<?php if ( $card_url ) : ?>
<div class="section-title">Visiting Card</div>
<p><img src="<?= esc_url($card_url) ?>" style="max-width:200px; max-height:120px;" alt="Visiting Card" /></p>
<?php endif; ?>

<?php if ( $form_date ) : ?>
<table style="margin-bottom:8px;">
    <tr><td class="lbl">Date</td><td><?= $form_date ?></td></tr>
</table>
<?php endif; ?>

<table style="margin-top:30px;">
    <tr>
        <td style="border:none; padding-top:25px; width:50%;">Authorized Signatory: ___________________________</td>
        <td style="border:none; padding-top:25px; width:50%; text-align:right;">Date: ___________________________</td>
    </tr>
</table>
</body>
</html>
