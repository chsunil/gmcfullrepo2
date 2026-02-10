<?php

/**
 * QMS – F-01 Application Form Template
 * This file should follow the same style as f03.php
 * Data is pulled via get_field()
 */

if (!defined('ABSPATH')) exit;

// Helper fallback function
function qms01_field($key, $post_id) {
    $val = get_field($key, $post_id);
    return !empty($val) ? $val : '-';
}

// 🔹 Pulling all required fields
$org_name       = qms01_field('organization_name', $post_id);
$head_office    = qms01_field('head_office', $post_id);
$main_site      = qms01_field('main_operative_site', $post_id);
$other_sites    = qms01_field('other_sites', $post_id);

// Contact Person (group)
$contact_name   = $contact['contact_person_contact_person_name'] ?? '-';
$contact_pos    = $contact['contact_person_contact_position'] ?? '-';
$contact_mobile = $contact['contact_person_contact_mobile'] ?? '-';
$contact_email  = $contact['contact_person_contact_email'] ?? '-';
$fax            = $contact['contact_person_fax'] ?? '-';
$tel            = $contact['contact_person_tel'] ?? '-';
$website        = $contact['contact_person_website'] ?? '-';

// Certification & Scope
$products_services  = qms01_field('products_services', $post_id);
$scope_cert         = qms01_field('scope_of_certification', $post_id);
$cert_scheme        = qms01_field('cert_scheme', $post_id);
$accreditation      = qms01_field('accreditation', $post_id);
$exclusions         = qms01_field('exclusions_only_for_iso_9001', $post_id);
$justification      = qms01_field('exclusions_only_for_iso_9002_Justification', $post_id);

// Outsourced Processes
$outsourced         = get_field('outsourced_processes', $post_id);
$op_process         = $outsourced['process'] ?? '-';
$op_supplier        = $outsourced['suppliersub_contractor'] ?? '-';
$op_compliance      = $outsourced['compliance_status_or_control_exercised'] ?? '-';

// Employee Matrix
$epme_matrix        = get_field('epme_matrix', $post_id);

// Declaration
$ack_group      = get_field('i_acknowledge_that_ :_', $post_id);
$ack_name       = $ack_group['i_acknowledge_that_ :_'] ?? '-';
$ack_design     = $ack_group['designation:'] ?? '-';
$ack_sign       = $ack_group['signature'] ?? '-';
$ack_date       = $ack_group['date:'] ?? '-';

// Top management
$tm_name = get_field('contact_person_top_management', $post_id);
$tm_designation = get_field('contact_person_designation', $post_id);
$tm_mobile = get_field('contact_person_mobile_number', $post_id);
$tm_email = get_field('contact_person_contact_email_new', $post_id);

?>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }

        h1,
        h2 {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }

        h2 {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .section-title {
            background: #f2f2f2;
            font-weight: bold;
            padding: 6px;
            margin-top: 15px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .label {
            width: 30%;
            font-weight: bold;
            background: #f9f9f9;
        }
    </style>

<body>
    <!-- ✅ HEADER -->
    <h1>APPLICATION FOR CERTIFICATION</h1>
    <h2>F-01 QMS (Version 4.00, 17.12.2020)</h2>

    <!-- ✅ GENERAL INFO -->
    <div class="section-title">GENERAL INFORMATION</div>
    <table>
        <tr>
            <td class="label">Organization Name</td>
            <td><?= $org_name ?></td>
        </tr>
        <tr>
            <td class="label">Head Office Address</td>
            <td><?= $head_office ?></td>
        </tr>
        <tr>
            <td class="label">Main Operative Site</td>
            <td><?= $main_site ?></td>
        </tr>
        <tr>
            <td class="label">Other Sites</td>
            <td><?= $other_sites ?></td>
        </tr>
    </table>

    <!-- ✅ TOP MANAGEMENT -->
    <div class="section-title">TOP MANAGEMENT</div>
    <table>
        <tr>
            <td class="label">Name</td>
            <td><?= $tm_name ?></td>
      
            <td class="label">Designation</td>
            <td><?= $tm_designation ?></td>
        </tr>
        <tr>
            <td class="label">Mobile</td>
            <td><?= $tm_mobile ?></td>
       
            <td class="label">Email</td>
            <td><?= $tm_email ?></td>
        </tr>
    </table>

    <!-- ✅ CONTACT PERSON -->
    <div class="section-title">CONTACT PERSON</div>
    <table>
        <tr>
            <td  class="label">Name</td>
            <td><?= $contact_name ?></td>
       
            <td class="label">Position</td>
            <td><?= $contact_pos ?></td>
       
            <td class="label">Mobile</td>
            <td><?= $contact_mobile ?></td>
             </tr>
        <tr>
            
      
            <td class="label">Email</td>
            <td><?= $contact_email ?></td>
       
            <td class="label">Tel</td>
            <td><?= $tel ?></td>
       
            <td class="label">Fax</td>
            <td><?= $fax ?></td>
       
            <td class="label">Website</td>
            <td><?= $website ?></td>
        </tr>
    </table>

    <!-- ✅ CERTIFICATION & SCOPE -->
    <div class="section-title">CERTIFICATION & SCOPE</div>
    <table>
        <tr>
            <td class="label">Products / Services</td>
            <td><?= $products_services ?></td>
        </tr>
        <tr>
            <td class="label">Desired Scope of Certification</td>
            <td><?= $scope_cert ?></td>
        </tr>
        <tr>
            <td class="label">Certification Scheme Applied</td>
            <td><?= $cert_scheme ?></td>
        </tr>
        <tr>
            <td class="label">Accreditation</td>
            <td><?= $accreditation ?></td>
        </tr>
        <tr>
            <td class="label">Exclusions (ISO 9001)</td>
            <td><?= $exclusions ?></td>
        </tr>
        <tr>
            <td class="label">Justification</td>
            <td><?= $justification ?></td>
        </tr>
    </table>

    <!-- ✅ OUTSOURCED PROCESSES -->
    <div class="section-title">OUTSOURCED PROCESSES</div>
    <table>
        <tr>
            <th>Process</th>
            <th>Supplier / Sub Contractor</th>
            <th>Compliance Status</th>
        </tr>
        <tr>
            <td><?= $op_process ?></td>
            <td><?= $op_supplier ?></td>
            <td><?= $op_compliance ?></td>
        </tr>
    </table>

    <!-- ✅ EMPLOYEE MATRIX -->
    <div class="section-title">EMPLOYEE / PROCESS / MACHINERY MATRIX</div>
    <table>
        <?php if (!empty($epme_matrix) && is_array($epme_matrix)) : ?>
            <?php foreach ($epme_matrix as $row_label => $cols): ?>
                <tr>
                    <td class="label"><?= $row_label ?></td>
                    <?php foreach ($cols as $val): ?>
                        <td><?= !empty($val) ? $val : '-' ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="10">-</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- ✅ DECLARATION -->
    <div class="section-title">DECLARATION</div>
    <table>
        <tr>
            <td class="label">Authorized Representative</td>
            <td><?= $ack_name ?></td>
        </tr>
        <tr>
            <td class="label">Designation</td>
            <td><?= $ack_design ?></td>
        </tr>
        <tr>
            <td class="label">Signature</td>
            <td><?= $ack_sign ?></td>
        </tr>
        <tr>
            <td class="label">Date</td>
            <td><?= $ack_date ?></td>
        </tr>
    </table>
</body>
</html>
