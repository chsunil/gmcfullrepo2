<?php
/**
 * QMS F-05a — Audit Team Allocation Plan
 * ACF Group: group_687a08899558e
 * Renders the same 3-page F-05 layout, driven by f05a own fields.
 *
 * Key field mappings (seamless clones, prefix_name=0 → source meta key):
 *   organization_name           — clone field_org_name
 *   proposal_ref_no             — clone field_qms_f05_7
 *   address                     — clone field_6996ae3512093 (group: head_office, main_operative_site, other_sites)
 *   scope_of_certification      — clone field_68173ed2a657a
 *   technical_code_area         — clone field_qms_f05_4
 *   types_and_extent_ict_used_by_the_organization_and_competency_level — clone field_68173ed2f4061
 *   cert_scheme                 — clone field_qms_f05_5
 *   exclusions_only_for_iso_9001— clone field_6817433c24058
 *   f01contact_person           — clone field_68173ed2aa379
 *   stage2_audit_surveillance_audit_date_initial — clone field_0023 (audit_dates)
 *   audit_time_to_be_implemented_for__initial_audit — clone field_68555bf43a45c (group: on_site_stage1, on_site_stage_2)
 *   stage2_intimation_date_surveillance_intimation_date_initial — clone field_0020 (prepared_date / Approved_Date)
 *   f05_audit_team              — repeater (same as f05, shared meta key)
 *
 * F-05a own fields:
 *   stagef05a                   — radio: Stage-1 | Stage-2 | Recertification | Surveillance Audit
 *   prepared_by:                — user field
 *   Approved_By                 — user field
 *   declaration_of_extent_of_interest_of_the_audit_team_members_if_any — text
 *   declaration_of_extent_of_interest_of_the_ict_if_any — text
 *   are_there_any_different_activities_performed_in_different_sites_how_many_similar_sites — text
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── helpers ───────────────────────────────────────────────────────────────────
if ( ! function_exists( 'f05a_v' ) ) {
    function f05a_v( $key, $pid, $fallback = '' ) {
        $v = get_field( $key, $pid );
        if ( is_array( $v ) ) {
            if ( isset( $v['display_name'] ) ) return esc_html( $v['display_name'] );
            return $fallback;
        }
        return ! empty( $v ) ? esc_html( $v ) : $fallback;
    }
}

if ( ! function_exists( 'f05a_cb' ) ) {
    function f05a_cb( $checked = false ) {
        return $checked ? '[X]' : '[  ]';
    }
}

if ( ! function_exists( 'f05a_date' ) ) {
    function f05a_date( $raw, $format = 'd/m/Y' ) {
        if ( empty( $raw ) ) return '';
        $ts = strtotime( $raw );
        return $ts ? date( $format, $ts ) : esc_html( $raw );
    }
}

// ── pull ACF data ─────────────────────────────────────────────────────────────
// Organization name
$org = f05a_v( 'organization_name', $post_id );

// GMCSPL Ref No
$gmcspl_ref_no = f05a_v( 'proposal_ref_no', $post_id );

// Audit stage radio — values: Stage-1 | Stage-2 | Recertification | Surveillance Audit
$stage = get_field( 'stagef05a', $post_id );

// Stage checkbox states
$is_stage1 = ( $stage === 'Stage-1' );
$is_stage2 = ( $stage === 'Stage-2' );
$is_recert = ( $stage === 'Recertification' );
$is_surv   = ( $stage === 'Surveillance Audit' );

// Address group (head_office = On Site, main_operative_site = Remote, other_sites = Temporary)
$address        = get_field( 'address', $post_id ) ?: [];
$addr_onsite    = esc_html( $address['head_office']         ?? '' );
$addr_remote    = esc_html( $address['main_operative_site'] ?? '' );
$addr_temporary = esc_html( $address['other_sites']         ?? '' );

// Scope of certification
$scope = f05a_v( 'scope_of_certification', $post_id );

// Technical Code/Area
$tech_code = f05a_v( 'technical_code_area', $post_id );

// ICT Used (different source field from f05)
$ict_used = f05a_v( 'types_and_extent_ict_used_by_the_organization_and_competency_level', $post_id, 'N/a' );

// Audit Criteria
$audit_criteria = f05a_v( 'cert_scheme', $post_id );

// Exclusions
$exclusions = f05a_v( 'exclusions_only_for_iso_9001', $post_id );

// Prime Contact (f01contact_person group)
$contact     = get_field( 'f01contact_person', $post_id ) ?: [];
$prime_name  = esc_html( $contact['contact_person_name'] ?? '' );
$prime_pos   = esc_html( $contact['contact_position']    ?? '' );
$prime_mob   = esc_html( $contact['contact_mobile']      ?? '' );
$prime_email = esc_html( $contact['contact_email']       ?? '' );

// Audit Objective (static per stage)
$audit_objective_text = "\u{2022}To Review Management System\u{2019}s Documented Information\n"
    . "\u{2022}To evaluate site specific condition and readiness for Stage 2 audit\n"
    . "\u{2022}To Evaluate if internal audits and management reviews are planned, substantiating the readiness for stage 2\n"
    . "\u{2022}To Evaluate the understanding regarding requirements of the standard, in particular with respect to the identification of key performance or significant aspects, processes, objectives and operation of the management system";

$obj_lines = array_filter( array_map( 'trim', explode( "\n", $audit_objective_text ) ) );
$obj_html  = '';
foreach ( $obj_lines as $line ) {
    $line     = ltrim( $line, "\xE2\x80\xa2\xC2\xB7-* " );
    $obj_html .= '<div class="bullet">&bull;' . esc_html( trim( $line ) ) . '</div>';
}
if ( empty( $obj_html ) ) $obj_html = '<span>—</span>';

// ── Audit date — stage-based lookup ──────────────────────────────────────────
$stage_date_map = [
    'Stage-1'           => 'stage1_audit_initial',
    'Stage-2'           => 'stage2_audit_surveillance_audit_date_initial',
    'Recertification'   => 'stage2_audit_surveillance_audit_date_surv2',
    'Surveillance Audit'=> 'stage2_audit_surveillance_audit_date_surv1',
];
$onsite_meta_key   = $stage_date_map[ $stage ] ?? 'stage2_audit_surveillance_audit_date_initial';
$raw_audit_date    = get_post_meta( $post_id, $onsite_meta_key, true );
$audit_date_onsite = f05a_date( $raw_audit_date );
$date_remote       = '';
$date_temp         = '';

// ── Total mandays (from audit_time_to_be_implemented_for__initial_audit group) ─
$mandays_group = get_field( 'audit_time_to_be_implemented_for__initial_audit', $post_id ) ?: [];
$md_stage1     = esc_html( $mandays_group['on_site_stage1']   ?? '' );
$md_stage2     = esc_html( $mandays_group['on_site_stage_2']  ?? '' );
// Show the relevant manday count based on selected stage
$total_md      = $is_stage1 ? $md_stage1 : $md_stage2;

// ── Audit team repeater (shared meta key with f05) ────────────────────────────
$audit_team = get_field( 'f05_audit_team', $post_id ) ?: [];

// ── Prepared By / Approved By (f05a own user fields) ─────────────────────────
$prep_user_raw = get_field( 'prepared_by:', $post_id );
$prep_name     = '';
if ( is_array( $prep_user_raw ) && isset( $prep_user_raw['display_name'] ) ) {
    $prep_name = esc_html( $prep_user_raw['display_name'] );
} elseif ( is_numeric( $prep_user_raw ) && $prep_user_raw ) {
    $u         = get_userdata( (int) $prep_user_raw );
    $prep_name = $u ? esc_html( $u->display_name ) : '';
}

$appr_user_raw = get_field( 'Approved_By', $post_id );
$appr_name     = '';
if ( is_array( $appr_user_raw ) && isset( $appr_user_raw['display_name'] ) ) {
    $appr_name = esc_html( $appr_user_raw['display_name'] );
} elseif ( is_numeric( $appr_user_raw ) && $appr_user_raw ) {
    $u         = get_userdata( (int) $appr_user_raw );
    $appr_name = $u ? esc_html( $u->display_name ) : '';
}

// Prepared/Approved dates — both clone field_0020 → stage2_intimation_date_surveillance_intimation_date_initial
$raw_prep_date = get_post_meta( $post_id, 'stage2_intimation_date_surveillance_intimation_date_initial', true );
$prep_date     = f05a_date( $raw_prep_date );
$appr_date     = $prep_date; // same source field

// ── Declaration fields (f05a own text fields) ─────────────────────────────────
$decl_team = esc_html( get_field( 'declaration_of_extent_of_interest_of_the_audit_team_members_if_any', $post_id ) ?: '' );
$decl_ict  = esc_html( get_field( 'declaration_of_extent_of_interest_of_the_ict_if_any', $post_id ) ?: '' );

// Different sites note
$diff_sites = esc_html( get_field( 'are_there_any_different_activities_performed_in_different_sites_how_many_similar_sites', $post_id ) ?: '' );

// ── Role label map ────────────────────────────────────────────────────────────
$team_role_labels = [
    'lead_auditor'            => 'Lead Auditor',
    'auditor'                 => 'Auditor(s)',
    'technical_expert'        => 'Technical Expert',
    'team_leader_supervision' => 'Team Leader under supervision if any/ Witness Auditor',
    'observer'                => 'Observer(s)',
    'interpreter'             => 'Interpreter(s)',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>F-05a Audit Team Allocation Plan</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
html{ margin-left:15mm; margin-right:15mm; margin-top:10mm; margin-bottom:10mm; }
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
    border: 1px solid #b0bec5;
    padding: 4px 6px;
    vertical-align: top;
}
.lbl { font-weight: bold; }
.logo-cell {
    border: none;
    vertical-align: middle;
    width: 16%;
}
.title-cell {
    border: none;
    text-align: center;
    vertical-align: middle;
    width: 52%;
}
.main-title {
    font-size: 12pt;
    font-weight: bold;
    letter-spacing: 0.5px;
}
.cb-cell {
    border: 1px solid #b0bec5;
    vertical-align: top;
    width: 32%;
    padding: 5px 7px;
}
.cb-list { font-size: 8.5pt; line-height: 1.9; }
.intro {
    font-size: 7.5pt;
    padding: 5px 7px;
    border: 1px solid #b0bec5;
    margin-bottom: 5px;
    line-height: 1.5;
    text-align: justify;
}
.bullet {
    font-size: 8.5pt;
    margin-bottom: 2px;
}
.team-th {
    background-color: #d6eaf8;
    font-weight: bold;
    text-align: center;
    font-size: 8.5pt;
    border: 1px solid #b0bec5;
}
.arrange-block {
    border: 1px solid #b0bec5;
    padding: 5px 8px;
    font-size: 8pt;
    margin-bottom: 4px;
    line-height: 1.5;
}
.arrange-block ul { padding-left: 14px; margin-top: 3px; }
.arrange-block li { margin-bottom: 2px; }
.decl-block {
    border: 1px solid #b0bec5;
    padding: 6px 8px;
    font-size: 7.5pt;
    margin-bottom: 4px;
    line-height: 1.6;
    text-align: justify;
}
.decl-block p { margin-bottom: 4px; }
.decl-block ul { padding-left: 14px; }
.decl-block li { margin-bottom: 2px; }
.decl-title {
    font-weight: bold;
    font-size: 8pt;
    margin: 4px 0 2px 0;
}
.nil-box {
    border: 1px solid #b0bec5;
    text-align: center;
    padding: 5px;
    font-size: 8pt;
    margin-bottom: 5px;
}
.date-val { font-weight: bold; }
.page-footer {
    font-size: 7.5pt;
    color: #777;
    text-align: right;
    margin-top: 5px;
    border-top: 1px solid #ddd;
    padding-top: 3px;
}
.corp-footer {
    text-align: center;
    font-size: 7.5pt;
    margin-top: 10px;
    border-top: 1px solid #aaa;
    padding-top: 5px;
    line-height: 1.7;
}
.page-break { page-break-before: always; }
</style>
</head>
<body>

<?php /* ══════════════════════════════════════════════ PAGE 1 ═══ */ ?>

