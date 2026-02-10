<?php
// pdf/qms-f08.php

$post_id = $args['post_id'] ?? get_the_ID();

// Pull your ACF values (fallback to '-' for empty values)
$org                     = get_field('organization_name',      $post_id) ?: '-';
$ref_no                  = get_field('Ref_No:',                $post_id) ?: '-';
$location                = get_field('location',               $post_id) ?: '-';
$date                    = get_field('issue_date',             $post_id) ?: '-';
$temp_sites              = get_field('Temporary_Sites_if_any',  $post_id) ?: '-';
$standard                = get_field('standards',              $post_id) ?: '-';
$ict                     = get_field('ict_details_if_any',      $post_id) ?: '-';
$observer                = get_field('observers_if_any*',      $post_id) ?: '-';
$interp                  = get_field('interpreters_if_any*',   $post_id) ?: '-';
$scope                   = get_field('scope_covered',           $post_id) ?: '-';
$authorized_signatory    = get_field('authorized_signatory',    $post_id) ?: '-';
// Use field key for repeater as JSON name is blank
$schedule                = get_field('field_685d1ea9adaa6',     $post_id) ?: [];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body{font-family:sans-serif;font-size:12px}
    table{width:100%;border-collapse:collapse;margin-bottom:1em}
    th,td{border:1px solid #333;padding:4px;vertical-align:top}
    .green{color:#00B050}
    .center{text-align:center}
    .small{font-size:10px}
    .no-border{border:none !important}
    .checkbox-cell input{margin-right:4px}
  </style>
</head>
<body>

<table>
  <!-- HEADER ROWS -->
  <tr>
    <td rowspan="2" class="no-border" style="width:15%">
      <div class="center">
        <!-- your logo as base64 or URL -->
       <img alt="" src="data:image/jpeg;base64,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" />
      
      </div>
    </td>
    <th colspan="4" class="center">Audit Schedule</th>
    <td rowspan="2" class="no-border checkbox-cell" style="width:20%">
      <label><input type="checkbox" checked> Stage-1</label><br>
      <label><input type="checkbox"> Stage-2</label><br>
      <label><input type="checkbox"> Re Certification</label><br>
      <label><input type="checkbox"> Surveillance Audit</label>
    </td>
  </tr>
  <tr>
    <td colspan="4" class="center small">
      F-08 <strong>(Version 2.00, 20.03.2016)</strong>
    </td>
  </tr>

  <!-- ORGANIZATION / REF NO -->
  <tr>
    <td class="green"><strong>Organization:</strong></td>
    <td colspan="3" class="green"><?= esc_html($org) ?></td>
    <td class="green"><strong>Ref No.:</strong></td>
    <td class="green"><?= esc_html($ref_no) ?></td>
  </tr>

  <!-- LOCATION / DATE -->
  <tr>
    <th>Location</th>
    <td colspan="3" class="green"><?= esc_html($location) ?></td>
    <th>Date:</th>
    <td class="green"><?= esc_html($date) ?></td>
  </tr>

  <!-- TEMP SITES / STANDARD -->
  <tr>
    <th>Temporary Sites if any</th>
    <td colspan="3" class="green"><?= esc_html($temp_sites) ?></td>
    <th>Standard(s)</th>
    <td class="green"><?= esc_html($standard) ?></td>
  </tr>

  <!-- ICT / OBSERVERS / INTERPRETERS -->
  <tr>
    <th>ICT details if any</th>
    <td class="green"><?= esc_html($ict) ?></td>
    <th>Observers if any*</th>
    <td class="green"><?= esc_html($observer) ?></td>
    <th>Interpreters if any*</th>
    <td class="green"><?= esc_html($interp) ?></td>
  </tr>

  <!-- SCOPE COVERED -->
  <tr>
    <th>Scope</th>
    <td colspan="5" class="green"><?= esc_html($scope) ?></td>
  </tr>

  <!-- AUTHORIZED SIGNATORY -->
  <tr>
    <th>Authorized Signatory</th>
    <td colspan="5" class="green"><?= esc_html($authorized_signatory) ?></td>
  </tr>

  <!-- SCHEDULE TABLE HEADER -->
  <thead>
    <tr>
      <th>Date</th>
      <th>Time</th>
      <th colspan="2">Activity/Process Area</th>
      <th>Auditor</th>
      <th>Auditee</th>
    </tr>
  </thead>
  <tbody>
    <?php if( is_array($schedule) && count($schedule) ): ?>
      <?php foreach($schedule as $row): ?>
        <tr>
          <td><?= esc_html($row['date'] ?? '-') ?></td>
            <td><?= esc_html($row['time'] ?? '-') ?></td>
          <td colspan="2"><?= nl2br( esc_html($row['activityprocess_area'] ?? '-') ) ?></td>
          <td><?= esc_html($row['auditor'] ?? '-') ?></td>
          <td><?= esc_html($row['auditee'] ?? '-') ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="6" class="center">-</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<p class="small">
  * Each man-day is equivalent to 8 working hours excluding lunch and travel.  
  * Role and responsibility of Observers/interpreters if any.
</p>

</body>
</html>
