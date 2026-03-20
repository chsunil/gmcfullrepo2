<?php
/**
 * QMS – F-05s2 Audit Team Allocation Plan (Surveillance Year 2)
 * ACF Group: group_8fa3b08f8716
 * Fields:
 *   s2_organization           — clone of field_org_name
 *   s2_audit_team_allocation_plan — radio
 *   s2_address                — clone of field_68173ed29add4
 *   s2_scope_of_certification — text
 *   s2_audit_objective        — text
 *   s2_technical_code         — clone of field_67fe8e52fc7e0
 *   s2_audit_criteria         — text
 *   s2_prime_contact          — text
 *   s2_gmcspl_ref_no          — text
 *   s2_ict_used               — text
 *   s2_exclusions             — text
 *   s2_mobile_no              — text
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

if ( ! function_exists('f05s2_val') ) {
    function f05s2_val( $v, $fb = '-' ) {
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

$org_raw  = get_field( 's2_organization', $post_id );
$org      = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
          : ( is_array($org_raw) ? f05s2_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );

$plan_type = f05s2_val( get_field( 's2_audit_team_allocation_plan', $post_id ) );
$address   = f05s2_val( get_field( 's2_address', $post_id ) );
$scope     = f05s2_val( get_field( 's2_scope_of_certification', $post_id ) );
$objective = f05s2_val( get_field( 's2_audit_objective', $post_id ) );
$tech_code = f05s2_val( get_field( 's2_technical_code', $post_id ) );
$criteria  = f05s2_val( get_field( 's2_audit_criteria', $post_id ) );
$prime_cnt = f05s2_val( get_field( 's2_prime_contact', $post_id ) );
$ref_no    = f05s2_val( get_field( 's2_gmcspl_ref_no', $post_id ) );
$ict_used  = f05s2_val( get_field( 's2_ict_used', $post_id ) );
$exclusion = f05s2_val( get_field( 's2_exclusions', $post_id ) );
$mobile    = f05s2_val( get_field( 's2_mobile_no', $post_id ) );
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
<h1>Audit Team Allocation Plan</h1>
<h2>F-05s2 &nbsp;|&nbsp; QMS Surveillance Year 2 &nbsp;|&nbsp; Version 1.00</h2>

<div class="section-title">Organisation Details</div>
<table>
    <tr><td class="lbl">Organization</td><td><?= $org ?></td></tr>
    <tr><td class="lbl">Audit Team Allocation Plan</td><td><?= $plan_type ?></td></tr>
    <tr><td class="lbl">Address</td><td><?= $address ?></td></tr>
    <tr><td class="lbl">GMCSPL Ref No.</td><td><?= $ref_no ?></td></tr>
    <tr><td class="lbl">Prime Contact Person</td><td><?= $prime_cnt ?></td></tr>
    <tr><td class="lbl">Mobile No.</td><td><?= $mobile ?></td></tr>
</table>

<div class="section-title">Audit Details</div>
<table>
    <tr><td class="lbl">Scope of Certification</td><td><?= $scope ?></td></tr>
    <tr><td class="lbl">Audit Objective</td><td><?= $objective ?></td></tr>
    <tr><td class="lbl">Technical Code / Area</td><td><?= $tech_code ?></td></tr>
    <tr><td class="lbl">Audit Criteria</td><td><?= $criteria ?></td></tr>
    <tr><td class="lbl">ICT Used</td><td><?= $ict_used ?></td></tr>
    <tr><td class="lbl">Exclusions</td><td><?= $exclusion ?></td></tr>
</table>

<table style="margin-top:30px;">
    <tr>
        <td style="border:none; padding-top:25px; width:50%;">Authorized By: ___________________________</td>
        <td style="border:none; padding-top:25px; width:50%; text-align:right;">Date: ___________________________</td>
    </tr>
</table>
</body>
</html>
