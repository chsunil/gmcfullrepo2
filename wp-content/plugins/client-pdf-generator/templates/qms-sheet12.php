<?php
/**
 * QMS Sheet-12 — Stage 2 Audit Notification Letter
 * ACF Group: group_6883495ce8192
 *
 * Fields (all seamless clones with prefix_name=0):
 *   sheet12date       — clone of field_0020  → stage2_intimation_date_surveillance_intimation_date_initial
 *   sheet12Ref_No     — clone of field_68554bdf55898 → proposal_ref_no
 *   sheet12to         — clone of field_68173ed29add4 → head_office (address)
 *   sheet12subject    — own text, default "Stage – 2 Audit reg."
 *   sheet12premises_on_date — clone of field_0023 → stage2_audit_surveillance_audit_date_initial
 *   sheet12the_audit_team_will_consist_of — user field (return_format: id)
 *
 * org name comes from seamless clone organization_name (shared meta key)
 */
if ( ! defined('ABSPATH') ) exit;

// ── helpers ────────────────────────────────────────────────────────────────────
function s12v( $key, $pid, $fallback = '' ) {
    $v = get_field( $key, $pid );
    if ( is_array( $v ) && isset( $v['display_name'] ) ) return esc_html( $v['display_name'] );
    return ! empty( $v ) ? esc_html( $v ) : $fallback;
}

function s12_format_date( $raw ) {
    if ( ! $raw ) return '';
    // ACF date pickers return Y-m-d or d/m/Y
    $dt = DateTime::createFromFormat( 'd-m-y', $raw )
       ?: DateTime::createFromFormat( 'd/m/Y', $raw );
    return $dt ? $dt->format( 'd/m/Y' ) : esc_html( $raw );
}

// ── pull ACF data ─────────────────────────────────────────────────────────────

// Letter date — clones field_0020 (stage2_intimation_date_surveillance_intimation_date_initial)
$letter_date_raw = get_post_meta( $post_id, 'stage2_intimation_date_surveillance_intimation_date_initial', true );
$letter_date     = gmc_format_date( $letter_date_raw );

// Ref No — seamless clone of proposal_ref_no; use get_post_meta to avoid array return
$ref_no = get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '';

// Organisation name
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

// Address — clones head_office
$address = esc_html( get_post_meta( $post_id, 'head_office', true ) ?: '' );

// Subject — own editable field
$subject = s12v( 'sheet12subject', $post_id, 'Stage &#8211; 2 Audit reg.' );

// Premises on date — clones field_0023 (stage2_audit_surveillance_audit_date_initial)
$premise_date = gmc_format_date(
    get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_initial', true )
);

// Audit team — single user field returns user ID
$team_uid = get_field( 'sheet12the_audit_team_will_consist_of', $post_id );
$team_str = '';
if ( $team_uid ) {
    $user = get_userdata( (int) $team_uid );
    if ( $user ) $team_str = 'Mr./Ms. ' . $user->display_name;
}

// Certification scheme
$cert_scheme = s12v( 'cert_scheme', $post_id );

// Signature
$sign_b64  = '';
$sign_path = get_stylesheet_directory() . '/sneat-assets/img/invoicesign.jpeg';
if ( file_exists( $sign_path ) ) {
    $sign_b64 = 'data:image/jpeg;base64,' . base64_encode( file_get_contents( $sign_path ) );
}

// Logo
$LOGO = '';
require __DIR__ . '/_logo.inc.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sheet-12 Audit Notification</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10.5pt;
    color: #000;
    line-height: 1.6;
}
@page { size: A4 portrait; margin: 10mm 8mm 10mm 8mm; }
html{margin: 10mm 8mm 10mm 8mm;}
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
    <div class="meta-row"><span class="meta-lbl">Ref No.:&nbsp;</span><span class="meta-val"><?= esc_html( $ref_no ) ?></span></div>
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
    This is to inform you that <?= $cert_scheme ? esc_html( $cert_scheme ) . ' ' : '' ?>Stage II audit shall be conducted at your premises on Dt.&nbsp;&nbsp;<strong><?= $premise_date ?></strong>
</p>

<?php if ( $team_str ) : ?>
<p class="team-line">
    The audit team will consist of:&nbsp;&nbsp;<strong><?= esc_html( $team_str ) ?></strong>
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
</div>

<!-- Footer -->
<div class="footer">
    <span class="footer-left">Global MCS</span>
    <span class="footer-right">Sheet12 QMS (Version 5.00, 30.10.2023)</span>
</div>

</body>
</html>
