<?php
if (!defined('ABSPATH')) exit;

class ACF_Field_MathCalculation extends acf_field {

    function __construct() {
        $this->name = 'math_calculation';
        $this->label = __('Math Calculation', 'acf');
        $this->category = 'basic';
        $this->defaults = ['formula' => '', 'readonly' => 1];
        parent::__construct();
    }

    function render_field_settings($field) {
        acf_render_field_setting($field, [
            'label' => __('Formula', 'acf'),
            'instructions' => 'Use field names like "field_a * 2 / 3 + field_b"',
            'type' => 'text',
            'name' => 'formula'
        ]);
        acf_render_field_setting($field, [
            'label' => __('Read Only', 'acf'),
            'type' => 'true_false',
            'name' => 'readonly',
            'ui' => 1
        ]);
    }

    function render_field($field) {
        $readonly = $field['readonly'] ? 'readonly' : '';
        echo '<input type="text" name="' . esc_attr($field['name']) . '" value="' . esc_attr($field['value']) . '" ' . $readonly . ' class="acf-math-calc" data-formula="' . esc_attr($field['formula']) . '">';
    }

}

new ACF_Field_MathCalculation();