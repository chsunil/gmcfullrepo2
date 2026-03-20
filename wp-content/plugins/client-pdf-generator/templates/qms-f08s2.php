<?php
/**
 * QMS – F-08s2 Audit Schedule (Surveillance Year 2)
 * ACF Group: group_cca0f89f58c2
 * Fields:
 *   s2_organization_name  — clone of field_org_name
 *   s2_Ref_No:            — clone of field_68554bdf55898
 *   s2_location           — clone of field_68173ed29add4
 *   s2_issue_date         — clone of field_audit_date_value
 *   s2_Temporary_Sites_if_any — clone of field_temporary_sites
 *   s2_standards          — clone of field_scope_cert
 *   s2_ict_details_if_any — clone
 *   s2_observers_if_any*  — clone
 *   s2_interpreters_if_any* — clone
 *   s2_scope_covered      — textarea
 *   s2_authorized_signatory — text
 *   field_4b864c1de36d    — repeater (unnamed) with sub-fields:
 *       s2_time, s2_date, s2_activityprocess_area, s2_auditor, s2_auditee
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

if ( ! function_exists('f08s2_val') ) {
    function f08s2_val( $v, $fb = '-' ) {
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

$org_raw  = get_field( 's2_organization_name', $post_id );
$org      = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
          : ( is_array($org_raw) ? f08s2_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );

$ref_no   = f08s2_val( get_field( 's2_Ref_No:', $post_id ) );
$location = f08s2_val( get_field( 's2_location', $post_id ) );
$date     = f08s2_val( get_field( 's2_issue_date', $post_id ) );
$temp     = f08s2_val( get_field( 's2_Temporary_Sites_if_any', $post_id ) );
$standard = f08s2_val( get_field( 's2_standards', $post_id ) );
$ict      = f08s2_val( get_field( 's2_ict_details_if_any', $post_id ) );
$observer = f08s2_val( get_field( 's2_observers_if_any*', $post_id ) );
$interp   = f08s2_val( get_field( 's2_interpreters_if_any*', $post_id ) );
$scope    = f08s2_val( get_field( 's2_scope_covered', $post_id ) );
$auth_sig = f08s2_val( get_field( 's2_authorized_signatory', $post_id ) );
$schedule = get_field( 'field_4b864c1de36d', $post_id );
if ( ! is_array($schedule) ) $schedule = [];
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 8px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
th, td { border: 1px solid #555; padding: 4px 5px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 9px; text-transform: uppercase; }
.lbl { background: #f2f2f2; font-weight: bold; white-space: nowrap; }
.h-logo { border: none; text-align: center; }
.no-border { border: none !important; }
.center { text-align: center; }
.title-row th { background: #c6c6c6; font-size: 12px; text-transform: uppercase; }
.note { font-size: 9px; color: #444; margin-top: 6px; }
</style>
</head>
<body>

<table>
  <thead>
    <tr>
      <?php if ( $LOGO ) : ?>
      <td rowspan="2" class="no-border" style="width:15%; text-align:center;">
        <img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:60px; width:auto;" />
      </td>
      <?php endif; ?>
      <th colspan="<?= $LOGO ? 4 : 5 ?>" class="title-row" style="font-size:12px;">Audit Schedule</th>
      <td rowspan="2" class="no-border" style="width:20%; font-size:9px; vertical-align:top; padding-top:4px;">
        <strong>Surveillance Audit – Year 2</strong>
      </td>
    </tr>
    <tr>
      <td colspan="<?= $LOGO ? 4 : 5 ?>" class="center" style="font-size:9px;">
        F-08s2 &nbsp;|&nbsp; <strong>Version 1.00</strong>
      </td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="lbl">Organization</td>
      <td colspan="3" style="color:#00604b;"><?= $org ?></td>
      <td class="lbl">Ref No.</td>
      <td style="color:#00604b;"><?= $ref_no ?></td>
    </tr>
    <tr>
      <td class="lbl">Location</td>
      <td colspan="3" style="color:#00604b;"><?= $location ?></td>
      <td class="lbl">Issue Date</td>
      <td style="color:#00604b;"><?= $date ?></td>
    </tr>
    <tr>
      <td class="lbl">Temporary Sites if any</td>
      <td colspan="3" style="color:#00604b;"><?= $temp ?></td>
      <td class="lbl">Standard(s)</td>
      <td style="color:#00604b;"><?= $standard ?></td>
    </tr>
    <tr>
      <td class="lbl">ICT Details if any</td>
      <td style="color:#00604b;"><?= $ict ?></td>
      <td class="lbl">Observers if any*</td>
      <td style="color:#00604b;"><?= $observer ?></td>
      <td class="lbl">Interpreters if any*</td>
      <td style="color:#00604b;"><?= $interp ?></td>
    </tr>
    <tr>
      <td class="lbl">Scope Covered</td>
      <td colspan="5" style="color:#00604b;"><?= nl2br( esc_html( (string) get_field('s2_scope_covered', $post_id) ) ) ?></td>
    </tr>
    <tr>
      <td class="lbl">Authorized Signatory</td>
      <td colspan="5" style="color:#00604b;"><?= $auth_sig ?></td>
    </tr>
  </tbody>
</table>

<!-- Schedule rows -->
<table>
  <thead>
    <tr>
      <th>Date</th>
      <th>Time</th>
      <th colspan="2">Activity / Process Area</th>
      <th>Auditor</th>
      <th>Auditee</th>
    </tr>
  </thead>
  <tbody>
    <?php if ( ! empty($schedule) ) : ?>
      <?php foreach ( $schedule as $row ) : ?>
        <tr>
          <td><?= esc_html( $row['s2_date'] ?? '-' ) ?></td>
          <td><?= esc_html( $row['s2_time'] ?? '-' ) ?></td>
          <td colspan="2"><?= nl2br( esc_html( $row['s2_activityprocess_area'] ?? '-' ) ) ?></td>
          <td><?= esc_html( $row['s2_auditor'] ?? '-' ) ?></td>
          <td><?= esc_html( $row['s2_auditee'] ?? '-' ) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else : ?>
      <tr><td colspan="6" class="center" style="color:#888; font-style:italic; padding:10px;">No schedule entries.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<p class="note">* Each man-day is equivalent to 8 working hours excluding lunch and travel. &nbsp;* Role and responsibility of Observers / Interpreters if any.</p>

</body>
</html>
