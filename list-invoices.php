<?php
require_once('wp-load.php');
$query = new WP_Query([
    'post_type' => 'gmc_invoice',
    'posts_per_page' => 5,
    'post_status' => 'publish',
]);
$results = [];
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $results[] = [
            'ID' => get_the_ID(),
            'title' => get_the_title(),
            'status' => get_field('status'),
        ];
    }
}
echo json_encode($results);
