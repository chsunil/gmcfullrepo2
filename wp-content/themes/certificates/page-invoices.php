<?php
/**
 * Template Name: Invoices
 */

// Enqueue local DataTables assets + Bootstrap Toast
add_action('wp_enqueue_scripts', function() {
    $uri = get_stylesheet_directory_uri();
    wp_enqueue_style('datatables-bs5-css',     $uri . '/css/dataTables.bootstrap5.min.css', [], '1.13.4');
    wp_enqueue_style('datatables-buttons-css', $uri . '/css/buttons.bootstrap5.min.css', [], '2.4.1');
    wp_enqueue_script('datatables-js',          $uri . '/js/jquery.dataTables.min.js',     ['jquery'], '1.13.4', true);
    wp_enqueue_script('datatables-bs5-js',      $uri . '/js/dataTables.bootstrap5.min.js', ['datatables-js'], '1.13.4', true);
    wp_enqueue_script('datatables-buttons',     $uri . '/js/dataTables.buttons.min.js',    ['datatables-js'], '2.4.1', true);
    wp_enqueue_script('datatables-buttons-bs5', $uri . '/js/buttons.bootstrap5.min.js',    ['datatables-buttons'], '2.4.1', true);
    wp_enqueue_script('jszip',                  $uri . '/js/jszip.min.js', [], '3.10.1', true);
    wp_enqueue_script('pdfmake',                $uri . '/js/pdfmake.min.js', [], '0.1.53', true);
    wp_enqueue_script('vfs-fonts',              $uri . '/js/vfs_fonts.js',   ['pdfmake'], '0.1.53', true);
    wp_enqueue_script('buttons-html5',          $uri . '/js/buttons.html5.min.js', ['datatables-buttons', 'jszip', 'pdfmake', 'vfs-fonts'], '2.4.1', true);
    wp_enqueue_script('buttons-print',          $uri . '/js/buttons.print.min.js', ['datatables-buttons'], '2.4.1', true);
});

get_header();

