<?php
/**
 * Template Name: Invoice Form
 */

// Enqueue flatpickr
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', [], '4.6.13', true);
});

get_header();

// Determine Action (Create or Edit)
$invoice_id = isset($_GET['invoice_id']) ? intval($_GET['invoice_id']) : 0;
$is_editing = $invoice_id > 0;
$page_title = $is_editing ? 'Edit Invoice' : 'Create New Invoice';
$return_url = site_url('/invoices/');

// ── Pre-load data if editing ──────────────────────────────────────────────────
$data = [
    'invoice_no'     => '',
    'invoice_date'   => date('d/m/Y'),
    'client_id'      => '',
    'client_name'    => '',
    'client_address' => '',
    'client_gst'     => '',
    'line_items'     => [['description' => '', 'amount' => '']],
    'gst_type'       => 'cgst_sgst', // or 'igst'
    'cgst_percent'   => 9,
    'sgst_percent'   => 9,
    'igst_percent'   => 18,
    'subtotal'       => 0,
    'total_amount'   => 0,
    'amount_in_words'=> '',
    'status'         => 'Unpaid',
    'team_member'    => '',
    'gst_regn_no'    => '36AAGCG3405N1ZH', // Default GST
];

if ($is_editing) {
    $data['invoice_no']      = get_field('invoice_no', $invoice_id) ?: '';
    $data['invoice_date']    = get_field('invoice_date', $invoice_id) ?: date('d/m/Y');
    $data['cgst_percent']    = (float)(get_field('cgst_percent', $invoice_id) ?? 9);
    $data['sgst_percent']    = (float)(get_field('sgst_percent', $invoice_id) ?? 9);
    $data['igst_percent']    = (float)(get_field('igst_percent', $invoice_id) ?? 18);
    $data['subtotal']        = (float)(get_field('subtotal', $invoice_id) ?? 0);
    $data['total_amount']    = (float)(get_field('total_amount', $invoice_id) ?? 0);
    $data['amount_in_words'] = get_field('amount_in_words', $invoice_id) ?: '';
    $data['status']          = get_field('status', $invoice_id) ?: 'Unpaid';
    $data['gst_type']        = get_field('gst_type', $invoice_id) ?: 'cgst_sgst';
    $data['team_member']     = get_field('team_member', $invoice_id) ?: '';
    $data['gst_regn_no']     = get_field('gst_regn_no', $invoice_id) ?: '36AAGCG3405N1ZH';

    $raw_items = get_field('line_items', $invoice_id);
    if ($raw_items) {
        $data['line_items'] = $raw_items;
    }

    $client_id = get_field('client_id', $invoice_id);
    if ($client_id) {
        $data['client_id']      = $client_id;
        $data['client_name']    = gmc_get_organization_name($client_id) ?: get_the_title($client_id);
        $data['client_address'] = get_field('address', $client_id)['head_office'] ?? '';
        $data['client_gst']     = get_field('cgt_regn_no', $client_id) ?: '';
    }
}

