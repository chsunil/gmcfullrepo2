<?php
/**
 * QMS – F-69s2 ISO 9001:2015 Surveillance Audit-2 Registration Letter
 * ACF Group: group_955cf0466343
 * Fields:
 *   s2_date     — clone of field_0015 (date)
 *   s2_ref_no   — clone of field_68554bdf55898
 *   s2_to       — clone of field_org_name
 *   s2_headoffice — clone of field_68173ed29add4 (address)
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

if ( ! function_exists('f69s2_val') ) {
    function f69s2_val( $v, $fb = '-' ) {
        if ( $v === null || $v === '' || $v === false ) return $fb;
        if ( is_array($v) ) {
            foreach ( ['display_name','label','name','value','address','city','state'] as $k ) {
                if ( ! empty($v[$k]) && is_string($v[$k]) ) return esc_html($v[$k]);
            }
            $flat = array_filter( array_map( fn($i) => is_string($i) ? trim($i) : '', $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fb;
        }
        return esc_html( (string) $v );
    }
}

$date_raw = get_field( 's2_date', $post_id );
$date     = $date_raw ? ( preg_match('/^\d{4}-\d{2}-\d{2}/', $date_raw)
            ? date('d/m/Y', strtotime($date_raw)) : esc_html($date_raw) ) : date('d/m/Y');

$ref_no  = f69s2_val( get_field( 's2_ref_no', $post_id ) );

$to_raw  = get_field( 's2_to', $post_id );
$to_org  = ( $to_raw && ! is_array($to_raw) ) ? esc_html($to_raw)
         : ( is_array($to_raw) ? f69s2_val($to_raw) : esc_html( get_post_field('post_title', $post_id) ) );

$addr_raw = get_field( 's2_headoffice', $post_id );
$address  = f69s2_val( $addr_raw );
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 20px; line-height: 1.6; }
h1 { text-align: center; font-size: 14px; font-weight: bold; margin: 0 0 4px 0; text-transform: uppercase; }
h2 { text-align: center; font-size: 10px; margin: 0 0 20px 0; color: #444; }
.h-logo { border: none; text-align: center; }
table { width: 100%; border-collapse: collapse; }
td { border: none; padding: 2px 4px; vertical-align: top; }
.header-tbl td { border: none; }
.ref-date { margin-bottom: 20px; }
.subject { font-weight: bold; text-decoration: underline; margin: 20px 0 10px 0; }
.body-text { margin-bottom: 10px; }
.sign-block { margin-top: 40px; }
</style>
</head>
<body>

<?php if ( $LOGO ) : ?>
<table class="header-tbl" style="margin-bottom:6px;"><tr><td class="h-logo" style="border:none;"><img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:80px; width:auto;" /></td></tr></table>
<?php endif; ?>

<h1>ISO 9001:2015 Surveillance Audit-2 Registration</h1>
<h2>F-69s2 &nbsp;|&nbsp; QMS Surveillance Year 2 &nbsp;|&nbsp; Version 1.00</h2>

<table class="ref-date" style="margin-bottom:16px;">
    <tr>
        <td style="width:50%;">Ref. No.: <strong><?= $ref_no ?></strong></td>
        <td style="width:50%; text-align:right;">Date: <strong><?= $date ?></strong></td>
    </tr>
</table>

<p>To,<br/>
<strong><?= $to_org ?></strong><br/>
<?= $address ?></p>

<div class="subject">Sub: ISO 9001:2015 Surveillance Audit – 2 Registration</div>

<div class="body-text">Dear Sir/Madam,</div>

<div class="body-text">
    We are pleased to inform you that the Surveillance Audit – 2 for your organization has been scheduled as per the agreed audit plan. This letter serves as the official registration confirmation for the surveillance audit to be conducted under ISO 9001:2015 Quality Management System.
</div>

<div class="body-text">
    Please ensure all relevant documents, records, and personnel are available during the audit. The audit team details and schedule will be communicated separately.
</div>

<div class="body-text">
    Kindly acknowledge receipt of this letter and confirm your readiness for the audit.
</div>

<div class="body-text">Thanking you,</div>

<div class="sign-block">
    <p>For GMCSPL Certification Body</p>
    <p style="margin-top:40px;">___________________________<br/>
    Authorized Signatory<br/>
    GMCSPL</p>
</div>

</body>
</html>