<!-- Header row -->
<table style="margin-bottom:6px;border:none;">
    <tr>
        <td class="logo-cell">
           <img alt="" src="data:image/jpeg;base64,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" />
        </td>
        <td class="title-cell">
            <div class="main-title">AUDIT TEAM ALLOCATION PLAN</div>
        </td>
        <td class="cb-cell">
            <div class="cb-list">
                <div><?= f05a_cb( $is_stage1 ) ?> Stage-1</div>
                <div><?= f05a_cb( $is_stage2 ) ?> Stage-2</div>
                <div><?= f05a_cb( $is_recert ) ?> Re Certification</div>
                <div><?= f05a_cb( $is_surv )   ?> Surveillance Audit</div>
            </div>
        </td>
    </tr>
</table>

<!-- Intro notice -->
<div class="intro">
    Please find attached the audit plan for the planned audit as per the Audit Programme. If you have any conflict of interest with any of the audit team members or any modification required in the audit plan, ICT, kindly inform within 2 working days or else audit team and plan shall be considered accepted. Any matter may be appealed in accordance with GMCSPL&#39; procedure &#34;Appeals &amp; Complaints (P-06)&#34;.
</div>

<!-- Main details table -->
<table>
    <tr>
        <td class="lbl" style="width:18%;">Organization</td>
        <td class="val" style="width:37%;font-weight:bold;font-size:10pt;"><?= $org ?></td>
        <td class="lbl" style="width:20%;">GMCSPL Ref. No.</td>
        <td class="val" style="width:25%;"><?= $gmcspl_ref_no ?></td>
    </tr>
    <tr>
        <td class="lbl" rowspan="3">Address(s)</td>
        <td colspan="3">
            <span class="lbl">On Site:&nbsp;</span><span class="val"><?= $addr_onsite ?></span>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <span class="lbl">Remote:&nbsp;</span><span class="val"><?= $addr_remote ?></span>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <span class="lbl">Temporary Site(s):&nbsp;</span><span class="val"><?= $addr_temporary ?></span>
        </td>
    </tr>
    <tr>
        <td class="lbl">Scope of Certification</td>
        <td class="val" colspan="3"><?= $scope ?></td>
    </tr>
    <tr>
        <td class="lbl" style="vertical-align:top;">Audit Objective</td>
        <td colspan="3"><?= $obj_html ?></td>
    </tr>
    <tr>
        <td class="lbl">Technical Code/Area</td>
        <td class="val"><?= $tech_code ?></td>
        <td class="lbl">ICT Used</td>
        <td class="val"><?= $ict_used ?></td>
    </tr>
    <tr>
        <td class="lbl">Audit Criteria:</td>
        <td class="val"><?= $audit_criteria ?></td>
        <td class="lbl">Exclusion[s]:</td>
        <td class="val"><?= $exclusions ?></td>
    </tr>
    <tr>
        <td class="lbl">Prime Contact Person</td>
        <td class="val"><?= $prime_name ?></td>
        <td class="lbl">Mobile No</td>
        <td class="val"><?= $prime_mob ?></td>
    </tr>
    <tr>
        <td class="lbl">Designation</td>
        <td class="val"><?= $prime_pos ?></td>
        <td class="lbl">E.mail:</td>
        <td class="val"><?= $prime_email ?></td>
    </tr>
    <tr>
        <td class="lbl" rowspan="3">Audit Date(s)</td>
        <td>
            <span class="lbl">Onsite:&nbsp;</span>
            <span class="val date-val"><?= $audit_date_onsite ?></span>
        </td>
        <td class="lbl">Total Mandays</td>
        <td class="val"><?= $total_md ?></td>
    </tr>
    <tr>
        <td>
            <span class="lbl">Remote:&nbsp;</span>
            <span class="val date-val"><?= $date_remote ?></span>
        </td>
        <td class="lbl">On Site Stage-1</td>
        <td class="val"><?= $md_stage1 ?></td>
    </tr>
    <tr>
        <td>
            <span class="lbl">Temporary Site(s):&nbsp;</span>
            <span class="val date-val"><?= $date_temp ?></span>
        </td>
        <td class="lbl">On Site Stage-2</td>
        <td class="val"><?= $md_stage2 ?></td>
    </tr>
    <?php if ( $diff_sites !== '' ) : ?>
    <tr>
        <td class="lbl" colspan="2">Different activities / similar sites</td>
        <td colspan="2" class="val"><?= $diff_sites ?></td>
    </tr>
    <?php endif; ?>
