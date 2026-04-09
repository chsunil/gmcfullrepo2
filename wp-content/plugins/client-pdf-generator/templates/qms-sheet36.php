<?php
/**
 * QMS Sheet-36 — Surveillance Audit-1 Notification Letter
 * ACF Group: group_bff8a27d39dd
 *
 * Fields (seamless clones, prefix_name=0 — read by source meta key):
 *   date (own date_picker, returns d/m/Y) — letter date
 *   ref_no     → proposal_ref_no  (clone of field_68554bdf55898)
 *   to         → organization_name (clone of field_org_name)
 *   headoffice → head_office (clone of field_68173ed29add4)
 *
 * No subject/team/premises fields in group — read from:
 *   premises_date: stage2_audit_surveillance_audit_date_surv2 (meta)
 *   team: audit_team_allocation_plan repeater (f05s1 group, same post)
 */
if ( ! defined('ABSPATH') ) exit;

// Logo
$LOGO = '';
require __DIR__ . '/_logo.inc.php';

// Letter date — own date_picker in group (returns d/m/Y already)
$letter_date_raw = get_field( 'sheet36date', $post_id );
$letter_date     = $letter_date_raw ? esc_html( $letter_date_raw ) : '';

// Ref No
$ref_no = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '' );

// Organisation name
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

// Address
$address = esc_html( get_post_meta( $post_id, 'head_office', true ) ?: '' );

// Subject — static (no field in group)
$subject = 'Surveillance Audit &#8211; 2 reg.';

// Premises on date — surv1 audit date
$premise_date = gmc_format_date(
    get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_surv2', true )
);

// Audit team — Lead Auditor from audit_team_allocation_plan repeater (f05s1 group, same post)
$team_str  = '';
$team_rows = get_field( 'f05s2audit_team_allocation_plan', $post_id ) ?: [];
foreach ( $team_rows as $tr ) {
    if ( stripos( $tr['role'] ?? '', 'Lead' ) !== false ) {
        $team_str = esc_html( $tr['name'] ?? '' );
        break;
    }
}
if ( ! $team_str && ! empty( $team_rows ) ) {
    $team_str = esc_html( $team_rows[0]['name'] ?? '' );
}

// Certification scheme
$cert_scheme_raw = get_post_meta( $post_id, 'cert_scheme', true );
$cert_scheme     = $cert_scheme_raw ? esc_html( $cert_scheme_raw ) . ' ' : '';

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
<title>Sheet-36 Surveillance Audit-1 Notification</title>
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
.meta-block .meta-lbl { display: inline; }
.meta-block .meta-val { font-weight: bold; }
.to-block { margin-bottom: 14px; }
.to-block .to-label { font-weight: bold; }
.to-block .to-org   { font-weight: bold; }
.to-block .to-addr  { font-weight: bold; white-space: pre-wrap; }
.subject  { margin-bottom: 10px; }
.salute   { margin-bottom: 10px; }
.body-para { margin-bottom: 10px; text-align: justify; }
.team-line { margin-bottom: 10px; }
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
<div class="subject"><strong>Sub:</strong> <?= $subject ?></div>

<!-- Salutation -->
<div class="salute">Dear Sir,</div>

<!-- Body -->
<p class="body-para">
    This is to inform you that <?= $cert_scheme ?>Surveillance Audit &#8211; II shall be conducted at your premises on Dt.&nbsp;&nbsp;<strong><?= $premise_date ?></strong>
</p>

<?php if ( $team_str ) : ?>
<p class="team-line">
    The audit team will consist of:&nbsp;&nbsp;<strong><?= $team_str ?></strong>
</p>
<?php endif; ?>

<p class="body-para">Kindly indicate any reservation or objection regarding members of the team before the audit date.</p>

<p class="body-para">Kindly extend your co-operation to the team.</p>

<p class="body-para">Also please confirm your availability and make necessary arrangements. Also find attached here with audit schedule for your reference.</p>

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
    <span class="footer-right">Sheet36 QMS (Version 1.00)</span>
</div>

</body>
</html>
