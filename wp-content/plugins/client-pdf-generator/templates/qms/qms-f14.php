<?php
/**
 * QMS F-14 — Confidential Information and No Conflict of Interest Declaration
 * Version 1.00, 02.08.2025
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── helpers ───────────────────────────────────────────────────────────────────
function f14v( $key, $pid, $fallback = '' ) {
    $v = get_field( $key, $pid );
    if ( is_array( $v ) && isset( $v['display_name'] ) ) return esc_html( $v['display_name'] );
    return ! empty( $v ) ? esc_html( $v ) : $fallback;
}

// ── pull ACF data ─────────────────────────────────────────────────────────────
$post_id = get_the_ID();

// Organization / Customer (seamless clone of field_org_name)
$org = f14v( 'organization_name', $post_id );

// Matrix: "This section shall be confirmed by Each audit team member"
// matrix_flexible stores data keyed by the EXACT trimmed row label string (from plugin: $value[$row][$colName])
$matrix_data = get_field( 'This_section_shall_be_confirmed_by_Each_audit_team_member', $post_id );
if ( ! is_array( $matrix_data ) ) $matrix_data = [];

// Row keys MUST match exactly the pipe-separated strings in the ACF field "rows" setting (trimmed).
// Display label => ACF storage key (raw text, no HTML entities)
$matrix_rows = [
    'I confirm that I or the organizations employing me have not provided any consulting or other services to or on behalf of Client during the 24 months period prior to the date hereof directly or indirectly.'
        => 'I confirm that I or the organizations employing me have not provided any consulting or other services to or on behalf of Client during the 24 months period prior to the date hereof directly or indirectly.',
    'I confirm that I will not during the 12 months period succeeding the last day on which I provide Registration Activities with respect to Client pursuant to the Agreement or any future agreement between GMCSPL and me  directly or indirectly provide any consulting or other services (including, but not limited to Registration Activities) to or on behalf of Client.'
        => 'I confirm that I will not during the 12 months period succeeding the last day on which I provide Registration Activities with respect to Client pursuant to the Agreement or any future agreement between GMCSPL and me  directly or indirectly provide any consulting or other services (including, but not limited to Registration Activities) to or on behalf of Client.',
    'I shall keep Confidential Information secret and confidential and not disclose such Confidential Information to any person or entity except for GMCSPL and if applicable a Contracted Registrar providing services to Client.'
        => 'I shall keep Confidential Information secret and confidential and not disclose such Confidential Information to any person or entity except for GMCSPL and if applicable a Contracted Registrar providing services to Client.',
    'I shall deliver to GMCSPL or at GMCSPL\' direction to Client all materials and reports (including all copies) in my possession (including quality manuals reports computerized data contained in any form) upon receipt of a written letter from Client or GMCSPL instructing me to return such materials.'
        => 'I shall deliver to GMCSPL or at GMCSPL\' direction to Client all materials and reports (including all copies) in my possession (including quality manuals reports computerized data contained in any form) upon receipt of a written letter from Client or GMCSPL instructing me to return such materials.',
    'I confirms that; I am independent of the organization being audited and I am not involved in design, development, internal audit, independent review of parts of management system requirements like SOA, Devise master files, EIA, HIRA, QP, Risk assessment &amp; Treatment and etc.'
        => 'I confirms that; I am independent of the organization being audited and I am not involved in design, development, internal audit, independent review of parts of management system requirements like SOA, Devise master files, EIA, HIRA, QP, Risk assessment & Treatment and etc.',
];

// Sign-off repeater (confidentialysign) — each row: role (unnamed select, not stored), date, signaturef14
$sign_rows = get_field( 'confidentialysign', $post_id ) ?: [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>F-14 Conflict of Interest Declaration</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
html { margin-left:15mm; margin-right:15mm; margin-top:10mm; margin-bottom:10mm; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    color: #000;
    line-height: 1.3;
}
@page {
    size: A4 portrait;
    margin: 12mm 10mm 14mm 10mm;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 4px;
}
td, th {
    border: 1px solid #000;
    padding: 4px 6px;
    vertical-align: top;
}
.logo-cell {
    border: none;
    vertical-align: middle;
    width: 18%;
}
.logo-cell img { max-width: 80px; }
.title-cell {
    border: none;
    text-align: center;
    vertical-align: middle;
}
.main-title {
    font-size: 11pt;
    font-weight: bold;
    letter-spacing: 0.3px;
}
.ref-cell {
    border: 1px solid #000;
    vertical-align: top;
    width: 28%;
    padding: 4px 6px;
    font-size: 8pt;
}
.lbl { font-weight: bold; }
.msg-block {
    border: 1px solid #000;
    padding: 5px 8px;
    font-size: 8pt;
    margin-bottom: 4px;
    line-height: 1.5;
    text-align: justify;
}
.section-hdr {
    font-weight: bold;
    font-size: 8.5pt;
    padding: 4px 6px;
    border: 1px solid #000;
    margin-bottom: 0;
}
.matrix-th {
    font-weight: bold;
    text-align: center;
    font-size: 8pt;
    background: #e0e0e0;
}
.obs-cell {
    text-align: center;
    width: 10%;
    font-size: 8pt;
}
.rmk-cell {
    width: 40%;
    font-size: 8pt;
}
.stmt-cell {
    width: 50%;
    font-size: 8pt;
}
.italic-note {
    font-size: 7.5pt;
    font-style: italic;
    border: 1px solid #000;
    padding: 4px 8px;
    margin-top: 4px;
    text-align: justify;
}
.page-footer {
    font-size: 7.5pt;
    color: #555;
    text-align: right;
    margin-top: 5px;
    border-top: 1px solid #aaa;
    padding-top: 3px;
}
</style>
</head>
<body>

<!-- Header -->
<table style="margin-bottom:6px;border:none;">
    <tr>
        <td class="logo-cell">
            <img alt="GMCSPL" src="data:image/jpeg;base64,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" />
        </td>
        <td class="title-cell">
            <div class="main-title">CONFIDENTIAL INFORMATION AND<br>NO CONFLICT OF INTEREST DECLARATION</div>
        </td>
        <td class="ref-cell">
            <div><span class="lbl">F-14 QMS</span></div>
            <div>Version 1.00, 02.08.2025</div>
        </td>
    </tr>
</table>

<!-- Customer / Org row -->
<table style="margin-bottom:4px;">
    <tr>
        <td class="lbl" style="width:25%;">Customer:</td>
        <td style="width:75%;"><?= $org ?></td>
    </tr>
</table>

<!-- Message 1 -->
<div class="msg-block">
    I have executed an agreement Assessor Agreement; F-36 with Global Management Certification Services Pvt. Ltd. to provide Certification related services to GMCSPL and its sub-contractors.<br>
    I am obligated to execute this Confidential Information and No Conflict of Interest Agreement for each client for which I perform Certification Activities.
</div>

<!-- Matrix section header -->
<div class="section-hdr">This section shall be confirmed by Each audit team member</div>

<!-- Matrix table -->
<table style="margin-bottom:4px;">
    <tr>
        <th class="matrix-th stmt-cell">Statement</th>
        <th class="matrix-th obs-cell">Observation</th>
        <th class="matrix-th rmk-cell">Remarks</th>
    </tr>
    <?php foreach ( $matrix_rows as $display_text => $row_key ) :
        $row_data = $matrix_data[ $row_key ] ?? [];
        $obs      = esc_html( $row_data['observation'] ?? '' );
        $rmk      = esc_html( $row_data['remarks']     ?? '' );
    ?>
    <tr>
        <td class="stmt-cell"><?= esc_html( $display_text ) ?></td>
        <td class="obs-cell"><?= $obs ?></td>
        <td class="rmk-cell"><?= $rmk ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Message 2 -->
<div class="msg-block">
    I understand that my obligations under this Confidential Information and No Conflict of Interest Agreement shall survive till the termination of &#8220;Assessor Agreement (F-36)&#8221;.<br>
    I hereby execute this Confidential Information and No Conflict of Interest Agreement with respect to above Client and declare that I have no conflict of interest with the client.
</div>

<!-- Sign-off repeater table -->
<table style="margin-bottom:4px;">
    <tr>
        <th class="matrix-th" style="width:34%;">Role</th>
        <th class="matrix-th" style="width:33%;">Signature</th>
        <th class="matrix-th" style="width:33%;">Date</th>
    </tr>
    <?php if ( ! empty( $sign_rows ) ) :
        foreach ( $sign_rows as $row ) :
            $type = esc_html( $row['type'] ?? '' ); // not stored, so won't be pre-filled on edit
            $s_sig  = esc_html( $row['signaturef14'] ?? '' );
            $s_date = esc_html( $row['date']         ?? '' );
    ?>
    <tr>
        <td style="height:36px;"><?= $type ?></td>
        <td><?= $s_sig ?></td>
        <td><?= $s_date ?></td>
    </tr>
    <?php endforeach;
    else : ?>
    <tr>
        <td style="height:36px;">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <?php endif; ?>
</table>

<!-- Italic footnote -->
<div class="italic-note">
    <em>*Only auditors and technical experts whose all answers are &#8220;Yes&#8221; can sign. If any answer is &#8220;No&#8221; he/she can&#8217;t participate in the subject assessment. The document should be signed on or before the date of assessment.</em>
</div>

<div class="page-footer">F-14 QMS (Version 1.00, 02.08.2025)</div>

</body>
</html>
