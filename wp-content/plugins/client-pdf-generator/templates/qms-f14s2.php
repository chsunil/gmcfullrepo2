<?php
// ===============================
// F14S2 – FINAL (Excel-accurate)
// ===============================

// Header fields
$ref_no   = get_field('s2_ref_no', $post_id);
$customer = get_field('s2_customer:', $post_id); // yes, colon is correct per ACF

$html = '
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
.title {
    font-weight: bold;
    text-align: center;
    border: none;
}
.subtitle {
    text-align: center;
    border: none;
}
.italic {
    font-style: italic;
}
</style>

<table>

<tr>
    <td colspan="10" class="title">
        CONFIDENTIALITY AND NO CONFLICT OF INTEREST DECLARATION
    </td>
</tr>

<tr>
    <td colspan="10" class="subtitle">
        F-14 (Version 2.00, 20.03.2016)
    </td>
</tr>

<tr>
    <td colspan="2"><strong>Ref No.:</strong></td>
    <td colspan="8">' . esc_html($ref_no) . '</td>
</tr>

<tr>
    <td colspan="2"><strong>Customer:</strong></td>
    <td colspan="8">' . esc_html($customer) . '</td>
</tr>

<tr>
    <td colspan="10">
        I have executed an agreement Assessor Agreement; F-36 with Global Management Certification Services Pvt. Ltd.
        to provide Certification related services to GMCSPL and its sub-contractors.
        <br>
        I am obligated to execute this Confidential Information and No Conflict of Interest Agreement for each client
        for which I perform Certification Activities.
    </td>
</tr>

<tr>
    <td colspan="8"><strong>This section shall be confirmed by Each audit team member;</strong></td>
    <td colspan="2"><strong>Remark</strong></td>
</tr>
';

// ===============================
// Declaration matrix (dynamic)
// ===============================
$matrix = get_field(
    's2_This_section_shall_be_confirmed_by_Each_audit_team_member_copy',
    $post_id
);

if (!empty($matrix) && is_array($matrix)) {
    foreach ($matrix as $row) {
        $observation = isset($row['observation']) ? $row['observation'] : '';
        $remarks     = isset($row['remarks']) ? $row['remarks'] : '';

        $html .= '
        <tr>
            <td colspan="8">' . esc_html($observation) . '</td>
            <td colspan="2">' . esc_html($remarks) . '</td>
        </tr>
        ';
    }
}

// ===============================
// Signature section (dynamic)
// ===============================
$html .= '
<tr>
    <th>Role</th>
    <th colspan="4">Name</th>
    <th colspan="3">Signature</th>
    <th colspan="2">Date</th>
</tr>
';

if (have_rows('field_69760e122dd80', $post_id)) {
    while (have_rows('field_69760e122dd80', $post_id)) {
        the_row();

        $group = get_sub_field('s2__copy');
        if (!$group) {
            continue;
        }

        $role      = isset($group[0]) ? $group[0] : '';
        $signature = isset($group['s2_signaturef14']) ? $group['s2_signaturef14'] : '';
        $date      = isset($group['s2_date']) ? $group['s2_date'] : '';

        $html .= '
        <tr>
            <td>' . esc_html($role) . '</td>
            <td colspan="4"></td>
            <td colspan="3">' . esc_html($signature) . '</td>
            <td colspan="2">' . esc_html($date) . '</td>
        </tr>
        ';
    }
}

// ===============================
// Footnote
// ===============================
$html .= '
<tr>
    <td colspan="10" class="italic">
        *Only auditors and technical experts whose all answers are “Yes” can sign.
        If any answer is “No” he/she can’t participate in the subject assessment.
        The document should be signed on or before the date of assessment.
    </td>
</tr>

</table>
';
