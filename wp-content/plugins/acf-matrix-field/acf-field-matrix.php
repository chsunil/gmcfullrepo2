<?php
if( ! defined( 'ABSPATH' ) ) exit;

class ACF_Field_Matrix extends acf_field {
    
    function __construct() {
        $this->name = 'matrix';
        $this->label = __('Matrix','acf');
        $this->category = 'basic';
        $this->defaults = array(
            'rows' => '',
            'columns' => '',
        );
        parent::__construct();
    }

    function render_field_settings( $field ) {
        acf_render_field_setting( $field, array(
            'label'        => __('Rows','acf'),
            'instructions' => 'Comma-separated list of rows',
            'type'         => 'text',
            'name'         => 'rows',
        ) );
        acf_render_field_setting( $field, array(
            'label'        => __('Columns','acf'),
            'instructions' => 'Comma-separated list of columns',
            'type'         => 'text',
            'name'         => 'columns',
        ) );
    }

    function render_field( $field ) {
        $rows = array_filter(array_map('trim', explode(',', $field['rows'])));
        $cols = array_filter(array_map('trim', explode(',', $field['columns'])));
        echo '<table class="acf-matrix-field"><tr><th></th>';
        foreach($cols as $col) echo "<th>{$col}</th>";
        echo '</tr>';
        $value = isset($field['value']) ? $field['value'] : array();
        foreach($rows as $r) {
            echo '<tr><th>'.esc_html($r).'</th>';
            foreach($cols as $c) {
                $name = "{$field['name']}[{$r}][{$c}]";
                $val = isset($value[$r][$c]) ? esc_attr($value[$r][$c]) : '';
                echo '<td><input type="text" name="'.esc_attr($name).'" value="'.esc_attr($val).'" /></td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
}

// initialize field type
new ACF_Field_Matrix();
