<?php
/**
 * One-time cleanup: delete the 4 wrongly-created duplicate ACF field groups.
 * DELETE THIS FILE after running.
 */
define('ABSPATH', __DIR__ . '/');
require __DIR__ . '/wp-load.php';

if ( ! current_user_can('manage_options') ) {
    die('Not authorised.');
}

$keys_to_delete = [
    'group_qms_f05s1',
    'group_qms_f08s1',
    'group_qms_f14s1',
    'group_qms_sheet25',
];

$deleted = [];
$not_found = [];

foreach ( $keys_to_delete as $key ) {
    $posts = get_posts([
        'post_type'  => 'acf-field-group',
        'name'       => $key,
        'numberposts'=> 1,
        'post_status'=> 'any',
    ]);

    if ( $posts ) {
        $id = $posts[0]->ID;
        // Delete the group post + all its child field posts
        $children = get_posts([
            'post_type'  => 'acf-field',
            'post_parent'=> $id,
            'numberposts'=> -1,
            'post_status'=> 'any',
        ]);
        foreach ( $children as $child ) {
            wp_delete_post( $child->ID, true );
        }
        wp_delete_post( $id, true );
        $deleted[] = $key . ' (ID ' . $id . ')';
    } else {
        $not_found[] = $key;
    }
}
?><!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>ACF Group Cleanup</title>
<style>body{font-family:sans-serif;max-width:600px;margin:40px auto;}</style>
</head>
<body>
<h2>ACF Group Cleanup</h2>
<?php if ( $deleted ) : ?>
<p style="color:green;"><strong>Deleted:</strong></p>
<ul><?php foreach($deleted as $d) echo "<li>$d</li>"; ?></ul>
<?php endif; ?>
<?php if ( $not_found ) : ?>
<p style="color:#888;"><strong>Not found (already gone):</strong></p>
<ul><?php foreach($not_found as $n) echo "<li>$n</li>"; ?></ul>
<?php endif; ?>
<p style="color:red;font-weight:bold;">Delete this file now: <code>delete-acf-groups.php</code></p>
</body>
</html>