// Query ALL invoices
$invoices_query = new WP_Query([
    'post_type'      => 'gmc_invoice',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

// Check for toast message from redirect
$toast_msg  = isset($_GET['invoice_saved']) ? ($_GET['invoice_saved'] === 'created' ? 'Invoice created successfully!' : 'Invoice updated successfully!') : '';
$toast_type = 'success';
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php get_sidebar('custom'); ?>
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">

                    <div class="card">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Invoices</h5>
                            <a href="<?php echo site_url('/invoice-form/'); ?>" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Create Invoice
                            </a>
                        </div>

                        <div class="card-datatable table-responsive p-3">
                            <table class="datatables-users table border-top" id="invoices-table">
                                <thead>
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th class="no-sort">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($invoices_query->have_posts()):
                                        while ($invoices_query->have_posts()):
                                            $invoices_query->the_post();
                                            $pid = get_the_ID();

                                            $invoice_no  = get_field('invoice_no', $pid) ?: 'Draft';
                                            $raw_date    = get_field('invoice_date', $pid) ?: '';
                                            $sort_date   = '';
                                            if ($raw_date) {
                                                $d = DateTime::createFromFormat('d/m/Y', $raw_date);
                                                if ($d) $sort_date = $d->format('Ymd');
                                            }
                                            $client_id   = get_field('client_id', $pid);
                                            $client_name = $client_id ? (get_field('organization_name', $client_id) ?: get_the_title($client_id)) : '—';
                                            $total       = (float)(get_field('total_amount', $pid) ?? 0);
                                            $status      = get_field('status', $pid) ?: 'Unpaid';
                                            $pdf_url     = get_field('invoice_pdf_url', $pid) ?: '';

                                            // Sum payments
                                            $payments = get_field('payments_received', $pid) ?: [];
                                            $paid_sum = array_sum(array_column($payments, 'amount'));
                                            $balance  = $total - $paid_sum;

                                            $edit_link   = add_query_arg(['invoice_id' => $pid], site_url('/invoice-form/'));
                                            $view_link   = add_query_arg(['invoice_id' => $pid, 'view' => 1], site_url('/invoice-form/'));
                                            $badges      = ['Paid' => 'bg-label-success', 'Unpaid' => 'bg-label-danger', 'Partial' => 'bg-label-warning'];
                                            $badge       = $badges[$status] ?? 'bg-label-secondary';
                                            $client_email = '';
                                            if ($client_id) {
                                                $client_email = get_field('contact_person_contact_email_new', $client_id) ?: '';
                                            }
                                    ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($invoice_no); ?></strong></td>
                                        <td data-order="<?php echo esc_attr($sort_date); ?>"><?php echo esc_html($raw_date); ?></td>
                                        <td><?php echo esc_html($client_name); ?></td>
                                        <td data-order="<?php echo esc_attr($total); ?>">₹ <?php echo number_format($total, 2); ?></td>
                                        <td data-order="<?php echo esc_attr($paid_sum); ?>">₹ <?php echo number_format($paid_sum, 2); ?></td>
                                        <td data-order="<?php echo esc_attr($balance); ?>">₹ <?php echo number_format($balance, 2); ?></td>
                                        <td><span class="badge <?php echo esc_attr($badge); ?>"><?php echo esc_html($status); ?></span></td>
                                        <td class="text-nowrap">
                                            <!-- View Invoice (web page) -->
                                            <a href="<?php echo esc_url($view_link); ?>" target="_blank"
                                               class="btn btn-sm p-0 me-1 text-info"
                                               title="View Invoice">
                                                <i class="bx bx-show bx-sm"></i>
                                            </a>
                                            <!-- View / Download PDF -->
                                            <?php if ($pdf_url): ?>
                                            <a href="<?php echo esc_url($pdf_url); ?>" target="_blank"
                                               class="btn btn-sm p-0 me-1 text-success"
                                               title="Download PDF">
                                                <i class="bx bxs-file-pdf bx-sm"></i>
                                            </a>
                                            <?php else: ?>
                                            <button type="button" class="btn btn-sm p-0 me-1 text-muted"
                                                title="No PDF yet — click ⚙ to generate" disabled>
                                                <i class="bx bxs-file-pdf bx-sm"></i>
                                            </button>
                                            <?php endif; ?>
                                            <!-- Generate / Regenerate PDF -->
                                            <button type="button" class="btn btn-sm p-0 me-1 text-secondary gen-pdf-btn"
                                                data-post-id="<?php echo $pid; ?>"
                                                data-invoice-no="<?php echo esc_attr($invoice_no); ?>"
                                                title="<?php echo $pdf_url ? 'Regenerate PDF' : 'Generate PDF'; ?>">
                                                <i class="bx bx-cog bx-sm"></i>
                                            </button>
                                            <!-- Send Email -->
                                            <button type="button" class="btn btn-sm p-0 me-1 text-primary send-email-btn"
                                                data-post-id="<?php echo $pid; ?>"
                                                data-invoice-no="<?php echo esc_attr($invoice_no); ?>"
                                                data-client-name="<?php echo esc_attr($client_name); ?>"
                                                data-client-email="<?php echo esc_attr($client_email); ?>"
                                                data-pdf-url="<?php echo esc_attr($pdf_url); ?>"
                                                data-total="<?php echo esc_attr(number_format($total, 2)); ?>"
                                                title="Send Email">
                                                <i class="bx bx-envelope bx-sm"></i>
                                            </button>
                                            <!-- Record Payment -->
                                            <button type="button" class="btn btn-sm p-0 me-1 text-warning record-payment-btn"
                                                data-post-id="<?php echo $pid; ?>"
                                                data-invoice-no="<?php echo esc_attr($invoice_no); ?>"
                                                data-total="<?php echo esc_attr($total); ?>"
                                                data-paid="<?php echo esc_attr($paid_sum); ?>"
                                                data-balance="<?php echo esc_attr($balance); ?>"
                                                title="Record Payment">
                                                <i class="bx bx-rupee bx-sm"></i>
                                            </button>
                                            <!-- Edit -->
                                            <a href="<?php echo esc_url($edit_link); ?>" class="btn btn-sm p-0 me-1 text-secondary" title="Edit">
                                                <i class="bx bx-edit bx-sm"></i>
                                            </a>
                                            <!-- Delete -->
                                            <button type="button" class="btn btn-sm p-0 text-danger delete-invoice-btn"
                                                data-post-id="<?php echo $pid; ?>"
                                                data-invoice-no="<?php echo esc_attr($invoice_no); ?>"
                                                title="Delete Invoice">
                                                <i class="bx bx-trash bx-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; wp_reset_postdata(); endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <?php get_template_part('template-parts/content-footer'); ?>
            </div>
        </div>
    </div>
</div>

<!-- ── Record Payment Modal ──────────────────────────────────── -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3 text-center">
                    <div class="col-4">
                        <div class="small text-muted">Invoice Total</div>
                        <div class="fw-bold fs-5" id="pm-total">—</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Already Paid</div>
                        <div class="fw-bold fs-5 text-success" id="pm-paid">—</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Balance Due</div>
                        <div class="fw-bold fs-5 text-danger" id="pm-balance">—</div>
                    </div>
                </div>
                <hr>
                <input type="hidden" id="pm-post-id">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="text" id="pm-date" class="form-control" placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" id="pm-amount" class="form-control" step="0.01" min="0.01">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <select id="pm-mode" class="form-select">
                            <option value="">-- Select --</option>
                            <option>Online</option>
                            <option>Cheque</option>
                            <option>Cash</option>
                            <option>TDS</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Reference No / Note</label>
                        <input type="text" id="pm-ref" class="form-control" placeholder="UTR, Cheque No...">
                    </div>
                </div>
                <div id="pm-error" class="alert alert-danger mt-3 d-none"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="pm-save-btn">
                    <i class="bx bx-save me-1"></i> Save Payment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Send Email Modal ──────────────────────────────────────── -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">
                    <i class="bx bx-envelope me-1"></i> Send Invoice
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="em-post-id">
                <input type="hidden" id="em-pdf-url">
                <div class="mb-3">
                    <label class="form-label fw-bold">To <span class="text-danger">*</span></label>
                    <input type="email" id="em-to" class="form-control" placeholder="client@email.com">
                    <div class="small text-muted mt-1" id="em-email-hint"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Subject</label>
                    <input type="text" id="em-subject" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Message</label>
                    <textarea id="em-message" class="form-control" rows="7"></textarea>
                </div>
                <div id="em-pdf-warning" class="alert alert-warning d-none">
                    <i class="bx bx-error-circle me-1"></i>
                    No PDF generated yet. The invoice link will be included in the email body instead.
                    <a href="#" id="em-gen-pdf-link" class="alert-link ms-1">Generate PDF first?</a>
                </div>
                <div id="em-error" class="alert alert-danger mt-2 d-none"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="em-send-btn">
                    <i class="bx bx-send me-1"></i> Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Delete Confirmation Modal ────────────────────────────── -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bx bx-trash me-1"></i> Delete Invoice
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bx bx-error-circle text-danger" style="font-size:3rem;"></i>
                <p class="mt-3 mb-1">Are you sure you want to delete invoice</p>
                <p class="fw-bold fs-5" id="del-invoice-no">—</p>
                <p class="text-muted small">This action cannot be undone. The invoice will be permanently removed.</p>
                <input type="hidden" id="del-post-id">
                <div id="del-error" class="alert alert-danger mt-3 d-none"></div>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger px-4" id="del-confirm-btn">
                    <i class="bx bx-trash me-1"></i> Yes, Delete It
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Toast ────────────────────────────────────────────────── -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999">
    <div id="gmc-toast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive">
        <div class="d-flex">
            <div class="toast-body fs-6" id="gmc-toast-msg"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const ajaxUrl = '<?php echo admin_url("admin-ajax.php"); ?>';
    const nonce   = '<?php echo wp_create_nonce("gmc_payment_nonce"); ?>';

    // ── Toast helper ─────────────────────────────────────────────────────────
    function showToast(msg, type) {
        const el = document.getElementById('gmc-toast');
        el.classList.remove('bg-success','bg-danger','bg-warning','bg-info');
        el.classList.add('bg-' + (type || 'success'));
        document.getElementById('gmc-toast-msg').textContent = msg;
        const toast = new bootstrap.Toast(el, {delay: 4000});
        toast.show();
    }

    // ── Show toast if redirected after save ──────────────────────────────────
    const params = new URLSearchParams(window.location.search);
    if (params.get('invoice_saved')) {
        const msg = params.get('invoice_saved') === 'created'
            ? '✅ Invoice created successfully!'
            : '✅ Invoice updated successfully!';
        showToast(msg, 'success');
        // Clean URL
        const clean = window.location.pathname;
        history.replaceState(null, '', clean);
    }

    // ── DataTable ────────────────────────────────────────────────────────────
    if (jQuery().DataTable) {
        jQuery('#invoices-table').DataTable({
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            "displayLength": 10,
            "lengthMenu": [7, 10, 25, 50, 75, 100],
            "order": [[1, "desc"]],
            "columnDefs": [{ "orderable": false, "targets": -1 }],
            "buttons": [
                {
                    extend: 'collection',
                    className: 'btn btn-label-secondary dropdown-toggle mx-3',
                    text: '<i class="bx bx-export me-1"></i>Export',
                    buttons: [
                        { extend: 'print', text: '<i class="bx bx-printer me-2"></i>Print', className: 'dropdown-item', exportOptions: {columns: [0,1,2,3,4,5,6]} },
                        { extend: 'csv',   text: '<i class="bx bx-file me-2"></i>CSV',   className: 'dropdown-item', exportOptions: {columns: [0,1,2,3,4,5,6]} },
                        { extend: 'excel', text: 'Excel', className: 'dropdown-item', exportOptions: {columns: [0,1,2,3,4,5,6]} },
                        { extend: 'pdf',   text: '<i class="bx bxs-file-pdf me-2"></i>PDF', className: 'dropdown-item', exportOptions: {columns: [0,1,2,3,4,5,6]} },
                        { extend: 'copy',  text: '<i class="bx bx-copy me-2"></i>Copy', className: 'dropdown-item', exportOptions: {columns: [0,1,2,3,4,5,6]} },
                    ]
                }
            ],
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries"
            },
            "initComplete": function() {
                this.api().buttons().container()
                    .appendTo(jQuery('.dataTables_filter', this.api().table().container()));
            }
        });
    }

    // ── Record Payment Modal ──────────────────────────────────────────────────
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

    document.querySelectorAll('.record-payment-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const pid     = this.dataset.postId;
            const invNo   = this.dataset.invoiceNo;
            const total   = parseFloat(this.dataset.total)  || 0;
            const paid    = parseFloat(this.dataset.paid)   || 0;
            const balance = parseFloat(this.dataset.balance)|| 0;

            document.getElementById('pm-post-id').value = pid;
            document.getElementById('paymentModalLabel').textContent = 'Record Payment — ' + invNo;
            document.getElementById('pm-total').textContent   = '₹ ' + total.toFixed(2);
            document.getElementById('pm-paid').textContent    = '₹ ' + paid.toFixed(2);
            document.getElementById('pm-balance').textContent = '₹ ' + balance.toFixed(2);

            // Pre-fill today's date
            const today = new Date();
            const dd = String(today.getDate()).padStart(2,'0');
            const mm = String(today.getMonth()+1).padStart(2,'0');
            document.getElementById('pm-date').value   = dd + '/' + mm + '/' + today.getFullYear();
            document.getElementById('pm-amount').value = balance > 0 ? balance.toFixed(2) : '';
            document.getElementById('pm-mode').value   = '';
            document.getElementById('pm-ref').value    = '';
            document.getElementById('pm-error').classList.add('d-none');

            paymentModal.show();
        });
    });

    // Save Payment
    document.getElementById('pm-save-btn').addEventListener('click', function() {
        const pid    = document.getElementById('pm-post-id').value;
        const date   = document.getElementById('pm-date').value.trim();
        const amount = parseFloat(document.getElementById('pm-amount').value);
        const mode   = document.getElementById('pm-mode').value;
        const ref    = document.getElementById('pm-ref').value.trim();
        const errBox = document.getElementById('pm-error');

        if (!date || isNaN(amount) || amount <= 0 || !mode) {
            errBox.textContent = 'Please fill Date, Amount, and Mode.';
            errBox.classList.remove('d-none');
            return;
        }
        errBox.classList.add('d-none');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const fd = new FormData();
        fd.append('action',       'gmc_record_payment');
        fd.append('nonce',        nonce);
        fd.append('post_id',      pid);
        fd.append('payment_date', date);
        fd.append('amount',       amount);
        fd.append('payment_mode', mode);
        fd.append('reference_no', ref);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    paymentModal.hide();
                    showToast('✅ Payment recorded! Refreshing...', 'success');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    errBox.textContent = res.data || 'Error saving payment.';
                    errBox.classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-save me-1"></i> Save Payment';
                }
            })
            .catch(() => {
                errBox.textContent = 'Network error. Please try again.';
                errBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-save me-1"></i> Save Payment';
            });
    });
    // ── Generate / Regenerate PDF ─────────────────────────────────────────────
    document.querySelectorAll('.gen-pdf-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const pid    = this.dataset.postId;
            const invNo  = this.dataset.invoiceNo;
            const icon   = this.querySelector('i');
            const origClass = icon.className;
            icon.className = 'bx bx-loader-alt bx-spin bx-sm';
            this.disabled  = true;

            const fd = new FormData();
            fd.append('action',     'gmc_generate_invoice_pdf');
            fd.append('nonce',      nonce);
            fd.append('post_id',    pid);

            fetch(ajaxUrl, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    icon.className = origClass;
                    this.disabled  = false;
                    if (res.success && res.data.pdf_url) {
                        showToast('✅ PDF generated for ' + invNo + '! Refreshing...', 'success');
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        showToast('❌ ' + (res.data || 'PDF generation failed.'), 'danger');
                    }
                })
                .catch(() => {
                    icon.className = origClass;
                    this.disabled  = false;
                    showToast('❌ Network error during PDF generation.', 'danger');
                });
        });
    });

    // ── Send Email Modal ──────────────────────────────────────────────────────
    const emailModal  = new bootstrap.Modal(document.getElementById('emailModal'));
    let emailPostId   = null;

    function buildEmailBody(invoiceNo, clientName, total, pdfUrl, viewUrl) {
        const link = pdfUrl || viewUrl;
        return `Dear ${clientName},\n\nPlease find enclosed the invoice ${invoiceNo} for ₹ ${total}.\n\n${link ? 'View/Download Invoice: ' + link + '\n\n' : ''}Payment is due as per our agreed payment terms.\n\nFor any queries, please contact us.\n\nWarm regards,\nGlobal Management Certification Services Pvt. Ltd.`;
    }

    document.querySelectorAll('.send-email-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            emailPostId = this.dataset.postId;
            const invNo       = this.dataset.invoiceNo;
            const clientName  = this.dataset.clientName;
            const clientEmail = this.dataset.clientEmail;
            const pdfUrl      = this.dataset.pdfUrl;
            const total       = this.dataset.total;
            const viewUrl     = '<?php echo site_url(); ?>' + '/gmc_invoice/' + emailPostId + '/';

            document.getElementById('em-post-id').value  = emailPostId;
            document.getElementById('em-pdf-url').value  = pdfUrl;
            document.getElementById('emailModalLabel').innerHTML = '<i class="bx bx-envelope me-1"></i> Send Invoice — ' + invNo;

            // To email
            document.getElementById('em-to').value = clientEmail;
            document.getElementById('em-email-hint').textContent = clientEmail
                ? 'Pre-filled from client record. You may edit.'
                : 'No email on record — please enter manually.';

            // Subject
            document.getElementById('em-subject').value = 'Invoice ' + invNo + ' from Global Management Certification Services';

            // Message body
            document.getElementById('em-message').value = buildEmailBody(invNo, clientName, total, pdfUrl, viewUrl);

            // PDF warning
            const warn = document.getElementById('em-pdf-warning');
            if (!pdfUrl) {
                warn.classList.remove('d-none');
                document.getElementById('em-gen-pdf-link').dataset.postId    = emailPostId;
                document.getElementById('em-gen-pdf-link').dataset.invoiceNo = invNo;
            } else {
                warn.classList.add('d-none');
            }

            document.getElementById('em-error').classList.add('d-none');
            document.getElementById('em-send-btn').disabled = false;
            document.getElementById('em-send-btn').innerHTML = '<i class="bx bx-send me-1"></i> Send Email';

            emailModal.show();
        });
    });

    // "Generate PDF first?" link inside email modal
    document.getElementById('em-gen-pdf-link').addEventListener('click', function(e) {
        e.preventDefault();
        const pid   = this.dataset.postId;
        const invNo = this.dataset.invoiceNo;
        this.textContent = 'Generating…';

        const fd = new FormData();
        fd.append('action',  'gmc_generate_invoice_pdf');
        fd.append('nonce',   nonce);
        fd.append('post_id', pid);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.success && res.data.pdf_url) {
                    document.getElementById('em-pdf-url').value = res.data.pdf_url;
                    document.getElementById('em-pdf-warning').classList.add('d-none');
                    // Update message body to include the new PDF link
                    const body     = document.getElementById('em-message').value;
                    const pdfLine  = 'View/Download Invoice: ' + res.data.pdf_url;
                    if (!body.includes(res.data.pdf_url)) {
                        document.getElementById('em-message').value = body.replace(
                            /View\/Download Invoice:.*\n?/, pdfLine + '\n'
                        ) || body + '\n' + pdfLine;
                    }
                    showToast('✅ PDF generated!', 'success');
                } else {
                    alert('PDF generation failed: ' + (res.data || 'Unknown error'));
                    this.textContent = 'Generate PDF first?';
                }
            });
    });

    // Send Email
    document.getElementById('em-send-btn').addEventListener('click', function() {
        const pid     = document.getElementById('em-post-id').value;
        const pdfUrl  = document.getElementById('em-pdf-url').value;
        const to      = document.getElementById('em-to').value.trim();
        const subject = document.getElementById('em-subject').value.trim();
        const message = document.getElementById('em-message').value.trim();
        const errBox  = document.getElementById('em-error');

        if (!to || !subject || !message) {
            errBox.textContent = 'Please fill To, Subject, and Message.';
            errBox.classList.remove('d-none');
            return;
        }
        errBox.classList.add('d-none');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending…';

        const fd = new FormData();
        fd.append('action',   'gmc_send_invoice_email');
        fd.append('nonce',    nonce);
        fd.append('post_id',  pid);
        fd.append('to',       to);
        fd.append('subject',  subject);
        fd.append('message',  message);
        fd.append('pdf_url',  pdfUrl);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    emailModal.hide();
                    showToast('✅ Invoice emailed to ' + to, 'success');
                } else {
                    errBox.textContent = res.data || 'Failed to send email.';
                    errBox.classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-send me-1"></i> Send Email';
                }
            })
            .catch(() => {
                errBox.textContent = 'Network error. Please try again.';
                errBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-send me-1"></i> Send Email';
            });
    });

    // ── Delete Invoice ────────────────────────────────────────────────────────
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let deleteRow     = null;

    document.querySelectorAll('.delete-invoice-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const pid   = this.dataset.postId;
            const invNo = this.dataset.invoiceNo;
            // Store the TR so we can remove it without reload
            deleteRow = this.closest('tr');

            document.getElementById('del-post-id').value    = pid;
            document.getElementById('del-invoice-no').textContent = invNo;
            document.getElementById('del-error').classList.add('d-none');
            document.getElementById('del-confirm-btn').disabled = false;
            document.getElementById('del-confirm-btn').innerHTML = '<i class="bx bx-trash me-1"></i> Yes, Delete It';

            deleteModal.show();
        });
    });

    document.getElementById('del-confirm-btn').addEventListener('click', function() {
        const pid    = document.getElementById('del-post-id').value;
        const errBox = document.getElementById('del-error');
        const btn    = this;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting…';
        errBox.classList.add('d-none');

        const fd = new FormData();
        fd.append('action',  'gmc_delete_invoice');
        fd.append('nonce',   nonce);
        fd.append('post_id', pid);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    deleteModal.hide();
                    // Remove from DataTable without full reload
                    if (deleteRow && jQuery().DataTable) {
                        jQuery('#invoices-table').DataTable().row(deleteRow).remove().draw();
                    } else if (deleteRow) {
                        deleteRow.remove();
                    }
                    showToast('🗑️ Invoice deleted successfully.', 'success');
                } else {
                    errBox.textContent = res.data || 'Failed to delete invoice.';
                    errBox.classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-trash me-1"></i> Yes, Delete It';
                }
            })
            .catch(() => {
                errBox.textContent = 'Network error. Please try again.';
                errBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-trash me-1"></i> Yes, Delete It';
            });
    });
});
</script>

<?php get_footer(); ?>
