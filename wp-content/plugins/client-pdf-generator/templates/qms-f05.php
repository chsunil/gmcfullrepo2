<?php
/**
 * QMS F-05 — Audit Team Allocation Plan
 * Matches F-05 (Version 2.00, 20.03.2016) reference PDF layout
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── helpers ───────────────────────────────────────────────────────────────────
function f05v( $key, $pid, $fallback = '' ) {
    $v = get_field( $key, $pid );
    if ( is_array( $v ) ) {
        if ( isset( $v['display_name'] ) ) return esc_html( $v['display_name'] );
        return $fallback;
    }
    return ! empty( $v ) ? esc_html( $v ) : $fallback;
}

function f05cb( $checked = false ) {
    return $checked ? '[X]' : '[  ]';
}

function f05_fmt_date( $raw, $format = 'd/m/Y' ) {
    if ( empty( $raw ) ) return '';
    $ts = strtotime( $raw );
    return $ts ? date( $format, $ts ) : esc_html( $raw );
}

function f05footer() {
    echo '<div class="page-footer">F-05 (Version 2.00, 20.03.2016)</div>';
}

// ── pull ACF data ─────────────────────────────────────────────────────────────
$post_id = get_the_ID();

// Organization name
$org = f05v( 'organization_name', $post_id );
// GMCSPL Ref No (clones proposal_ref_no — field_68554bdf55898, seamless prefix_name:0)
$f05gmcspl_ref_no = f05v( 'proposal_ref_no', $post_id );

// Audit stage radio
$stage = get_field( 'f05audit_stage', $post_id );

// Address group (head_office = On Site, main_operative_site = Remote, other_sites = Temporary)
$address        = get_field( 'address', $post_id ) ?: [];
$addr_onsite    = esc_html( $address['head_office']          ?? '' );
$addr_remote    = esc_html( $address['main_operative_site']  ?? '' );
$addr_temporary = esc_html( $address['other_sites']          ?? '' );

// Scope of certification (clones field_68173ed2a657a → name: scope_of_certification)
$scope = f05v( 'scope_of_certification', $post_id );

// Audit Objective (F-05 own textarea)
$audit_objective = get_field( 'f05audit_objective', $post_id );

// Technical Code/Area (clones field_67fe8e52fc7e0 — original name: technical_code_area)
$tech_code = f05v( 'technical_code_area', $post_id );

// ICT Used (clones field_67fe92f174030 — original name: Type_and_extent_of_ICT_used_if_any)
$ict_used = f05v( 'Type_and_extent_of_ICT_used_if_any', $post_id, 'N/a' );

// Audit Criteria (clones field_68173ed2b0218 → name: cert_scheme)
$audit_criteria = f05v( 'cert_scheme', $post_id );

// Exclusions (clones field_6817433c24058 → name: exclusions_only_for_iso_9001)
$exclusions = f05v( 'exclusions_only_for_iso_9001', $post_id );

// Prime Contact (clones f01contact_person group → field_68173ed2aa379)
$contact     = get_field( 'f01contact_person', $post_id ) ?: [];
$prime_name  = esc_html( $contact['contact_person_name'] ?? '' );
$prime_pos   = esc_html( $contact['contact_position']    ?? '' );
$prime_mob   = esc_html( $contact['contact_mobile']      ?? '' );
$prime_email = esc_html( $contact['contact_email']       ?? '' );

// ── Audit date — Onsite: pulled from Audit Dates Table based on selected stage ─
// f05_audit_dates clones field_0014 (stage1_intimation); stage-based lookup gives
// the correct actual audit date regardless of which stage is selected.
$stage_date_map = [
    'stage1'             => 'stage1_audit_initial',
    'stage2'             => 'stage2_audit_surveillance_audit_date_initial',
    'surveillance_surv1' => 'stage2_audit_surveillance_audit_date_surv1',
    'surveillance_surv2' => 'stage2_audit_surveillance_audit_date_surv2',
    'recertification'    => 'stage2_audit_surveillance_audit_date_surv2',
];
$onsite_meta_key   = $stage_date_map[ $stage ] ?? null;
$audit_date_onsite = '';
if ( $onsite_meta_key ) {
    $raw               = get_post_meta( $post_id, $onsite_meta_key, true );
    $audit_date_onsite = f05_fmt_date( $raw );
}
// Remote / Temporary: no longer separate ACF fields (f05_audit_dates is now a
// single-date clone); leave blank — admin can note these in Remarks if needed.
$date_remote = '';
$date_temp   = '';

// Mandays / audit times
// f05_total_mandays now clones field_6855522c5993c (re_certification=23_of_initial_md)
// Access directly to avoid group-key complexity with seamless clone.
$total_md     = esc_html( get_field( 're_certification=23_of_initial_md', $post_id ) ?: '' );
$mandays      = get_field( 'f05_mandays', $post_id ) ?: [];
$onsite_time  = esc_html( $mandays['f05_onsite_audit_time']     ?? '' );
$offsite_time = esc_html( $mandays['f05_offsite_activity_time'] ?? '' );

// Audit team repeater
$audit_team = get_field( 'f05_audit_team', $post_id ) ?: [];

// Remarks
$remarks = f05v( 'f05_remarks', $post_id );

// Prepared By — name is now a user field (return_format: array)
// date clones field_0014 → key: stage1_intimation_date_yearly_surveillance_intimation_for_s1_&_s2_initial
$prep      = get_field( 'f05_prepared_by', $post_id ) ?: [];
$prep_user = $prep['f05_prepared_by_name'] ?? null;
$prep_name = '';
if ( is_array( $prep_user ) && isset( $prep_user['display_name'] ) ) {
    $prep_name = esc_html( $prep_user['display_name'] );
} elseif ( is_numeric( $prep_user ) && $prep_user ) {
    $u         = get_userdata( (int) $prep_user );
    $prep_name = $u ? esc_html( $u->display_name ) : '';
}
$prep_date = f05_fmt_date( $prep['stage1_intimation_date_yearly_surveillance_intimation_for_s1_&_s2_initial'] ?? '' );

// Approved By — name is plain text; date clones field_0014 (same key as prepared_by)
$appr      = get_field( 'f05_approved_by', $post_id ) ?: [];
$appr_name = esc_html( $appr['f05_approved_by_name'] ?? '' );
$appr_date = f05_fmt_date( $appr['stage1_intimation_date_yearly_surveillance_intimation_for_s1_&_s2_initial'] ?? '' );

// Page 2 — Declaration textareas (new in fo5updated2)
$decl_team = esc_html( get_field( 'f05declaration_of_extent_of_interest_of_the_audit_team_members_if_any', $post_id ) ?: '' );
$decl_ict  = esc_html( get_field( 'f05declaration_of_extent_of_interest_of_the_ict_if_any', $post_id ) ?: '' );

// Page 3 — Final sign-off group (unnamed group field_69b4163df9108; sub-fields stored at post level)
$fin_user        = get_field( 'fo5finalname', $post_id );
$fin_name        = '';
if ( is_array( $fin_user ) && isset( $fin_user['display_name'] ) ) {
    $fin_name = esc_html( $fin_user['display_name'] );
}
$fin_designation = esc_html( get_field( 'designation',      $post_id ) ?: '' );
$fin_signature   = esc_html( get_field( 'fo5finsignature',  $post_id ) ?: '' );
$fin_date        = esc_html( get_field( 'f05findate',       $post_id ) ?: '' ); // already d/m/Y

// ── Stage checkbox states ─────────────────────────────────────────────────────
$is_stage1 = ( $stage === 'stage1' );
$is_stage2 = ( $stage === 'stage2' );
$is_recert = ( $stage === 'recertification' );
$is_surv   = in_array( $stage, [ 'surveillance_surv1', 'surveillance_surv2' ] );

// ── Audit Objective: format bullet lines for HTML ─────────────────────────────
$obj_lines = array_filter( array_map( 'trim', explode( "\n", $audit_objective ?? '' ) ) );
$obj_html  = '';
foreach ( $obj_lines as $line ) {
    $line      = ltrim( $line, "\xE2\x80\xa2\xC2\xB7-* " ); // strip bullet chars
    $obj_html .= '<div class="bullet">&bull;' . esc_html( trim( $line ) ) . '</div>';
}
if ( empty( $obj_html ) ) {
    $obj_html = '<span>—</span>';
}

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
<title>F-05 Audit Team Allocation Plan</title>
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
.lbl {
    font-weight: bold;
}
.val {
}
/* Header */
.logo-cell {
    border: none;
    vertical-align: middle;
    width: 16%;
}
.logo-text {
    font-size: 11pt;
    font-weight: bold;
}
.logo-sub {
    font-size: 7pt;
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
/* Intro */
.intro {
    font-size: 7.5pt;
    padding: 5px 7px;
    border: 1px solid #b0bec5;
    margin-bottom: 5px;
    line-height: 1.5;
    text-align: justify;
}
/* Bullet lines in audit objective */
.bullet {
    font-size: 8.5pt;
    margin-bottom: 2px;
}
/* Audit team header row */
.team-th {
    background-color: #d6eaf8;
    font-weight: bold;
    text-align: center;
    font-size: 8.5pt;
    border: 1px solid #b0bec5;
}
/* Facility requirements block */
.arrange-block {
    border: 1px solid #b0bec5;
    padding: 5px 8px;
    font-size: 8pt;
    margin-bottom: 4px;
    line-height: 1.5;
}
.arrange-block ul {
    padding-left: 14px;
    margin-top: 3px;
}
.arrange-block li { margin-bottom: 2px; }
/* Declaration block */
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
/* Date values in red like PDF */
.date-val {
    font-weight: bold;
}
/* Page footer */
.page-footer {
    font-size: 7.5pt;
    color: #777;
    text-align: right;
    margin-top: 5px;
    border-top: 1px solid #ddd;
    padding-top: 3px;
}
/* Corporate footer (page 3) */
.corp-footer {
    text-align: center;
    font-size: 7.5pt;
    margin-top: 10px;
    border-top: 1px solid #aaa;
    padding-top: 5px;
    line-height: 1.7;
}
/* Page break */
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
                <div><?= f05cb( $is_stage1 ) ?> Stage-1</div>
                <div><?= f05cb( $is_stage2 ) ?> Stage-2</div>
                <div><?= f05cb( $is_recert ) ?> Re Certification</div>
                <div><?= f05cb( $is_surv )   ?> Surveillance Audit</div>
            </div>
        </td>
    </tr>
</table>

<!-- Intro notice -->
<div class="intro">
    Please find attached the audit plan for the planned audit as per the Audit Programme. If you have any conflict of interest with any of the audit team members or any modification required in the audit plan, ICT, kindly inform with in 2 working days or else audit team and plan shall be considered accepted. Any matter may be appealed in accordance with GMCSPL&#39; procedure &#34;Appeals &amp; Complaints (P-06)&#34;.
</div>

<!-- Main details table -->
<table>
    <!-- Organization + GMCSPL Ref -->
    <tr>
        <td class="lbl" style="width:18%;">Organization</td>
        <td class="val" style="width:37%;font-weight:bold;font-size:10pt;"><?= $org ?></td>
        <td class="lbl" style="width:20%;">GMCSPL Ref. No.</td>
        <td class="val" style="width:25%;"><?= $f05gmcspl_ref_no ?></td>
    </tr>
    <!-- Address — On Site -->
    <tr>
        <td class="lbl" rowspan="3">Address(s)</td>
        <td colspan="3">
            <span class="lbl">On Site:&nbsp;</span><span class="val"><?= $addr_onsite ?></span>
        </td>
    </tr>
    <!-- Address — Remote -->
    <tr>
        <td colspan="3">
            <span class="lbl">Remote:&nbsp;</span><span class="val"><?= $addr_remote ?></span>
        </td>
    </tr>
    <!-- Address — Temporary -->
    <tr>
        <td colspan="3">
            <span class="lbl">Temporary Site(s):&nbsp;</span><span class="val"><?= $addr_temporary ?></span>
        </td>
    </tr>
    <!-- Scope of Certification -->
    <tr>
        <td class="lbl">Scope of Certification</td>
        <td class="val" colspan="3"><?= $scope ?></td>
    </tr>
    <!-- Audit Objective -->
    <tr>
        <td class="lbl" style="vertical-align:top;">Audit Objective</td>
        <td colspan="3"><?= $obj_html ?></td>
    </tr>
    <!-- Technical Code | ICT Used -->
    <tr>
        <td class="lbl">Technical Code/Area</td>
        <td class="val"><?= $tech_code ?></td>
        <td class="lbl">ICT Used</td>
        <td class="val"><?= $ict_used ?></td>
    </tr>
    <!-- Audit Criteria | Exclusions -->
    <tr>
        <td class="lbl">Audit Criteria:</td>
        <td class="val"><?= $audit_criteria ?></td>
        <td class="lbl">Exclusion[s]:</td>
        <td class="val"><?= $exclusions ?></td>
    </tr>
    <!-- Prime Contact | Mobile -->
    <tr>
        <td class="lbl">Prime Contact Person</td>
        <td class="val"><?= $prime_name ?></td>
        <td class="lbl">Mobile No</td>
        <td class="val"><?= $prime_mob ?></td>
    </tr>
    <!-- Designation | Email -->
    <tr>
        <td class="lbl">Designation</td>
        <td class="val"><?= $prime_pos ?></td>
        <td class="lbl">E.mail:</td>
        <td class="val"><?= $prime_email ?></td>
    </tr>
    <!-- Audit Date(s): 3 sub-rows + Mandays on right -->
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
        <td class="lbl">Onsite Audit Time</td>
        <td class="val"><?= $onsite_time ?></td>
    </tr>
    <tr>
        <td>
            <span class="lbl">Temporary Site(s):&nbsp;</span>
            <span class="val date-val"><?= $date_temp ?></span>
        </td>
        <td class="lbl">Off-site activity time</td>
        <td class="val"><?= $offsite_time ?></td>
    </tr>
    <!-- Bottom timing row -->
    <tr>
        <td class="lbl" colspan="2">On site audit time</td>
        <td class="lbl" colspan="2">Off-site activity time</td>
    </tr>
</table>

<?php f05footer(); ?>

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
            $role_key   = $member['f05_team_role']      ?? '';
            $role_label = $team_role_labels[ $role_key ] ?? esc_html( $role_key );
            // f05_team_name is a user field (return_format: id) — resolve to display name
            $user_id = $member['f05_team_name'] ?? 0;
            $t_name  = '';
            if ( $user_id ) {
                $u      = get_userdata( (int) $user_id );
                $t_name = $u ? esc_html( $u->display_name ) : '';
            }
            $t_mob  = esc_html( $member['f05_team_mail_mobile'] ?? '' );
            $t_ict  = esc_html( $member['f05_team_onsite_ict']  ?? '' );
    ?>
    <tr>
        <td class="lbl"><?= $role_label ?></td>
        <td class="val"><?= $t_name ?></td>
        <td class="val"><?= $t_mob ?></td>
        <td style="text-align:center;"><?= $t_ict ?></td>
    </tr>
    <?php endforeach;
    else :
        // Render empty rows for all default roles
        foreach ( $team_role_labels as $label ) : ?>
    <tr>
        <td class="lbl"><?= esc_html( $label ) ?></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php endforeach;
    endif; ?>
    <!-- Remarks -->
    <tr>
        <td class="lbl">Remarks</td>
        <td colspan="3" class="val"><?= esc_html( $remarks ) ?></td>
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
    </ul>
</div>

<div class="decl-title">Declaration of extent of interest of the audit team members (if any)</div>
<div class="nil-box"><?= $decl_team !== '' ? $decl_team : 'Nil' ?></div>

<div class="decl-title">Declaration of extent of interest of the ICT (if any)</div>
<div class="nil-box"><?= $decl_ict !== '' ? $decl_ict : 'Nil' ?></div>


<?php /* ══════════════════════════════════════════════ PAGE 3 ═══ */ ?>

<!-- Director sign-off table -->
<table style="margin-bottom:10px;">
    <tr>
        <th class="team-th" style="width:25%;">Name</th>
        <th class="team-th" style="width:25%;">Designation</th>
        <th class="team-th" style="width:25%;">Signature</th>
        <th class="team-th" style="width:25%;">Date</th>
    </tr>
    <tr>
        <td class="val" style="font-weight:bold;"><?= $fin_name !== '' ? $fin_name : '&nbsp;' ?></td>
        <td class="val"><?= $fin_designation !== '' ? $fin_designation : '&nbsp;' ?></td>
        <td style="height:45px;"><?= $fin_signature ?></td>
        <td class="date-val"><?= $fin_date ?></td>
    </tr>
</table>

<!-- Corporate footer -->
<div class="corp-footer">
    Corporate Office: Flat No.402, Plot No.410, Matrusri nagar, Miyapur, Hyderabad-500 049, India.<br>
    Tel.: 040-48559001,&nbsp;&nbsp; E-mail: info@mcsglobal.in &nbsp;&nbsp; Website: www.mcsglobal.in<br>
    <strong>F-05 (Version 2.00, 20.03.2016)</strong>
</div>
<?php f05footer(); ?>

</body>
</html>
