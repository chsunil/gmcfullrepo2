<?php
/**
 * IMS – F-25 Assessment Check List
 * ACF Group: group_ims_f25
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$ref_no    = get_field('refno', $post_id) ?: get_field('proposal_ref_no', $post_id) ?: '-';
$standard  = get_field('cert_scheme', $post_id) ?: '-';

$matrix = get_field( 'assessment_check_list', $post_id ) ?: [];

// --------------------------------------------------
// Which stage column(s) to print — set via query var by the AJAX handler
// (user ticks "Print in PDF" beside Initial/Surv-1/Surv-2 column headers)
// Empty / missing = print all 3 (default, matches old behaviour)
// --------------------------------------------------
$all_stage_cols = [
    'initial_certification' => 'Initial',
    'surveillance_1'        => 'Surv-1',
    'surveillance_2'        => 'Surv-2',
];
$selected_keys = get_query_var( 'cpdf_print_stages', [] );
if ( empty( $selected_keys ) || ! is_array( $selected_keys ) ) {
    $selected_keys = array_keys( $all_stage_cols );
}
$print_cols = array_intersect_key( $all_stage_cols, array_flip( $selected_keys ) );
if ( empty( $print_cols ) ) {
    $print_cols = $all_stage_cols; // safety fallback — never print an empty table
}
$single_mode    = ( count( $print_cols ) === 1 );
$data_col_count = count( $print_cols );
$data_col_width = round( 75 / $data_col_count, 2 ) . '%';

$logo_b64 = 'data:image/jpeg;base64,...'; // Omitted
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { size: A4 landscape; margin: 10mm 10mm; }
    body { font-family: Arial, sans-serif; font-size: 8.5px; color: #333; line-height: 1.2; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    th, td { border: 1px solid #555; padding: 4px; vertical-align: middle; text-align: left; }
    th { background: #eaeff2; font-weight: bold; text-align: center; font-size: 8.5px; }
    
    .lbl { background: #f8f9fa; font-weight: bold; width: 22%; }
    .center { text-align: center; }
</style>
</head>
<body>

<table style="border:none;">
    <tr>
        <td style="border:none; width:15%;"><img src="data:image/jpeg;base64,...Logo..." alt="Logo" style="max-height:45px;"></td>
        <td style="border:none; text-align:center;">
            <h1 style="margin:0; font-size:14pt;">Assessment Check List</h1>
            <div style="font-size:9pt;">IMS (9001, 14001 & 45001)</div>
        </td>
        <td style="border:none; width:15%; text-align:right; font-size:7.5px;">F-25<br>Ver 5.00</td>
    </tr>
</table>

<table>
    <tr><td class="lbl">Organization</td><td colspan="3" style="font-weight:bold;"><?= $org ?></td><td class="lbl">Ref. No.</td><td><?= $ref_no ?></td></tr>
    <tr><td class="lbl">Standard</td><td><?= $standard ?></td><td class="lbl">&nbsp;</td><td>&nbsp;</td><td class="lbl">&nbsp;</td><td>&nbsp;</td></tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width:25%;">IMS Requirement / Clause</th>
            <?php foreach ( $print_cols as $col_key => $col_label ) : ?>
                <th style="width:<?php echo esc_attr( $data_col_width ); ?>;">
                    <?php echo $single_mode ? esc_html( 'Evidence / Records (' . $col_label . ')' ) : esc_html( $col_label ); ?>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($matrix)) : ?>
            <?php foreach ($matrix as $row => $cols) : ?>
            <tr>
                <td class="lbl"><?= esc_html($row) ?></td>
                <?php foreach ( $print_cols as $col_key => $col_label ) : ?>
                    <td><?= nl2br( esc_html( $cols[ $col_key ] ?? '' ) ) ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="<?php echo esc_attr( 1 + $data_col_count ); ?>" class="center">No checklist data entered.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
