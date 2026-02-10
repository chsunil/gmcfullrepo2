<?php
/*
Plugin Name: ACF Matrix Field
Description: Adds a customizable matrix field type to ACF.
Version: 1.0
Author: Your Name
*/

if( ! defined( 'ABSPATH' ) ) exit;

// Register the field type for ACF v5 and above
add_action('acf/include_field_types', 'acf_register_matrix_field');
function acf_register_matrix_field( $version ) {
    include_once( plugin_dir_path(__FILE__) . 'acf-field-matrix.php' );
     include_once( plugin_dir_path(__FILE__) . 'acf-field-matrix-flexible.php' );
     include_once plugin_dir_path(__FILE__) . 'acf-field-math-calculation.php';

}

// Register the field type for older ACF versions
add_action('acf/register_fields', 'acf_register_matrix_field_old');
function acf_register_matrix_field_old() {
    include_once( plugin_dir_path(__FILE__) . 'acf-field-matrix.php' );
     include_once( plugin_dir_path(__FILE__) . 'acf-field-matrix-flexible.php' );
     include_once plugin_dir_path(__FILE__) . 'acf-field-math-calculation.php';

}
add_action('acf/input/admin_enqueue_scripts', function(){
  wp_enqueue_script(
    'acf-math-calc',
    plugin_dir_url(__FILE__) . 'acf-math-calc.js',
    ['acf-input','jquery'],  // ensure ACF and jQuery load first
    '1.0',
    true
  );
});
