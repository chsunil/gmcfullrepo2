<?php
/**
 * Single Invoice View/Print Template
 * Template for post type: gmc_invoice
 */

get_header();

$post_id      = get_the_ID();
$invoice_no   = get_field('invoice_no',     $post_id) ?: 'N/A';
$invoice_date = get_field('invoice_date',   $post_id) ?: '';
$gst_type     = get_field('gst_type',       $post_id) ?: 'cgst_sgst';
$cgst_p       = (float)(get_field('cgst_percent', $post_id) ?? 9);
$sgst_p       = (float)(get_field('sgst_percent', $post_id) ?? 9);
$igst_p       = (float)(get_field('igst_percent', $post_id) ?? 18);
$subtotal     = (float)(get_field('subtotal',     $post_id) ?? 0);
$cgst_amt     = (float)(get_field('cgst_amount',  $post_id) ?? 0);
$sgst_amt     = (float)(get_field('sgst_amount',  $post_id) ?? 0);
$igst_amt     = (float)(get_field('igst_amount',  $post_id) ?? 0);
$total_amt    = (float)(get_field('total_amount', $post_id) ?? 0);
$amt_words    = get_field('amount_in_words', $post_id) ?: '';
$status       = get_field('status', $post_id) ?: 'Unpaid';
$line_items   = get_field('line_items', $post_id) ?: [];

// Client
$client_id      = get_field('client_id', $post_id);
$client_name    = '';
$client_address = '';
$client_gst     = '';
if ($client_id) {
    $client_name    = get_field('organization_name', $client_id) ?: get_the_title($client_id);
    $addr           = get_field('address', $client_id);
    $client_address = $addr['head_office'] ?? '';
    $client_gst     = get_field('cgt_regn_no', $client_id) ?: '';
}

$edit_link = add_query_arg(['invoice_id' => $post_id], site_url('/invoice-form/'));
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php get_sidebar('custom'); ?>
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">

                    <!-- Actions Bar (hidden on print) -->
                    <div class="no-print d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <a href="<?php echo site_url('/invoices/'); ?>" class="btn btn-outline-secondary me-2">
                                <i class="bx bx-arrow-back me-1"></i> Back to List
                            </a>
                            <a href="<?php echo esc_url($edit_link); ?>" class="btn btn-outline-primary me-2">
                                <i class="bx bx-edit me-1"></i> Edit
                            </a>
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="bx bx-printer me-1"></i> Print
                            </button>
                        </div>
                        <?php
                        $badges = ['Paid' => 'bg-label-success', 'Unpaid' => 'bg-label-danger', 'Partial' => 'bg-label-warning'];
                        $badge  = $badges[$status] ?? 'bg-label-secondary';
                        ?>
                        <span class="badge <?php echo esc_attr($badge); ?> fs-6"><?php echo esc_html($status); ?></span>
                    </div>

<?php
$is_paid    = (strtolower($status) === 'paid');
$title      = $is_paid ? 'INVOICE' : 'PROFORMA INVOICE';
$inv_prefix = $is_paid ? 'INV. No' : 'P.INV. No';
?>

