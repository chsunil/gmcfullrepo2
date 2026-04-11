<?php
/**
 * QMS Sheet-6 — Audit Notification Letter
 * Version 5.00, 30.10.2023
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── helpers ───────────────────────────────────────────────────────────────────
function s6v( $key, $pid, $fallback = '' ) {
    $v = get_field( $key, $pid );
    if ( is_array( $v ) && isset( $v['display_name'] ) ) return esc_html( $v['display_name'] );
    return ! empty( $v ) ? esc_html( $v ) : $fallback;
}

// ── pull ACF data ─────────────────────────────────────────────────────────────
$post_id = get_the_ID();

// Date (already d/m/Y from ACF)
$letter_date = s6v( 'sheet6date', $post_id );

// Ref No — seamless clone of field_69b4128404509 (prefix_name:0 → stored under original field name)
// Using field key directly for lookup
$ref_no = get_field( 'field_69b4128404509', $post_id );
if ( empty( $ref_no ) ) $ref_no = '';

// Customer name (seamless clone of field_org_name)
$org = s6v( 'organization_name', $post_id );

// Address — from the address group sub-field head_office
$address_grp = get_field( 'address', $post_id ) ?: [];
$address     = $address_grp['head_office'] ?? '';

// Premise date — stage1_audit_initial (ACF returns Y-m-d via get_field)
$premise_raw  = get_field( 'stage1_audit_initial', $post_id );
$premise_date = '';
if ( $premise_raw ) {
    $dt = DateTime::createFromFormat( 'Y-m-d', $premise_raw );
    if ( $dt ) $premise_date = $dt->format( 'd/m/Y' );
}

// Audit team — seamless clone of field_f05_audit_team repeater (f05_team_name is user id)
$audit_team = get_field( 'f05_audit_team', $post_id ) ?: [];
$team_names = [];
foreach ( $audit_team as $row ) {
    $uid = $row['f05_team_name'] ?? '';
    if ( $uid ) {
        $user = get_userdata( (int) $uid );
        if ( $user ) $team_names[] = 'Mr./Ms. ' . $user->display_name;
    }
}
$team_str = ! empty( $team_names ) ? implode( ', ', $team_names ) : '';

// Certification scheme — for body text
$cert_scheme = s6v( 'cert_scheme', $post_id );

// Signature — same as invoice page
$sign_b64 = '';
$sign_path = get_stylesheet_directory() . '/sneat-assets/img/invoicesign.jpeg';
if ( file_exists( $sign_path ) ) {
    $sign_b64 = 'data:image/jpeg;base64,' . base64_encode( file_get_contents( $sign_path ) );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sheet-6 Audit Notification</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
html { margin-left:18mm; margin-right:18mm; margin-top:8mm; margin-bottom:8mm; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10.5pt;
    color: #000;
    line-height: 1.6;
}
@page {
    size: A4 portrait;
    margin: 12mm 15mm 14mm 15mm;
}
.logo-wrap {
    text-align: center;
    margin-bottom: 14px;
}
.logo-wrap img { max-width: 90px; }
.meta-block { margin-bottom: 12px; }
.meta-block .meta-row { margin-bottom: 2px; }
.meta-block .meta-lbl { display: inline; }
.meta-block .meta-val { font-weight: bold; }
.to-block { margin-bottom: 14px; }
.to-block .to-label { font-weight: bold; }
.to-block .to-org   { font-weight: bold; }
.to-block .to-addr  { font-weight: bold; white-space: pre-wrap; }
.subject { margin-bottom: 10px; }
.salute  { margin-bottom: 10px; }
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
<div class="logo-wrap">
    <img alt="GMCSPL" src="data:image/jpeg;base64,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" />
</div>

<?php
// ── Date & Ref ─────────────────────────────────────────────────────────────
?>
<div class="meta-block">
    <div class="meta-row"><span class="meta-lbl">Date.:&nbsp;&nbsp;</span><span class="meta-val"><?= $letter_date ?></span></div>
    <div class="meta-row"><span class="meta-lbl">Ref No.:&nbsp;</span><span class="meta-val"><?= esc_html( $ref_no ) ?></span></div>
</div>

<!-- To block -->
<div class="to-block">
    <div class="to-label">To,</div>
    <div class="to-org"><?= $org ?></div>
    <?php if ( $address ) : ?>
    <div class="to-addr"><?= esc_html( $address ) ?></div>
    <?php endif; ?>
</div>

<!-- Subject -->
<div class="subject"><strong>Sub:</strong> Stage &#8211; 1 Audit reg.</div>

<!-- Salutation -->
<div class="salute">Dear Sir,</div>

<!-- Body -->
<p class="body-para">
    This is to inform you that <?= $cert_scheme ? esc_html( $cert_scheme ) . ' ' : '' ?>Stage I audit shall be conducted at your premises on Dt.&nbsp;&nbsp;<strong><?= $premise_date ?></strong>
</p>

<p class="team-line">
    The audit team will consist of:&nbsp;&nbsp;<strong><?= esc_html( $team_str ) ?></strong>
</p>

<p class="body-para">Kindly indicate any reservation or objection regarding members of the team before audit date.</p>

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
    <span class="footer-right">Sheet6 QMS (Version 5.00, 30.10.2023)</span>
</div>

</body>
</html>
