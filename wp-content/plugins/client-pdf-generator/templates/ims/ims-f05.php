<?php
/**
 * IMS – F-05 Audit Team Allocation Plan
 * Modernized Version for IMS Track
 * Based on Version 2.00, 20.03.2016
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── helpers ───────────────────────────────────────────────────────────────────
if ( ! function_exists( 'imsf05v' ) ) {
    function imsf05v( $key, $pid, $fallback = '' ) {
        $v = get_field( $key, $pid );
        if ( is_array( $v ) ) {
            if ( isset( $v['display_name'] ) ) return esc_html( $v['display_name'] ?? '');
            return $fallback;
        }
        return ! empty( $v ) ? esc_html( $v ) : $fallback;
    }
}

if ( ! function_exists( 'imsf05cb' ) ) {
    function imsf05cb( $checked = false ) {
        return $checked ? '[X]' : '[  ]';
    }
}

if ( ! function_exists( 'imsf05_fmt_date' ) ) {
    function imsf05_fmt_date( $raw, $format = 'd/m/Y' ) {
        if ( empty( $raw ) ) return '';
        $ts = strtotime( $raw );
        return $ts ? date( $format, $ts ) : esc_html( $raw );
    }
}

if ( ! function_exists( 'imsf05footer' ) ) {
    function imsf05footer() {
        echo '<div class="page-footer" style="text-align:right; font-size:8pt; color:#94a3b8; margin-top:20px;">IMS – F-05 (Version 2.00, 20.03.2016)</div>';
    }
}

// ── pull ACF data ─────────────────────────────────────────────────────────────
$post_id = $args['post_id'] ?? get_the_ID();

// Organization details
$org = function_exists('gmc_get_organization_name') ? gmc_get_organization_name($post_id) : imsf05v( 'organization_name', $post_id );
$f05gmcspl_ref_no = imsf05v( 'proposal_ref_no', $post_id );

// Audit stage radio
$stage = get_field( 'f05audit_stage', $post_id );

// Address group
$address        = get_field( 'address', $post_id ) ?: [];
$addr_onsite    = esc_html( $address['head_office']          ?? '' );
$addr_remote    = esc_html( $address['main_operative_site']  ?? '' );
$addr_temporary = esc_html( $address['other_sites']          ?? '' );

// Scope
$scope = imsf05v( 'scope_of_certification', $post_id );

// Audit Objective
$audit_objective = get_field( 'f05audit_objective', $post_id );

// Technical Code / ICT
$tech_code = imsf05v( 'technical_code_area', $post_id );
$ict_used = imsf05v( 'Type_and_extent_of_ICT_used_if_any', $post_id, 'N/a' );

// Criteria / Exclusions
$audit_criteria = imsf05v( 'cert_scheme', $post_id );
$exclusions = imsf05v( 'exclusions_only_for_iso_9001', $post_id, 'Nil' );

// Prime Contact
$contact     = get_field( 'f01contact_person', $post_id ) ?: [];
$prime_name  = esc_html( $contact['contact_person_name'] ?? '' );
$prime_pos   = esc_html( $contact['contact_position']    ?? '' );
$prime_mob   = esc_html( $contact['contact_mobile']      ?? '' );
$prime_email = esc_html( $contact['contact_email']       ?? '' );

// ── Audit date — lookup based on stage ─────────────────────────────────────────
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
    $audit_date_onsite = imsf05_fmt_date( $raw );
}
$date_remote = '';
$date_temp   = '';

// Mandays / audit times
$total_md     = esc_html( get_field( 're_certification=23_of_initial_md', $post_id ) ?: '' );
$mandays      = get_field( 'f05_mandays', $post_id ) ?: [];
$onsite_time  = esc_html( $mandays['f05_onsite_audit_time']     ?? '' );
$offsite_time = esc_html( $mandays['f05_offsite_activity_time'] ?? '' );

// Audit team
$audit_team = get_field( 'f05_audit_team', $post_id ) ?: [];

// Remarks
$remarks = imsf05v( 'f05_remarks', $post_id, 'Nil' );

// Prepared / Approved
$prep      = get_field( 'f05_prepared_by', $post_id ) ?: [];
$prep_user = $prep['f05_prepared_by_name'] ?? null;
$prep_name = ( is_array( $prep_user ) && isset( $prep_user['display_name'] ) ) ? esc_html( $prep_user['display_name'] ) : '';
$prep_date = imsf05_fmt_date( $prep['f05_prepared_by_date'] ?? '' );

$appr      = get_field( 'f05_approved_by', $post_id ) ?: [];
$appr_name = esc_html( $appr['f05_approved_by_name'] ?? '' );
$appr_date = imsf05_fmt_date( $appr['f05_approved_by_date'] ?? '' );

// Declarations
$decl_team = esc_html( get_field( 'f05declaration_of_extent_of_interest_of_the_audit_team_members_if_any', $post_id ) ?: '' );
$decl_ict  = esc_html( get_field( 'f05declaration_of_extent_of_interest_of_the_ict_if_any', $post_id ) ?: '' );

// Final Sign-off
$fin_user        = get_field( 'fo5finalname', $post_id );
$fin_name        = ( is_array( $fin_user ) && isset( $fin_user['display_name'] ) ) ? esc_html( $fin_user['display_name'] ) : '';
$fin_designation = esc_html( get_field( 'designation',      $post_id ) ?: '' );
$fin_signature   = esc_html( get_field( 'fo5finsignature',  $post_id ) ?: '' );
$fin_date        = esc_html( get_field( 'f05findate',       $post_id ) ?: '' );

// ── Stage logic ───────────────────────────────────────────────────────────────
$is_stage1 = ( $stage === 'stage1' );
$is_stage2 = ( $stage === 'stage2' );
$is_recert = ( $stage === 'recertification' );
$is_surv   = in_array( $stage, [ 'surveillance_surv1', 'surveillance_surv2' ] );

// ── Objective formatting ──────────────────────────────────────────────────────
$obj_lines = array_filter( array_map( 'trim', explode( "\n", $audit_objective ?? '' ) ) );
$obj_html  = '';
foreach ( $obj_lines as $line ) {
    $line      = ltrim( $line, "\xE2\x80\xa2\xC2\xB7-* " );
    $obj_html .= '<div class="bullet">&bull; ' . esc_html( trim( $line ) ) . '</div>';
}
if ( empty( $obj_html ) ) { $obj_html = '<span>—</span>'; }

$team_role_labels = [
    'lead_auditor'            => 'Lead Auditor',
    'auditor'                 => 'Auditor(s)',
    'technical_expert'        => 'Technical Expert',
    'team_leader_supervision' => 'Team Leader (under supervision) / Witness Auditor',
    'observer'                => 'Observer(s)',
    'interpreter'             => 'Interpreter(s)',
];

$logo_b64 = 'data:image/jpeg;base64,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';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    @page { size: A4 portrait; margin: 12mm 10mm 15mm 10mm; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #000; line-height: 1.35; padding: 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
    th, td { border: 1px solid #b0bec5; padding: 5px 8px; vertical-align: top; }
    .lbl { font-weight: bold; background-color: #f8fafc; color: #334155; }
    .val { background-color: #fff; }
    .logo-cell { border: none; width: 15%; vertical-align: middle; }
    .title-cell { border: none; text-align: center; vertical-align: middle; width: 55%; }
    .main-title { font-size: 13pt; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; }
    .form-ref { font-size: 8.5pt; color: #64748b; margin-top: 3px; }
    .track-cell { border: 1px solid #94a3b8; width: 30%; padding: 8px; background-color: #f1f5f9; }
    .stage-row { font-size: 8.5pt; margin-bottom: 3px; }
    .stage-row.active { font-weight: bold; color: #020617; }
    .intro { font-size: 8pt; border: 1px solid #cbd5e1; padding: 6px 10px; margin-bottom: 8px; text-align: justify; background: #fafafa; border-radius: 2px; }
    .bullet { font-size: 9pt; margin-bottom: 2px; color: #1e293b; }
    .team-th { background-color: #e2e8f0; font-weight: bold; text-align: center; font-size: 9pt; height: 28px; }
    .arrange-block { border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 8.5pt; margin-bottom: 8px; background: #fdfdfd; }
    .arrange-block strong { color: #0f172a; }
    .arrange-block ul { padding-left: 20px; margin-top: 4px; }
    .arrange-block li { margin-bottom: 3px; list-style-type: square; }
    .decl-block { border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 8pt; margin-bottom: 8px; text-align: justify; line-height: 1.5; color: #334155; }
    .decl-block strong { color: #020617; }
    .section-title { font-weight: bold; font-size: 9.5pt; margin: 8px 0 4px 0; color: #0f172a; border-left: 3px solid #64748b; padding-left: 6px; }
    .nil-box { border: 1px solid #e2e8f0; padding: 10px; font-size: 9pt; background: #fff; min-height: 35px; color: #64748b; }
    .footer-label { font-size: 8pt; color: #94a3b8; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 4px; margin-top: 10px; text-transform: uppercase; }
    .corp-footer { text-align: center; font-size: 8pt; color: #475569; margin-top: 15px; border-top: 1.5px solid #cbd5e1; padding-top: 8px; line-height: 1.6; }
    .page-break { page-break-before: always; }
    .date-val { font-weight: bold; font-family: monospace; }
</style>
</head>
<body>

<!-- Header row -->
<table style="border:none; margin-bottom:10px;">
    <tr>
        <td class="logo-cell">
           <img src="<?= $logo_b64 ?>" alt="Logo" style="max-width:85px;">
        </td>
        <td class="title-cell">
            <div class="main-title">Audit Team Allocation Plan</div>
            <div class="form-ref">IMS – F-05 (Version 2.00, 20.03.2016)</div>
        </td>
        <td class="track-cell">
            <div class="stage-row <?= $is_stage1 ? 'active' : '' ?>"><?= imsf05cb($is_stage1) ?> Stage-1</div>
            <div class="stage-row <?= $is_stage2 ? 'active' : '' ?>"><?= imsf05cb($is_stage2) ?> Stage-2</div>
            <div class="stage-row <?= $is_recert ? 'active' : '' ?>"><?= imsf05cb($is_recert) ?> Re Certification</div>
            <div class="stage-row <?= $is_surv   ? 'active' : '' ?>"><?= imsf05cb($is_surv)   ?> Surveillance Audit</div>
        </td>
    </tr>
</table>

<!-- Intro notice -->
<div class="intro">
    Please find attached the audit plan for the planned audit as per the Audit Programme. If you have any conflict of interest with any of the audit team members or any modification required in the audit plan, ICT, kindly inform with in 2 working days or else audit team and plan shall be considered accepted. Any matter may be appealed in accordance with GMCSPL' procedure "Appeals & Complaints (P-06)".
</div>

<!-- Main Details -->
<table>
    <tr>
        <td class="lbl" style="width:20%;">Organization</td>
        <td class="val" style="width:40%; font-weight:bold; font-size:10.5pt; color:#0f172a;"><?= $org ?></td>
        <td class="lbl" style="width:20%;">GMCSPL Ref No.</td>
        <td class="val" style="width:20%;"><?= $f05gmcspl_ref_no ?></td>
    </tr>
    <tr>
        <td class="lbl" style="vertical-align:middle;">Address(s)</td>
        <td colspan="3" style="padding:0;">
            <table style="border:none; margin:0;">
                <tr><td class="lbl" style="border:none; border-bottom:1px solid #eee; width:100px;">On Site:</td><td class="val" style="border:none; border-bottom:1px solid #eee;"><?= $addr_onsite ?: '-' ?></td></tr>
                <tr><td class="lbl" style="border:none; border-bottom:1px solid #eee;">Remote:</td><td class="val" style="border:none; border-bottom:1px solid #eee;"><?= $addr_remote ?: '-' ?></td></tr>
                <tr><td class="lbl" style="border:none;">Temporary:</td><td class="val" style="border:none;"><?= $addr_temporary ?: '-' ?></td></tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="lbl">Scope of Certification</td>
        <td class="val" colspan="3"><?= $scope ?></td>
    </tr>
    <tr>
        <td class="lbl">Audit Objective</td>
        <td colspan="3"><?= $obj_html ?></td>
    </tr>
    <tr>
        <td class="lbl">Technical Code</td>
        <td class="val"><?= $tech_code ?></td>
        <td class="lbl">ICT Used</td>
        <td class="val"><?= $ict_used ?></td>
    </tr>
    <tr>
        <td class="lbl">Audit Criteria</td>
        <td class="val"><?= $audit_criteria ?></td>
        <td class="lbl">Exclusion[s]</td>
        <td class="val"><?= $exclusions ?></td>
    </tr>
    <tr>
        <td class="lbl">Prime Contact</td>
        <td class="val"><?= $prime_name ?: '-' ?></td>
        <td class="lbl">Mobile No.</td>
        <td class="val"><?= $prime_mob ?: '-' ?></td>
    </tr>
    <tr>
        <td class="lbl">Designation</td>
        <td class="val"><?= $prime_pos ?: '-' ?></td>
        <td class="lbl">E-mail</td>
        <td class="val" style="font-size:8.5pt;"><?= $prime_email ?: '-' ?></td>
    </tr>
    <tr>
        <td class="lbl" rowspan="3" style="vertical-align:middle;">Audit Date(s)</td>
        <td class="val"><span class="lbl">Onsite:</span> <span class="date-val"><?= $audit_date_onsite ?: '-' ?></span></td>
        <td class="lbl">Total Mandays</td>
        <td class="val" style="font-weight:bold;"><?= $total_md ?></td>
    </tr>
    <tr>
        <td class="val"><span class="lbl">Remote:</span> <span class="date-val"><?= $date_remote ?: '-' ?></span></td>
        <td class="lbl">Onsite Audit Time</td>
        <td class="val"><?= $onsite_time ?></td>
    </tr>
    <tr>
        <td class="val"><span class="lbl">Temporary:</span> <span class="date-val"><?= $date_temp ?: '-' ?></span></td>
        <td class="lbl">Off-site activity time</td>
        <td class="val"><?= $offsite_time ?></td>
    </tr>
</table>

<?php imsf05footer(); ?>

<div class="page-break"></div>

<!-- Audit Team -->
<div class="section-title">Audit Team Configuration</div>
<table>
    <thead>
        <tr>
            <th class="team-th" style="width:30%;">Role</th>
            <th class="team-th" style="width:30%;">Name</th>
            <th class="team-th" style="width:25%;">Contact Details</th>
            <th class="team-th" style="width:15%;">Onsite/ICT</th>
        </tr>
    </thead>
    <tbody>
        <?php if ( ! empty( $audit_team ) ) :
            foreach ( $audit_team as $member ) :
                $role_key   = $member['f05_team_role']      ?? '';
                $role_label = $team_role_labels[ $role_key ] ?? esc_html( $role_key );
                $user_id    = $member['f05_team_name']      ?? 0;
                $t_name     = '';
                if ( $user_id ) {
                    $u      = get_userdata( (int) $user_id );
                    $t_name = $u ? esc_html( $u->display_name ) : '';
                }
                $t_mob      = esc_html( $member['f05_team_mail_mobile'] ?? '' );
                $t_ict      = esc_html( $member['f05_team_onsite_ict']  ?? '' );
        ?>
        <tr>
            <td class="lbl"><?= $role_label ?></td>
            <td class="val" style="font-weight:bold;"><?= $t_name ?></td>
            <td class="val" style="font-size:8.5pt;"><?= $t_mob ?></td>
            <td style="text-align:center;"><?= $t_ict ?></td>
        </tr>
        <?php endforeach; else : ?>
            <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:20px;">No audit team members assigned.</td></tr>
        <?php endif; ?>
        <tr>
            <td class="lbl">Remarks</td>
            <td colspan="3" class="val"><?= $remarks ?></td>
        </tr>
    </tbody>
</table>

<div class="arrange-block">
    <strong>Please arrange the following for our audit team:</strong>
    <ul>
        <li>Working space and access to a telephone, internet and photocopying facilities.</li>
        <li>Knowledgeable person to accompany during the audit.</li>
        <li>Knowledgeable person to handle the selected technology, to present the data/information.</li>
        <li>Access to pertinent manuals, procedures and to access the required data/information.</li>
        <li>Safety Requirements as per your procedures.</li>
        <li>Should you have any query with regard to the arrangements for this audit, please contact us.</li>
    </ul>
</div>

<table style="margin-bottom:10px;">
    <tr>
        <td class="lbl" style="width:20%;">Prepared By</td>
        <td class="val" style="width:30%; font-weight:bold;"><?= $prep_name ?: '-' ?></td>
        <td class="lbl" style="width:20%;">Approved By</td>
        <td class="val" style="width:30%; font-weight:bold;"><?= $appr_name ?: '-' ?></td>
    </tr>
    <tr>
        <td class="lbl">Date</td>
        <td class="val date-val"><?= $prep_date ?: '-' ?></td>
        <td class="lbl">Date</td>
        <td class="val date-val"><?= $appr_date ?: '-' ?></td>
    </tr>
</table>

<div class="decl-block">
    <p><strong>Audited Company Declaration:</strong></p>
    <p>By signing this document, we accept the nominated audit team and confirm:</p>
    <ul style="margin:4px 0 0 16px;">
        <li>No member of the audit team has provided consultancy or executed internal audits for our company.</li>
        <li>No audit team members were involved in product design for the scope under assessment.</li>
        <li>Audit team members do not have financial, business, or relational interest in our company.</li>
    </ul>
    <p style="margin-top:8px;">We accept the use of IT / ICT as a mode of this Assessment for selected locations in accordance with data protection measures. ICT may include teleconferencing and real-time/synchronous remote access to records.</p>
</div>

<div class="section-title">Declaration of extent of interest (if any)</div>
<div style="margin-bottom:10px;">
    <div style="font-size:8pt; margin-bottom:2px; color:#64748b;">Audit Team Members:</div>
    <div class="nil-box"><?= $decl_team !== '' ? $decl_team : 'Nil' ?></div>
</div>
<div>
    <div style="font-size:8pt; margin-bottom:2px; color:#64748b;">ICT / Remote Mode:</div>
    <div class="nil-box"><?= $decl_ict !== '' ? $decl_ict : 'Nil' ?></div>
</div>

<div class="page-break"></div>
<div class="section-title">Director Sign-off</div>
<table>
    <thead>
        <tr>
            <th class="team-th">Name</th>
            <th class="team-th">Designation</th>
            <th class="team-th">Signature</th>
            <th class="team-th">Date</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="val" style="font-weight:bold; height:50px; text-align:center; vertical-align:middle;"><?= $fin_name ?: '—' ?></td>
            <td class="val" style="text-align:center; vertical-align:middle;"><?= $fin_designation ?: '—' ?></td>
            <td style="background:#f8fafc; color:#cbd5e1; text-align:center; vertical-align:middle; font-size:7pt;">Signature Required</td>
            <td class="val date-val" style="text-align:center; vertical-align:middle;"><?= imsf05_fmt_date($fin_date) ?: '—' ?></td>
        </tr>
    </tbody>
</table>

<div class="corp-footer">
    <strong>Corporate Office:</strong> Flat No.402, Plot No.410, Matrusri Nagar, Miyapur, Hyderabad-500 049, India.<br>
    Tel: 040-48559001 | E-mail: info@mcsglobal.in | Website: www.mcsglobal.in<br>
    <strong>IMS – F-05 (Version 2.00, 20.03.2016)</strong>
</div>

<?php imsf05footer(); ?>

</body>
</html>