</table>

<div class="page-footer">F-05a (Version 1.00)</div>

<?php /* ══════════════════════════════════════════════ PAGE 2 ═══ */ ?>
<div class="page-break"></div>

<!-- Audit Team table -->
<table>
    <tr>
        <th class="team-th" style="width:28%;">Role</th>
        <th class="team-th" style="width:30%;">Name</th>
        <th class="team-th" style="width:24%;">Mail/Mobile</th>
        <th class="team-th" style="width:18%;">Onsite/ICT &amp; Remarks</th>
    </tr>
    <?php if ( ! empty( $audit_team ) ) :
        foreach ( $audit_team as $member ) :
            $role_key   = $member['f05_team_role']     ?? '';
            $role_label = $team_role_labels[ $role_key ] ?? esc_html( $role_key );
            $user_id    = $member['f05_team_name']     ?? 0;
            $t_name     = '';
            if ( $user_id ) {
                $u      = get_userdata( (int) $user_id );
                $t_name = $u ? esc_html( $u->display_name ) : '';
            }
            $t_mob = esc_html( $member['f05_team_mail_mobile'] ?? '' );
            $t_ict = esc_html( $member['f05_team_onsite_ict']  ?? '' );
    ?>
    <tr>
        <td class="lbl"><?= $role_label ?></td>
        <td class="val"><?= $t_name ?></td>
        <td class="val"><?= $t_mob ?></td>
        <td style="text-align:center;"><?= $t_ict ?></td>
    </tr>
    <?php endforeach;
    else :
        foreach ( $team_role_labels as $label ) : ?>
    <tr>
        <td class="lbl"><?= esc_html( $label ) ?></td>
        <td></td><td></td><td></td>
    </tr>
    <?php endforeach;
    endif; ?>
    <tr>
        <td class="lbl">Remarks</td>
        <td colspan="3"></td>
    </tr>
