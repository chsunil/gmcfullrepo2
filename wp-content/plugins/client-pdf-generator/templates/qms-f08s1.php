<?php
/**
 * QMS – F-08S1 Audit Schedule (Surveillance Audit-1)
 * ACF Group: group_6970cd4a77bdc
 *
 * Seamless clones (prefix_name=0) — read by source meta key:
 *   organization_name   ← field_org_name
 *   proposal_ref_no     ← field_68554bdf55898 (clone name: Ref_No:)
 *   head_office         ← field_68173ed29add4 (clone name: location)
 *   main_operative_site ← field_temporary_sites (clone name: Temporary_Sites_if_any)
 *   cert_scheme         ← field_scope_cert (clone name: standards)
 *   ict_details_if_any  ← field_audited_company_declaration
 *   observers_if_any*   ← broken clone (empty source)
 *   interpreters_if_any*← broken clone (empty source)
 *
 * Own fields:
 *   f08s1scope_covered (textarea)
 *   f08s1authorized_signatory (text)
 *   (unnamed repeater, key field_6970cd4aa63fc): date, time, lead_auditor, coauditor, auditee
 */
if ( ! defined('ABSPATH') ) exit;

if ( ! function_exists('f08s1v') ) {
    function f08s1v( $key, $post_id, $fallback = '-' ) {
        $v = get_field( $key, $post_id );
        if ( empty($v) ) return $fallback;
        if ( is_array($v) ) {
            if ( isset($v['display_name']) ) return esc_html( $v['display_name'] );
            $flat = array_filter( array_map( fn($i) => is_string($i) ? $i : ( $i['label'] ?? $i['value'] ?? '' ), $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fallback;
        }
        return esc_html( $v );
    }
}

// ── Top-level fields (read by source meta key) ────────────────────────────────
$org        = function_exists('gmc_get_organization_name')
              ? gmc_get_organization_name($post_id)
              : f08s1v('organization_name', $post_id);
$ref_no     = get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '-';
$location   = get_post_meta( $post_id, 'head_office', true ) ?: '-';
$date_raw   = get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_surv1', true );
$issue_date = function_exists('gmc_format_date') ? gmc_format_date($date_raw) : esc_html($date_raw);
$temp_sites = esc_html( get_post_meta( $post_id, 'main_operative_site', true ) ?: '-' );
$standard   = esc_html( get_post_meta( $post_id, 'cert_scheme', true ) ?: '-' );
$ict        = f08s1v('ict_details_if_any',       $post_id);
$observer   = f08s1v('observers_if_any*',        $post_id);
$interp     = f08s1v('interpreters_if_any*',     $post_id);
$scope      = f08s1v('f08s1scope_covered',        $post_id);
$auth_sign  = f08s1v('f08s1authorized_signatory', $post_id, '');

// ── Schedule repeater ─────────────────────────────────────────────────────────
$schedule = get_field('field_6970cd4aa63fc', $post_id) ?: [];

// ── Logo ──────────────────────────────────────────────────────────────────────
$LOGO = '';
require __DIR__ . '/_logo.inc.php';

// ── Stage checkboxes — Surv1 ticked ──────────────────────────────────────────
$stages = [
    'stage1'             => 'Stage-1',
    'stage2'             => 'Stage-2',
    'recertification'    => 'Re Certification',
    'surveillance_surv1' => 'Surveillance Audit (Surv1)',
    'surveillance_surv2' => 'Surveillance Audit (Surv2)',
];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 18mm 15mm 15mm 15mm; }
    body   { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 0; line-height: 1.4; }
    table  { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    th, td { border: 1px solid #000; padding: 4px 5px; vertical-align: top; }
    th     { background: #e8e8e8; font-weight: bold; text-align: left; }
    .header-logo  { width: 15%; text-align: center; border: none; vertical-align: middle; }
    .header-logo img { max-width: 80px; max-height: 70px; }
    .header-title { text-align: center; font-size: 14px; font-weight: bold; }
    .header-form  { text-align: center; font-size: 10px; }
    .header-stage { width: 22%; border: 1px solid #000; vertical-align: top; padding: 4px 6px; font-size: 10px; }
    .stage-item   { margin-bottom: 4px; font-size: 10px; }
    .cb-on        { font-weight: bold; }
    .cb-off       { color: #666; }
    .sched-head th { background: #c8c8c8; text-align: center; font-size: 10px; }
    .sched-date   { width: 12%; text-align: center; }
    .sched-time   { width: 16%; text-align: center; }
    .sched-area   { width: 40%; }
    .sched-auditor{ width: 16%; }
    .sched-auditee{ width: 16%; }
    .label  { font-weight: bold; width: 28%; background: #f0f0f0; }
    .note   { font-size: 9px; color: #333; margin-top: 8px; }
    .no-b   { border: none !important; background: transparent !important; }
  </style>
</head>
<body>

<!-- ══ HEADER ════════════════════════════════════════════════════════════════ -->
<table>
  <tr>
    <?php if ( $LOGO ) : ?>
    <td class="header-logo no-b" rowspan="2">
      <img src="<?= $LOGO ?>" alt="Logo">
    </td>
    <?php endif; ?>
    <td class="header-title">Audit Schedule</td>
    <td class="header-stage" rowspan="2">
      <?php foreach ( $stages as $key => $label ) :
        $on = ( $key === 'surveillance_surv1' );
      ?>
      <div class="stage-item <?= $on ? 'cb-on' : 'cb-off' ?>">
        <?= $on ? '[X]' : '[&nbsp;&nbsp;]' ?> <?= esc_html($label) ?>
      </div>
      <?php endforeach; ?>
    </td>
  </tr>
  <tr>
    <td class="header-form">F-08S1 &nbsp;<strong>(Version 1.00)</strong></td>
  </tr>
</table>

<!-- ══ INFO TABLE ════════════════════════════════════════════════════════════ -->
<table>
  <tr>
    <td class="label">Organization</td>
    <td colspan="3"><?= esc_html($org) ?></td>
    <td class="label">Ref No.</td>
    <td><?= esc_html($ref_no) ?></td>
  </tr>
  <tr>
    <td class="label">Location</td>
    <td colspan="3"><?= esc_html($location) ?></td>
    <td class="label">Date</td>
    <td><?= $issue_date ?></td>
  </tr>
  <tr>
    <td class="label">Temporary Sites if any</td>
    <td colspan="3"><?= $temp_sites ?></td>
    <td class="label">Standard(s)</td>
    <td><?= $standard ?></td>
  </tr>
  <tr>
    <td class="label">ICT details if any</td>
    <td colspan="5"><?= $ict ?></td>
  </tr>
  <tr>
    <td class="label">Observers if any*</td>
    <td colspan="3"><?= nl2br(esc_html($observer)) ?></td>
    <td class="label">Interpreters if any*</td>
    <td><?= nl2br(esc_html($interp)) ?></td>
  </tr>
  <tr>
    <td class="label">Scope Covered</td>
    <td colspan="5"><?= $scope ?></td>
  </tr>
  <?php if ( $auth_sign ) : ?>
  <tr>
    <td class="label">Authorized Signatory</td>
    <td colspan="5"><?= esc_html($auth_sign) ?></td>
  </tr>
  <?php endif; ?>
</table>

<!-- ══ SCHEDULE TABLE ════════════════════════════════════════════════════════ -->
<table class="sched-head">
  <tr>
    <th class="sched-date">Date</th>
    <th class="sched-time">Time</th>
    <th class="sched-area">Lead Auditor</th>
    <th class="sched-auditor">Co-Auditor</th>
    <th class="sched-auditee">Auditee</th>
  </tr>
  <?php if ( is_array($schedule) && count($schedule) ) : ?>
    <?php foreach ( $schedule as $row ) :
      $raw_date = $row['date'] ?? '';
      $row_date = $raw_date
          ? ( function_exists('gmc_format_date') ? gmc_format_date($raw_date) : esc_html($raw_date) )
          : '-';
      $time_str = esc_html( $row['time']        ?? '-' );
      $lead_aud = esc_html( $row['lead_auditor'] ?? '-' );
      $co_aud   = esc_html( $row['coauditor']    ?? '-' );
      $auditee  = esc_html( $row['auditee']      ?? '-' );
    ?>
    <tr>
      <td class="sched-date"><?= esc_html($row_date) ?></td>
      <td class="sched-time"><?= $time_str ?></td>
      <td class="sched-area"><?= $lead_aud ?></td>
      <td class="sched-auditor"><?= $co_aud ?></td>
      <td class="sched-auditee"><?= $auditee ?></td>
    </tr>
    <?php endforeach; ?>
  <?php else : ?>
    <tr><td colspan="5" style="text-align:center;color:#999;">No schedule entries found.</td></tr>
  <?php endif; ?>
</table>

<!-- ══ FOOTER ════════════════════════════════════════════════════════════════ -->
<p class="note">
  * Each man-day is equivalent to 8 working hours excluding lunch and travel.<br>
  * Role and responsibility of Observers / Interpreters if any.
</p>

</body>
</html>
