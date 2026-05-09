<?php
/**
 * IMS – Sheet 12 Audit Notification Email
 * ACF Group: group_ims_sheet12
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
$org      = get_field( 'organization_name', $post_id ) ?: get_the_title($post_id);
$ref_no   = get_field( 'sheet12Ref_No', $post_id ) ?: '-';
$date     = get_field( 'sheet12date', $post_id ) ? date('d/m/Y', strtotime(get_field('sheet12date', $post_id))) : '-';
$to       = get_field( 'sheet12to', $post_id ) ?: '-';
$subject  = get_field( 'sheet12subject', $post_id ) ?: 'Integrated Stage 2 Audit reg.';
$on_date  = get_field( 'sheet12premises_on_date', $post_id ) ? date('d/m/Y', strtotime(get_field('sheet12premises_on_date', $post_id))) : '-';

$auditor_id = get_field( 'sheet12the_audit_team_will_consist_of', $post_id );
$auditor    = $auditor_id ? get_userdata($auditor_id)->display_name : '-';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #333; margin: 0; padding: 20mm; }
    .header { margin-bottom: 30px; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
    .meta { margin-bottom: 20px; }
    .meta div { margin-bottom: 4px; }
    .subject { font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
    .content p { margin-bottom: 15px; }
    .signature { margin-top: 40px; }
</style>
</head>
<body>

<div class="header">
    <div style="font-size: 14pt; font-weight: bold; color: #2c3e50;">AUDIT NOTIFICATION</div>
    <div style="font-size: 9pt; color: #7f8c8d;">IMS (QMS, EMS & OH&SMS)</div>
</div>

<div class="meta">
    <div><strong>Date:</strong> <?= $date ?></div>
    <div><strong>Ref No:</strong> <?= $ref_no ?></div>
    <div><strong>To:</strong> <?= $to ?></div>
    <div><strong>Organization:</strong> <?= $org ?></div>
</div>

<div class="subject">Sub: <?= $subject ?></div>

<div class="content">
    <p>Dear Sir/Madam,</p>
    <p>With reference to the certification agreement, we are pleased to inform you that the Stage – 2 Integrated Management System Audit for your organization is scheduled to be conducted at your premises on <strong><?= $on_date ?></strong>.</p>
    <p>The audit team will consist of: <strong><?= $auditor ?></strong></p>
    <p>The Audit Schedule (F-08a) is attached for your reference. Kindly ensure the availability of all relevant personnel and documentation during the audit.</p>
    <p>Should you have any queries or require any changes to the schedule, please inform us within 2 working days.</p>
</div>

<div class="signature">
    Yours sincerely,<br><br><br>
    <strong>Certification Manager</strong><br>
    GMC Services Private Limited
</div>

</body>
</html>
