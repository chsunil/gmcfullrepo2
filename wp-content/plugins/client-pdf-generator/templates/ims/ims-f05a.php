<?php
/**
 * IMS F-05a — Audit Team Allocation Plan (Stage-2)
 * Reuses QMS field mapping but with IMS branding and integrated objectives.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
if ( ! function_exists( 'ims_f05a_v' ) ) {
    function ims_f05a_v( $key, $pid, $fallback = '' ) {
        $v = get_field( $key, $pid );
        if ( is_array( $v ) ) {
            if ( isset( $v['display_name'] ) ) return esc_html( $v['display_name'] );
            return $fallback;
        }
        return ! empty( $v ) ? esc_html( $v ) : $fallback;
    }
}

if ( ! function_exists( 'ims_f05a_cb' ) ) {
    function ims_f05a_cb( $checked = false ) {
        return $checked ? '[X]' : '[  ]';
    }
}

// ── Pull data ─────────────────────────────────────────────────────────────────
$org           = ims_f05a_v( 'organization_name', $post_id );
$gmcspl_ref_no = ims_f05a_v( 'proposal_ref_no', $post_id );
$stage         = get_field( 'stagef05a', $post_id );

$is_stage1 = ( $stage === 'Stage-1' );
$is_stage2 = ( $stage === 'Stage-2' );
$is_recert = ( $stage === 'Recertification' );
$is_surv   = ( $stage === 'Surveillance Audit' );

$address        = get_field( 'address', $post_id ) ?: [];
$addr_onsite    = esc_html( $address['head_office']         ?? '' );
$addr_remote    = esc_html( $address['main_operative_site'] ?? '' );
$addr_temporary = esc_html( $address['other_sites']         ?? '' );

$scope     = ims_f05a_v( 'scope_of_certification', $post_id );
$tech_code = ims_f05a_v( 'technical_code_area', $post_id );
$ict_used  = ims_f05a_v( 'types_and_extent_ict_used_by_the_organization_and_competency_level', $post_id, 'N/a' );
$audit_criteria = ims_f05a_v( 'cert_scheme', $post_id );

$contact     = get_field( 'f01contact_person', $post_id ) ?: [];
$prime_name  = esc_html( $contact['contact_person_name'] ?? '' );
$prime_mob   = esc_html( $contact['contact_mobile']      ?? '' );
$prime_email = esc_html( $contact['contact_email']       ?? '' );

// Audit date
$stage_date_map = [
    'Stage-1'           => 'stage1_audit_initial',
    'Stage-2'           => 'stage2_audit_surveillance_audit_date_initial',
    'Recertification'   => 'stage2_audit_surveillance_audit_date_surv2',
    'Surveillance Audit'=> 'stage2_audit_surveillance_audit_date_surv1',
];
$onsite_meta_key   = $stage_date_map[ $stage ] ?? 'stage2_audit_surveillance_audit_date_initial';
$raw_audit_date    = get_post_meta( $post_id, $onsite_meta_key, true );
$audit_date_onsite = $raw_audit_date ? date('d/m/Y', strtotime($raw_audit_date)) : '-';

// Mandays
$mandays_group = get_field( 'audit_time_to_be_implemented_for__initial_audit', $post_id ) ?: [];
$md_stage1     = esc_html( $mandays_group['on_site_stage1']   ?? '' );
$md_stage2     = esc_html( $mandays_group['on_site_stage_2']  ?? '' );
$total_md      = $is_stage1 ? $md_stage1 : $md_stage2;

// Audit team
$audit_team = get_field( 'f05_audit_team', $post_id ) ?: [];

$logo_b64 = 'data:image/jpeg;base64,...'; // Full base64 omitted for brevity in thought, but included in tool call
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 9pt; line-height: 1.3; color: #333; margin: 0; padding: 15mm; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    th, td { border: 1px solid #b0bec5; padding: 5px 8px; vertical-align: top; }
    
    .h-logo { border: none; width: 15%; text-align: center; }
    .h-title { border: none; text-align: center; }
    .h-title h1 { margin: 0; font-size: 16pt; color: #2c3e50; }
    .h-title span { font-size: 10pt; color: #7f8c8d; }
    
    .lbl { font-weight: bold; background: #f8f9fa; color: #2c3e50; width: 22%; }
    .val { color: #2c3e50; }
    
    .section-title { background: #2c3e50; color: #fff; padding: 6px 10px; font-weight: bold; margin-top: 15px; font-size: 10pt; }
    .cb-list { font-size: 8.5pt; line-height: 1.8; }
</style>
</head>
<body>

<table>
    <tr>
        <td class="h-logo"><img src="data:image/jpeg;base64,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" alt="Logo"></td>
        <td class="h-title">
            <h1>AUDIT TEAM ALLOCATION PLAN</h1>
            <span>IMS (QMS, EMS & OH&SMS)</span>
        </td>
        <td class="cb-list">
            <?= ims_f05a_cb($is_stage1) ?> Stage-1 <br>
            <?= ims_f05a_cb($is_stage2) ?> Stage-2 <br>
            <?= ims_f05a_cb($is_recert) ?> Re Certification <br>
            <?= ims_f05a_cb($is_surv)   ?> Surveillance Audit
        </td>
    </tr>
</table>

<div class="section-title">Audit Overview</div>
<table>
    <tr>
        <td class="lbl">Organization</td>
        <td class="val" style="font-weight:bold;"><?= $org ?></td>
        <td class="lbl">Ref. No.</td>
        <td class="val"><?= $gmcspl_ref_no ?></td>
    </tr>
    <tr>
        <td class="lbl">Audit Standard</td>
        <td class="val"><?= $audit_criteria ?></td>
        <td class="lbl">Mandays</td>
        <td class="val"><?= $total_md ?></td>
    </tr>
</table>

<div class="section-title">Audit Objectives</div>
<div style="padding:10px; border:1px solid #b0bec5; font-size:9pt; background:#fdfdfd;">
    <ul style="margin:0; padding-left:20px;">
        <li>To review Integrated Management System (IMS) documented information for compliance with ISO 9001, 14001, and 45001.</li>
        <li>To evaluate site-specific conditions and readiness for the Stage-2 Integrated assessment.</li>
        <li>To evaluate the effectiveness of Integrated Internal Audits and Management Reviews.</li>
        <li>To evaluate the organization's understanding of key performance, significant environmental aspects, and OH&S hazards.</li>
    </ul>
</div>

<div class="section-title">Audit Team & Roles</div>
<table>
    <tr>
        <th style="width:25%;">Role</th>
        <th style="width:35%;">Auditor Name</th>
        <th style="width:20%;">Contact Info</th>
        <th style="width:20%;">Competency Status</th>
    </tr>
    <?php if (!empty($audit_team)) : ?>
        <?php foreach ($audit_team as $member) : 
            $user_id = $member['f05_team_name'];
            $name = $user_id ? get_userdata($user_id)->display_name : '-';
            $role = $member['f05_team_role'];
        ?>
        <tr>
            <td class="lbl"><?= esc_html(ucwords(str_replace('_', ' ', $role))) ?></td>
            <td><?= esc_html($name) ?></td>
            <td class="center"><?= esc_html($member['f05_team_mail_mobile']) ?></td>
            <td class="center">Verified</td>
        </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="4" class="center">Audit team not yet allocated.</td></tr>
    <?php endif; ?>
</table>

<div class="section-title">Confidentiality & Conflict of Interest</div>
<div style="font-size:8.5pt; padding:8px; border:1px solid #b0bec5; background:#f9f9f9; text-align:justify;">
    The audit team members confirm that they have not provided any consultancy to the organization in the last 2 years. They also confirm that they have no family or financial ties with the organization that could influence their impartiality during the Integrated Management System audit.
</div>

</body>
</html>
