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

    // Flatpickr date picker
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', [], '4.6.13', true);
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

                   
                            <style>
                                #invoices-table thead th { 
                                    font-weight: bold !important; 
                                    text-align: center;
                                    padding: 12px 4px !important;
                                }
                                #invoices-table tbody td { 
                                    padding: 8px 10px !important;
                                    font-weight: 500;
                                }
                                /* Compact filters row */
                                .filter-input-group { display: flex; align-items: center; gap: 2px; flex-wrap: nowrap; }
                                .filter-input-group input, .filter-input-group select, 
                                #filter-row .form-control, #filter-row .form-select { 
                                    font-size: 0.7rem !important; 
                                    padding: 2px 4px !important; 
                                    height: 24px !important; 
                                    border: 1px solid #d9dee3 !important; 
                                    border-radius: 4px !important;
                                }
                                #filter-row td { 
                                    background: #fcfcfd !important; 
                                    padding: 4px 2px !important; 
                                    border-color: #e6e8eb !important; 
                                    vertical-align: middle;
                                }
                                #filter-clear-all {
                                    background: transparent;
                                    color: #8592a3;
                                    border: 1px solid #8592a3;
                                    font-size: 10px;
                                    padding: 1px 4px;
                                    line-height: 1;
                                    border-radius: 4px;
                                    white-space: nowrap;
                                }
                                #filter-clear-all:hover {
                                    background: #8592a3;
                                    color: #fff;
                                }
                                .badge { font-family: inherit; }
                                /* Align buttons and search */
                                .card-header .head-label { margin-bottom: 0; }
                                .dataTables_wrapper .card-header { 
                                    padding: 1rem 1.5rem;
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    border-bottom: 0;
                                }
                                .dataTables_info { padding-top: 1rem; }
                                .dataTables_paginate { padding-top: 1rem; }
                                /* Hide default DT expansion icon */
                                #invoices-table td.dt-control::before {
                                    display: none !important;
                                }
                                #invoices-table td.dt-control {
                                    cursor: pointer;
                                    vertical-align: middle;
                                }
                                .expand-icon {
                                    transition: transform 0.2s ease;
                                    font-size: 1.1rem;
                                }
                                tr.shown .expand-icon {
                                    transform: rotate(90deg);
                                    color: #696cff !important;
                                }
                                /* Full screen mode */
                                #invoice-card.is-full-screen {
                                    position: fixed !important;
                                    top: 0 !important;
                                    left: 0 !important;
                                    width: 100vw !important;
                                    height: 100vh !important;
                                    z-index: 9999 !important;
                                    margin: 0 !important;
                                    border-radius: 0 !important;
                                    overflow: auto;
                                    background: #fff;
                                }
                                #invoice-card.is-full-screen .dataTables_scrollBody {
                                    max-height: calc(100vh - 200px) !important;
                                }
                            </style>
                            <div class="" id="invoice-card">
                            <table class="table table-hover border-top" id="invoices-table">
                                <thead>
                                    <tr>
                                        <th style="width:20px"></th>
                                        <th style="width:40px">S.No</th>
                                        <th style="width:60px">Month</th>
                                        <th style="width:85px">Date</th>
                                        <th style="width:110px">Invoice No</th>
                                        <th style="min-width:180px">Company Name</th>
                                        <th style="width:130px">#GST</th>
                                        <th style="width:80px">Amount</th>
                                        <th style="width:70px">IGST</th>
                                        <th style="width:70px">CGST</th>
                                        <th style="width:70px">SGST</th>
                                        <th style="width:90px">TOTAL</th>
                                        <th style="width:80px">Received</th>
                                        <th style="width:80px">Balance</th>
                                        <th style="width:100px">Payment</th>
                                        <th style="width:80px">team</th>
                                        <th class="no-sort" style="width:60px">Actions</th>
                                    </tr>
                                    <tr id="filter-row">
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <select id="filter-inv-month" class="form-select">
                                                <option value="">Mo</option>
                                                <?php $months = ['','April','May','June','July','August','September','October','November','December','January','February','March'];
                                                foreach($months as $mi => $mname): if(!$mname) continue; ?>
                                                <option value="<?php echo $mname; ?>"><?php echo substr($mname, 0, 3); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="filter-input-group">
                                                <input type="text" id="filter-date-from" class="form-control" placeholder="F" style="width:40px">
                                                <input type="text" id="filter-date-to" class="form-control" placeholder="T" style="width:40px">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="filter-input-group">
                                                <input type="text" id="filter-inv-seq" class="form-control" placeholder="Seq" style="width:40px">
                                                <select id="filter-inv-year" class="form-select" style="width:65px">
                                                    <option value="">Yr</option>
                                                    <?php $y = (int)date('Y'); for($i=$y-2;$i<=$y+1;$i++): $yy=substr($i,2).'-'.substr($i+1,2); ?>
                                                    <option value="<?php echo $yy; ?>"><?php echo $yy; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td><input type="text" id="filter-company" class="form-control" placeholder="Company"></td>
                                        <td><input type="text" id="filter-gst" class="form-control" placeholder="GST"></td>
                                        <td></td><!-- Amount -->
                                        <td></td><!-- IGST -->
                                        <td></td><!-- CGST -->
                                        <td></td><!-- SGST -->
                                        <td></td><!-- TOTAL -->
                                        <td></td><!-- Received -->
                                        <td></td><!-- Balance -->
                                        <td>
                                            <select id="filter-status" class="form-select">
                                                <option value="">All</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Unpaid">Unpaid</option>
                                                <option value="Partial">Partial</option>
                                            </select>
                                        </td>
                                        <td><input type="text" id="filter-team" class="form-control" placeholder="Team"></td>
                                        <td class="text-center">
                                            <button id="filter-clear-all" class="btn btn-sm btn-outline-secondary p-0 px-1" title="Clear All">
                                                <i class="bx bx-refresh"></i> Clear Filters
                                            </button>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($invoices_query->have_posts()):
                                        $row_idx = 1;
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
                                            $client_name = $client_id ? (gmc_get_organization_name($client_id) ?: get_the_title($client_id)) : '—';
                                            $total       = (float)(get_field('total_amount', $pid) ?? 0);
                                            $subtotal    = (float)(get_field('subtotal', $pid) ?? 0);
                                            $gst_type    = get_field('gst_type', $pid) ?: 'cgst_sgst';
                                            $cgst_p      = (float)(get_field('cgst_percent', $pid) ?? 9);
                                            $sgst_p      = (float)(get_field('sgst_percent', $pid) ?? 9);
                                            $igst_p      = (float)(get_field('igst_percent', $pid) ?? 18);
                                            $cgst_amt    = (float)(get_field('cgst_amount', $pid) ?? 0);
                                            $sgst_amt    = (float)(get_field('sgst_amount', $pid) ?? 0);
                                            $igst_amt    = (float)(get_field('igst_amount', $pid) ?? 0);
                                            $status      = get_field('status', $pid) ?: 'Unpaid';
                                            $pdf_url       = get_field('invoice_pdf_url', $pid) ?: get_post_meta($pid, 'invoice_pdf_url', true);
                                            $pdf_print_url = get_field('invoice_pdf_print_url', $pid) ?: get_post_meta($pid, 'invoice_pdf_print_url', true);
                                            $line_items  = get_field('line_items', $pid) ?: [];

                                            // Sum payments
                                            $payments = get_field('payments_received', $pid) ?: [];
                                            $paid_sum = array_sum(array_column($payments, 'amount'));
                                            $balance  = $total - $paid_sum;

                                            // Most recent payment date
                                            $last_payment_date = '—';
                                            $last_payment_sort = '';
                                            if (!empty($payments)) {
                                                $pay_dates = array_column($payments, 'payment_date');
                                                // Sort dates to find the latest
                                                usort($pay_dates, function($a, $b) {
                                                    $da = DateTime::createFromFormat('d/m/Y', $a);
                                                    $db = DateTime::createFromFormat('d/m/Y', $b);
                                                    return ($da && $db) ? $da <=> $db : 0;
                                                });
                                                $last_payment_date = end($pay_dates) ?: '—';
                                                if ($last_payment_date !== '—') {
                                                    $lpd = DateTime::createFromFormat('d/m/Y', $last_payment_date);
                                                    if ($lpd) $last_payment_sort = $lpd->format('Ymd');
                                                }
                                            }

                                            $edit_link   = add_query_arg(['invoice_id' => $pid], site_url('/invoice-form/'));
                                            $view_link   = add_query_arg(['invoice_id' => $pid, 'view' => 1], site_url('/invoice-form/'));
                                            $badges      = ['Paid' => 'bg-label-success', 'Unpaid' => 'bg-label-danger', 'Partial' => 'bg-label-warning'];
                                            $badge       = $badges[$status] ?? 'bg-label-secondary';
                                            $client_email = '';
                                            if ($client_id) {
                                                $client_email = get_field('contact_person_contact_email_new', $client_id) ?: '';
                                            }

                                            // Get Team (Assigned Employee)
                                            $team_name = '—';
                                            // Get Team (Priority to Invoice's team_member field)
                                            $team_name = get_field('team_member', $pid) ?: '—';
                                            if ($team_name === '—' && $client_id) {
                                                $assigned = get_field('assigned_employee', $client_id);
                                                if ($assigned) {
                                                    $assigned = maybe_unserialize($assigned);
                                                    if (is_array($assigned) && !empty($assigned)) {
                                                        $names = [];
                                                        foreach ($assigned as $uid) {
                                                            $u = get_user_by('id', $uid);
                                                            if ($u) $names[] = $u->display_name;
                                                        }
                                                        $team_name = implode(', ', $names);
                                                    }
                                                }
                                            }

                                            // Get Financial Month
                                            $fin_month_name = '—';
                                            if ($raw_date) {
                                                $d = DateTime::createFromFormat('d/m/Y', $raw_date);
                                                if ($d) {
                                                    $m = (int)$d->format('n');
                                                     // Financial month name (April is 1, so offset)
                                                    $months = ['','April','May','June','July','August','September','October','November','December','January','February','March'];
                                                    $idx = ($m - 4 + 12) % 12 + 1;
                                                    $fin_month_name = $months[$idx];
                                                }
                                            }

                                            // Get Client GST
                                            $client_gst = '';
                                            if ($client_id) {
                                                $client_gst = get_field('cgt_regn_no', $client_id) ?: '';
                                            }

                                            // Build detail JSON
                                            $detail_data = json_encode([
                                                'pid'        => $pid,
                                                'invoice_no' => $invoice_no,
                                                'subtotal'   => $subtotal,
                                                'gst_type'   => $gst_type,
                                                'cgst_p'     => $cgst_p,
                                                'sgst_p'     => $sgst_p,
                                                'igst_p'     => $igst_p,
                                                'cgst_amt'   => $cgst_amt,
                                                'sgst_amt'   => $sgst_amt,
                                                'igst_amt'   => $igst_amt,
                                                'total'      => $total,
                                                'paid_sum'   => $paid_sum,
                                                'balance'    => $balance,
                                                'status'     => $status,
                                                'line_items' => $line_items,
                                                'payments'   => $payments,
                                                'team'       => $team_name,
                                                'gst_no'     => $client_gst,
                                            ], JSON_HEX_QUOT | JSON_HEX_TAG);
                                    ?>
                                    <tr data-details="<?php echo esc_attr($detail_data); ?>"
                                        data-post-id="<?php echo $pid; ?>"
                                        data-invoice-no="<?php echo esc_attr($invoice_no); ?>"
                                        data-client-name="<?php echo esc_attr($client_name); ?>"
                                        data-client-email="<?php echo esc_attr($client_email); ?>"
                                        data-pdf-url="<?php echo esc_attr($pdf_url); ?>"
                                        data-total="<?php echo esc_attr($total); ?>"
                                        data-paid="<?php echo esc_attr($paid_sum); ?>"
                                        data-balance="<?php echo esc_attr($balance); ?>">
                                        <td class="dt-control text-center"><i class="bx bx-chevron-right expand-icon text-secondary"></i></td>
                                        <td class="text-center"><?php echo $row_idx++; ?></td>
                                        <td><strong><?php echo esc_html($fin_month_name); ?></strong></td>
                                        <td data-order="<?php echo esc_attr($sort_date); ?>"><?php echo esc_html($raw_date); ?></td>
                                        <td><strong><?php echo esc_html($invoice_no); ?></strong></td>
                                        <td><?php echo esc_html($client_name); ?></td>
                                        <td><?php echo esc_html($client_gst); ?></td>
                                        <td class="text-end"><?php echo number_format($subtotal, 2, '.', ''); ?></td>
                                        <td class="text-end"><?php echo number_format($igst_amt, 2, '.', ''); ?></td>
                                        <td class="text-end"><?php echo number_format($cgst_amt, 2, '.', ''); ?></td>
                                        <td class="text-end"><?php echo number_format($sgst_amt, 2, '.', ''); ?></td>
                                        <td class="text-end fw-bold"><?php echo number_format($total, 2, '.', ''); ?></td>
                                        <td class="text-end"><?php echo number_format($paid_sum, 2, '.', ''); ?></td>
                                        <td class="text-end"><?php echo number_format($balance, 2, '.', ''); ?></td>
                                        <td class="text-center">
                                            <span class="badge <?php echo esc_attr($badge); ?> pill" style="font-size:0.75rem"><?php echo esc_html($status); ?></span>
                                        </td>
                                        <td><?php echo esc_html($team_name); ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded bx-sm text-secondary"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="<?php echo esc_url($view_link); ?>" target="_blank"><i class="bx bx-show me-2"></i> View</a></li>
                                                    <li><a class="dropdown-item" href="<?php echo esc_url($edit_link); ?>"><i class="bx bx-edit me-2"></i> Edit</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <?php if ($pdf_url): ?>
                                                    <li><a class="dropdown-item text-success" href="<?php echo esc_url($pdf_url); ?>" target="_blank"><i class="bx bx-envelope me-2"></i> Email PDF</a></li>
                                                    <?php endif; ?>
                                                    <?php if ($pdf_print_url): ?>
                                                    <li><a class="dropdown-item text-success" href="<?php echo esc_url($pdf_print_url); ?>" target="_blank"><i class="bx bx-printer me-2"></i> Print PDF</a></li>
                                                    <?php endif; ?>
                                                    <li><a class="dropdown-item text-secondary gen-pdf-btn" href="javascript:void(0);" data-post-id="<?php echo $pid; ?>" data-invoice-no="<?php echo esc_attr($invoice_no); ?>"><i class="bx bx-cog me-2"></i> Regenerate</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-primary send-email-btn" href="javascript:void(0);"
                                                           data-post-id="<?php echo $pid; ?>"
                                                           data-invoice-no="<?php echo esc_attr($invoice_no); ?>"
                                                           data-client-name="<?php echo esc_attr($client_name); ?>"
                                                           data-client-email="<?php echo esc_attr($client_email); ?>"
                                                           data-pdf-url="<?php echo esc_attr($pdf_url); ?>"
                                                           data-total="<?php echo esc_attr(number_format($total, 2)); ?>"><i class="bx bx-send me-2"></i> Send Email</a></li>
                                                    <li><a class="dropdown-item text-warning record-payment-btn" href="javascript:void(0);"
                                                           data-post-id="<?php echo $pid; ?>"
                                                           data-invoice-no="<?php echo esc_attr($invoice_no); ?>"
                                                           data-total="<?php echo esc_attr($total); ?>"
                                                           data-paid="<?php echo esc_attr($paid_sum); ?>"
                                                           data-balance="<?php echo esc_attr($balance); ?>"><i class="bx bx-rupee me-2"></i> Record Payment</a></li>
                                                    <?php if (current_user_can('administrator')): ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger delete-invoice-btn" href="javascript:void(0);"
                                                           data-post-id="<?php echo $pid; ?>"
                                                           data-invoice-no="<?php echo esc_attr($invoice_no); ?>"><i class="bx bx-trash me-2"></i> Delete</a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; wp_reset_postdata(); endif; ?>
                                </tbody>
                            </table>
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
                <div class="mb-3 px-3 py-2 border rounded bg-light d-none" id="em-attachment-box">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small fw-semibold text-secondary">
                            <i class="bx bx-paperclip me-1"></i> Attachment:
                        </span>
                        <span class="small text-success fw-bold" id="em-attachment-info">
                            invoice.pdf
                        </span>
                    </div>
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

