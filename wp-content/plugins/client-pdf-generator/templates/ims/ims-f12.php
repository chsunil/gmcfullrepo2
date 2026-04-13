<?php
/**
 * IMS – F-12 Scope of Certification
 * Based on QMS-F12 with IMS branding
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';

// Organization name
$org = function_exists('gmc_get_organization_name')
    ? gmc_get_organization_name( $post_id )
    : get_post_meta( $post_id, 'organization_name', true );
if ( ! $org ) $org = get_post_field( 'post_title', $post_id );
$org = esc_html( (string)$org );

// Ref No
$ref_no = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '' );

// Address & Site
$address = esc_html( get_post_meta( $post_id, 'head_office', true ) ?: '' );
$site    = esc_html( get_post_meta( $post_id, 'main_operative_site', true ) ?: '' );

// Standard — Integrated list
$standard = esc_html( get_post_meta( $post_id, 'cert_scheme', true ) ?: 'IMS (9001/14001/45001)' );

// Scope
$scope = esc_html( get_post_meta( $post_id, 'scope_of_certification', true ) ?: '' );

// Exclusions
$exclusions = esc_html( get_post_meta( $post_id, 'exclusions_only_for_iso_9001', true ) ?: '' );

// If final scope different
$final_diff = esc_html( get_field( 'if_final_scope_is_different_from_application', $post_id ) ?: '' );

// Confirmation — Organization rep
$org_rep_raw = get_field( 'f12organization', $post_id );
if ( is_array($org_rep_raw) ) {
    $org_rep = esc_html( $org_rep_raw['contact_person_name'] ?? $org_rep_raw['name'] ?? reset($org_rep_raw) ?? '' );
} elseif ( $org_rep_raw ) {
    $org_rep = esc_html( (string)$org_rep_raw );
} else {
    $cp_raw = get_field( 'f01contact_person', $post_id );
    $org_rep = is_array($cp_raw)
        ? esc_html( $cp_raw['contact_person_name'] ?? reset($cp_raw) ?? '' )
        : esc_html( (string)($cp_raw ?: '') );
}

// Confirmation — Team Leader
$team_leader = esc_html( get_post_meta( $post_id, 'audit_team_leader', true )
    ?: get_field( 'f12teamleadercopy', $post_id )
    ?: '' );
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 12mm 10mm 12mm 10mm; }
body  { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 0; padding: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
th, td { border: 1px solid #555; padding: 3px 4px; vertical-align: top; text-align: left; }
.no-border { border: none !important; background: transparent !important; }
.lbl  { font-weight: bold; background: #f2f2f2; white-space: nowrap; width: 35%; }
.title-row th { background: #c6c6c6; font-size: 11px; text-transform: uppercase; text-align: center; }
.section-hdr td { background: #c6c6c6; font-weight: bold; font-size: 9px; text-transform: uppercase; }
.scope-box { background: #f7f7f7; padding: 6px 8px; border-left: 3px solid #00604b; font-size: 9px; line-height: 1.5; }
.note-box { font-size: 7.5px; color: #444; border: 1px solid #ccc; padding: 4px 6px; background: #fafafa; margin-bottom: 6px; }
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
        <th colspan="<?= $LOGO ? 2 : 3 ?>" class="title-row no-border">Scope of Certification (IMS)</th>
        <td class="no-border" style="width:22%; font-size:8px; vertical-align:top; padding-top:2px;">
            <strong>F-12 (Version 1.00, 12.04.2024)</strong><br>IMS Integrated
        </td>
    </tr>
</table>

<!-- Organisation info -->
<table style="margin-bottom:4px;">
    <tr>
        <td class="lbl">Ref. No.</td>
        <td><?= $ref_no ?: '&nbsp;' ?></td>
    </tr>
    <tr>
        <td class="lbl">Organization</td>
        <td style="font-weight:bold;"><?= $org ?></td>
    </tr>
    <tr>
        <td class="lbl">Address</td>
        <td><?= nl2br($address) ?></td>
    </tr>
    <?php if ( $site ) : ?>
    <tr>
        <td class="lbl">Main Operative Site</td>
        <td><?= nl2br($site) ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td class="lbl">Standard(s)</td>
        <td><?= $standard ?></td>
    </tr>
</table>

<!-- Scope Statement -->
<table style="margin-bottom:4px;">
    <tr class="section-hdr"><td>Recommended Scope Statement</td></tr>
    <tr>
        <td>
            <div class="scope-box"><?= $scope ?: '<em style="color:#999;">No scope statement entered.</em>' ?></div>
        </td>
    </tr>
</table>

<!-- Exclusions -->
<table style="margin-bottom:4px;">
    <tr class="section-hdr"><td>Exclusion(s) / Justification</td></tr>
    <tr>
        <td><?= $exclusions ?: '<em style="color:#999;">None.</em>' ?></td>
    </tr>
</table>

<!-- Final scope diff -->
<table style="margin-bottom:4px;">
    <tr class="section-hdr">
        <td>If final scope is different from Application — Team Leader's comments</td>
    </tr>
    <tr>
        <td style="min-height:25px;"><?= nl2br($final_diff) ?: '&nbsp;' ?></td>
    </tr>
</table>

<!-- Notes -->
<div class="note-box">
    &raquo; To be finalised at closing meeting during Stage 2 audit.<br>
    &raquo; GMCSPL Auditors to ensure before finalising the scope: not excludes part of processes, products or services
    (unless allowed by regulatory authorities) from the scope of certification when those processes, products or services
    have an influence on the safety and quality of products.
</div>

<!-- Confirmation -->
<table style="margin-bottom:4px;">
    <tr class="section-hdr">
        <td colspan="2">Confirmation</td>
    </tr>
    <tr>
        <td style="width:50%; text-align:center; font-weight:bold;">Organization</td>
        <td style="width:50%; text-align:center; font-weight:bold;">Team Leader</td>
    </tr>
    <tr>
        <td style="text-align:center; padding:8px 4px;">(Signature)</td>
        <td style="text-align:center; padding:8px 4px;">(Signature)</td>
    </tr>
    <tr>
        <td style="text-align:center;"><?= $org_rep ?: '&nbsp;' ?></td>
        <td style="text-align:center;"><?= $team_leader ?: '&nbsp;' ?></td>
    </tr>
</table>

</body>
</html>
