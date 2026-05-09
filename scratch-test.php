<?php
require_once 'wp-load.php';

echo "SITEURL: " . get_option('siteurl') . "\n";

$clients = get_posts([
    'post_type' => 'client',
    'posts_per_page' => 5,
]);

echo "CLIENTS:\n";
foreach ($clients as $client) {
    $cert_type = get_field('certification_type', $client->ID);
    $stage = get_field('client_stage', $client->ID);
    echo "ID: {$client->ID} | Title: {$client->post_title} | Type: {$cert_type} | Stage: {$stage}\n";
}