// ── Auto-generate Invoice No for new invoices ─────────────────────────────────
if (!$is_editing && empty($data['invoice_no'])) {
    // Count existing invoices this financial year
    $year     = (int)date('Y');
    $month    = (int)date('n');
    $fy_start = $month >= 4 ? $year : $year - 1;
    $fy_end   = $fy_start + 1;

    $count_query = new WP_Query([
        'post_type'      => 'gmc_invoice',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'date_query'     => [
            ['after' => ($fy_start . '-03-31'), 'before' => ($fy_end . '-04-01'), 'inclusive' => true],
        ],
        'fields' => 'ids',
    ]);
    // Financial month index: April=01, May=02, ..., March=12
    $fin_month = ($month - 4 + 12) % 12 + 1;
    $fin_month_str = str_pad($fin_month, 2, '0', STR_PAD_LEFT);
    
    $seq = str_pad($count_query->found_posts + 1, 3, '0', STR_PAD_LEFT);
    $data['invoice_no'] = $seq . '/' . $fin_month_str . '/' . substr($fy_start, 2) . '-' . substr($fy_end, 2);
}
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php get_sidebar('custom'); ?>
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">

                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><?php echo esc_html($page_title); ?></h4>
                        <div>
                            <?php if ($is_editing): ?>
                            <a href="<?php echo esc_url(get_permalink($invoice_id)); ?>" class="btn btn-outline-info me-2" target="_blank">
                                <i class="bx bx-show me-1"></i> View Invoice
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo esc_url($return_url); ?>" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <!-- GST Invoice Form -->
                    <form id="gmc-invoice-form" method="post">
                        <?php wp_nonce_field('gmc_save_invoice_nonce', 'gmc_nonce'); ?>
                        <input type="hidden" name="invoice_id" value="<?php echo esc_attr($invoice_id); ?>">
                        <input type="hidden" name="action" value="gmc_save_invoice">

                        <div class="card">
                            <div class="card-body p-4">

                                <!-- Invoice Paper -->
                                <div class="invoice-paper">

                                    <!-- Header -->
                                    <div class="text-center mb-3">
                                        <?php
                                        $logo = get_stylesheet_directory_uri() . '/images/logo.png';
                                        ?>
                                        <h2 class="invoice-title">INVOICE</h2>
                                    </div>

                                    <!-- Top Section: Client + Company Meta -->
                                    <table class="invoice-table w-100 mb-0">
                                        <tr>
                                            <td class="client-cell" style="width:60%">
                                                <div class="mb-2"><strong>To,</strong></div>

                                                <!-- Client Selector -->
                                                <div class="mb-2">
                                                    <?php
                                                    $clients = get_posts(['post_type' => 'client', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
                                                    ?>
                                                    <select id="client-selector" name="client_id" class="form-select form-select-sm">
                                                        <option value="">-- Select Client --</option>
                                                        <?php foreach ($clients as $c): ?>
                                                        <option value="<?php echo $c->ID; ?>"
                                                            data-name="<?php echo esc_attr(gmc_get_organization_name($c->ID) ?: $c->post_title); ?>"
                                                            data-address="<?php
                                                                $addr = get_field('address', $c->ID);
                                                                echo esc_attr($addr['head_office'] ?? '');
                                                            ?>"
                                                            data-gst="<?php echo esc_attr(get_field('cgt_regn_no', $c->ID) ?: ''); ?>"
                                                            <?php selected($data['client_id'], $c->ID); ?>>
                                                            <?php echo esc_html(gmc_get_organization_name($c->ID) ?: $c->post_title); ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- Client Info (auto-filled) -->
                                                <div id="client-details-display">
                                                    <div class="fw-bold" id="display-client-name"><?php echo esc_html($data['client_name']); ?></div>
                                                    <div class="text-muted small" id="display-client-address" style="white-space:pre-line"><?php echo esc_html($data['client_address']); ?></div>
                                                    <div class="mt-1"><strong>GST No: </strong><span id="display-client-gst"><?php echo esc_html($data['client_gst']); ?></span></div>
                                                </div>
                                                <input type="hidden" id="hidden-client-name"    name="client_name"    value="<?php echo esc_attr($data['client_name']); ?>">
                                                <input type="hidden" id="hidden-client-address" name="client_address" value="<?php echo esc_attr($data['client_address']); ?>">
                                                <input type="hidden" id="hidden-client-gst"     name="client_gst"     value="<?php echo esc_attr($data['client_gst']); ?>">
                                            </td>
                                            <td class="meta-cell" style="width:40%">
                                                <table class="invoice-meta-table w-100">
                                                    <tr>
                                                        <td class="meta-label">INV. No</td>
                                                        <td><input type="text" name="invoice_no" class="form-control form-control-sm" value="<?php echo esc_attr($data['invoice_no']); ?>"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="meta-label">Date</td>
                                                        <td>
                                                            <input type="text" name="invoice_date" id="invoice-date" class="form-control form-control-sm"
                                                                value="<?php echo esc_attr($data['invoice_date']); ?>"
                                                                placeholder="dd/mm/yyyy">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="meta-label">Pan No</td>
                                                        <td><span class="static-field">AAGCG3405N</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="meta-label">GST Regn. No.</td>
                                                        <td><input type="text" name="gst_regn_no" class="form-control form-control-sm" value="<?php echo esc_attr($data['gst_regn_no']); ?>"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="meta-label">SAC CODE</td>
                                                        <td><span class="static-field">998214</span></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Line Items Table -->
                                    <table class="invoice-table w-100 mt-0" id="line-items-table">
                                        <thead>
                                            <tr>
                                                <th style="width:8%">S.No</th>
                                                <th>Description</th>
                                                <th style="width:20%">Amount (Rs.)</th>
                                                <th class="no-print" style="width:5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="line-items-body">
                                            <?php foreach ($data['line_items'] as $i => $item): ?>
                                            <tr class="line-item-row">
                                                <td class="text-center row-number"><?php echo $i + 1; ?></td>
                                                <td><input type="text" name="line_items[<?php echo $i; ?>][description]" class="form-control form-control-sm item-desc" value="<?php echo esc_attr($item['description']); ?>"></td>
                                                <td><input type="number" step="0.01" name="line_items[<?php echo $i; ?>][amount]" class="form-control form-control-sm item-amount text-end" value="<?php echo esc_attr($item['amount']); ?>"></td>
                                                <td class="no-print text-center">
                                                    <button type="button" class="btn btn-sm btn-link text-danger remove-item"><i class="bx bx-trash"></i>Delete</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <!-- Add Row Button -->
                                    <div class="no-print mt-2 mb-2">
                                        <button type="button" id="add-line-item" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-plus me-1"></i> Add Row
                                        </button>
                                    </div>

                                    <!-- GST Type Toggle + Totals -->
                                    <table class="invoice-table w-100 mt-0">
                                        <tr>
                                            <td style="width:70%; border-right:0" rowspan="6">
                                                <!-- GST Type -->
                                                <div class="mb-2 no-print">
                                                    <strong>GST Type:</strong>
                                                    <div class="form-check form-check-inline ms-2">
                                                        <input class="form-check-input" type="radio" name="gst_type" value="cgst_sgst" id="gst-cgst-sgst"
                                                            <?php checked($data['gst_type'], 'cgst_sgst'); ?>>
                                                        <label class="form-check-label" for="gst-cgst-sgst">CGST + SGST (Intra-State)</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="gst_type" value="igst" id="gst-igst"
                                                            <?php checked($data['gst_type'], 'igst'); ?>>
                                                        <label class="form-check-label" for="gst-igst">IGST (Inter-State)</label>
                                                    </div>
                                                </div>

                                                <!-- Payment Terms -->
                                                <div><strong>Payment Terms:</strong> 100% on presentation.</div>

                                                <!-- Amount in Words -->
                                                <div class="mt-2">
                                                    <strong>Amount in Words:</strong>
                                                    <div id="amount-in-words-display" class="fst-italic"></div>
                                                    <input type="hidden" name="amount_in_words" id="amount-in-words-hidden">
                                                </div>
                                            </td>

                                            <td class="text-end pe-2" style="border-left:2px solid #333">Subtotal:</td>
                                            <td class="text-end" style="width:15%"><span id="display-subtotal">0.00</span>
                                                <input type="hidden" name="subtotal" id="hidden-subtotal">
                                            </td>
                                        </tr>
                                        <tr id="row-cgst" class="<?php echo $data['gst_type'] === 'igst' ? 'd-none' : ''; ?>">
                                            <td class="text-end pe-2">
                                                CGST @<input type="number" name="cgst_percent" id="cgst-percent" class="form-control form-control-sm d-inline" style="width:55px" value="<?php echo esc_attr($data['cgst_percent']); ?>">%:
                                            </td>
                                            <td class="text-end"><span id="display-cgst">0.00</span>
                                                <input type="hidden" name="cgst_amount" id="hidden-cgst">
                                            </td>
                                        </tr>
                                        <tr id="row-sgst" class="<?php echo $data['gst_type'] === 'igst' ? 'd-none' : ''; ?>">
                                            <td class="text-end pe-2">
                                                SGST @<input type="number" name="sgst_percent" id="sgst-percent" class="form-control form-control-sm d-inline" style="width:55px" value="<?php echo esc_attr($data['sgst_percent']); ?>">%:
                                            </td>
                                            <td class="text-end"><span id="display-sgst">0.00</span>
                                                <input type="hidden" name="sgst_amount" id="hidden-sgst">
                                            </td>
                                        </tr>
                                        <tr id="row-igst" class="<?php echo $data['gst_type'] === 'cgst_sgst' ? 'd-none' : ''; ?>">
                                            <td class="text-end pe-2">
                                                IGST @<input type="number" name="igst_percent" id="igst-percent" class="form-control form-control-sm d-inline" style="width:55px" value="<?php echo esc_attr($data['igst_percent']); ?>">%:
                                            </td>
                                            <td class="text-end"><span id="display-igst">0.00</span>
                                                <input type="hidden" name="igst_amount" id="hidden-igst">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-end pe-2 fw-bold">Total:</td>
                                            <td class="text-end fw-bold"><span id="display-total">0.00</span>
                                                <input type="hidden" name="total_amount" id="hidden-total">
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Bank Details -->
                                    <table class="invoice-table w-100 mt-0">
                                        <tr>
                                            <td>
                                                <strong>Bank Account Details:</strong><br>
                                                Global Management Certification Services Pvt.Ltd.<br>
                                                Bank &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: State Bank of India.<br>
                                                Branch &nbsp;&nbsp;: Road No.1, KPHB Colony, Kukatpally, Hyd.<br>
                                                A/c No. &nbsp;: 67384332714<br>
                                                IFSC Code : SBIN0070743
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Footer -->
                                    <div class="text-end mt-4">
                                        <div>For Global Management Certification Services Pvt. Ltd.</div>
                                        <div class="mt-3 mb-3" style="min-height: 100px;">
                                            
                                        </div>
                                        <div><em>Authorized Signatory</em></div>
                                    </div>

                                </div><!-- /.invoice-paper -->

                                <!-- Status + Submit -->
                                <div class="row mt-4 no-print">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Invoice Status</label>
                                        <select name="status" class="form-select">
                                            <option value="Unpaid"  <?php selected($data['status'], 'Unpaid'); ?>>Unpaid</option>
                                            <option value="Partial" <?php selected($data['status'], 'Partial'); ?>>Partial</option>
                                            <option value="Paid"    <?php selected($data['status'], 'Paid'); ?>>Paid</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8 d-flex align-items-end justify-content-end gap-2">
                                        <div id="save-message" class="me-3"></div>
                                        <a href="<?php echo esc_url($return_url); ?>" class="btn btn-outline-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary" id="save-invoice-btn">
                                            <i class="bx bx-save me-1"></i>
                                            <?php echo $is_editing ? 'Update Invoice' : 'Create Invoice'; ?>
                                        </button>
                                    </div>
                                </div>

                            </div><!-- /.card-body -->
                        </div><!-- /.card -->
                    </form>

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
.invoice-meta-table { border-collapse: collapse; }
.invoice-meta-table td { border: 1px solid #333; padding: 4px 8px; vertical-align: middle; }
.meta-label { font-weight: bold; white-space: nowrap; width: 50%; }
.static-field { font-weight: 500; }
.client-cell { vertical-align: top; }
.meta-cell { vertical-align: top; padding: 0 !important; }
@media print {
    .no-print, .layout-menu, .layout-navbar, nav, .sidebar, .btn { display: none !important; }
    .invoice-paper { max-width: 100%; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Invoice Date Picker & Dynamic No ─────────────────────────────────────
    const dateInput = document.getElementById('invoice-date');
    const invNoInput = document.querySelector('input[name="invoice_no"]');
    const isEditing = <?php echo $is_editing ? 'true' : 'false'; ?>;

    if (typeof flatpickr !== 'undefined' && dateInput) {
        flatpickr(dateInput, {
            dateFormat: 'd/m/Y',
            allowInput: true,
            onChange: function(selectedDates, dateStr) {
                if (isEditing || !invNoInput) return;
                
                // Parse dd/mm/yyyy
                const parts = dateStr.split('/');
                if (parts.length !== 3) return;
                
                const d = parseInt(parts[0]);
                const m = parseInt(parts[1]);
                const y = parseInt(parts[2]);
                
                // Financial Year
                const fy_start = m >= 4 ? y : y - 1;
                const fy_end = fy_start + 1;
                const fy_str = String(fy_start).slice(-2) + '-' + String(fy_end).slice(-2);
                
                // Financial Month Index
                const fin_month = (m - 4 + 12) % 12 + 1;
                const fin_month_str = String(fin_month).padStart(2, '0');
                
                const currentInvNo = invNoInput.value;
                const invParts = currentInvNo.split('/');
                
                // We keep the sequence (part 0) if it exists, otherwise use '001'
                const seq = (invParts.length > 0 && invParts[0]) ? invParts[0] : '001';
                invNoInput.value = seq + '/' + fin_month_str + '/' + fy_str;
            }
        });
    }

    // ── Client Selector ─────────────────────────────────────────────────────
    const clientSelector = document.getElementById('client-selector');
    clientSelector.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        const name    = opt.dataset.name    || '';
        const address = opt.dataset.address || '';
        const gst     = opt.dataset.gst     || '';
        document.getElementById('display-client-name').textContent    = name;
        document.getElementById('display-client-address').textContent = address;
        document.getElementById('display-client-gst').textContent     = gst;
        document.getElementById('hidden-client-name').value    = name;
        document.getElementById('hidden-client-address').value = address;
        document.getElementById('hidden-client-gst').value     = gst;
    });

    // ── GST Type Toggle ─────────────────────────────────────────────────────
    document.querySelectorAll('input[name="gst_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const isCgstSgst = this.value === 'cgst_sgst';
            document.getElementById('row-cgst').classList.toggle('d-none', !isCgstSgst);
            document.getElementById('row-sgst').classList.toggle('d-none', !isCgstSgst);
            document.getElementById('row-igst').classList.toggle('d-none',  isCgstSgst);
            recalculate();
        });
    });

    // ── Add / Remove Line Items ─────────────────────────────────────────────
    const tbody = document.getElementById('line-items-body');

    document.getElementById('add-line-item').addEventListener('click', function() {
        const rows = tbody.querySelectorAll('.line-item-row');
        const idx  = rows.length;
        const tr   = document.createElement('tr');
        tr.className = 'line-item-row';
        tr.innerHTML = `
            <td class="text-center row-number">${idx + 1}</td>
            <td><input type="text" name="line_items[${idx}][description]" class="form-control form-control-sm item-desc"></td>
            <td><input type="number" step="0.01" name="line_items[${idx}][amount]" class="form-control form-control-sm item-amount text-end" value="0"></td>
            <td class="no-print text-center"><button type="button" class="btn btn-sm btn-link text-danger remove-item"><i class="bx bx-trash"></i></button></td>`;
        tbody.appendChild(tr);
        bindItemEvents(tr);
        recalculate();
    });

    tbody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('.line-item-row');
            if (tbody.querySelectorAll('.line-item-row').length > 1) {
                row.remove();
                renumberRows();
                recalculate();
            }
        }
    });

    function bindItemEvents(container) {
        container.querySelectorAll('.item-amount').forEach(function(inp) {
            inp.addEventListener('input', recalculate);
        });
    }

    // Bind existing rows
    tbody.querySelectorAll('.line-item-row').forEach(bindItemEvents);

    // Also bind rate inputs
    ['cgst-percent', 'sgst-percent', 'igst-percent'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', recalculate);
    });

    function renumberRows() {
        tbody.querySelectorAll('.line-item-row').forEach(function(tr, i) {
            tr.querySelector('.row-number').textContent = i + 1;
            tr.querySelectorAll('input').forEach(function(inp) {
                inp.name = inp.name.replace(/\[\d+\]/, '[' + i + ']');
            });
        });
    }

    // ── Calculations ─────────────────────────────────────────────────────────
    function fmt(n) { return parseFloat(n).toFixed(2); }

    function recalculate() {
        let subtotal = 0;
        tbody.querySelectorAll('.item-amount').forEach(function(inp) {
            subtotal += parseFloat(inp.value) || 0;
        });

        const gstType = document.querySelector('input[name="gst_type"]:checked')?.value || 'cgst_sgst';
        let total = subtotal;
        let cgst = 0, sgst = 0, igst = 0;

        if (gstType === 'cgst_sgst') {
            const cp = parseFloat(document.getElementById('cgst-percent').value) || 0;
            const sp = parseFloat(document.getElementById('sgst-percent').value) || 0;
            cgst = subtotal * cp / 100;
            sgst = subtotal * sp / 100;
            total = subtotal + cgst + sgst;
            document.getElementById('display-cgst').textContent = fmt(cgst);
            document.getElementById('display-sgst').textContent = fmt(sgst);
            document.getElementById('hidden-cgst').value = fmt(cgst);
            document.getElementById('hidden-sgst').value = fmt(sgst);
            document.getElementById('hidden-igst').value = '0';
        } else {
            const ip = parseFloat(document.getElementById('igst-percent').value) || 0;
            igst  = subtotal * ip / 100;
            total = subtotal + igst;
            document.getElementById('display-igst').textContent = fmt(igst);
            document.getElementById('hidden-igst').value = fmt(igst);
            document.getElementById('hidden-cgst').value = '0';
            document.getElementById('hidden-sgst').value = '0';
        }

        document.getElementById('display-subtotal').textContent = fmt(subtotal);
        document.getElementById('display-total').textContent    = fmt(total);
        document.getElementById('hidden-subtotal').value = fmt(subtotal);
        document.getElementById('hidden-total').value    = fmt(total);

        const words = numberToWords(Math.round(total));
        document.getElementById('amount-in-words-display').textContent = 'Rupees ' + words + ' Only.';
        document.getElementById('amount-in-words-hidden').value = 'Rupees ' + words + ' Only.';
    }

    // ── Number to Words ───────────────────────────────────────────────────────
    function numberToWords(num) {
        if (num === 0) return 'Zero';
        const a = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
        const b = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
        function inWords(n) {
            if (n < 20)   return a[n];
            if (n < 100)  return b[Math.floor(n/10)] + (n%10 ? ' ' + a[n%10] : '');
            if (n < 1000) return a[Math.floor(n/100)] + ' Hundred' + (n%100 ? ' ' + inWords(n%100) : '');
            if (n < 100000)  return inWords(Math.floor(n/1000)) + ' Thousand' + (n%1000 ? ' ' + inWords(n%1000) : '');
            if (n < 10000000) return inWords(Math.floor(n/100000)) + ' Lakh' + (n%100000 ? ' ' + inWords(n%100000) : '');
            return inWords(Math.floor(n/10000000)) + ' Crore' + (n%10000000 ? ' ' + inWords(n%10000000) : '');
        }
        return inWords(num);
    }

    // Initial calculation
    recalculate();

    // ── AJAX Form Submit ───────────────────────────────────────────────────────
    document.getElementById('gmc-invoice-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('save-invoice-btn');
        const msg = document.getElementById('save-message');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const formData = new FormData(this);

        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                msg.innerHTML = '<span class="text-success"><i class="bx bx-check me-1"></i>' + data.data.message + '</span>';
                if (data.data.redirect) {
                    setTimeout(() => window.location.href = data.data.redirect, 1000);
                }
            } else {
                msg.innerHTML = '<span class="text-danger"><i class="bx bx-error me-1"></i>' + (data.data || 'Error saving.') + '</span>';
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-save me-1"></i> <?php echo $is_editing ? "Update Invoice" : "Create Invoice"; ?>';
            }
        })
        .catch(() => {
            msg.innerHTML = '<span class="text-danger">Network error. Please try again.</span>';
            btn.disabled = false;
        });
    });
});
</script>

<?php get_footer(); ?>
