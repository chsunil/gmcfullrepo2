<?php
/**
 * IMS Sheet-6 — Audit Notification Letter
 * Adapted from QMS Sheet-6 with IMS branding
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function imss6v( $key, $pid, $fallback = '' ) {
    $v = get_field( $key, $pid );
    if ( is_array( $v ) && isset( $v['display_name'] ) ) return esc_html( $v['display_name'] );
    return ! empty( $v ) ? esc_html( $v ) : $fallback;
}

$post_id = get_the_ID();
$letter_date = imss6v( 'sheet6date', $post_id );
$ref_no = get_field( 'field_69b4128404509', $post_id ) ?: '';
$org = imss6v( 'organization_name', $post_id );
$address_grp = get_field( 'address', $post_id ) ?: [];
$address = $address_grp['head_office'] ?? '';

$premise_raw = get_field( 'stage1_audit_initial', $post_id );
$premise_date = $premise_raw ? date('d/m/Y', strtotime($premise_raw)) : '';

$audit_team = get_field( 'f05_audit_team', $post_id ) ?: [];
$team_names = [];
foreach ( $audit_team as $row ) {
    $uid = $row['f05_team_name'] ?? '';
    if ( $uid ) {
        $user = get_userdata( (int) $uid );
        if ( $user ) $team_names[] = 'Mr./Ms. ' . $user->display_name;
    }
}
$team_str = implode( ', ', $team_names );
$cert_scheme = imss6v( 'cert_scheme', $post_id );

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
<style>
    body { font-family: Arial, sans-serif; font-size: 10.5pt; line-height: 1.6; margin: 20mm; }
    .logo-wrap { text-align: center; margin-bottom: 15px; }
    .logo-wrap img { width: 90px; }
    .meta-block { margin-bottom: 12px; }
    .meta-val { font-weight: bold; }
    .footer { margin-top: 20px; font-size: 8pt; border-top: 1px solid #000; padding-top: 5px; }
</style>
</head>
<body>

<div class="logo-wrap">
     <img src="data:image/jpeg;base64,...(same)...">
</div>

<div class="meta-block">
    <div>Date: <span class="meta-val"><?= $letter_date ?></span></div>
    <div>Ref No: <span class="meta-val"><?= $ref_no ?></span></div>
</div>

<div style="margin-bottom:15px;">
    <strong>To,</strong><br>
    <strong><?= $org ?></strong><br>
    <?= esc_html($address) ?>
</div>

<div style="margin-bottom:10px;"><strong>Sub: IMS Stage – 1 Audit Notification</strong></div>

<p>Dear Sir,</p>
<p>This is to inform you that <strong><?= $cert_scheme ?></strong> Stage I audit shall be conducted at your premises on <strong><?= $premise_date ?></strong>.</p>
<p>The audit team: <strong><?= $team_str ?></strong></p>

<p>Kindly confirm availability...</p>

<p>Yours faithfully,</p>
<div style="margin-top:20px;">
    <?php if ($sign_b64): ?><img src="<?= $sign_b64 ?>" height="60"><?php endif; ?>
</div>

<div class="footer">
    <span style="float:left;">Global MCS</span>
    <span style="float:right;">Sheet6 IMS (Version 1.00, 11.04.2024)</span>
</div>

</body>
</html>
