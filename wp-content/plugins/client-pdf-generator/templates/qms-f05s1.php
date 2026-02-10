<?php
// ==============================
// QMS – F05S1
// AUDIT TEAM ALLOCATION PLAN
// ==============================
if (!defined('ABSPATH')) exit;
// Header fields
$org_name      = get_field('organization_name', $post_id);
$ref_no        = get_field('gmcspl_ref_no', $post_id);
$onsite_addr   = get_field('head_office', $post_id);
$scope         = get_field('scope_of_certification', $post_id);
$tech_code     = get_field('technical_code', $post_id);
$ict_used      = get_field('ict_used', $post_id);
$audit_criteria= get_field('audit_criteria', $post_id);
$exclusions    = get_field('exclusions', $post_id);
$contact_name  = get_field('prime_contact', $post_id);
$contact_mobile= get_field('mobile_no', $post_id);
$designation   = get_field('s1_designation', $post_id);
$email         = get_field('s1_email', $post_id);
$audit_date    = get_field('s1_audit_date_onsite', $post_id);
$total_md      = get_field('s1_total_mandays', $post_id);

$prepared_by   = get_field('s1_prepared_by', $post_id);
$prepared_date = get_field('s1_prepared_date', $post_id);
$approved_by   = get_field('s1_approved_by', $post_id);
$approved_date = get_field('s1_approved_date', $post_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

<style>
table {
    border-collapse: collapse;
    width: 100%;
    font-size: 11px;
}
td, th {
    border: 1px solid #000;
    padding: 4px;
    vertical-align: top;
}
.no-border td {
    border: none;
}
.title {
    text-align: center;
    font-weight: bold;
    font-size: 14px;
    border: none;
}
.section {
    font-weight: bold;
    background-color: #f2f2f2;
}
.center{
    text-allign: center;
}
</style>
</head>
<body>
<table>

<tr><td colspan="8" class="title">AUDIT TEAM ALLOCATION PLAN</td></tr>

<tr><td colspan="8" class="no-border">
Please find attached the audit plan for the planned audit as per the Audit Programme. 
If you have any conflict of interest with any of the audit team members or any modification required in the audit plan, ICT, kindly inform within 2 working days or else audit team and plan shall be considered accepted. 
Any matter may be appealed in accordance with GMCSPL’ procedure “Appeals & Complaints (P-06)”.
</td></tr>

<tr>
    <td><strong>Organization</strong></td>
    <td colspan="3"><?php echo esc_html($org_name);  ?></td>
    <td><strong>GMCSPL Ref. No.</strong></td>
    <td colspan="3"><?php echo esc_html($ref_no);  ?></td>
</tr>

<tr>
    <td><strong>Address</strong></td>
    <td><strong>Onsite:</strong></td>
    <td colspan="6"><?php echo esc_html($onsite_addr);  ?></td>
</tr>

<tr>
    <td></td>
    <td><strong>Remote:</strong></td>
    <td colspan="6">N/A</td>
</tr>

<tr>
    <td></td>
    <td><strong>Temporary Site(s):</strong></td>
    <td colspan="6">-</td>
</tr>

<tr>
    <td><strong>Scope of Certification</strong></td>
    <td colspan="7"><?php echo esc_html($scope);  ?></td>
</tr>

<tr>
    <td><strong>Audit Objective</strong></td>
    <td colspan="7">
        &#8226; Collecting information and evidence about conformity to all requirements of the management system standard<br>
        &#8226; Performance monitoring, measuring, reporting and reviewing against key performance objectives and targets<br>
        &#8226; Determination of the ability of the management system to ensure adherence to applicable statutory, regulatory and contractual requirements<br>
        &#8226; Auditing operational control of the client’s processes<br>
        &#8226; Effectiveness of internal auditing and Management review<br>
        &#8226; Management responsibility for the client’s policies
    </td>
</tr>

<tr>
    <td><strong>Technical Code</strong></td>
    <td><?php echo esc_html($tech_code);  ?></td>
    <td><strong>ICT Used</strong></td>
    <td><?php echo esc_html($ict_used);  ?></td>
    <td><strong>Audit Criteria</strong></td>
    <td><?php echo esc_html($audit_criteria);  ?></td>
    <td><strong>Exclusion(s)</strong></td>
    <td><?php echo esc_html($exclusions);  ?></td>
</tr>

<tr>
    <td><strong>Prime Contact Person</strong></td>
    <td><?php echo esc_html($contact_name);  ?></td>
    <td><strong>Mobile No</strong></td>
    <td><?php echo esc_html($contact_mobile);  ?></td>
    <td><strong>Designation</strong></td>
    <td><?php echo esc_html($designation);  ?></td>
    <td><strong>E-mail</strong></td>
    <td><?php echo esc_html($email);  ?></td>
</tr>

<tr>
    <td><strong>Audit Date(s)</strong></td>
    <td><strong>Onsite:</strong></td>
    <td><?php echo esc_html($audit_date);  ?></td>
    <td><strong>Total Mandays</strong></td>
    <td><?php echo esc_html($total_md);  ?></td>
    <td colspan="3"></td>
</tr>

<tr class="section">
    <td colspan="8">Audit Team</td>
</tr>

<tr>
    <th>Role</th>
    <th colspan="2">Name</th>
    <th colspan="2">Mail / Mobile</th>
    <th colspan="3">Onsite / ICT & Remarks</th>
</tr>
<tr><td colspan="8"><strong>Remarks</strong></td></tr>

<tr><td colspan="8">
Please arrange the following for our audit team:<br>
&#8226; Working space and access to telephone, internet and photocopying facilities<br>
&#8226; Knowledgeable person to accompany during the audit<br>
&#8226; Knowledgeable person to handle selected technology<br>
&#8226; Access to pertinent manuals and procedures<br>
&#8226; Safety requirements as per procedures<br>
&#8226; Contact us for any clarification regarding audit arrangements
</td></tr>

<tr>
    <td><strong>Prepared By</strong></td>
    <td colspan="2"><?php echo esc_html($prepared_by);  ?></td>
    <td><strong>Date</strong></td>
    <td colspan="4"><?php echo esc_html($prepared_date);  ?></td>
</tr>

<tr>
    <td><strong>Approved By</strong></td>
    <td colspan="2"><?php echo esc_html($approved_by);  ?></td>
    <td><strong>Date</strong></td>
    <td colspan="4"><?php echo esc_html($approved_date);  ?></td>
</tr>

<tr><td colspan="8"><strong>Audited Company Declaration</strong></td></tr>

<tr><td colspan="8">
<strong>we accept the nominated audit team</strong> 
<ul>
<li> we confirm that no member of the audit team has provided consultancy to our company or executed any internal audit in our company.
</li><li> we confirm that no Audit team members involved in product design
</li><li> we confirm that the audit team members do not have financial, business, relational involvement or interest in our company.
We accept the use of Information and Communication Technology (ICT) as a mode of this Audit / Assessment for the selected Location/s in accordance with information security and data protection measures and regulations if any.
The use of ICT shall include, but is not limited to:
</li><li> Meetings; by means of teleconference facilities, including audio, video and data sharing
</li><li> Audit/Assessment of documents and records by means of remote access, either synchronously (in real time) or asynchronously (when applicable)
</li><li> Recording of information and evidence by means of still video, video or audio recordings
</li><li> Providing visual / audio access to remote or potentially hazardous locations"	</li>
</ul>								

</td></tr>

<tr><td colspan="8" class="no-border center">
Corporate Office: Flat No.402, Plot No.410, Matrusri Nagar, Miyapur, Hyderabad-500049, India.<br>
Tel: 040-48559001 | Email: info@mcsglobal.in | Website: www.mcsglobal.in<br>
<strong>F-05 (Version 2.00, 20.03.2016)</strong>
</td></tr>

</table>
