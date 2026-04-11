<?php
/**
 * QMS F-05S2 — Surveillance Audit-2 Team Allocation Plan
 * ACF Group: group_8fa3b08f8716
 *
 * Seamless clones (read via source meta key):
 *   organization_name        ← field_org_name
 *   proposal_ref_no          ← field_68554bdf55898 (clone name: f05s2gmcspl_ref_no, display: group)
 *   scope_of_certification   ← field_68173ed2a657a
 *   technical_code           ← field_67fe8e52fc7e0
 *   ict_used                 ← field_qms_f05_8
 *   audit_criteria           ← field_qms_f05_5 (cert_scheme)
 *
 * Own fields:
 *   exclusions (text)
 *   audit_objective (textarea)
 *   (unnamed group key field_69765217382e2): prime_contact, mobile_no, designation, email:
 *   audit_dates (group): on_site, total_mandays, remote, onsite_audit_time, temporary_site, off-site_audit_time
 *   audit_team_allocation_plan (repeater): role, name, mailmobile, onsiteict_&_remarks
 *   (unnamed group key field_6976e70b30626): prepared_by: (user id), prepared_date (d/m/Y),
 *                                             prepared_by:_copy (user id), approved_date (d/m/Y)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── helpers ───────────────────────────────────────────────────────────────────
function f05s2_fmt( $raw, $format = 'd/m/Y' ) {
    if ( empty( $raw ) ) return '';
    $ts = strtotime( $raw );
    return $ts ? date( $format, $ts ) : esc_html( $raw );
}

function f05s2_cb( $checked = false ) {
    return $checked ? '[X]' : '[  ]';
}

// ── pull shared (seamless clone) data via get_post_meta() ─────────────────────
$org     = function_exists('gmc_get_organization_name')
           ? gmc_get_organization_name($post_id)
           : esc_html( get_post_meta( $post_id, 'organization_name', true ) ?: get_post_field('post_title', $post_id) );
$ref_no  = esc_html( get_post_meta( $post_id, 'proposal_ref_no', true ) ?: '' );

// Address — read source meta keys directly (address clone is self-referencing/broken)
$addr_onsite    = esc_html( get_post_meta( $post_id, 'head_office', true ) ?: '' );
$addr_remote    = esc_html( get_post_meta( $post_id, 'main_operative_site', true ) ?: '' );
$addr_temporary = '';

$scope          = esc_html( get_post_meta( $post_id, 'scope_of_certification', true ) ?: '' );
$tech_code      = esc_html( get_post_meta( $post_id, 'technical_code', true )
                          ?: get_post_meta( $post_id, 'technical_code_area', true ) ?: '' );
$ict_used       = esc_html( get_post_meta( $post_id, 'ict_used', true )
                          ?: get_post_meta( $post_id, 'Type_and_extent_of_ICT_used_if_any', true ) ?: 'N/a' );
$audit_criteria = esc_html( get_post_meta( $post_id, 'cert_scheme', true ) ?: '' );
$exclusions     = esc_html( get_field( 'f05s2exclusions', $post_id ) ?: '' );

// Prime contact person — unnamed group key field_69765217382e2
$contact     = get_field( 'field_fce630d72341', $post_id ) ?: [];
$prime_name  = esc_html( $contact['prime_contact'] ?? '' );
$prime_pos   = esc_html( $contact['designation']   ?? '' );
$prime_mob   = esc_html( $contact['mobile_no']     ?? '' );
$prime_email = esc_html( $contact['email:']        ?? '' );

// ── pull own fields ───────────────────────────────────────────────────────────
$audit_objective = get_field( 'f05s2audit_objective', $post_id ) ?: '';

// Audit dates group
$audit_dates_grp  = get_field( 'f05s2audit_dates', $post_id ) ?: [];
$audit_date_onsite = gmc_format_date(
    $audit_dates_grp['stage1_intimation_date_yearly_surveillance_intimation_for_s1_&_s2_surv2']
    ?? get_post_meta( $post_id, 'stage2_audit_surveillance_audit_date_surv2', true )
);
$total_md     = esc_html( $audit_dates_grp['total_mandays']      ?? '' );
$onsite_time  = esc_html( $audit_dates_grp['onsite_audit_time']  ?? '' );
$offsite_time = esc_html( $audit_dates_grp['off-site_audit_time'] ?? '' );

// Audit team repeater
$audit_team = get_field( 'f05s2audit_team_allocation_plan', $post_id ) ?: [];

// Prepared/Approved — unnamed group key field_6976e70b30626
$prep_appr = get_field( 'field_7bdbf8a79069', $post_id ) ?: [];

$prep_uid  = $prep_appr['prepared_by:'] ?? null;
$prep_name = '';
if ( $prep_uid ) {
    $u = get_userdata( (int) $prep_uid );
    $prep_name = $u ? esc_html( $u->display_name ) : '';
}
$prep_date = esc_html( $prep_appr['prepared_date'] ?? '' );

$appr_uid  = $prep_appr['prepared_by:_copy'] ?? null;
$appr_name = '';
if ( $appr_uid ) {
    $u = get_userdata( (int) $appr_uid );
    $appr_name = $u ? esc_html( $u->display_name ) : '';
}
$appr_date = esc_html( $prep_appr['approved_date'] ?? '' );

// ── Audit Objective: format bullet lines ──────────────────────────────────────
$obj_lines = array_filter( array_map( 'trim', explode( "\n", $audit_objective ) ) );
$obj_html  = '';
foreach ( $obj_lines as $line ) {
    $line      = ltrim( $line, "\xE2\x80\xa2\xC2\xB7-* " );
    $obj_html .= '<div class="bullet">&bull;' . esc_html( trim( $line ) ) . '</div>';
}
if ( empty( $obj_html ) ) $obj_html = '<span>—</span>';

// Logo
$LOGO = '';
require __DIR__ . '/_logo.inc.php';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>F-05S2 Surveillance Audit-2 Team Allocation Plan</title>
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
td, th { border: 1px solid #b0bec5; padding: 4px 6px; vertical-align: top; }
.lbl { font-weight: bold; }
.logo-cell { border: none; vertical-align: middle; width: 16%; }
.title-cell { border: none; text-align: center; vertical-align: middle; width: 52%; }
.main-title { font-size: 12pt; font-weight: bold; letter-spacing: 0.5px; }
.cb-cell { border: 1px solid #b0bec5; vertical-align: top; width: 32%; padding: 5px 7px; }
.cb-list { font-size: 8.5pt; line-height: 1.9; }
.intro { font-size: 7.5pt; padding: 5px 7px; border: 1px solid #b0bec5; margin-bottom: 5px; line-height: 1.5; text-align: justify; }
.bullet { font-size: 8.5pt; margin-bottom: 2px; }
.team-th { background-color: #d6eaf8; font-weight: bold; text-align: center; font-size: 8.5pt; }
.sec-hdr td { background-color: #ecf0f1; font-weight: bold; font-size: 8.5pt; }
.decl-box { border: 1px solid #b0bec5; padding: 5px 7px; font-size: 8pt; line-height: 1.5; margin-bottom: 4px; }
.decl-box ul { padding-left: 14px; margin: 3px 0; }
.decl-box li { margin-bottom: 2px; }
.sign-row td { border: 1px solid #b0bec5; padding: 4px 6px; font-size: 8.5pt; }
.page-break { page-break-before: always; }
.footer-row td { border: none; text-align: center; font-size: 7.5pt; color: #555; padding-top: 6px; }
</style>
</head>
<body>

<!-- ═══════════════════════ PAGE 1 ═══════════════════════ -->

<!-- Header -->
<table>
<tr>
    <?php if ( $LOGO ) : ?>
    <td class="logo-cell"><img src="<?= $LOGO ?>" alt="GMCSPL" style="max-height:50px;width:auto;"></td>
    <?php endif; ?>
    <td class="title-cell">
        <div class="main-title">AUDIT TEAM ALLOCATION PLAN</div>
        <div style="font-size:8pt;">Surveillance Audit &#8211; II</div>
    </td>
    <td class="cb-cell">
        <div class="cb-list">
            <div><?= f05s2_cb(false) ?> Stage-1</div>
            <div><?= f05s2_cb(false) ?> Stage-2</div>
            <div><?= f05s2_cb(false) ?> Surveillance Audit (Surv1)</div>
            <div><?= f05s2_cb(true)  ?> Surveillance Audit (Surv2)</div>
            <div><?= f05s2_cb(false) ?> Re Certification</div>
        </div>
    </td>
</tr>
</table>

<!-- Intro notice -->
<div class="intro">
    Please find attached the audit plan for the planned audit as per the Audit Programme.
    If you have any conflict of interest with any of the audit team members or any modification required in the audit plan, ICT, kindly inform within 2 working days or else audit team and plan shall be considered accepted.
    Any matter may be appealed in accordance with GMCSPL&#39; procedure &#8220;Appeals &amp; Complaints (P-06)&#8221;.
</div>

<!-- Main table -->
<table>
<tr>
    <td class="lbl" style="width:20%;">Organization</td>
    <td colspan="3"><?= $org ?></td>
    <td class="lbl" style="width:16%;">GMCSPL Ref. No.</td>
    <td colspan="3"><?= $ref_no ?></td>
</tr>
<tr>
    <td class="lbl" rowspan="3">Address</td>
    <td class="lbl" style="width:8%;">Onsite:</td>
    <td colspan="6"><?= $addr_onsite ?></td>
</tr>
<tr>
    <td class="lbl">Remote:</td>
    <td colspan="6"><?= $addr_remote ?: 'N/A' ?></td>
</tr>
<tr>
    <td class="lbl">Temporary Site(s):</td>
    <td colspan="6"><?= $addr_temporary ?: '&#8212;' ?></td>
</tr>
<tr>
    <td class="lbl">Scope of Certification</td>
    <td colspan="7"><?= $scope ?></td>
</tr>
<tr>
    <td class="lbl">Audit Objective</td>
    <td colspan="7"><?= $obj_html ?></td>
</tr>
<tr>
    <td class="lbl">Technical Code</td>
    <td><?= $tech_code ?></td>
    <td class="lbl">ICT Used</td>
    <td><?= $ict_used ?></td>
    <td class="lbl">Audit Criteria</td>
    <td><?= $audit_criteria ?></td>
    <td class="lbl">Exclusion(s)</td>
    <td><?= $exclusions ?></td>
</tr>
<tr>
    <td class="lbl">Prime Contact Person</td>
    <td><?= $prime_name ?><?= $prime_pos ? "<br><small>{$prime_pos}</small>" : '' ?></td>
    <td class="lbl">Mobile No.</td>
    <td><?= $prime_mob ?></td>
    <td class="lbl">E-mail</td>
    <td colspan="3"><?= $prime_email ?></td>
</tr>
<tr>
    <td class="lbl" rowspan="3">Audit Date(s)</td>
    <td class="lbl">Onsite:</td>
    <td><?= $audit_date_onsite ?></td>
    <td class="lbl">Total Mandays</td>
    <td><?= $total_md ?></td>
    <td class="lbl">Onsite Time</td>
    <td><?= $onsite_time ?></td>
    <td><?= $offsite_time ? 'Offsite: ' . $offsite_time : '' ?></td>
</tr>
<tr>
    <td class="lbl">Remote:</td>
    <td colspan="6">&#8212;</td>
</tr>
<tr>
    <td class="lbl">Temporary:</td>
    <td colspan="6">&#8212;</td>
</tr>

<!-- Audit Team header -->
<tr class="sec-hdr"><td colspan="8">Audit Team</td></tr>
<tr>
    <th class="team-th" colspan="2">Role</th>
    <th class="team-th" colspan="2">Name</th>
    <th class="team-th" colspan="2">Mail / Mobile</th>
    <th class="team-th" colspan="2">Onsite / ICT &amp; Remarks</th>
</tr>

<?php if ( ! empty( $audit_team ) ) : ?>
    <?php foreach ( $audit_team as $member ) :
        $role     = esc_html( $member['role']              ?? '' );
        $m_name   = esc_html( $member['name']              ?? '' );
        $m_mail   = esc_html( $member['mailmobile']        ?? '' );
        $m_onsite = esc_html( $member['onsiteict_&_remarks'] ?? 'Onsite' );
    ?>
    <tr>
        <td colspan="2"><?= $role ?></td>
        <td colspan="2"><?= $m_name ?></td>
        <td colspan="2"><?= $m_mail ?></td>
        <td colspan="2"><?= $m_onsite ?></td>
    </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr><td colspan="8" style="text-align:center;color:#888;font-style:italic;padding:8px;">No audit team members added.</td></tr>
<?php endif; ?>

<!-- Remarks (static — no remarks field in ACF group) -->
<tr class="sec-hdr"><td colspan="8">Remarks</td></tr>
<tr><td colspan="8">&nbsp;</td></tr>
<tr><td colspan="8">
Please arrange the following for our audit team:<br>
&#8226; Working space and access to telephone, internet and photocopying facilities<br>
&#8226; Knowledgeable person to accompany during the audit<br>
&#8226; Knowledgeable person to handle selected technology<br>
&#8226; Access to pertinent manuals and procedures<br>
&#8226; Safety requirements as per procedures<br>
&#8226; Contact us for any clarification regarding audit arrangements
</td></tr>

<!-- Prepared / Approved -->
<tr>
    <td class="lbl">Prepared By</td>
    <td colspan="2"><?= $prep_name ?></td>
    <td class="lbl">Date</td>
    <td colspan="4"><?= $prep_date ?></td>
</tr>
<tr>
    <td class="lbl">Approved By</td>
    <td colspan="2"><?= $appr_name ?></td>
    <td class="lbl">Date</td>
    <td colspan="4"><?= $appr_date ?></td>
</tr>

<!-- Audited Company Declaration -->
<tr class="sec-hdr"><td colspan="8">Audited Company Declaration</td></tr>
<tr><td colspan="8">
<strong>we accept the nominated audit team</strong>
<ul>
<li>we confirm that no member of the audit team has provided consultancy to our company or executed any internal audit in our company.</li>
<li>we confirm that no Audit team members involved in product design</li>
<li>we confirm that the audit team members do not have financial, business, relational involvement or interest in our company.</li>
</ul>
We accept the use of Information and Communication Technology (ICT) as a mode of this Audit / Assessment for the selected Location/s in accordance with information security and data protection measures and regulations if any.
The use of ICT shall include, but is not limited to:
<ul>
<li>Meetings; by means of teleconference facilities, including audio, video and data sharing</li>
<li>Audit/Assessment of documents and records by means of remote access, either synchronously (in real time) or asynchronously (when applicable)</li>
<li>Recording of information and evidence by means of still video, video or audio recordings</li>
<li>Providing visual / audio access to remote or potentially hazardous locations</li>
</ul>
</td></tr>

<tr class="footer-row">
    <td colspan="8">
        Corporate Office: Flat No.402, Plot No.410, Matrusri Nagar, Miyapur, Hyderabad-500049, India.<br>
        Tel: 040-48559001 | Email: info@mcsglobal.in | Website: www.mcsglobal.in<br>
        <strong>F-05S2 (Version 1.00)</strong>
    </td>
</tr>
</table>

<!-- ═══════════════════════ PAGE 2 — Declarations ═══════════════════════ -->
<div class="page-break"></div>

<table>
<tr class="sec-hdr"><td colspan="4">Declaration of extent of interest of the audit team members (if any)</td></tr>
<tr><td colspan="4" style="height:80px;">&nbsp;</td></tr>

<tr class="sec-hdr"><td colspan="4">Declaration of extent of interest of the ICT (if any)</td></tr>
<tr><td colspan="4" style="height:80px;">&nbsp;</td></tr>

<tr class="sec-hdr"><td colspan="4">Audited Company Representative Sign-off</td></tr>
<tr>
    <td class="lbl" style="width:20%;">Name</td>
    <td style="width:30%;">&nbsp;</td>
    <td class="lbl" style="width:20%;">Designation</td>
    <td>&nbsp;</td>
</tr>
<tr>
    <td class="lbl">Signature</td>
    <td>&nbsp;</td>
    <td class="lbl">Date</td>
    <td>&nbsp;</td>
</tr>
</table>

</body>
</html>
