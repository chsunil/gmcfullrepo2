<?php
define('WP_USE_THEMES', false);
require_once('wp-load.php');

$client_id = 4882;
$audit_fields = [
    'application_date',
    'application_review_date',
    'agreement',
    'auditor_allocation',
    'stage1_intimation_date_yearly_surveillance_intimation_for_s1_&_s2',
    'stage1_audit',
    'stage2_intimation_date_surveillance_intimation_date',
    'stage2_audit_surveillance_audit_date',
    'certification_decision_date',
    'internal_audit_date',
    'mrm_date',
    'certificate_issue_date',
    'certificate_expiry_date'
];

$output = "Audit Dates for Client $client_id\n";
$output .= "================================\n\n";

foreach ($audit_fields as $base) {
    foreach (['_initial', '_surv1', '_surv2'] as $suffix) {
        $meta_key = $base . $suffix;
        $val = get_post_meta($client_id, $meta_key, true);
        $acf_val = get_field($meta_key, $client_id);
        $output .= sprintf("%-70s | Meta: %-15s | ACF: %-15s\n", $meta_key, var_export($val, true), var_export($acf_val, true));
    }
}

$output .= "\nLegacy Sync Fields\n";
$output .= "==================\n";
$legacy_map = [
    'f2reviweddate',
    'initial_audit_to_be_held_in',
    '1st_surveillance_in_',
    '2nd_Surveillance_in',
    'internal_audit_date',
    'mrm-audit_date'
];

foreach ($legacy_map as $key) {
    $val = get_post_meta($client_id, $key, true);
    $output .= sprintf("%-30s | Meta: %s\n", $key, var_export($val, true));
}

file_put_contents('dump_4882.txt', $output);
echo "Dumped data to dump_4882.txt\n";
