<?php
/**
 * QMS – F-15 Correspondence & Communication Details
 * ACF Group: group_6932f2a846e82
 *
 * The group "new_field" contains seamless clones (prefix_name=0) plus one own checkbox.
 * Seamless clones share the source field's meta key, so we read them directly.
 * The checkbox (preferred_mode_of_communication) is inside the group, so we
 * read the group array to get it.
 *
 * Direct reads (prefix_name=0 seamless clones):
 *   organization_name    — clone of field_org_name
 *   head_office          — clone of field_68173ed29add4
 *   f01contact_person    — clone of field_68173ed2aa379 (group with sub-fields)
 *
 * Via group read:
 *   new_field['preferred_mode_of_communication'] — checkbox (Courier / e-Mail)
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

$org_raw  = get_field( 'organization_name', $post_id );
$org      = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
          : esc_html( get_post_field('post_title', $post_id) );

$address  = esc_html( get_field('head_office', $post_id) ?: '' );

$contact_group = get_field('f01contact_person', $post_id);
$contact_name  = esc_html( $contact_group['contact_person_name'] ?? '' );
$contact_pos   = esc_html( $contact_group['contact_position'] ?? '' );
$contact_mob   = esc_html( $contact_group['contact_mobile'] ?? '' );
$contact_email = esc_html( $contact_group['contact_email'] ?? '' );
$contact_fax   = esc_html( $contact_group['fax'] ?? '' );
$contact_tel   = esc_html( $contact_group['tel'] ?? '' );
$contact_web   = esc_html( $contact_group['website'] ?? '' );

// Date — seamless clone of field_0023 → stage2_audit_surveillance_audit_date_initial
$form_date_raw = get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_initial', true );
$form_date     = $form_date_raw
    ? ( function_exists('gmc_format_date') ? gmc_format_date($form_date_raw) : esc_html($form_date_raw) )
    : '';

// new_field group: preferred_mode_of_communication + 2nd contact person fields
$new_field_group  = get_field('new_field', $post_id);
$pref_comm        = [];
if ( is_array($new_field_group) && isset($new_field_group['preferred_mode_of_communication']) ) {
    $v = $new_field_group['preferred_mode_of_communication'];
    $pref_comm = is_array($v) ? $v : ( $v ? [$v] : [] );
}
// Contact Person 2 — seamless clones of Top Management group sub-fields
// Source meta keys: contact_person_top_management, contact_person_designation,
//                   contact_person_mobile_number, contact_person_contact_email_new
$contact2_name  = esc_html( get_post_meta( $post_id, 'contact_person_top_management',    true ) ?: '' );
$contact2_desg  = esc_html( get_post_meta( $post_id, 'contact_person_designation',        true ) ?: '' );
$mobile2        = esc_html( get_post_meta( $post_id, 'contact_person_mobile_number',      true ) ?: '' );
$email2         = esc_html( get_post_meta( $post_id, 'contact_person_contact_email_new',  true ) ?: '' );
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 12mm 10mm 12mm 10mm; }
body  { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
th, td { border: 1px solid #555; padding: 3px 4px; vertical-align: top; text-align: left; }
.no-border { border: none !important; }
.lbl  { font-weight: bold; background: #f2f2f2; white-space: nowrap; width: 35%; }
.title-row th { background: #c6c6c6; font-size: 11px; text-transform: uppercase; text-align: center; }
.section-hdr td { background: #c6c6c6; font-weight: bold; font-size: 9px; text-transform: uppercase; }
.info-box { font-size: 8px; color: #444; border: 1px solid #d0d0d0; padding: 5px 7px; margin-bottom: 5px; background: #fffdf5; line-height: 1.5; }
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
        <th colspan="<?= $LOGO ? 2 : 3 ?>" class="title-row no-border">Correspondence &amp; Communication Details</th>
        <td class="no-border" style="width:22%; font-size:8px; vertical-align:top; padding-top:2px;">
            <strong>F-15 (Version 2.00, 20.03.2016)</strong><br>QMS
        </td>
    </tr>
</table>

<!-- Note -->
<div class="info-box">
    Please note this is meant for capturing updated information for correspondence and communication purposes only,
    to ensure that you receive the relevant information about Audit &amp; Certification at the right place &amp;
    at the right time. Kindly return the filled information by fax / scan / post.
</div>

<!-- Company & Address -->
<table style="margin-bottom:4px;">
    <tr class="section-hdr"><td colspan="2">Organisation Information</td></tr>
    <tr>
        <td class="lbl">Company Name</td>
        <td style="font-weight:bold;"><?= $org ?></td>
    </tr>
    <tr>
        <td class="lbl">Correspondence Address</td>
        <td><?= nl2br($address) ?></td>
    </tr>
</table>

<!-- Contact Person -->
<table style="margin-bottom:4px;">
    <tr class="section-hdr">
        <td style="width:25%;">&nbsp;</td>
        <td style="width:37.5%;">Contact Person 1</td>
        <td style="width:37.5%;">Contact Person 2</td>
    </tr>
    <tr>
        <td class="lbl">Name</td>
        <td><?= $contact_name ?></td>
        <td><?= $contact2_name ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Designation</td>
        <td><?= $contact_pos ?></td>
        <td><?= $contact2_desg ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Mobile No.</td>
        <td><?= $contact_mob ?></td>
        <td><?= $mobile2 ?: '&nbsp;' ?></td>
    </tr>
    <?php if ( $contact_tel ) : ?>
    <tr>
        <td class="lbl">Landline No.</td>
        <td colspan="2"><?= $contact_tel ?></td>
    </tr>
    <?php endif; ?>
    <?php if ( $contact_fax ) : ?>
    <tr>
        <td class="lbl">Fax No.</td>
        <td colspan="2"><?= $contact_fax ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td class="lbl">E-Mail</td>
        <td><?= $contact_email ?: '&nbsp;' ?></td>
        <td><?= $email2 ?: '&nbsp;' ?></td>
    </tr>
    <?php if ( $contact_web ) : ?>
    <tr>
        <td class="lbl">Website</td>
        <td colspan="2"><?= $contact_web ?></td>
    </tr>
    <?php endif; ?>
</table>

<!-- Preferred Communication -->
<table style="margin-bottom:4px;">
    <tr class="section-hdr"><td colspan="2">Preferred Mode of Communication</td></tr>
    <tr>
        <td class="lbl">Preference(s)</td>
        <td>
            <?php if ( ! empty($pref_comm) ) : ?>
                <?php foreach ( $pref_comm as $method ) : ?>
                    &#10003; <?= esc_html($method) ?>&nbsp;&nbsp;
                <?php endforeach; ?>
            <?php else : ?>
                <em style="color:#999;">Not specified.</em>
            <?php endif; ?>
        </td>
    </tr>
</table>

<!-- Date -->
<?php if ( $form_date ) : ?>
<table style="margin-bottom:4px;">
    <tr>
        <td class="lbl" style="width:35%;">Date</td>
        <td><?= $form_date ?></td>
    </tr>
</table>
<?php endif; ?>

<!-- Signatures -->
<table style="margin-top:16px;">
    <tr>
        <td style="border:none; font-size:8px; width:50%;">
            <br><br><br>
            <br><br><br>
            <br><br><br>
            <br><br><br>
        </td>
       </tr>
       <tr>
        <p style="text-align:center; font-size:9px; margin-top:10px;">
           Global Management Certification Services Pvttd.<br>
           Office: Flat No.402, Plot No.410, Matrusri Nagar, Miyapur, Hyderabad-500 049, India.<br>
         Tel: 040-4855 9001, Email:info@mcsglobal.in, Website: www.mcsglobal.in									

        </p>
       </tr>
</table>

</body>
</html>
