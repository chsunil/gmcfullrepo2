<?php
/**
 * QMS – F-08a Audit Schedule (Stage 2 / Surveillance)
 * ACF Group: group_6884e410c0ca6
 *
 * All top-level fields are seamless clones (prefix_name=0) — read by source meta key:
 *   organization_name                          ← field_org_name
 *   proposal_ref_no                            ← field_68554bdf55898
 *   head_office                                ← field_68173ed29add4
 *   stage2_audit_surveillance_audit_date_initial ← field_0023
 *   main_operative_site                        ← field_68173ed29cf0b
 *   cert_scheme                                ← field_68173ed2b0218
 *   ict_details_if_any                         ← field_68554e333ee7d
 *   proposed_audit_team                        ← field_6883436eb352e
 *   interpreters_if_any*                       ← field_6867af9198bb9
 *   scope_of_certification                     ← field_68173ed2a657a
 *
 * Repeater: field_6884e71159f4c (_copy)
 *   sub-fields: time (g:i a), date (d/m/Y), activityprocess_area, auditor (text), auditee (text)
 */

if ( ! defined('ABSPATH') ) exit;

// ── Safe field helper ─────────────────────────────────────────────────────────
if ( ! function_exists('f08av') ) {
    function f08av( $key, $post_id, $fallback = '-' ) {
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

// ── Top-level fields (read by source meta key — seamless clone, prefix_name=0) ─
$org        = function_exists('gmc_get_organization_name')
              ? gmc_get_organization_name($post_id)
              : f08av('organization_name', $post_id);

$ref_no     = get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '-';
$location   = get_post_meta( $post_id, 'head_office', true ) ?: '-';
$date_raw   = get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_initial', true );
$issue_date = function_exists('gmc_format_date') ? gmc_format_date($date_raw) : esc_html($date_raw);
$temp_sites = f08av('main_operative_site',      $post_id);
$standard   = f08av('cert_scheme',              $post_id);
$ict        = f08av('ict_details_if_any',       $post_id);
$observer   = f08av('proposed_audit_team',      $post_id);
$interp     = f08av('interpreters_if_any*',     $post_id);
$scope      = f08av('scope_of_certification',   $post_id);

// ── Repeater (use field key for reliable resolution) ──────────────────────────
$schedule   = get_field('field_6884e71159f4c',  $post_id) ?: [];

// ── Stage list — Stage-2 always ticked for this form ─────────────────────────
$stages = [
    'stage1'             => 'Stage-1',
    'stage2'             => 'Stage-2',
    'recertification'    => 'Re Certification',
    'surveillance_surv1' => 'Surveillance Audit (Surv1)',
    'surveillance_surv2' => 'Surveillance Audit (Surv2)',
];

// ── Logo ──────────────────────────────────────────────────────────────────────
$LOGO = '';
require dirname(__DIR__) . '/_logo.inc.php';
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

<!-- ══ HEADER TABLE ══════════════════════════════════════════════════════════ -->
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
        $on = ( $key === 'stage2' );
      ?>
      <div class="stage-item <?= $on ? 'cb-on' : 'cb-off' ?>">
        <?= $on ? '[X]' : '[&nbsp;&nbsp;]' ?> <?= esc_html($label) ?>
      </div>
      <?php endforeach; ?>
    </td>
  </tr>
  <tr>
    <td class="header-form">F-08a &nbsp;<strong>(Version 2.00, 20.03.2016)</strong></td>
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
</table>

<!-- ══ SCHEDULE TABLE ════════════════════════════════════════════════════════ -->
<table class="sched-head">
  <thead>
    <tr>
      <th class="sched-date">Date</th>
      <th class="sched-time">Time</th>
      <th class="sched-area">Activity / Process Area</th>
      <th class="sched-auditor">Auditor</th>
      <th class="sched-auditee">Auditee</th>
    </tr>
  </thead>
  <tbody>
    <?php if ( is_array($schedule) && count($schedule) ) : ?>
      <?php foreach ( $schedule as $row ) :
        // date sub-field return_format is d/m/Y — pass through gmc_format_date for WP format
        $raw_date = $row['date'] ?? '';
        $row_date = $raw_date
            ? ( function_exists('gmc_format_date') ? gmc_format_date($raw_date) : esc_html($raw_date) )
            : '-';

        $time_str = esc_html( $row['time'] ?? '-' );
        $activity = $row['activityprocess_area'] ?? '-';
        $auditor  = esc_html( $row['auditor']  ?? '-' ); // plain text field
        $auditee  = esc_html( $row['auditee']  ?? '-' ); // plain text field
      ?>
      <tr>
        <td class="sched-date"><?= esc_html($row_date) ?></td>
        <td class="sched-time"><?= $time_str ?></td>
        <td class="sched-area"><?= nl2br(esc_html($activity)) ?></td>
        <td class="sched-auditor"><?= $auditor ?></td>
        <td class="sched-auditee"><?= $auditee ?></td>
      </tr>
      <?php endforeach; ?>
    <?php else : ?>
      <tr><td colspan="5" style="text-align:center;color:#999;">No schedule entries found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- ══ FOOTER NOTE ═══════════════════════════════════════════════════════════ -->
<p class="note">
  * Each man-day is equivalent to 8 working hours excluding lunch and travel.<br>
  * Role and responsibility of Observers / Interpreters if any.
</p>

</body>
</html>
