<?php
/**
 * Class GMC_Invoice
 * 
 * Handles Post Type Registration and ACF Field Definitions for Invoices.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GMC_Invoice {

    public function __construct() {
        // add_action( 'init', [ $this, 'register_post_type' ] );
        // add_action( 'acf/init', [ $this, 'register_acf_fields' ] );
        
        // Auto-generate invoice number on save if empty
        add_action( 'acf/save_post', [ $this, 'generate_invoice_number' ], 20 );
    }

    /**
     * Register Custom Post Type
     */
    /*
    public function register_post_type() {
        $labels = [
            'name'                  => _x( 'Invoices', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Invoice', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Invoices', 'text_domain' ),
            'name_admin_bar'        => __( 'Invoice', 'text_domain' ),
            'archives'              => __( 'Invoice Archives', 'text_domain' ),
            'attributes'            => __( 'Invoice Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Invoice:', 'text_domain' ),
            'all_items'             => __( 'All Invoices', 'text_domain' ),
            'add_new_item'          => __( 'Add New Invoice', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New Invoice', 'text_domain' ),
            'edit_item'             => __( 'Edit Invoice', 'text_domain' ),
            'update_item'           => __( 'Update Invoice', 'text_domain' ),
            'view_item'             => __( 'View Invoice', 'text_domain' ),
            'view_items'            => __( 'View Invoices', 'text_domain' ),
            'search_items'          => __( 'Search Invoice', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into invoice', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this invoice', 'text_domain' ),
            'items_list'            => __( 'Invoices list', 'text_domain' ),
            'items_list_navigation' => __( 'Invoices list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter invoices list', 'text_domain' ),
        ];
        $args = [
            'label'                 => __( 'Invoice', 'text_domain' ),
            'description'           => __( 'Client Invoices and Payments', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => [ 'title', 'custom-fields' ],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-media-spreadsheet',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        ];
        register_post_type( 'gmc_invoice', $args );
    }
    */

    /**
     * Register ACF Fields programmatically
     */
    /*
    public function register_acf_fields() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        acf_add_local_field_group( [
            'key' => 'group_gmc_invoice_details',
            'title' => 'Invoice Details',
            'fields' => [
                // Tab: Basic Info
                [
                    'key' => 'field_inv_tab_basic',
                    'label' => 'Basic Info',
                    'type' => 'tab',
                ],
                [
                    'key' => 'field_inv_number',
                    'label' => 'Invoice No',
                    'name' => 'invoice_no',
                    'type' => 'text',
                    'instructions' => 'Leave empty to auto-generate like 001/MM/YY-YY',
                    'required' => 0,
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_inv_date',
                    'label' => 'Invoice Date',
                    'name' => 'invoice_date',
                    'type' => 'date_picker',
                    'display_format' => 'd/m/Y',
                    'return_format' => 'd/m/Y',
                    'default_value' => date('Ymd'),
                    'required' => 1,
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_inv_client',
                    'label' => 'Client',
                    'name' => 'client_id',
                    'type' => 'post_object',
                    'post_type' => ['client'],
                    'return_format' => 'id',
                    'ui' => 1,
                    'required' => 1,
                ],

                // Tab: Financials
                [
                    'key' => 'field_inv_tab_financials',
                    'label' => 'Financials',
                    'type' => 'tab',
                ],
                [
                    'key' => 'field_inv_line_items',
                    'label' => 'Line Items',
                    'name' => 'line_items',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'min' => 1,
                    'sub_fields' => [
                        [
                            'key' => 'field_inv_item_desc',
                            'label' => 'Description',
                            'name' => 'description',
                            'type' => 'text',
                            'required' => 1,
                        ],
                        [
                            'key' => 'field_inv_item_amount',
                            'label' => 'Amount',
                            'name' => 'amount',
                            'type' => 'number',
                            'min' => 0,
                            'step' => 0.01,
                            'required' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'field_inv_subtotal',
                    'label' => 'Subtotal',
                    'name' => 'subtotal',
                    'type' => 'number',
                    'readonly' => 1,
                    'wrapper' => ['width' => '25'],
                ],
                [
                    'key' => 'field_inv_cgst',
                    'label' => 'CGST %',
                    'name' => 'cgst_percent',
                    'type' => 'number',
                    'default_value' => 9,
                    'wrapper' => ['width' => '25'],
                ],
                [
                    'key' => 'field_inv_sgst',
                    'label' => 'SGST %',
                    'name' => 'sgst_percent',
                    'type' => 'number',
                    'default_value' => 9,
                    'wrapper' => ['width' => '25'],
                ],
                [
                    'key' => 'field_inv_total',
                    'label' => 'Total Amount',
                    'name' => 'total_amount',
                    'type' => 'number',
                    'readonly' => 1,
                    'wrapper' => ['width' => '25'],
                ],
                [
                    'key' => 'field_inv_amount_words',
                    'label' => 'Amount in Words',
                    'name' => 'amount_in_words',
                    'type' => 'text',
                ],

                // Tab: Payments
                [
                    'key' => 'field_inv_tab_payments',
                    'label' => 'Status & Payments',
                    'type' => 'tab',
                ],
                [
                    'key' => 'field_inv_status',
                    'label' => 'Status',
                    'name' => 'status',
                    'type' => 'select',
                    'choices' => [
                        'Unpaid' => 'Unpaid',
                        'Partial' => 'Partial',
                        'Paid' => 'Paid',
                    ],
                    'default_value' => 'Unpaid',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_inv_payments',
                    'label' => 'Payments Received',
                    'name' => 'payments_received',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'sub_fields' => [
                        [
                            'key' => 'field_pay_date',
                            'label' => 'Date',
                            'name' => 'payment_date',
                            'type' => 'date_picker',
                            'display_format' => 'd/m/Y',
                            'return_format' => 'd/m/Y',
                            'default_value' => date('Ymd'),
                        ],
                        [
                            'key' => 'field_pay_amount',
                            'label' => 'Amount',
                            'name' => 'amount',
                            'type' => 'number',
                        ],
                        [
                            'key' => 'field_pay_mode',
                            'label' => 'Mode',
                            'name' => 'payment_mode',
                            'type' => 'select',
                            'choices' => [
                                'Cheque' => 'Cheque',
                                'Online' => 'Online',
                                'Cash' => 'Cash',
                                'TDS' => 'TDS',
                            ],
                        ],
                        [
                            'key' => 'field_pay_ref',
                            'label' => 'Reference No / Note',
                            'name' => 'reference_no',
                            'type' => 'text',
                        ],
                    ],
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'gmc_invoice',
                    ],
                ],
            ],
        ] );
    }
    */

    /**
     * Auto-generate invoice no if empty
     */
    public function generate_invoice_number( $post_id ) {
        if ( get_post_type( $post_id ) !== 'gmc_invoice' ) {
            return;
        }

        $inv_no = get_field( 'invoice_no', $post_id );
        if ( empty( $inv_no ) ) {
            // Count total invoices to generate sequential ID
            $count = wp_count_posts( 'gmc_invoice' )->publish + 1;
            $seq = str_pad( $count, 3, '0', STR_PAD_LEFT );
            $month = date('m');
            $year = date('y');
            $next_year = $year + 1;
            
            // Format: 006/01/25-26 (Seq/Month/Year-NextYear) matches screenshot roughly
            // Screenshot says: 006/01/25-26. Let's replicate this format.
            $new_inv_no = sprintf( '%s/%s/%s-%s', $seq, $month, $year, $next_year );
            
            // Update without triggering infinite loop
            remove_action( 'acf/save_post', [ $this, 'generate_invoice_number' ], 20 );
            update_field( 'invoice_no', $new_inv_no, $post_id );
            
            // Also set post title to Invoice No
            wp_update_post( [
                'ID' => $post_id,
                'post_title' => $new_inv_no,
                'post_name'  => sanitize_title( $new_inv_no ),
            ] );
            
            add_action( 'acf/save_post', [ $this, 'generate_invoice_number' ], 20 );
        }
    }
}

new GMC_Invoice();
