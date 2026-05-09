<?php
/**
 * IMS F-14 — Confidential Information and No Conflict of Interest Declaration
 * Adapted from QMS F-14 with IMS branding
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function imsf14v( $key, $pid, $fallback = '' ) {
    $v = get_field( $key, $pid );
    if ( is_array( $v ) && isset( $v['display_name'] ) ) return esc_html( $v['display_name'] );
    return ! empty( $v ) ? esc_html( $v ) : $fallback;
}

$post_id = get_the_ID();
$org = imsf14v( 'organization_name', $post_id );

$matrix_data = get_field( 'This_section_shall_be_confirmed_by_Each_audit_team_member', $post_id ) ?: [];

$matrix_rows = [
    'I confirm that I or the organizations employing me have not provided any consulting or other services to or on behalf of Client during the 24 months period prior to the date hereof directly or indirectly.'
        => 'I confirm that I or the organizations employing me have not provided any consulting or other services to or on behalf of Client during the 24 months period prior to the date hereof directly or indirectly.',
    'I confirm that I will not during the 12 months period succeeding the last day on which I provide Registration Activities with respect to Client pursuant to the Agreement or any future agreement between GMCSPL and me  directly or indirectly provide any consulting or other services (including, but not limited to Registration Activities) to or on behalf of Client.'
        => 'I confirm that I will not during the 12 months period succeeding the last day on which I provide Registration Activities with respect to Client pursuant to the Agreement or any future agreement between GMCSPL and me  directly or indirectly provide any consulting or other services (including, but not limited to Registration Activities) to or on behalf of Client.',
    'I shall keep Confidential Information secret and confidential and not disclose such Confidential Information to any person or entity except for GMCSPL and if applicable a Contracted Registrar providing services to Client.'
        => 'I shall keep Confidential Information secret and confidential and not disclose such Confidential Information to any person or entity except for GMCSPL and if applicable a Contracted Registrar providing services to Client.',
    'I shall deliver to GMCSPL or at GMCSPL\' direction to Client all materials and reports (including all copies) in my possession (including quality manuals reports computerized data contained in any form) upon receipt of a written letter from Client or GMCSPL instructing me to return such materials.'
        => 'I shall deliver to GMCSPL or at GMCSPL\' direction to Client all materials and reports (including all copies) in my possession (including quality manuals reports computerized data contained in any form) upon receipt of a written letter from Client or GMCSPL instructing me to return such materials.',
    'I confirms that; I am independent of the organization being audited and I am not involved in design, development, internal audit, independent review of parts of management system requirements like SOA, Devise master files, EIA, HIRA, QP, Risk assessment & Treatment and etc.'
        => 'I confirms that; I am independent of the organization being audited and I am not involved in design, development, internal audit, independent review of parts of management system requirements like SOA, Devise master files, EIA, HIRA, QP, Risk assessment & Treatment and etc.',
];

$sign_rows = get_field( 'confidentialysign', $post_id ) ?: [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 9pt; line-height: 1.3; margin: 15px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
    td, th { border: 1px solid #000; padding: 4px 6px; }
    .main-title { font-size: 11pt; font-weight: bold; text-align: center; }
    .lbl { font-weight: bold; }
    .matrix-th { background: #e0e0e0; font-weight: bold; text-align: center; }
    .page-footer { font-size: 8pt; text-align: right; border-top: 1px solid #aaa; margin-top: 10px; }
</style>
</head>
<body>

<table style="border:none;">
    <tr>
        <td style="border:none; width:20%"><img src="data:image/jpeg;base64,...(same)..." width="80"></td>
        <td style="border:none;" class="main-title">CONFIDENTIAL INFORMATION AND<br>NO CONFLICT OF INTEREST DECLARATION (IMS)</td>
        <td style="width:25%">
            <div class="lbl">F-14 IMS</div>
            <div>Version 1.00, 11.04.2024</div>
        </td>
    </tr>
</table>

<table>
    <tr><td class="lbl" style="width:25%">Customer:</td><td><?= $org ?></td></tr>
</table>

<div style="border:1px solid #000; padding:5px; font-size:8.5pt; margin-bottom:5px;">
    I have executed an agreement Assessor Agreement... [IMS specific text if any, usually same]
</div>

<div class="lbl" style="border:1px solid #000; padding:4px;">This section shall be confirmed by Each audit team member</div>

<table>
    <tr><th class="matrix-th">Statement</th><th class="matrix-th">Observation</th><th class="matrix-th">Remarks</th></tr>
    <?php foreach ($matrix_rows as $display => $key): 
        $row = $matrix_data[$key] ?? []; ?>
    <tr><td><?= $display ?></td><td style="text-align:center"><?= esc_html($row['observation'] ?? '') ?></td><td><?= esc_html($row['remarks'] ?? '') ?></td></tr>
    <?php endforeach; ?>
</table>

<div class="page-footer">F-14 IMS (Version 1.00, 11.04.2024)</div>

</body>
</html>
