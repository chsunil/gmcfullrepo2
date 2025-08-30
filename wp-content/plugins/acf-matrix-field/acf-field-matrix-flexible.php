<?php
if (!defined('ABSPATH')) exit;

class ACF_Field_MatrixFlexible extends acf_field {

    function __construct() {
        $this->name = 'matrix_flexible';
        $this->label = __('Matrix Flexible', 'acf');
        $this->category = 'layout';
        $this->defaults = [
            'rows' => '',
            'columns_json' => '[]',
        ];
        parent::__construct();
    }

    function render_field_settings($field) {
        acf_render_field_setting($field, [
            'label' => __('Rows', 'acf'),
            'instructions' => __('Separated list of row labels with |', 'acf'),
            'type' => 'textarea',
            'name' => 'rows'
        ]);

        acf_render_field_setting($field, [
            'label' => __('Columns (JSON)', 'acf'),
            'instructions' => __('Enter JSON array: label, name, type (text|select|checkbox), width, choices (if select)', 'acf'),
            'type' => 'textarea',
            'name' => 'columns_json'
        ]);
    }

    function render_field($field) {
        // $rows = array_filter(array_map('trim', explode('|', $field['rows'])));
        // handle both string and array in $field['rows']
if ( is_array($field['rows']) ) {
    $rows = array_filter(array_map('trim', $field['rows']));
} else {
    $rows = array_filter(array_map('trim', explode('|', $field['rows'])));
}
        $columns = json_decode($field['columns_json'], true) ?: [];
        $value = isset($field['value']) ? $field['value'] : [];

        echo '<table class="acf-matrix-field table table-striped"><thead><tr><th></th>';
        foreach ($columns as $col) {
            $width = isset($col['width']) ? ' style="width:' . esc_attr($col['width']) . ';"' : '';
            echo '<th' . $width . '>' . esc_html($col['label']) . '</th>';
        }
        echo '</tr></thead><tbody>';

        foreach ($rows as $row) {
            echo '<tr><th>' . esc_html($row) . '</th>';
            foreach ($columns as $col) {
                $colName = $col['name'];
                $fieldName = "{$field['name']}[{$row}][{$colName}]";
                $val = isset($value[$row][$colName]) ? $value[$row][$colName] : '';
                echo '<td>';

                switch ($col['type']) {
                     // — TEXT INPUT (default)
                    case 'text':
                        printf(
                            '<input type="text" name="%1$s" value="%2$s" />',
                            esc_attr($fieldName),
                            esc_attr($val)
                        );
                        break;
                    // — TEXTAREA
                    case 'textarea':
                        printf(
                            '<textarea name="%1$s">%2$s</textarea>',
                            esc_attr($fieldName),
                            esc_textarea($val)
                        );
                        break;

                    // — NUMBER
                    case 'number':
                        printf(
                            '<input type="number" name="%1$s" value="%2$s" />',
                            esc_attr($fieldName),
                            esc_attr($val)
                        );
                        break;

                    // — SELECT
                    case 'select':
                        echo '<select name="' . esc_attr($fieldName) . '">';
                        foreach ($col['choices'] as $choice) {
                            echo '<option value="' . esc_attr($choice) . '"' . selected($val, $choice, false) . '>' . esc_html($choice) . '</option>';
                        }
                        echo '</select>';
                        break;
                        // — RADIO
                    case 'radio':
                        foreach( $col['choices'] as $choice ) {
                            printf(
                                '<label style="margin-right:1em;"><input type="radio" name="%1$s" value="%2$s"%3$s /> %2$s</label>',
                                esc_attr($fieldName),
                                esc_attr($choice),
                                checked($val, $choice, false)
                            );
                        }
                        break;

                    // — CHECKBOX
                    case 'checkbox':
                        echo '<input type="checkbox" name="' . esc_attr($fieldName) . '" value="1"' . checked($val, '1', false) . ' />';
                        break;
                         // — DATE PICKER
                    case 'date_picker':
                        printf(
                            '<input type="text" class="acf-date-picker" name="%1$s" value="%2$s" />',
                            esc_attr($fieldName),
                            esc_attr($val)
                        );
                        break;
                    default:
                        echo '<input type="text" name="' . esc_attr($fieldName) . '" value="' . esc_attr($val) . '" />';
                }

                echo '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody></table>';
    }

    function input_admin_enqueue_scripts() {
        wp_enqueue_style('acf-matrix-flexible', plugin_dir_url(__FILE__) . 'acf-matrix-flexible.css');
    }
    
    function input_admin_footer() {
        // Enqueue on frontend too
        if (!is_admin()) {
            wp_enqueue_style('acf-matrix-flexible', plugin_dir_url(__FILE__) . 'acf-matrix-flexible.css');
        }
    }
}

// Register the field
new ACF_Field_MatrixFlexible();