<!-- ── Edit Payment Modal ───────────────────────────────────── -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentModalLabel"><i class="bx bx-edit me-1"></i> Edit Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ep-post-id">
                <input type="hidden" id="ep-index">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="text" id="ep-date" class="form-control" placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" id="ep-amount" class="form-control" step="0.01" min="0.01">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <select id="ep-mode" class="form-select">
                            <option value="">-- Select --</option>
                            <option>Online</option>
                            <option>Cheque</option>
                            <option>Cash</option>
                            <option>TDS</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Reference No / Note</label>
                        <input type="text" id="ep-ref" class="form-control" placeholder="UTR, Cheque No...">
                    </div>
                </div>
                <div id="ep-error" class="alert alert-danger mt-3 d-none"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="ep-save-btn"><i class="bx bx-save me-1"></i> Update Payment</button>
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

    // ── Toast helper ──────────────────────────────────────────────────────────
    function showToast(msg, type) {
        const el = document.getElementById('gmc-toast');
        el.classList.remove('bg-success','bg-danger','bg-warning','bg-info');
        el.classList.add('bg-' + (type || 'success'));
        document.getElementById('gmc-toast-msg').textContent = msg;
        new bootstrap.Toast(el, {delay: 4000}).show();
    }

    // ── Show toast if redirected after save ───────────────────────────────────
    const params = new URLSearchParams(window.location.search);
    if (params.get('invoice_saved')) {
        const msg = params.get('invoice_saved') === 'created'
            ? '✅ Invoice created successfully!'
            : '✅ Invoice updated successfully!';
        showToast(msg, 'success');
        history.replaceState(null, '', window.location.pathname);
    }

    // ── Build row-detail HTML ─────────────────────────────────────────────────
    function fmt(n) {
        return '₹ ' + parseFloat(n||0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,',');
    }

    function buildDetailHtml(d) {
        const badges  = {Paid:'bg-label-success', Unpaid:'bg-label-danger', Partial:'bg-label-warning'};
        const status  = d.status || 'Unpaid';
        const badgeCls = badges[status] || 'bg-label-secondary';

        // Summary bar
        const summaryBar = `<div class="d-flex gap-4 align-items-center px-4 pt-4 pb-2 flex-wrap border-bottom bg-white">
            <div class="d-flex flex-column">
                <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Team / Assigned</span>
                <span class="text-dark fw-bold">${d.team || '—'}</span>
            </div>
            <div class="d-flex flex-column">
                <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Client GST</span>
                <span class="text-dark fw-bold">${d.gst_no || '—'}</span>
            </div>
            <div class="ms-auto d-flex gap-4 align-items-center">
                <div class="text-end">
                    <span class="text-muted small d-block">Paid Amount</span>
                    <strong class="text-success">${fmt(d.paid_sum)}</strong>
                </div>
                <div class="text-end">
                    <span class="text-muted small d-block">Balance</span>
                    <strong class="text-danger">${fmt(d.balance)}</strong>
                </div>
                <span class="badge ${badgeCls} p-2 px-3">${status}</span>
            </div>
        </div>`;

        // Line items table
        let liRows = '';
        (d.line_items || []).forEach(function(it, i) {
            liRows += `<tr><td class="text-center text-muted small">${i+1}</td><td>${it.description||''}</td><td class="text-end fw-medium">${fmt(it.amount)}</td></tr>`;
        });
        const gstRows = d.gst_type === 'igst'
            ? `<tr class="table-light"><td colspan="2" class="text-end text-muted">IGST @ ${d.igst_p}%</td><td class="text-end">${fmt(d.igst_amt)}</td></tr>`
            : `<tr class="table-light"><td colspan="2" class="text-end text-muted">CGST @ ${d.cgst_p}%</td><td class="text-end">${fmt(d.cgst_amt)}</td></tr>
               <tr class="table-light"><td colspan="2" class="text-end text-muted">SGST @ ${d.sgst_p}%</td><td class="text-end">${fmt(d.sgst_amt)}</td></tr>`;

        // Payments table
        let pmRows = '';
        if (d.payments && d.payments.length) {
            d.payments.forEach(function(p, i) {
                pmRows += `<tr>
                    <td>${p.payment_date||''}</td>
                    <td class="text-end fw-medium">${fmt(p.amount)}</td>
                    <td><span class="badge bg-label-secondary small">${p.payment_mode||''}</span></td>
                    <td class="text-muted small">${p.reference_no||''}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-icon btn-sm btn-label-primary edit-payment-row-btn"
                                data-pid="${d.pid}" data-idx="${i}"
                                data-date="${p.payment_date||''}" data-amount="${p.amount||0}"
                                data-mode="${p.payment_mode||''}" data-ref="${p.reference_no||''}"
                                title="Edit"><i class="bx bx-edit-alt"></i></button>
                            <button class="btn btn-icon btn-sm btn-label-danger del-payment-row-btn"
                                data-pid="${d.pid}" data-idx="${i}"
                                title="Delete"><i class="bx bx-trash"></i></button>
                        </div>
                    </td></tr>`;
            });
        } else {
            pmRows = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="bx bx-info-circle me-1"></i> No payments recorded yet.</td></tr>';
        }

        return `<div class="bg-light p-0">
            ${summaryBar}
            <div class="row g-0 p-4 pt-3">
                <div class="col-lg-7 pe-lg-3">
                    <p class="fw-bold mb-2 small text-uppercase text-primary"><i class="bx bx-list-ul me-1"></i> Line Items</p>
                    <div class="table-responsive border rounded bg-white">
                        <table class="table table-sm mb-0">
                            <thead class="table-light"><tr><th style="width:45px" class="text-center">#</th><th>Description</th><th class="text-end" style="width:140px">Amount</th></tr></thead>
                            <tbody>${liRows}</tbody>
                            <tfoot class="border-top-2">
                                <tr class="table-light"><td colspan="2" class="text-end text-muted">Subtotal</td><td class="text-end">${fmt(d.subtotal)}</td></tr>
                                ${gstRows}
                                <tr class="bg-white"><td colspan="2" class="text-end fw-bold text-dark">Grand Total</td><td class="text-end fw-black text-dark" style="font-size:1.1rem">${fmt(d.total)}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-lg-5 ps-lg-3 mt-4 mt-lg-0 border-start-lg">
                    <p class="fw-bold mb-2 small text-uppercase text-warning"><i class="bx bx-history me-1"></i> Payment History</p>
                    <div class="table-responsive border rounded bg-white">
                        <table class="table table-sm mb-0" data-pid="${d.pid}">
                            <thead class="table-light"><tr><th>Date</th><th class="text-end">Amount</th><th>Mode</th><th>Ref</th><th class="text-center"></th></tr></thead>
                            <tbody>${pmRows}</tbody>
                            <tfoot class="border-top-2">
                                <tr class="table-success table-opacity-10"><td colspan="2" class="text-end fw-semibold">Total Paid</td><td colspan="3" class="fw-bold text-success">${fmt(d.paid_sum)}</td></tr>
                                <tr class="table-warning table-opacity-10"><td colspan="2" class="text-end fw-semibold">Balance Due</td><td colspan="3" class="fw-bold text-danger">${fmt(d.balance)}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>`;
    }

    // ── DataTable ─────────────────────────────────────────────────────────────
    let dt;
    if (jQuery().DataTable) {
        dt = jQuery('#invoices-table').DataTable({
            "dom": '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            "displayLength": 25,
            "lengthMenu": [10, 25, 50, 100],
            "order": [[3, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [0, 1, 16] },
                { "targets": [7, 8, 9, 10, 11, 12, 13], "className": "text-end" }
            ],
            "buttons": [
                {
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle mx-3',
                    text: '<i class="bx bx-export me-1"></i>Export',
                    buttons: [
                        { extend: 'print', text: '<i class="bx bx-printer me-2"></i>Print', className: 'dropdown-item', exportOptions: {columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]} },
                        { extend: 'csv',   text: '<i class="bx bx-file me-2"></i>CSV',   className: 'dropdown-item', exportOptions: {columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]} },
                        { extend: 'excel', text: '<i class="bx bx-spreadsheet me-2"></i>Excel', className: 'dropdown-item', exportOptions: {columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]} },
                        { extend: 'pdf',   text: '<i class="bx bxs-file-pdf me-2"></i>PDF', className: 'dropdown-item', exportOptions: {columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]} },
                        { extend: 'copy',  text: '<i class="bx bx-copy me-2"></i>Copy', className: 'dropdown-item', exportOptions: {columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]} },
                    ]
                }
            ],
            "language": {
                "search": "",
                "searchPlaceholder": "Search Invoices",
                "lengthMenu": "_MENU_",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries"
            },
            "initComplete": function() {
                // Add Create Invoice button beside Export
                const createBtn = `<a href="<?php echo site_url('/invoice-form/'); ?>" class="btn btn-primary ms-2"><i class="bx bx-plus me-1"></i> Create Invoice</a>`;
                
                // Add Full Screen Toggle Button next to length menu
                const fsBtn = `<button type="button" class="btn btn-outline-primary ms-2" id="toggle-fullscreen-btn" title="Full Screen"><i class="bx bx-fullscreen"></i></button>`;
                
                jQuery('div.dt-action-buttons').append(createBtn);
                jQuery('div.dataTables_length').append(fsBtn);
                
                // Set Title
                jQuery('div.head-label').html('<h5 class="card-title mb-0">Invoice List</h5>');

                // Full Screen Toggle Event
                document.getElementById('toggle-fullscreen-btn').addEventListener('click', function() {
                    const card = document.getElementById('invoice-card');
                    card.classList.toggle('is-full-screen');
                    const icon = this.querySelector('i');
                    if (card.classList.contains('is-full-screen')) {
                        icon.className = 'bx bx-exit-fullscreen';
                        document.body.style.overflow = 'hidden';
                    } else {
                        icon.className = 'bx bx-fullscreen';
                        document.body.style.overflow = '';
                    }
                    // Trigger table redraw to fix headers/alignment
                    if (dt) dt.columns.adjust().draw();
                });
            },
            fixedHeader: true,
            responsive: false, // Changed to false because the 15-column table is too wide for responsive auto-magic
            scroller: true,
            scrollX: true
            
        });
    }

    // ── Row expand / collapse ─────────────────────────────────────────────────
    jQuery('#invoices-table tbody').on('click', 'td.dt-control', function() {
        if (!dt) return;
        const tr   = jQuery(this).closest('tr');
        const icon = tr.find('.expand-icon');
        const row  = dt.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            try {
                const d = JSON.parse(tr.attr('data-details') || '{}');
                row.child(buildDetailHtml(d)).show();
                tr.addClass('shown');
                bindPaymentRowButtons(row.child());
            } catch(e) {
                console.error('Row detail parse error', e);
            }
        }
    });

    // ── Multi-Column Filters ──────────────────────────────────────────────────
    const filterIds = [
        'filter-inv-month', 'filter-date-from', 'filter-date-to',
        'filter-inv-seq', 'filter-inv-year',
        'filter-company', 'filter-gst', 'filter-status', 'filter-team'
    ];

    function parseDMY(str) {
        const p = (str || '').trim().split('/');
        if (p.length !== 3) return null;
        const d = new Date(+p[2], +p[1]-1, +p[0]);
        return isNaN(d) ? null : d;
    }

    jQuery.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'invoices-table') return true;

        const month  = document.getElementById('filter-inv-month').value;
        const dFrom  = document.getElementById('filter-date-from').value;
        const dTo    = document.getElementById('filter-date-to').value;
        const seq    = document.getElementById('filter-inv-seq').value.toLowerCase();
        const year   = document.getElementById('filter-inv-year').value;
        const company = document.getElementById('filter-company').value.toLowerCase();
        const gst     = document.getElementById('filter-gst').value.toLowerCase();
        const status  = document.getElementById('filter-status').value;
        const team    = document.getElementById('filter-team').value.toLowerCase();

        // data[2]=Month, data[3]=Date, data[4]=Inv No, data[5]=Company, data[6]=GST, data[14]=Status, data[15]=Team
        const rowMonth   = data[2] || '';
        const rowDate    = parseDMY(data[3]);
        const rowInv     = data[4] || '';
        const rowCompany = (data[5] || '').toLowerCase();
        const rowGST     = (data[6] || '').toLowerCase();
        const rowStatus  = data[14];
        const rowTeam    = (data[15] || '').toLowerCase();

        // 1. Month Filter
        if (month && rowMonth !== month) return false;

        // 2. Date Range
        if (dFrom || dTo) {
            if (!rowDate) return false;
            const from = parseDMY(dFrom);
            const to   = parseDMY(dTo);
            if (from && rowDate < from) return false;
            if (to   && rowDate > to)   return false;
        }

        // 3. Invoice No (Sequence + Year)
        if (seq && !rowInv.toLowerCase().startsWith(seq)) return false;
        if (year) {
            const parts = rowInv.split('/');
            if (parts.length < 3 || parts[2] !== year) return false;
        }

        // 4. Company Name
        if (company && !rowCompany.includes(company)) return false;

        // 5. GST No
        if (gst && !rowGST.includes(gst)) return false;

        // 6. Status
        if (status && rowStatus !== status) return false;

        // 7. Team
        if (team && !rowTeam.includes(team)) return false;

        return true;
    });

    // Trigger redraw on change
    filterIds.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const eventType = el.tagName === 'SELECT' ? 'change' : 'input';
        el.addEventListener(eventType, () => dt.draw());
    });

    // Clear All Filters
    document.getElementById('filter-clear-all').addEventListener('click', function() {
        filterIds.forEach(id => document.getElementById(id).value = '');
        dt.draw();
    });

    // Flatpickr date pickers
    if (typeof flatpickr !== 'undefined') {
        const fpConfig = { dateFormat: 'd/m/Y', allowInput: true };
        flatpickr('#filter-date-from', fpConfig);
        flatpickr('#filter-date-to',   fpConfig);
        flatpickr('#filter-pay-from',  fpConfig);
        flatpickr('#filter-pay-to',    fpConfig);
    }

    // ── Edit Payment (from row detail) ────────────────────────────────────────
    const editPaymentModal = new bootstrap.Modal(document.getElementById('editPaymentModal'));

    function bindPaymentRowButtons(ctx) {
        ctx.find('.edit-payment-row-btn').on('click', function() {
            const b = jQuery(this);
            document.getElementById('ep-post-id').value  = b.data('pid');
            document.getElementById('ep-index').value    = b.data('idx');
            document.getElementById('ep-date').value     = b.data('date');
            document.getElementById('ep-amount').value   = b.data('amount');
            document.getElementById('ep-mode').value     = b.data('mode');
            document.getElementById('ep-ref').value      = b.data('ref') || '';
            document.getElementById('ep-error').classList.add('d-none');
            document.getElementById('ep-save-btn').disabled = false;
            document.getElementById('ep-save-btn').innerHTML = '<i class="bx bx-save me-1"></i> Update Payment';
            editPaymentModal.show();
        });

        ctx.find('.del-payment-row-btn').on('click', function() {
            if (!confirm('Delete this payment entry?')) return;
            const pid = jQuery(this).data('pid');
            const idx = jQuery(this).data('idx');
            const fd  = new FormData();
            fd.append('action',        'gmc_delete_payment');
            fd.append('nonce',         nonce);
            fd.append('post_id',       pid);
            fd.append('payment_index', idx);

            fetch(ajaxUrl, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        showToast('✅ Payment deleted. Refreshing…', 'success');
                        updateParentRowCells(pid, res.data);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast('❌ ' + (res.data || 'Failed.'), 'danger');
                    }
                })
                .catch(() => showToast('❌ Network error.', 'danger'));
        });
    }

    document.getElementById('ep-save-btn').addEventListener('click', function() {
        const pid    = document.getElementById('ep-post-id').value;
        const idx    = document.getElementById('ep-index').value;
        const date   = document.getElementById('ep-date').value.trim();
        const amount = parseFloat(document.getElementById('ep-amount').value);
        const mode   = document.getElementById('ep-mode').value;
        const ref    = document.getElementById('ep-ref').value.trim();
        const errBox = document.getElementById('ep-error');

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
        fd.append('action',        'gmc_update_payment');
        fd.append('nonce',         nonce);
        fd.append('post_id',       pid);
        fd.append('payment_index', idx);
        fd.append('payment_date',  date);
        fd.append('amount',        amount);
        fd.append('payment_mode',  mode);
        fd.append('reference_no',  ref);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    editPaymentModal.hide();
                    showToast('✅ Payment updated. Refreshing…', 'success');
                    updateParentRowCells(pid, res.data);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    errBox.textContent = res.data || 'Error updating payment.';
                    errBox.classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-save me-1"></i> Update Payment';
                }
            })
            .catch(() => {
                errBox.textContent = 'Network error. Please try again.';
                errBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-save me-1"></i> Update Payment';
            });
    });

    function updateParentRowCells(pid, data) {
        if (!data) return;
        const tr      = jQuery('#invoices-table tbody tr[data-post-id="' + pid + '"]');
        const badges  = {Paid:'bg-label-success', Unpaid:'bg-label-danger', Partial:'bg-label-warning'};
        const paidSum = parseFloat(data.paid_sum || 0);
        const balance = parseFloat(data.balance  || 0);
        const status  = data.status || 'Unpaid';
        tr.find('.row-paid-cell').text('₹ ' + paidSum.toFixed(2));
        tr.find('.row-balance-cell').text('₹ ' + balance.toFixed(2));
        tr.find('.row-status-cell .badge')
            .attr('class', 'badge ' + (badges[status] || 'bg-label-secondary'))
            .text(status);
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
            const pid       = this.dataset.postId;
            const invNo     = this.dataset.invoiceNo;
            const icon      = this.querySelector('i');
            const origClass = icon.className;
            icon.className  = 'bx bx-loader-alt bx-spin bx-sm';
            this.disabled   = true;

            const fd = new FormData();
            fd.append('action',     'gmc_generate_invoice_pdf');
            fd.append('nonce',      nonce);
            fd.append('post_id',    pid);

            fetch(ajaxUrl, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    icon.className = origClass;
                    this.disabled  = false;
                    if (res.success && (res.data.pdf_url || res.data.pdf_print_url)) {
                        showToast('✅ Email & Print PDFs generated! Refreshing...', 'success');
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
    const emailModal = new bootstrap.Modal(document.getElementById('emailModal'));
    let emailPostId  = null;

    function buildEmailBody(invoiceNo, clientName, total, pdfUrl, viewUrl) {
        const link = pdfUrl || viewUrl;
        return `Dear ${clientName},\n\nPlease find enclosed the invoice ${invoiceNo} for ₹ ${total}.\n\n${link ? '' : ''}\nPayment is due as per our agreed payment terms.\n\nFor any queries, please contact us.\n\nWarm regards,\nGlobal Management Certification Services Pvt. Ltd.`;
    }

    document.querySelectorAll('.send-email-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            emailPostId       = this.dataset.postId;
            const invNo       = this.dataset.invoiceNo;
            const clientName  = this.dataset.clientName;
            const clientEmail = this.dataset.clientEmail;
            const pdfUrl      = this.dataset.pdfUrl;
            const total       = this.dataset.total;
            const viewUrl     = '<?php echo site_url(); ?>' + '/gmc_invoice/' + emailPostId + '/';

            document.getElementById('em-post-id').value = emailPostId;
            document.getElementById('em-pdf-url').value = pdfUrl;
            document.getElementById('emailModalLabel').innerHTML = '<i class="bx bx-envelope me-1"></i> Send Invoice — ' + invNo;
            document.getElementById('em-to').value = clientEmail;
            document.getElementById('em-email-hint').textContent = clientEmail
                ? 'Pre-filled from client record. You may edit.'
                : 'No email on record — please enter manually.';
            document.getElementById('em-subject').value = 'Invoice ' + invNo + ' from Global Management Certification Services';
            document.getElementById('em-message').value = buildEmailBody(invNo, clientName, total, pdfUrl, viewUrl);

            const warn = document.getElementById('em-pdf-warning');
            const box  = document.getElementById('em-attachment-box');
            if (!pdfUrl) {
                warn.classList.remove('d-none');
                box.classList.add('d-none');
                document.getElementById('em-gen-pdf-link').dataset.postId    = emailPostId;
                document.getElementById('em-gen-pdf-link').dataset.invoiceNo = invNo;
            } else {
                warn.classList.add('d-none');
                box.classList.remove('d-none');
                const fname = pdfUrl.split('/').pop().split('?')[0];
                document.getElementById('em-attachment-info').textContent = fname;
            }
            document.getElementById('em-error').classList.add('d-none');
            document.getElementById('em-send-btn').disabled = false;
            document.getElementById('em-send-btn').innerHTML = '<i class="bx bx-send me-1"></i> Send Email';
            emailModal.show();
        });
    });

    document.getElementById('em-gen-pdf-link').addEventListener('click', function(e) {
        e.preventDefault();
        const pid = this.dataset.postId;
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
                    const body    = document.getElementById('em-message').value;
                    const pdfLine = 'View/Download Invoice: ' + res.data.pdf_url;
                    if (!body.includes(res.data.pdf_url)) {
                        document.getElementById('em-message').value =
                            body.replace(/View\/Download Invoice:.*\n?/, pdfLine+'\n') || body+'\n'+pdfLine;
                    }
                    showToast('✅ PDF generated!', 'success');
                } else {
                    alert('PDF generation failed: ' + (res.data || 'Unknown error'));
                    this.textContent = 'Generate PDF first?';
                }
            });
    });

    document.getElementById('em-send-btn').addEventListener('click', function() {
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
        fd.append('action',  'gmc_send_invoice_email');
        fd.append('nonce',   nonce);
        fd.append('post_id', document.getElementById('em-post-id').value);
        fd.append('to',      to);
        fd.append('subject', subject);
        fd.append('message', message);
        fd.append('pdf_url', document.getElementById('em-pdf-url').value);

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
    let deleteRow = null;

    document.querySelectorAll('.delete-invoice-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            deleteRow = this.closest('tr');
            document.getElementById('del-post-id').value            = this.dataset.postId;
            document.getElementById('del-invoice-no').textContent   = this.dataset.invoiceNo;
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
                    if (deleteRow && dt) {
                        dt.row(deleteRow).remove().draw();
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
    // ── Full Screen Escape Key ───────────────────────────────────────────────
    document.addEventListener('keyup', function(e) {
        if (e.key === 'Escape') {
            const card = document.getElementById('invoice-card');
            if (card && card.classList.contains('is-full-screen')) {
                card.classList.remove('is-full-screen');
                const btn = document.getElementById('toggle-fullscreen-btn');
                if (btn) btn.querySelector('i').className = 'bx bx-fullscreen';
                document.body.style.overflow = '';
                if (dt) dt.columns.adjust().draw();
            }
        }
    });
});
</script>

<?php get_footer(); ?>
