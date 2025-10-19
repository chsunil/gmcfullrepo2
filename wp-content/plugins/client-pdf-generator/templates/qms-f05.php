<?php
/**
 * QMS F-05 Audit Team Allocation Plan Template
 * This file generates the F-05 PDF form for audit team allocation
 * Data is pulled via get_field() from ACF
 */

if (!defined('ABSPATH')) exit;

// Helper fallback function
function qms05_field($key, $post_id, $fallback = '-') {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : $fallback;
}

// Get current post ID
$post_id = get_the_ID();

// Pull available ACF fields
$organization = qms05_field('organization', $post_id);
$address = qms05_field('address', $post_id);
$audit_team_plan = qms05_field('audit_team_allocation_plan', $post_id);
$scope_cert = qms05_field('scope_of_certification', $post_id);
$audit_objective = qms05_field('audit_objective', $post_id);
$technical_code = qms05_field('technical_code', $post_id);
$audit_criteria = qms05_field('audit_criteria', $post_id);
$prime_contact = qms05_field('prime_contact', $post_id);
$gmcspl_ref = qms05_field('gmcspl_ref_no', $post_id);
$ict_used = qms05_field('ict_used', $post_id);
$exclusions = qms05_field('exclusions', $post_id);
$mobile_no = qms05_field('mobile_no', $post_id);

