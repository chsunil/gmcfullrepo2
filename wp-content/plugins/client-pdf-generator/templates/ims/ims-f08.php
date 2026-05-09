<?php
/**
 * IMS – F-08 Audit Schedule
 * ACF Group: group_qms_f08 (IMS Track)
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Safe field helper ────────────────────────────────────────────────────────
if ( ! function_exists('f08v') ) {
    function f08v( $key, $post_id, $fallback = '-' ) {
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

// ── Top-level fields ─────────────────────────────────────────────────────────
$org        = function_exists('gmc_get_organization_name')
              ? gmc_get_organization_name($post_id)
              : f08v('organization_name', $post_id);

$objectives = get_field('f08audit_objectives',       $post_id) ?: '';
$ref_no     = f08v('f08Ref_No',                      $post_id);
$location   = f08v('f08location',                    $post_id);
$issue_date = f08v('f08issue_date',                  $post_id);
$temp_sites = f08v('f08Temporary_Sites_if_any',      $post_id);
$standard   = f08v('f08standards',                   $post_id);
$ict        = f08v('f08ict_details_if_any',          $post_id);
$observer   = f08v('f08observers_if_any*',           $post_id);
$interp     = f08v('f08interpreters_if_any*',        $post_id);
$scope      = f08v('f08scope_covered',               $post_id);
$auth_sig   = f08v('f08authorized_signatory',        $post_id);

// IMS Specific Repeater – Use field key
$schedule   = get_field('field_69da78d7fb454',       $post_id) ?: [];

// ── Logo (same base64) ───────────────────────────────────────────────────────
$logo_b64 = 'data:image/jpeg;base64,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';
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
        .header-stage { width: 22%; border: 1px solid #000; vertical-align: middle; text-align: center; font-size: 11px; font-weight: bold; }
        .sched-head th { background: #c8c8c8; text-align: center; font-size: 10px; }
        .sched-date   { width: 12%; text-align: center; }
        .sched-time   { width: 16%; text-align: center; }
        .sched-area   { width: 40%; }
        .sched-auditor{ width: 16%; }
        .sched-auditee{ width: 16%; }
        .label  { font-weight: bold; width: 25%; background: #f0f0f0; }
        .note   { font-size: 9px; color: #333; margin-top: 8px; }
        .no-b   { border: none !important; background: transparent !important; }
        .obj-box { border: 1px solid #000; padding: 8px; margin-bottom: 8px; min-height: 60px; }
    </style>
</head>
<body>

<table>
    <tr>
        <td class="header-logo no-b" rowspan="2">
            <img src="<?= $logo_b64 ?>" alt="Logo">
        </td>
        <td class="header-title">Audit Schedule</td>
        <td class="header-stage" rowspan="2">IMS Track</td>
    </tr>
    <tr>
        <td class="header-form">F-08 &nbsp;<strong>(Version 2.00, 20.03.2016)</strong></td>
    </tr>
</table>

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
        <td><?= esc_html($issue_date) ?></td>
    </tr>
    <tr>
        <td class="label">Temporary Sites if any</td>
        <td colspan="3"><?= esc_html($temp_sites) ?></td>
        <td class="label">Standard(s)</td>
        <td><?= esc_html($standard) ?></td>
    </tr>
    <tr>
        <td class="label">ICT details if any</td>
        <td colspan="5"><?= esc_html($ict) ?></td>
    </tr>
    <tr>
        <td class="label">Observers if any*</td>
        <td colspan="3"><?= nl2br(esc_html($observer)) ?></td>
        <td class="label">Interpreters if any*</td>
        <td><?= nl2br(esc_html($interp)) ?></td>
    </tr>
    <tr>
        <td class="label">Scope Covered</td>
        <td colspan="5"><?= esc_html($scope) ?></td>
    </tr>
</table>

<?php if ( ! empty($objectives) ) : ?>
<div style="font-weight:bold; margin-bottom:4px;">Audit Objectives:</div>
<div class="obj-box"><?= nl2br(esc_html($objectives)) ?></div>
<?php endif; ?>

<table class="sched-head">
    <thead>
        <tr>
            <th class="sched-date">Date</th>
            <th class="sched-time">Time<br><small>(From – To)</small></th>
            <th class="sched-area">Clause</th>
            <th class="sched-auditor">Auditor</th>
            <th class="sched-auditee">Auditee</th>
        </tr>
    </thead>
    <tbody>
        <?php if ( is_array($schedule) && count($schedule) ) : ?>
            <?php foreach ( $schedule as $row ) :
                // Date: prefixed and cloned, usually 'stage1_audit_initial' due to field_0017
                $raw_date = $row['stage1_audit_initial'] ?? $row['f08date'] ?? '';
                $row_date = $raw_date ? date('d/m/Y', strtotime($raw_date)) : '-';

                $from_time = $row['f08_from_time'] ?? '-';
                $to_time   = $row['f08_to_time']   ?? '-';
                $time_str  = ( $from_time !== '-' || $to_time !== '-' )
                             ? esc_html($from_time) . ' – ' . esc_html($to_time)
                             : '-';

                $clause    = $row['clause'] ?? '-';

                $auditor_id   = $row['auditor'] ?? 0;
                $auditor_name = '-';
                if ( $auditor_id && is_numeric($auditor_id) ) {
                    $user = get_userdata( (int) $auditor_id );
                    $auditor_name = $user ? $user->display_name : '-';
                }

                $auditee = $row['auditee'] ?? '-';
            ?>
            <tr>
                <td class="sched-date"><?= esc_html($row_date) ?></td>
                <td class="sched-time"><?= $time_str ?></td>
                <td class="sched-area"><?= nl2br(esc_html($clause)) ?></td>
                <td class="sched-auditor"><?= esc_html($auditor_name) ?></td>
                <td class="sched-auditee"><?= esc_html($auditee) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="5" style="text-align:center;color:#999;">No schedule entries found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<p class="note">
    * Each man-day is equivalent to 8 working hours excluding lunch and travel.<br>
    * Role and responsibility of Observers / Interpreters if any.
</p>

</body>
</html>
