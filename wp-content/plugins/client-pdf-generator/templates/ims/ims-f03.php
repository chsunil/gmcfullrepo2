<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>F-03 IMS Certification Agreement</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.4; margin: 20px; }
        .center { text-align: center; }
        h1 { font-size: 15pt; margin-bottom: 0; }
        .subtitle { font-weight: bold; margin-top: 5px; }
        .address, .contact-info { font-size: 9pt; margin-top: 5px; }
        table.toc, table.details { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table.toc th, table.toc td, table.details td { border: 1px solid #000; padding: 6px; }
        table.toc th { background: #eee; }
        .article { page-break-inside: avoid; margin-bottom: 15px; }
        .article h2 { font-size: 13pt; margin-bottom: 5px; }
        .signature-section { width: 100%; margin-top: 30px; display: table; }
        .signature { display: table-cell; width: 50%; vertical-align: top; }
        .pagebreak { page-break-before: always; }
        @page { margin: 10mm; size: A4; }
        html { margin: 10mm 15mm; }
    </style>
</head>

<body>
<?php
// Core fields (cloned for IMS track)
$org             = esc_html(get_field('f03company_name', $post_id) ?: '');
$proposal_ref_no = esc_html(get_field('proposal_ref_no', $post_id)   ?: '');
$accreditation   = esc_html(get_field('accreditation', $post_id)     ?: '');
$cert_scheme     = esc_html(get_field('cert_scheme', $post_id)       ?: '');
$scope_of_certification = esc_html(get_field('scope_of_certification', $post_id) ?: '');

// Address
$addr_grp = get_field('address', $post_id) ?: [];
$head_office = esc_html($addr_grp['head_office'] ?? '');

// Fee data
$sf = get_field('f03_service_fee', $post_id) ?: [];
$sf_app_fee   = !empty($sf['application_fee']) ? $sf['application_fee'] : '10,000';
$sf_audit_fee = !empty($sf['audit_fee']) ? $sf['audit_fee'] : '';
$sf_cert_fee  = !empty($sf['certificate_issue_fee']) ? $sf['certificate_issue_fee'] : '';
$sf_surv_fee  = !empty($sf['each_surveillance_audit_fee']) ? $sf['each_surveillance_audit_fee'] : '';
$sf_audit_md  = !empty($sf['audit_man_days']) ? $sf['audit_man_days'] : '';
$sf_surv_md   = !empty($sf['surveillance_man_days']) ? $sf['surveillance_man_days'] : '';

?>
    <div class="center">
        <div style="text-align:center;">
             <img src="data:image/jpeg;base64,...(same)...">
        </div>
        <h1>CERTIFICATION AGREEMENT (IMS)</h1>
        <div class="subtitle">GLOBAL MANAGEMENT CERTIFICATION SERVICES PRIVATE LIMITED.</div>
        <div class="address">Flat No. 402, Plot No. 410, Matrusri Nagar, Miyapur, Hyderabad – 500 049, India.</div>
        <div class="contact-info">Tel: 040-4855 9001 | E-mail: info@mcsglobal.in | Website: www.mcsglobal.in</div>
    </div>

    <table class="details">
        <tr><td style="width:30%">Proposal Ref No:</td><td><?= $proposal_ref_no ?></td></tr>
        <tr><td>Company Name:</td><td><?= $org ?></td></tr>
        <tr><td>Certification Scheme:</td><td><?= $cert_scheme ?> (Integrated Management System)</td></tr>
        <tr><td>Accreditation Offered:</td><td><?= $accreditation ?></td></tr>
    </table>

    <div class="article">
        <h2>Article 1: Introduction</h2>
        <p>This agreement governs the IMS certification services provided by GLOBAL MCS...</p>
    </div>

    <div class="pagebreak"></div>
    <div class="article">
        <h2>Article 17: Service Fee (IMS)</h2>
        <table class="toc">
            <thead>
                <tr><th>Elements</th><th>Fee (Rs.)</th><th>Audit Man days</th><th>Remarks</th></tr>
            </thead>
            <tbody>
                <tr><td>Application</td><td><?= $sf_app_fee ?>/-</td><td rowspan="3" style="text-align:center;"><?= $sf_audit_md ?></td><td></td></tr>
                <tr><td>Audit</td><td><?= $sf_audit_fee ?>/-</td><td></td></tr>
                <tr><td>Certificate Issue</td><td><?= $sf_cert_fee ?>/-</td><td></td></tr>
                <tr><td>Each Surveillance</td><td><?= $sf_surv_fee ?>/-</td><td style="text-align:center;"><?= $sf_surv_md ?></td><td>Annual</td></tr>
            </tbody>
        </table>
    </div>

    <div class="signature-section">
        <div class="signature"><p><strong>For GLOBAL MCS</strong></p><p>Signature: ________________</p></div>
        <div class="signature"><p><strong>For Client</strong></p><p>Signature: ________________</p></div>
    </div>

    <div style="margin-top:20px; border-top:1px solid #000; font-size:9pt;">
        <span style="float:left;">Global MCS</span>
        <span style="float:right;">F-03 IMS (Version 1.00, 11.04.2024)</span>
    </div>

</body>
</html>
