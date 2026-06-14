<?php
/*
Plugin Name: Client PDF Generator
Description: Generate Client & QMS PDFs via AJAX using DOMPDF.
Version:     1.2.4
Author:      Sunil
*/

if (! defined('ABSPATH')) exit;

// 1) Load DOMPDF on init
add_action('init', function () {
    require_once plugin_dir_path(__FILE__) . 'dompdf/autoload.inc.php';
});

// 2) Enqueue our JS
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('cpdf-generate-pdf', plugin_dir_url(__FILE__) . 'assets/js/generate-pdf.js', ['jquery', 'toast-helper'], '1.3', true);
    wp_localize_script('cpdf-generate-pdf', 'cpdf_vars', [
        'ajax_url'           => admin_url('admin-ajax.php'),
        'generate_pdf_nonce' => wp_create_nonce('cpdf_generate_pdf'),
    ]);
});

// 3) AJAX handler
add_action('wp_ajax_generate_pdf', 'cpdf_handle_generate_pdf');
function cpdf_handle_generate_pdf() {
    check_ajax_referer('cpdf_generate_pdf', 'nonce');

    $post_id = intval($_POST['post_id'] ?? 0);
    $scheme  = sanitize_text_field($_POST['scheme']  ?? 'ems');
    $stage   = sanitize_text_field($_POST['stage']   ?? 'f03');
    if (! $post_id || get_post_type($post_id) !== 'client') {
        wp_send_json_error(['message' => 'Invalid post ID']);
    }

    // ── F-25: each PDF covers exactly ONE Assessment Check List stage column ──
    // (Initial Certification / Surveillance-1 / Surveillance-2 — separate PDF each)
    $f25_print_stages = [];
    $f25_suffix = '';
    $pdf_field_key = "{$stage}_pdf";
    if ($stage === 'f25') {
        $valid_stage_codes = [
            'initial_certification' => 'initial',
            'surveillance_1'        => 'surv1',
            'surveillance_2'        => 'surv2',
        ];
        $requested = sanitize_text_field($_POST['print_stages'] ?? '');
        if (! isset($valid_stage_codes[$requested])) {
            wp_send_json_error(['message' => 'F-25 PDF requires a single valid stage (Initial / Surveillance-1 / Surveillance-2).']);
        }
        $variant_code     = $valid_stage_codes[$requested];
        $f25_print_stages = [$requested];
        $f25_suffix       = "-{$variant_code}";
        $pdf_field_key    = "f25_pdf_{$variant_code}";
    }
    set_query_var('cpdf_print_stages', $f25_print_stages);

    // 3.1) Locate the HTML template (with fallback to QMS)
    $tpl = plugin_dir_path(__FILE__) . "templates/{$scheme}/{$scheme}-{$stage}.php";
    if (! file_exists($tpl) && $scheme === 'ims') {
        // Special case for IMS: many forms are identical to QMS but just need rebranding
        $fallback_tpl = plugin_dir_path(__FILE__) . "templates/qms/qms-{$stage}.php";
        if (file_exists($fallback_tpl)) {
            $tpl = $fallback_tpl;
        }
    }

    if (! file_exists($tpl)) {
        wp_send_json_error(['message' => "Template not found: {$scheme}-{$stage}"]);
    }

    // 3.2) Capture its output
    global $post;
    $post = get_post($post_id);
    setup_postdata($post);
    set_query_var('cpdf_post_id', $post_id);
    set_query_var('cpdf_scheme', $scheme); // Pass the scheme (qms/ims/ems) to the template
    ob_start();
    include $tpl;
    $html = ob_get_clean();

    // 3.3) Generate PDF
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 3.4) Save it
    $upload = wp_upload_dir();
    $dir    = trailingslashit($upload['basedir']) . 'client_pdfs/';
    if (! is_dir($dir)) wp_mkdir_p($dir);

    $filename = sprintf(
        '%s-%d%s-%s.pdf',
        strtoupper("{$scheme}_{$stage}"),
        $post_id,
        $f25_suffix,
        date('Ymd_His')
    );
    $path = $dir . $filename;
    file_put_contents($path, $dompdf->output());

    $url = trailingslashit($upload['baseurl']) . "client_pdfs/{$filename}";
    update_field( $pdf_field_key, $url, $post_id);

    wp_send_json_success(['pdf_url' => $url]);
}

// 4) DELETE PDF AJAX handler
add_action('wp_ajax_delete_pdf', 'cpdf_handle_delete_pdf');
function cpdf_handle_delete_pdf() {
    check_ajax_referer('cpdf_generate_pdf', 'nonce');

    $post_id = intval($_POST['post_id'] ?? 0);
    $stage   = sanitize_text_field($_POST['stage']  ?? '');
    $variant = sanitize_text_field($_POST['variant'] ?? '');

    if (!$post_id || !$stage) {
        wp_send_json_error(['message' => 'Invalid parameters']);
    }

    $field_key = "{$stage}_pdf";
    if ($stage === 'f25') {
        if (! in_array($variant, ['initial', 'surv1', 'surv2'], true)) {
            wp_send_json_error(['message' => 'Invalid F-25 PDF variant']);
        }
        $field_key = "f25_pdf_{$variant}";
    }

    $pdf_url   = get_field($field_key, $post_id);

    if ($pdf_url) {
        // Attempt to delete physical file
        $upload_dir = wp_upload_dir();
        $base_url   = trailingslashit($upload_dir['baseurl']) . 'client_pdfs/';
        $base_path  = trailingslashit($upload_dir['basedir']) . 'client_pdfs/';
        
        $filename   = str_replace($base_url, '', $pdf_url);
        $file_path  = $base_path . $filename;

        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Clear ACF field
        update_field($field_key, '', $post_id);
    }

    wp_send_json_success(['message' => 'PDF deleted successfully']);
}