<!-- Invoice Paper -->
<div class="card">
    <div class="card-body p-5">
        <div class="invoice-paper">

            <!-- Logo (visible on print) -->
            <div class="text-center mb-3 d-none d-print-block">
                <?php
                $logo = get_stylesheet_directory_uri() . '/sneat-assets/img/logo.png'; // Re-use logo
                ?>
                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/sneat-assets/img/logo.png'); ?>" style="height:70px;" alt="Global MCS">
            </div>

            <!-- Title -->
            <div class="text-center mb-4">
                <h2 class="invoice-title"><?php echo esc_html($title); ?></h2>
            </div>

            <!-- Top: Client + Meta -->
            <table class="invoice-table w-100">
                <tr>
                    <td style="width:60%; vertical-align:top; padding:12px 16px;">
                        <div><strong>To,</strong></div>
                        <div class="mt-1 fw-bold"><?php echo esc_html($client_name); ?></div>
                        <div style="white-space:pre-line; font-size: 11px;"><?php echo esc_html($client_address); ?></div>
                        <?php if ($client_gst): ?>
                        <div class="mt-1"><strong>GST No:</strong> <?php echo esc_html($client_gst); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="width:40%; padding:0; vertical-align:top;">
                        <table class="invoice-meta-table w-100 h-100">
                            <tr>
                                <td class="meta-label"><?php echo esc_html($inv_prefix); ?></td>
                                <td><?php echo esc_html($invoice_no); ?></td>
                            </tr>
                            <tr>
                                <td class="meta-label">Date</td>
                                <td><?php echo esc_html($invoice_date); ?></td>
                            </tr>
                            <tr>
                                <td class="meta-label">Pan No</td>
                                <td>AAGCG3405N</td>
                            </tr>
                            <tr>
                                <td class="meta-label">GST Regn. No.</td>
                                <td>36 AAGCG3405N1ZH</td>
                            </tr>
                            <tr>
                                <td class="meta-label" style="border-bottom:none;">SAC CODE</td>
                                <td style="border-bottom:none;">998214</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Line Items -->
            <table class="invoice-table w-100 mt-4">
                <thead>
                    <tr>
                        <th style="width:8%; text-align:center;">S.No</th>
                        <th style="text-align:center;">Description</th>
                        <th style="width:25%; text-align:center;">Total Amount (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($line_items as $i => $item): ?>
                    <tr>
                        <td class="text-center"><?php echo $i + 1; ?>.</td>
                        <td><?php echo esc_html($item['description']); ?></td>
                        <td class="text-end"><?php echo number_format((float)$item['amount'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- GST Rows -->
                    <?php if ($gst_type === 'cgst_sgst'): ?>
                    <tr>
                        <td class="text-center"><?php echo count($line_items) + 1; ?>.</td>
                        <td>CGST @ <?php echo $cgst_p; ?>%</td>
                        <td class="text-end"><?php echo number_format($cgst_amt, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="text-center"><?php echo count($line_items) + 2; ?>.</td>
                        <td>SGST @ <?php echo $sgst_p; ?>%</td>
                        <td class="text-end"><?php echo number_format($sgst_amt, 2); ?></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td class="text-center"><?php echo count($line_items) + 1; ?>.</td>
                        <td>IGST @ <?php echo $igst_p; ?>%</td>
                        <td class="text-end"><?php echo number_format($igst_amt, 2); ?></td>
                    </tr>
                    <?php endif; ?>

                    <!-- Total Row -->
                    <tr>
                        <td colspan="2" class="text-end fw-bold">Total:</td>
                        <td class="text-end fw-bold"><?php echo number_format($total_amt, 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- Footer Tables -->
            <table class="invoice-table w-100 mt-4">
                <tr>
                    <td style="width:40%; border-right:1px solid #333;">
                        <strong>Payment Terms:</strong><br>
                        100% on presentation.
                    </td>
                    <td style="border:none;">
                        <strong>Amount in Words:</strong><br>
                        <em><?php echo esc_html($amt_words); ?></em>
                    </td>
                </tr>
            </table>

            <table class="invoice-table w-100 mt-4">
                <tr>
                    <td style="border:none;">
                        <strong>Bank Account Details:</strong><br>
                        Global Management Certification Services Pvt. Ltd.<br>
                        Bank &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: State Bank of India.<br>
                        Branch &nbsp;&nbsp;: Road No.1, KPHB Colony, Kukatpally, Hyd.<br>
                        A/c No. &nbsp;: 67384332714<br>
                        IFSC Code : SBIN0070743
                    </td>
                </tr>
            </table>

            <!-- Signature -->
            <div class="text-end mt-4 sign-box">
                <div>For Global Management Certification Services Pvt. Ltd.</div>
                <?php
                $sign_url = get_stylesheet_directory_uri() . '/sneat-assets/img/invoicesign.jpeg';
                ?>
                <div class="mt-2"><img src="<?php echo esc_url($sign_url); ?>" style="height:80px;" alt="Signature"></div>
                <div class="mt-1"><strong>Authorized Signatory</strong></div>
            </div>

            <!-- Green Footer (visible on print) -->
            <div class="d-none d-print-block mt-4 pt-2" style="border-top: 2px solid #2e7d32; color: #2e7d32;">
                <div style="font-size: 14pt; font-weight: bold;">Global Management Certification Services Pvt. Ltd.</div>
                <div class="d-flex justify-content-between mt-1" style="font-size: 9pt; color: #333;">
                    <div>
                        Flat No.402, Plot No.410, Matrusri Nagar, Miyapur,<br>
                        Hyderabad-500 049, Telangana, India.
                    </div>
                    <div class="text-end">
                        Phone: 040 - 4855 9001<br>
                        E-mail: info@mcsglobal.in | Web: www.mcsglobal.in
                    </div>
                </div>
            </div>

        </div><!-- /.invoice-paper -->
    </div>
</div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.invoice-paper { max-width: 900px; margin: 0 auto; font-family: "Times New Roman", serif; font-size: 13px; color: #000; }
.invoice-title { font-size: 1.4rem; font-weight: bold; text-decoration: underline; letter-spacing: 2px; }
.invoice-table { border-collapse: collapse; }
.invoice-table td, .invoice-table th { border: 1px solid #333; padding: 6px 10px; vertical-align: middle; }
.invoice-meta-table { border-collapse: collapse; height: 100%; }
.invoice-meta-table td { border: 1px solid #333; padding: 5px 8px; }
.meta-label { font-weight: bold; white-space: nowrap; }
@media print {
    .no-print, .layout-menu, .layout-navbar, nav, #sidebar-custom, header { display: none !important; }
    .layout-wrapper, .layout-container, .layout-page, .content-wrapper { display: block !important; }
    .card { border: none !important; box-shadow: none !important; }
    .card-body { padding: 0 !important; }
    body { margin: 0; }
    .invoice-paper { max-width: 100%; }
    @page { size: A4; margin: 15mm; }
}
</style>

<?php get_footer(); ?>
