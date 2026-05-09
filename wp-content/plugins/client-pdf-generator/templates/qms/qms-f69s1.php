<?php
/**
 * QMS – F-69S1: ISO 9001:2015 Surveillance Audit-1 Registration Letter
 * ACF Group: group_69707ce350599
 *
 * All fields are seamless clones (prefix_name=0) — read via get_post_meta():
 *   date       → clone of field_0015 → stage1_intimation_date_yearly_surveillance_intimation_for_s1_&_s2_surv1
 *   ref_no     → clone of field_68554bdf55898 → proposal_ref_no
 *   to         → clone of field_org_name → organization_name
 *   headoffice → clone of field_68173ed29add4 → head_office
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

// Letter date — surv1 intimation date
$letter_date = gmc_format_date(
    get_post_meta( $post_id, 'stage1_intimation_date_yearly_surveillance_intimation_for_s1_&_s2_surv1', true )
);

// Ref No
$ref_no = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '' );

// Organisation name
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

// Address
$address = esc_html( get_post_meta( $post_id, 'head_office', true ) ?: '' );

// Surveillance 1 audit date — for body text month reference
$surv1_date = gmc_format_date(
    get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_surv1', true )
);

// Certification scheme
$cert_scheme_raw = get_post_meta( $post_id, 'cert_scheme', true );
$cert_scheme     = $cert_scheme_raw ? esc_html($cert_scheme_raw) . ' ' : '';

// Signature
$sign_b64  = '';
$sign_path = get_stylesheet_directory() . '/sneat-assets/img/invoicesign.jpeg';
if ( file_exists( $sign_path ) ) {
    $sign_b64 = 'data:image/jpeg;base64,' . base64_encode( file_get_contents( $sign_path ) );
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>F-69S1 Surveillance Audit-1 Registration</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10.5pt;
    color: #000;
    line-height: 1.6;
}
@page { size: A4 portrait; margin: 10mm 8mm 10mm 8mm; }
html { margin: 10mm 8mm 10mm 8mm; }
.logo-wrap { text-align: center; margin-bottom: 14px; }
.logo-wrap img { max-height: 70px; width: auto; }
.meta-block { margin-bottom: 12px; }
.meta-block .meta-row { margin-bottom: 2px; }
.meta-lbl { display: inline; }
.meta-val { font-weight: bold; }
.to-block { margin-bottom: 14px; }
.to-block .to-label { font-weight: bold; }
.to-block .to-org   { font-weight: bold; }
.to-block .to-addr  { font-weight: bold; white-space: pre-wrap; }
.subject  { margin-bottom: 10px; font-weight: bold; }
.salute   { margin-bottom: 10px; }
.body-para { margin-bottom: 10px; text-align: justify; }
.sign-block { margin-top: 18px; }
.sign-block p { margin-bottom: 2px; }
.sign-img { height: 60px; margin: 4px 0; }
.footer {
    margin-top: 18px;
    font-size: 7.5pt;
    border-top: 1px solid #000;
    padding-top: 3px;
    display: table;
    width: 100%;
}
.footer-left  { display: table-cell; text-align: left; }
.footer-right { display: table-cell; text-align: right; }
</style>
</head>
<body>

<!-- Logo -->
<?php if ( $LOGO ) : ?>
<div class="logo-wrap">
    <img alt="GMCSPL" src="<?= $LOGO ?>">
</div>
<?php endif; ?>

<!-- Date & Ref -->
<div class="meta-block">
    <div class="meta-row"><span class="meta-lbl">Date.:&nbsp;&nbsp;</span><span class="meta-val"><?= $letter_date ?></span></div>
    <div class="meta-row"><span class="meta-lbl">Ref No.:&nbsp;</span><span class="meta-val"><?= $ref_no ?></span></div>
</div>

<!-- To block -->
<div class="to-block">
    <div class="to-label">To,</div>
    <div class="to-org"><?= $org ?></div>
    <?php if ( $address ) : ?>
    <div class="to-addr"><?= $address ?></div>
    <?php endif; ?>
</div>

<!-- Subject -->
<div class="subject">Sub: <?= $cert_scheme ?>ISO 9001:2015 &#8211; Surveillance Audit &#8211; I &#8211; Registration</div>

<!-- Salutation -->
<div class="salute">Dear Sir,</div>

<!-- Body -->
<p class="body-para">
    At the outset, we thank you for choosing Global Management Certification Services Pvt. Ltd. as your Certification Body. We would like to inform you that the annual <?= $cert_scheme ?>Surveillance Audit &#8211; I is due<?= $surv1_date ? " in the month of&nbsp;<strong>{$surv1_date}</strong>" : '' ?>. Kindly inform us of the possible dates so that we can finalize the schedule with our auditors.
</p>

<p class="body-para">
    We request you to inform us about the changes encountered within your organization with respect to Product, Process, Manpower, and Management etc., to enable us to make an audit schedule.
</p>

<p class="body-para">
    You may provide us the information regarding changes by filling in the enclosed F-22 at the earliest to schedule the audit.
</p>

<p class="body-para">Kindly indicate any reservation or objection regarding members of the team before the audit date.</p>

<p class="body-para">
    A line of confirmation on the audit dates shall be highly appreciable. Kindly note that surveillance audits once within every year is a requirement for maintenance of <?= $cert_scheme ?>certification.
</p>

<p class="body-para">Thanking you,</p>

<p class="body-para">Yours faithfully,</p>

<!-- Sign block -->
<div class="sign-block">
    <?php if ( $sign_b64 ) : ?>
    <img class="sign-img" src="<?= $sign_b64 ?>" alt="Signature">
    <?php else : ?>
    <div style="height:60px;">&nbsp;</div>
    <?php endif; ?>
    <p><strong>For Global Management Certification Services Pvt. Ltd.</strong></p>
    <p>Authorized Signatory</p>
</div>

<!-- Footer -->
<div class="footer">
    <span class="footer-left">Global MCS</span>
</div>

</body>
</html>
