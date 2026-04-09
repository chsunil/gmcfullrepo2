<?php
/**
 * QMS F-14S2 — Confidential Information and No Conflict of Interest Declaration
 *              Surveillance Audit-2
 * ACF Group: group_a8577ab07da7
 *
 * Seamless clones:
 *   proposal_ref_no   ← field_68554bdf55898 (clone name: ref_no)
 *   organization_name ← field_org_name (clone name: customer:)
 * Own fields:
 *   This_section_shall_be_confirmed_by_Each_audit_team_member_copy (matrix_flexible)
 *   _copy (group): unnamed select (key field_6970c40554e92), signaturef14 (text), date (date_picker d/m/Y)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Logo
$LOGO = '';
require __DIR__ . '/_logo.inc.php';

// Organization / Customer
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

// Matrix data
$matrix_data = get_field( 'f14s2This_section_shall_be_confirmed_by_Each_audit_team_member_copy', $post_id );
if ( ! is_array( $matrix_data ) ) $matrix_data = [];

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

// Sign-off — GROUP named _copy (single row, not repeater)
$sign_group  = get_field( 'f14s2_copy', $post_id ) ?: [];
$sign_role   = get_field( 'field_12b8d67d7b4f', $post_id ) ?: ''; // unnamed select inside _copy
$sign_sig    = esc_html( $sign_group['signaturef14'] ?? '' );
$sign_date   = esc_html( $sign_group['date']         ?? '' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>F-14S2 Conflict of Interest Declaration</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
html { margin-left:15mm; margin-right:15mm; margin-top:10mm; margin-bottom:10mm; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    color: #000;
    line-height: 1.3;
}
@page { size: A4 portrait; margin: 12mm 10mm 14mm 10mm; }
table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
td, th { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
.logo-cell { border: none; vertical-align: middle; width: 18%; }
.logo-cell img { max-width: 80px; }
.title-cell { border: none; text-align: center; vertical-align: middle; }
.main-title { font-size: 11pt; font-weight: bold; letter-spacing: 0.3px; }
.ref-cell { border: 1px solid #000; vertical-align: top; width: 28%; padding: 4px 6px; font-size: 8pt; }
.lbl { font-weight: bold; }
.msg-block { border: 1px solid #000; padding: 5px 8px; font-size: 8pt; margin-bottom: 4px; line-height: 1.5; text-align: justify; }
.section-hdr { font-weight: bold; font-size: 8.5pt; padding: 4px 6px; border: 1px solid #000; margin-bottom: 0; }
.matrix-th { font-weight: bold; text-align: center; font-size: 8pt; background: #e0e0e0; }
.obs-cell { text-align: center; width: 10%; font-size: 8pt; }
.rmk-cell { width: 40%; font-size: 8pt; }
.stmt-cell { width: 50%; font-size: 8pt; }
.italic-note { font-size: 7.5pt; font-style: italic; border: 1px solid #000; padding: 4px 8px; margin-top: 4px; text-align: justify; }
.page-footer { font-size: 7.5pt; color: #555; text-align: right; margin-top: 5px; border-top: 1px solid #aaa; padding-top: 3px; }
</style>
</head>
<body>

<!-- Header -->
<table style="margin-bottom:6px;border:none;">
    <tr>
        <?php if ( $LOGO ) : ?>
        <td class="logo-cell">
            <img alt="GMCSPL" src="<?= $LOGO ?>">
        </td>
        <?php endif; ?>
        <td class="title-cell">
            <div class="main-title">CONFIDENTIAL INFORMATION AND<br>NO CONFLICT OF INTEREST DECLARATION</div>
            <div style="font-size:8pt;margin-top:2px;">Surveillance Audit &#8211; II</div>
        </td>
        <td class="ref-cell">
            <div><span class="lbl">F-14S2 QMS</span></div>
            <div>Version 1.00</div>
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

<!-- Sign-off group table (single row) -->
<table style="margin-bottom:4px;">
    <tr>
        <th class="matrix-th" style="width:34%;">Role</th>
        <th class="matrix-th" style="width:33%;">Signature</th>
        <th class="matrix-th" style="width:33%;">Date</th>
    </tr>
    <tr>
        <td style="height:36px;"><?= esc_html($sign_role) ?></td>
        <td><?= $sign_sig ?></td>
        <td><?= $sign_date ?></td>
    </tr>
</table>

<!-- Italic footnote -->
<div class="italic-note">
    <em>*Only auditors and technical experts whose all answers are &#8220;Yes&#8221; can sign. If any answer is &#8220;No&#8221; he/she can&#8217;t participate in the subject assessment. The document should be signed on or before the date of assessment.</em>
</div>

<div class="page-footer">F-14S2 QMS (Version 1.00)</div>

</body>
</html>