// Placeholder values for missing fields - you can fill these later
$audit_start_date = '-';
$audit_end_date = '-';
$total_duration = '-';
$working_language = '-';
$lead_auditor_name = '-';
$lead_auditor_qual = '-';
$auditor1_name = '-';
$auditor1_qual = '-';
$auditor2_name = '-';
$auditor2_qual = '-';
$technical_expert_name = '-';
$technical_expert_qual = '-';
$observer_name = '-';
$observer_qual = '-';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>F-05 QMS Audit Team Allocation Plan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            margin: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .header h1 {
            font-size: 14px;
            margin: 5px 0;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 12px;
            margin: 3px 0;
        }
        
        .form-info {
            text-align: right;
            font-size: 9px;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }
        
        .label-cell {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 20%;
        }
        
        .center {
            text-align: center;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .team-header {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 20px;
        }
        
        .signature-row {
            height: 40px;
        }
        
        .small-text {
            font-size: 8px;
        }
        
        .audit-type {
            background-color: #f5f5f5;
            padding: 5px;
            margin: 5px 0;
            border: 1px solid #ccc;
        }
        
        @page {
            margin: 10mm;
            size: A4;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>GLOBAL MANAGEMENT CERTIFICATION SERVICES PVT. LTD.</h1>
        <h2>AUDIT TEAM ALLOCATION PLAN</h2>
    </div>
    
    <div class="form-info">
        F-05 QMS (Version 3.00, 25.05.2021)
    </div>
    
    <!-- Audit Type Selection -->
    <div class="audit-type">
        <strong>AUDIT TYPE:</strong> 
        <?php if ($audit_team_plan): ?>
            <?php
            $audit_types = [
                'stage1' => '☑ Stage 1',
                'stage2' => '☑ Stage 2', 
                'recertifitation' => '☑ Re-certification',
                'surveillanceaudit' => '☑ Surveillance Audit'
            ];
            
            foreach ($audit_types as $key => $label) {
                if ($audit_team_plan === $key) {
                    echo $label . ' ';
                } else {
                    echo str_replace('☑', '☐', $label) . ' ';
                }
            }
            ?>
        <?php else: ?>
            ☐ Stage 1 ☐ Stage 2 ☐ Re-certification ☐ Surveillance Audit
        <?php endif; ?>
    </div>
    
    <!-- Organization Details -->
    <table>
        <tr>
            <td class="label-cell">Organization</td>
            <td colspan="3"><?= esc_html($organization) ?></td>
        </tr>
        <tr>
            <td class="label-cell">Address</td>
            <td colspan="3"><?= esc_html($address) ?></td>
        </tr>
        <tr>
            <td class="label-cell">GMCSPL Ref No.</td>
            <td><?= esc_html($gmcspl_ref) ?></td>
            <td class="label-cell">Mobile No.</td>
            <td><?= esc_html($mobile_no) ?></td>
        </tr>
    </table>
    
    <!-- Audit Details -->
    <table>
        <tr>
            <td class="label-cell">Scope of Certification</td>
            <td colspan="3"><?= esc_html($scope_cert) ?></td>
        </tr>
        <tr>
            <td class="label-cell">Audit Objective</td>
            <td colspan="3"><?= esc_html($audit_objective) ?></td>
        </tr>
        <tr>
            <td class="label-cell">Audit Criteria</td>
            <td colspan="3"><?= esc_html($audit_criteria) ?></td>
        </tr>
        <tr>
            <td class="label-cell">Technical Code/Area</td>
            <td><?= esc_html($technical_code) ?></td>
            <td class="label-cell">ICT Used</td>
            <td><?= esc_html($ict_used) ?></td>
        </tr>
        <tr>
            <td class="label-cell">Exclusions</td>
            <td><?= esc_html($exclusions) ?></td>
            <td class="label-cell">Working Language</td>
            <td><?= esc_html($working_language) ?></td>
        </tr>
        <tr>
            <td class="label-cell">Prime Contact Person</td>
            <td><?= esc_html($prime_contact) ?></td>
            <td class="label-cell">Audit Duration</td>
            <td><?= esc_html($total_duration) ?></td>
        </tr>
        <tr>
            <td class="label-cell">Audit Start Date</td>
            <td><?= esc_html($audit_start_date) ?></td>
            <td class="label-cell">Audit End Date</td>
            <td><?= esc_html($audit_end_date) ?></td>
        </tr>
    </table>
    
    <!-- Audit Team Members -->
    <table>
        <tr class="team-header">
            <td rowspan="2" class="center bold">S.No</td>
            <td rowspan="2" class="center bold">Role</td>
            <td rowspan="2" class="center bold">Name</td>
            <td colspan="3" class="center bold">Qualifications</td>
            <td rowspan="2" class="center bold">Signature</td>
        </tr>
        <tr class="team-header">
            <td class="center bold">Educational</td>
            <td class="center bold">Professional</td>
            <td class="center bold">Experience</td>
        </tr>
        <tr>
            <td class="center">1</td>
            <td class="bold">Lead Auditor</td>
            <td><?= esc_html($lead_auditor_name) ?></td>
            <td><?= esc_html($lead_auditor_qual) ?></td>
            <td>-</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td class="center">2</td>
            <td class="bold">Auditor</td>
            <td><?= esc_html($auditor1_name) ?></td>
            <td><?= esc_html($auditor1_qual) ?></td>
            <td>-</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td class="center">3</td>
            <td class="bold">Auditor</td>
            <td><?= esc_html($auditor2_name) ?></td>
            <td><?= esc_html($auditor2_qual) ?></td>
            <td>-</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td class="center">4</td>
            <td class="bold">Technical Expert</td>
            <td><?= esc_html($technical_expert_name) ?></td>
            <td><?= esc_html($technical_expert_qual) ?></td>
            <td>-</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td class="center">5</td>
            <td class="bold">Observer/Trainee</td>
            <td><?= esc_html($observer_name) ?></td>
            <td><?= esc_html($observer_qual) ?></td>
            <td>-</td>
            <td>-</td>
            <td></td>
        </tr>
    </table>
    
    <!-- Process Coverage Table -->
    <table>
        <tr class="team-header">
            <td rowspan="2" class="center bold">S.No</td>
            <td rowspan="2" class="center bold">Process/Department</td>
            <td colspan="2" class="center bold">Audit Coverage</td>
            <td rowspan="2" class="center bold">Assigned Auditor</td>
            <td rowspan="2" class="center bold">Time Allocated</td>
        </tr>
        <tr class="team-header">
            <td class="center bold">Yes</td>
            <td class="center bold">No</td>
        </tr>
        <!-- Add rows for processes - leaving empty for you to fill -->
        <tr>
            <td class="center">1</td>
            <td>-</td>
            <td class="center">☐</td>
            <td class="center">☐</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="center">2</td>
            <td>-</td>
            <td class="center">☐</td>
            <td class="center">☐</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="center">3</td>
            <td>-</td>
            <td class="center">☐</td>
            <td class="center">☐</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="center">4</td>
            <td>-</td>
            <td class="center">☐</td>
            <td class="center">☐</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="center">5</td>
            <td>-</td>
            <td class="center">☐</td>
            <td class="center">☐</td>
            <td>-</td>
            <td>-</td>
        </tr>
    </table>
    
    <!-- Audit Schedule -->
    <table>
        <tr class="team-header">
            <td class="center bold">Date</td>
            <td class="center bold">Time</td>
            <td class="center bold">Activity</td>
            <td class="center bold">Responsible Auditor</td>
            <td class="center bold">Participants</td>
        </tr>
        <!-- Opening Meeting -->
        <tr>
            <td>-</td>
            <td>-</td>
            <td class="bold">Opening Meeting</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <!-- Documentation Review -->
        <tr>
            <td>-</td>
            <td>-</td>
            <td class="bold">Documentation Review</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <!-- On-site Audit Activities -->
        <tr>
            <td>-</td>
            <td>-</td>
            <td class="bold">On-site Audit Activities</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <!-- Closing Meeting -->
        <tr>
            <td>-</td>
            <td>-</td>
            <td class="bold">Closing Meeting</td>
            <td>-</td>
            <td>-</td>
        </tr>
    </table>
    
    <!-- Special Requirements -->
    <table>
        <tr>
            <td class="label-cell">Safety Requirements</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="label-cell">Confidentiality Requirements</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="label-cell">Special Instructions</td>
            <td>-</td>
        </tr>
    </table>
    
    <!-- Approval Section -->
    <div class="signature-section">
        <table>
            <tr class="team-header">
                <td class="center bold">Action</td>
                <td class="center bold">Name</td>
                <td class="center bold">Designation</td>
                <td class="center bold">Date</td>
                <td class="center bold">Signature</td>
            </tr>
            <tr class="signature-row">
                <td class="bold">Prepared by</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr class="signature-row">
                <td class="bold">Reviewed by</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr class="signature-row">
                <td class="bold">Approved by</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>
        </table>
    </div>
    
    <!-- Footer -->
    <div style="text-align: center; margin-top: 20px; font-size: 8px; color: #666;">
        <p>This document is confidential and proprietary to Global Management Certification Services Pvt. Ltd.</p>
        <p>F-05 QMS (Version 3.00, 25.05.2021)</p>
    </div>
    
</body>
</html>