<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>F-06 Documentation Review Report</title>


  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
    }

    th,
    td {
      border: 1px solid #333;
      padding: 6px;
      vertical-align: top;
    }

    th {
      background: #eee;
    }

    .header td {
      border: none;
      padding: 4px;
    }

    .header .title {
      text-align: center;
      font-size: 16px;
      font-weight: bold;
    }
  </style>
</head>

<body>
    <?php
  // fetch our ACF fields
  $org          = get_field('organization_name',$post_id) ?: '—';
  // $date         = get_field('f06_date',$post_id) ?: date('d-m-Y');
  // $standard     = get_field('f06_standard',$post_id) ?: '—';
  // $auditors     = get_field('f06_auditors',$post_id) ?: [];
  // $sites        = get_field('f06_sites',$post_id) ?: [];
  // $quality_date = get_field('f06_quality_manual_date',$post_id) ?: '';
  // $quality_no   = get_field('f06_quality_manual_issueno', $post_id) ?: '';
  // $clauses      = get_field('f06_clauses', $post_id) ?: [];

  // start buffering
  ?>
  <table class="header">
    <tr>
      <td>
        <div style="text-align:center;">
        <img alt="" src="data:image/jpeg;base64,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" />    
            </div>
      </td>
      <td class="title">DOCUMENTATION REVIEW REPORT</td>
      <td>F-06 (Version 2.00, 20.03.2016)</td>
    </tr>
  </table>

  <table class="header" style="margin-top:8px;">
    <tr>
      <td><strong>Organization:</strong> <?php echo esc_html($org) ?></td>
      <td><strong>Date:</strong> </td>
    </tr>
    <tr>
      <td><strong>Standard:</strong> </td>
      <td><strong>Auditor(s):</strong> </td>
    </tr>
    <tr>
      <td colspan="2"><strong>Sites:</strong> </td>
    </tr>
    <tr>
      <td><strong>Quality Manual Date:</strong></td>
      <td><strong>Issue No:</strong> </td>
    </tr>
  </table>

  <table style="margin-top:12px;">
    <tr>
      <th style="width:8%">Cl No</th>
      <th style="width:52%">Requirement</th>
      <th style="width:10%">Compliance</th>
      <th>Comments</th>
    </tr>
<tr><td colspan="4"><strong>4. Context of the Organization</strong></td></tr>
    <tr><td>4.1</td><td>Understanding the Organization and its Context</td><td>Yes</td><td>SUB-SECTION NO 4.1 OF QM</td></tr>
    <tr><td>4.2</td><td>Understanding the needs and expectations of interested parties</td><td>Yes</td><td>SUB-SECTION NO 4.2 OF QM</td></tr>
    <tr><td>4.3</td><td>Determining the scope of the quality management system</td><td></td><td>SUB-SECTION NO 4.3 OF QM</td></tr>
    <tr><td>4.4</td><td>Quality Management System and its Processes</td><td></td><td>SUB-SECTION NO 4.4 OF QM</td></tr>

    <tr><td colspan="4"><strong>5. Leadership</strong></td></tr>
    <tr><td>5.1</td><td>Leadership and Commitment</td><td>Yes</td><td>SUB-SECTION NO 5.1 OF QM</td></tr>
    <tr><td>5.2</td><td>Quality Policy</td><td>Yes</td><td>SUB-SECTION NO 5.2 OF QM</td></tr>
    <tr><td>5.3</td><td>Organizational roles, responsibilities and authorities</td><td>Yes</td><td>SUB-SECTION NO 5.3 OF QM</td></tr>

    <tr><td colspan="4"><strong>6. Planning</strong></td></tr>
    <tr><td>6.1</td><td>Actions to address risks and opportunities</td><td>Yes</td><td>SUB-SECTION NO 6.1 OF QM</td></tr>
    <tr><td>6.2</td><td>Quality objectives and planning to achieve them</td><td>Yes</td><td>SUB-SECTION NO 6.2 OF QM</td></tr>
    <tr><td>6.3</td><td>Planning of changes</td><td>Yes</td><td>SUB-SECTION NO 6.3 OF QM</td></tr>

    <tr><td colspan="4"><strong>7. Support</strong></td></tr>
    <tr><td>7.1</td><td>Resources</td><td>Yes</td><td>SUB-SECTION NO 7.1 OF QM</td></tr>
    <tr><td>7.2</td><td>Competence</td><td>Yes</td><td>SUB-SECTION NO 7.2 OF QM</td></tr>
    <tr><td>7.3</td><td>Awareness</td><td>Yes</td><td>SUB-SECTION NO 7.3 OF QM</td></tr>
    <tr><td>7.4</td><td>Communication</td><td>Yes</td><td>SUB-SECTION NO 7.4 OF QM</td></tr>
    <tr><td>7.5</td><td>Documented Information</td><td>Yes</td><td>SUB-SECTION NO 7.5 OF QM</td></tr>

    <tr><td colspan="4"><strong>8. Operation</strong></td></tr>
    <tr><td>8.1</td><td>Operational Planning and Control</td><td>Yes</td><td>SUB-SECTION NO 8.1 OF QM</td></tr>
    <tr><td>8.2</td><td>Requirements for Products and Services</td><td>Yes</td><td>SUB-SECTION NO 8.2 OF QM</td></tr>
    <tr><td>8.3</td><td>Design and Development of Products and Services</td><td>Yes</td><td>Excluded</td></tr>
    <tr><td>8.4</td><td>Control of externally provided processes, products and services</td><td>Yes</td><td>SUB-SECTION NO 8.4 OF QM</td></tr>
    <tr><td>8.5</td><td>Production and Service Provision</td><td>Yes</td><td>SUB-SECTION NO 8.5 OF QM</td></tr>
    <tr><td>8.6</td><td>Release of Products and Services</td><td>Yes</td><td>SUB-SECTION NO 8.6 OF QM</td></tr>
    <tr><td>8.7</td><td>Control of nonconforming outputs</td><td>Yes</td><td>SUB-SECTION NO 8.7 OF QM</td></tr>

    <tr><td colspan="4"><strong>9. Performance Evaluation</strong></td></tr>
    <tr><td>9.1</td><td>Monitoring, measurement, analysis and evaluation</td><td>Yes</td><td>SUB-SECTION NO 9.1 OF QM</td></tr>
    <tr><td>9.2</td><td>Internal Audit</td><td>Yes</td><td>SUB-SECTION NO 9.2 OF QM</td></tr>
    <tr><td>9.3</td><td>Management Review</td><td>Yes</td><td>SUB-SECTION NO 9.3 OF QM</td></tr>

    <tr><td colspan="4"><strong>10. Improvement</strong></td></tr>
    <tr><td>10.1</td><td>General</td><td>Yes</td><td>SUB-SECTION NO 10.1 OF QM</td></tr>
    <tr><td>10.2</td><td>Non Conformity and Corrective action</td><td>Yes</td><td>SUB-SECTION NO10.2 OF QM</td></tr>
    <tr><td>10.3</td><td>Continual Improvement</td><td>Yes</td><td>SUB-SECTION NO 10.3 OF QM</td></tr>
 

   
  </table>



</body>
</html>
