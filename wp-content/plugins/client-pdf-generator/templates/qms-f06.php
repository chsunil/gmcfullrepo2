<?php
/**
 * QMS – F-06 Documentation Review Report
 * ACF Group: group_qms_f06
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Safe field helper ─────────────────────────────────────────────────────────
if ( ! function_exists('f06v') ) {
    function f06v( $key, $post_id, $fallback = '-' ) {
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

// ── Header fields ─────────────────────────────────────────────────────────────
$org = function_exists('gmc_get_organization_name')
     ? gmc_get_organization_name( $post_id )
     : f06v( 'organization_name', $post_id );
// Fallback: try direct get_post_meta if still empty
if ( empty( $org ) || $org === '-' ) {
    $org = get_post_meta( $post_id, 'organization_name', true ) ?: get_the_title( $post_id );
}

// Date: clone of field_0017 → stored as stage1_audit_initial (Ymd raw)
$raw_date = get_field( 'stage1_audit_initial', $post_id );
$date     = $raw_date ? date( 'd/m/Y', strtotime( $raw_date ) ) : '-';

// Standard: clone of field_68173ed2b0218 → cert_scheme (may be checkbox array)
$standard = f06v( 'cert_scheme', $post_id );

// Auditors: clone of field_6860c39cae165 → lead_auditor (group)
// Sub-field labeled "SIGNATURE" in admin UI → key is 'signature'
$lead_auditor = get_field( 'lead_auditor', $post_id ) ?: [];
if ( is_array( $lead_auditor ) && ! empty( $lead_auditor ) ) {
    $auditors = $lead_auditor['signature']
             ?? $lead_auditor['lead_auditor_name']
             ?? $lead_auditor['auditor_name']
             ?? $lead_auditor['name']
             ?? implode( ', ', array_filter( array_map( fn($v) => is_string($v) ? trim($v) : '', $lead_auditor ) ) )
             ?: '-';
} else {
    $auditors = ( is_string($lead_auditor) && $lead_auditor ) ? $lead_auditor : '-';
}

// Sites: clone of field_68173ed29cf0b → main_operative_site (text)
$sites    = f06v( 'main_operative_site', $post_id );

// Quality Manual Date (direct date_picker, returns d/m/Y)
$qm_date  = f06v( 'date_of_current_issue', $post_id );

// Issue No (direct number field) — name has trailing colon, use field key
$issue_no = get_field( 'field_6868dd5a2bb11', $post_id );
$issue_no = ( $issue_no !== null && $issue_no !== '' ) ? esc_html( $issue_no ) : '-';

// ── Matrix field data ─────────────────────────────────────────────────────────
// matrix_flexible stores rows keyed by the FULL row string from the 'rows' config,
// e.g. "4\tContext of the Organization" — NOT just "4".
// Use field key (field_6868e00f103bb) to avoid issues with colon in field name.
// We build a normalized lookup indexed by just the clause number (text before first tab).
$matrix_raw = get_field( 'field_6868e00f103bb', $post_id ) ?: [];
$matrix = [];
foreach ( $matrix_raw as $row_key => $cols ) {
    // Extract clause number: everything before the first tab character
    $parts  = explode( "\t", $row_key, 2 );
    $clause = trim( $parts[0] );
    if ( $clause !== '' ) {
        $matrix[ $clause ] = $cols;
    }
}

// Helper: get compliance & comments for a clause key
// Note: 'f06comments ' column name has a trailing space in the ACF config
function f06_cell( $matrix, $key, $col ) {
    return isset( $matrix[ $key ][ $col ] ) ? esc_html( $matrix[ $key ][ $col ] ) : '';
}

// ── Clause definitions ────────────────────────────────────────────────────────
// Each entry: [ key, label, is_section_header ]
$clauses = [
    [ '4',    'Context of the Organization',                                               true  ],
    [ '4.1',  'Understanding the Organization and its Context',                            false ],
    [ '4.2',  'Understanding the needs and expectations of interested parties',            false ],
    [ '4.3',  'Determining the scope of the quality management system',                    false ],
    [ '4.4',  'Quality Management System and its Processes',                               false ],
    [ '5',    'Leadership',                                                                true  ],
    [ '5.1',  'Leadership and Commitment',                                                 false ],
    [ '5.2',  'Quality Policy',                                                            false ],
    [ '5.3',  'Organizational roles, responsibilities and authorities',                    false ],
    [ '6',    'Planning',                                                                  true  ],
    [ '6.1',  'Actions to address risks and opportunities',                                false ],
    [ '6.2',  'Quality objectives and planning to achieve them',                           false ],
    [ '6.3',  'Planning of changes',                                                       false ],
    [ '7',    'Support',                                                                   true  ],
    [ '7.1',  'Resources',                                                                 false ],
    [ '7.2',  'Competence',                                                                false ],
    [ '7.3',  'Awareness',                                                                 false ],
    [ '7.4',  'Communication',                                                             false ],
    [ '7.5',  'Documented Information',                                                    false ],
    [ '8',    'Operation',                                                                 true  ],
    [ '8.1',  'Operational Planning and Control',                                          false ],
    [ '8.2',  'Requirements for Products and Services',                                    false ],
    [ '8.3',  'Design and Development of Products and Services',                           false ],
    [ '8.4',  'Control of externally provided processes, products and services',           false ],
    [ '8.5',  'Production and Service Provision',                                          false ],
    [ '8.6',  'Release of Products and Services',                                          false ],
    [ '8.7',  'Control of nonconforming outputs',                                          false ],
    [ '9',    'Performance Evaluation',                                                    true  ],
    [ '9.1',  'Monitoring, measurement, analysis and evaluation',                          false ],
    [ '9.2',  'Internal Audit',                                                            false ],
    [ '9.3',  'Management Review',                                                         false ],
    [ '10',   'Improvement',                                                               true  ],
    [ '10.1', 'General',                                                                   false ],
    [ '10.2', 'Non Conformity and Corrective action',                                      false ],
    [ '10.3', 'Continual Improvement',                                                     false ],
];

// ── Logo ──────────────────────────────────────────────────────────────────────
$logo_b64 = 'data:image/jpeg;base64,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';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 15mm 12mm 12mm 12mm; }
    body  { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; line-height: 1.4; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    th, td { border: 1px solid #000; padding: 4px 5px; vertical-align: top; }
    th { font-weight: bold; text-align: left; background: #ddd; }

    /* Header */
    .h-logo  { width: 14%; border: none; text-align: center; vertical-align: middle; }
    .h-logo img { max-width: 75px; max-height: 65px; }
    .h-title { text-align: center; font-size: 13px; font-weight: bold; vertical-align: middle; }
    .h-form  { text-align: right; font-size: 9px; vertical-align: bottom; white-space: nowrap; }
    .no-b    { border: none !important; background: transparent !important; }

    /* Info rows */
    .lbl { font-weight: bold; width: 22%; background: #f0f0f0; }

    /* Clause table */
    .cl-no   { width: 7%;  text-align: center; }
    .cl-req  { width: 51%; }
    .cl-comp { width: 10%; text-align: center; }
    .cl-comm { width: 32%; }
    .cl-sect { background: #e0e0e0; font-weight: bold; }
    .yes     { font-weight: bold; }
    .no      { color: #555; }
  </style>
</head>
<body>

<!-- ══ HEADER ══════════════════════════════════════════════════════════════ -->
<table>
  <tr>
    <td class="h-logo no-b">
      <img src="<?= $logo_b64 ?>" alt="Logo">
    </td>
    <td class="h-title no-b" style="text-align: center;">DOCUMENTATION REVIEW REPORT</td>
    <td class="h-form no-b">F-06<br><strong>(Version 2.00, 20.03.2016)</strong></td>
  </tr>
</table>

<!-- ══ INFO TABLE ══════════════════════════════════════════════════════════ -->
<table>
  <tr>
    <td class="lbl">Organization</td>
    <td colspan="3"><?= esc_html($org) ?></td>
    <td class="lbl">Date</td>
    <td><?= esc_html($date) ?></td>
  </tr>
  <tr>
    <td class="lbl">Standard</td>
    <td colspan="3"><?= $standard ?></td>
    <td class="lbl">Auditor(s)</td>
    <td><?= esc_html($auditors) ?></td>
  </tr>
  <tr>
    <td class="lbl">Sites</td>
    <td colspan="5"><?= $sites ?></td>
  </tr>
  <tr>
    <td class="lbl">Quality Manual Date</td>
    <td colspan="3"><?= $qm_date ?></td>
    <td class="lbl">Issue No.</td>
    <td><?= $issue_no ?></td>
  </tr>
</table>

<!-- ══ REQUIREMENT TABLE ═══════════════════════════════════════════════════ -->
<table>
  <thead>
    <tr>
      <th class="cl-no">Cl No</th>
      <th class="cl-req">Requirement</th>
      <th class="cl-comp">Compliance</th>
      <th class="cl-comm">Comments</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ( $clauses as [ $key, $label, $is_header ] ) : ?>
      <?php if ( $is_header ) : ?>
        <tr>
          <td colspan="4" class="cl-sect"><?= esc_html($key) ?>.&nbsp; <?= esc_html($label) ?></td>
        </tr>
      <?php else :
        $compliance = f06_cell( $matrix, $key, 'Compliance' );
        $comments   = f06_cell( $matrix, $key, 'f06comments ' ); // trailing space is intentional
      ?>
        <tr>
          <td class="cl-no"><?= esc_html($key) ?></td>
          <td class="cl-req"><?= esc_html($label) ?></td>
          <td class="cl-comp <?= $compliance === 'Yes' ? 'yes' : ( $compliance === 'No' ? 'no' : '' ) ?>">
            <?= $compliance ?: '&nbsp;' ?>
          </td>
          <td class="cl-comm"><?= nl2br( $comments ) ?></td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>