</table>

<!-- Facility arrangement request -->
<div class="arrange-block">
    <strong>Please arrange the following for our audit team:</strong>
    <ul>
        <li>Working space and access to a telephone, internet and photocopying facilities</li>
        <li>Knowledgeable person to accompany during the audit.</li>
        <li>Knowledgeable person to handle the selected technology, to present the data/information</li>
        <li>Access to pertinent manuals, procedures and to access the required data/information</li>
        <li>Safety Requirements as per your procedures</li>
        <li>Should you have any query with regard to the arrangements for this audit, please contact us.</li>
    </ul>
</div>

<!-- Prepared By / Approved By -->
<table style="margin-bottom:5px;">
    <tr>
        <td class="lbl" style="width:20%;">Prepared By:</td>
        <td class="val" style="width:35%;"><?= $prep_name ?></td>
        <td class="lbl" style="width:10%;">Date</td>
        <td class="date-val" style="width:35%;"><?= $prep_date ?></td>
    </tr>
    <tr>
        <td class="lbl">Approved By:</td>
        <td class="val"><?= $appr_name ?></td>
        <td class="lbl">Date</td>
        <td class="date-val"><?= $appr_date ?></td>
    </tr>
</table>

<!-- Audited Company Declaration -->
<div class="decl-block">
    <p><strong>Audited Company Declaration:</strong></p>
    <p><strong>we accept the nominated audit team</strong></p>
    <ul>
        <li>we confirm that no member of the audit team has provided consultancy to our company or executed any internal audit in our company.</li>
        <li>we confirm that no Audit team members involved in product design</li>
        <li>we confirm that the audit team members do not have financial, business, relational involvement or interest in our company.</li>
    </ul>
    <p style="margin-top:5px;"><strong>We accept the use of Information and Communication Technology (ICT) as a mode of this Audit / Assessment for the selected Location/s in accordance with information security and data protection measures and regulations if any.</strong></p>
    <p><strong>The use of ICT shall include, but is not limited to:</strong></p>
    <ul>
        <li>Meetings; by means of teleconference facilities, including audio, video and data sharing</li>
        <li>Audit/Assessment of documents and records by means of remote access, either synchronously (in real time) or asynchronously (when applicable)</li>
        <li>Recording of information and evidence by means of still video, video or audio recordings</li>
        <li>Providing visual / audio access to remote or potentially hazardous locations</li>
    </ul>
</div>

<div class="decl-title">Declaration of extent of interest of the audit team members (if any)</div>
<div class="nil-box"><?= $decl_team !== '' ? $decl_team : 'Nil' ?></div>

<div class="decl-title">Declaration of extent of interest of the ICT (if any)</div>
<div class="nil-box"><?= $decl_ict !== '' ? $decl_ict : 'Nil' ?></div>

<div class="page-footer">F-05a (Version 1.00)</div>

<?php /* ══════════════════════════════════════════════ PAGE 3 ═══ */ ?>

<!-- Corporate footer / sign-off -->
<div class="corp-footer">
    Corporate Office: Flat No.402, Plot No.410, Matrusri nagar, Miyapur, Hyderabad-500 049, India.<br>
    Tel.: 040-48559001,&nbsp;&nbsp; E-mail: info@mcsglobal.in &nbsp;&nbsp; Website: www.mcsglobal.in<br>
    <strong>F-05a (Version 1.00)</strong>
</div>

</body>
</html>
