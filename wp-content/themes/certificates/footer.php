<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

?>
<?php astra_content_bottom(); ?>
</div> <!-- ast-container -->
<!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div class="d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            © <?php echo date('Y'); ?> GMC
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->
</div><!-- #content -->
 
<?php
astra_content_after();

astra_footer_before();

astra_footer();

astra_footer_after();
?>
</div><!-- #page -->

<?php
/**
 * Fixed Footer Action Bar - Shows on client form pages
 * Supports: template-client-form.php AND page-client-single.php
 */
$show_fixed_footer = false;
$post_id = 0;
$is_new = true;
$stage_key = '';
$stages = [];
$client_stage = '';
$certification_type = 'qms';

// Detect which template is active (robust: checks is_page_template + global $template filename)
global $template;
$current_template_basename = $template ? basename($template) : '';

$is_client_form    = is_page_template('template-client-form.php') && !empty($GLOBALS['client_form_data']);
$is_multi_step     = is_page_template('page-client-single.php') || $current_template_basename === 'page-client-single.php';

// Source 1: template-client-form.php (uses $GLOBALS['client_form_data'])
if ($is_client_form) :
    $data = $GLOBALS['client_form_data'];
    $post_id = $data['post_id'] ?? 0;
    $is_new = $data['is_new'] ?? true;
    $stage_key = $data['stage_key'] ?? '';
    $stages = $data['stages'] ?? [];
    $client_stage = $data['client_stage'] ?? '';
    $certification_type = $data['certification_type'] ?? 'qms';
    $show_fixed_footer = (!$is_new && $post_id > 0);

// Source 2: page-client-single.php (Multi-Step ACF Form with Tabs)
elseif ($is_multi_step) :
    $new_id = isset($_GET['new_post_id']) ? intval($_GET['new_post_id']) : 0;
    if ($new_id > 0) {
        $post_id = $new_id;
        $is_new = false;
        $certification_type = get_field('certification_type', $post_id) ?: 'qms';
        $client_stage = get_field('client_stage', $post_id) ?: 'draft';
        $stage_key = isset($_GET['stage']) ? sanitize_text_field($_GET['stage']) : $client_stage;
        
        // Get stages from certification_stages
        if (function_exists('get_certification_stages')) {
            $all_stages = get_certification_stages();
            $stages = isset($all_stages[$certification_type]) ? $all_stages[$certification_type] : [];
        }
        $show_fixed_footer = true;
    }
endif;

if ($show_fixed_footer) :
    // Calculate stage variables
    $current_stage_key = isset($_GET['stage']) ? sanitize_text_field($_GET['stage']) : $client_stage;
        $stage = isset($stages[$current_stage_key]) ? $stages[$current_stage_key] : null;
        
        // Get email template info
        if (function_exists('get_certification_emails')) {
            $certification_emails = get_certification_emails();
            $emails = isset($certification_emails[$certification_type]) ? $certification_emails[$certification_type] : [];
            $has_email = isset($emails[$current_stage_key]);
            $pdf_field = $has_email && !empty($emails[$current_stage_key]['pdf_field']) ? $emails[$current_stage_key]['pdf_field'] : '';
            $has_pdf = $pdf_field && get_field($pdf_field, $post_id);
        } else {
            $has_email = false;
            $pdf_field = '';
            $has_pdf = false;
        }
        
    // Use current stage key
    $stage_key = $current_stage_key;

    // Check PDF availability via get_certification_pdf() (for Multi-Step template)
    $newpdf_field = $stage_key . '_pdf';
    $isithas_pdf = get_field($newpdf_field, $post_id);
    
    // Check if this stage supports PDF generation
    $stage_has_pdf_support = false;
    if (function_exists('get_certification_pdf')) {
        $pdf_stages = get_certification_pdf();
        $pdf_stage_list = isset($pdf_stages[$certification_type]) ? $pdf_stages[$certification_type] : [];
        $stage_has_pdf_support = in_array($stage_key, $pdf_stage_list, true);
    }
    // Fallback: also show PDF for non-draft stages
    if ($stage_key !== 'draft') {
        $stage_has_pdf_support = true;
    }
    
    // Build next stage URL for Multi-Step template (link-based navigation)
    $next_stage_url = '';
    if ($is_multi_step && $stage && !empty($stage['next'])) {
        $next_stage_url = add_query_arg(
            ['new_post_id' => $post_id, 'stage' => $stage['next']],
            get_permalink()
        );
    }
?>
<!-- ===================================== -->
<!-- FIXED FOOTER ACTION BAR               -->
<!-- ===================================== -->
<div class="fixed-footer-bar" id="fixedFooterBar">
    <div class="fixed-footer-inner">
        <!-- Unsaved Changes Label -->
        <div class="footer-status">
            <i class="bx bx-error-circle footer-status-icon" id="footer-status-icon"></i>
            <span class="footer-status-text" id="save-status">Unsaved Changes</span>
        </div>

        <!-- Action Buttons -->
        <div class="footer-actions">
            <!-- Save Draft Button (uses JS to find and submit the ACF form) -->
            <button type="button" class="footer-btn footer-btn-draft" id="save-btn" onclick="document.querySelector('.acf-form input[type=submit]').click();">
                <i class="bx bx-save"></i>
                <span>Save Draft</span>
            </button>

            <!-- Generate PDF / View PDF Button -->
            <?php if ($stage_has_pdf_support && $stage_key !== 'draft') : ?>
                <?php if ($isithas_pdf) : ?>
                    <a href="<?php echo esc_url($isithas_pdf); ?>" 
                       target="_blank" 
                       class="footer-btn footer-btn-pdf">
                        <i class="bx bx-file"></i>
                        <span>View PDF</span>
                    </a>
                    <button type="button" class="footer-btn footer-btn-delete delete-pdf" 
                            data-post-id="<?php echo esc_attr($post_id); ?>" 
                            data-stage="<?php echo esc_attr($stage_key); ?>" 
                            title="Delete & Regenerate">
                        <i class="bx bx-trash"></i> Delete
                    </button>
                <?php else : ?>
                    <button type="button" class="footer-btn footer-btn-pdf generate-pdf" 
                            data-scheme="<?php echo esc_attr($certification_type); ?>" 
                            data-stage="<?php echo esc_attr($stage_key); ?>" 
                            data-post-id="<?php echo esc_attr($post_id); ?>">
                        <i class="bx bx-file-blank"></i>
                        <span>Generate PDF</span>
                    </button>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Send Email Button -->
            <?php if ($has_email) : ?>
            <button type="button" class="footer-btn footer-btn-email send-email-btn" 
                    data-bs-toggle="modal" 
                    data-bs-target="#sendEmailModal"
                    data-post-id="<?php echo esc_attr($post_id); ?>"
                    data-client-name="<?php echo esc_attr(get_the_title($post_id)); ?>"
                    data-email="<?php echo esc_attr(get_field('contact_person_contact_email_new', $post_id)); ?>"
                    data-pdf-url="<?php echo esc_url(get_field($pdf_field, $post_id)); ?>"
                    data-pdf-filename="<?php echo esc_attr(basename(get_field($pdf_field, $post_id))); ?>"
                    data-stage="<?php echo esc_attr($stage_key); ?>">
                <i class="bx bx-envelope"></i>
                <span>Send Email</span>
            </button>
            <?php endif; ?>

            <!-- Next Stage Button -->
            <?php if ($stage && !empty($stage['next'])) : 
                $next_stage_data = isset($stages[$stage['next']]) ? $stages[$stage['next']] : null;
                $next_stage_title = $next_stage_data ? $next_stage_data['title'] : $stage['next'];
            ?>
                <?php if ($next_stage_url) : ?>
                <!-- Multi-Step template: link-based navigation -->
                <a href="<?php echo esc_url($next_stage_url); ?>" class="footer-btn footer-btn-next">
                    <span>Next: <?php echo esc_html($next_stage_title); ?></span>
                    <i class="bx bx-right-arrow-alt"></i>
                </a>
                <?php else : ?>
                <!-- Client Form template: AJAX-based stage update -->
                <button type="button" class="footer-btn footer-btn-next next-stage-btn" 
                        data-current="<?php echo esc_attr($stage_key); ?>" 
                        data-next="<?php echo esc_attr($stage['next']); ?>">
                    <span>Next: <?php echo esc_html($next_stage_title); ?></span>
                    <i class="bx bx-right-arrow-alt"></i>
                </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* ============================================= */
/* FIXED FOOTER ACTION BAR - Modern App Style    */
/* ============================================= */
.fixed-footer-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1040;
    padding: 12px 20px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    transition: all 0.3s ease;
}

@media (min-width: 1200px) {
    .fixed-footer-bar {
        left: 260px; /* Sneat sidebar width */
    }
}

.fixed-footer-inner {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 16px;
    max-width: 1400px;
    margin: 0 auto;
    padding: 14px 20px;
    border: 2px dashed #3b82f6;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.08);
    flex-wrap: wrap;
}

/* ---- Status Indicator ---- */
.footer-status {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 160px;
}

.footer-status-icon {
    font-size: 20px;
    color: #f59e0b;
}

.footer-status-text {
    font-size: 15px;
    font-weight: 600;
    font-style: italic;
    color: #f59e0b;
    white-space: nowrap;
}

.footer-status-text.saved {
    color: #22c55e !important;
    font-style: normal;
}

.footer-status-text.saving {
    color: #3b82f6 !important;
    font-style: normal;
}

.footer-status-text.unsaved {
    color: #f59e0b !important;
}

/* ---- Action Buttons ---- */
.footer-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.footer-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    white-space: nowrap;
    line-height: 1.4;
    border: 1.5px solid transparent;
    background: transparent;
}

.footer-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.footer-btn:active {
    transform: translateY(0);
}

/* Save Draft */
.footer-btn-draft {
    border-color: #9ca3af;
    color: #4b5563;
    background: #fff;
}
.footer-btn-draft:hover {
    background: #f3f4f6;
    color: #374151;
}

/* Generate PDF */
.footer-btn-pdf {
    border-color: #14b8a6;
    color: #0d9488;
    background: #fff;
}
.footer-btn-pdf:hover {
    background: #f0fdfa;
    color: #0f766e;
}

/* Send Email */
.footer-btn-email {
    border-color: #22c55e;
    color: #16a34a;
    background: #fff;
}
.footer-btn-email:hover {
    background: #f0fdf4;
    color: #15803d;
}

/* Delete PDF */
.footer-btn-delete {
    border-color: #ef4444;
    color: #dc2626;
    background: #fff;
}
.footer-btn-delete:hover {
    background: #fef2f2;
    color: #b91c1c;
}

/* Next Stage */
.footer-btn-next {
    border-color: #2563eb;
    background: #2563eb;
    color: #fff;
    font-weight: 600;
}
.footer-btn-next:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #fff;
}

/* ---- Spacer for fixed footer ---- */
.fixed-footer-spacer {
    display: block;
    height: 90px;
}

/* ---- Responsive ---- */
@media (max-width: 767px) {
    .fixed-footer-bar {
        padding: 8px 10px;
    }
    
    .fixed-footer-inner {
        padding: 10px 12px;
        gap: 10px;
    }

    .footer-status {
        min-width: auto;
        width: 100%;
    }
    
    .footer-btn {
        padding: 6px 12px;
        font-size: 13px;
    }

    .footer-btn span {
        display: none;
    }

    .footer-btn i {
        font-size: 18px;
    }

    .fixed-footer-spacer {
        height: 120px;
    }
}

@media (max-width: 400px) {
    .footer-actions {
        gap: 6px;
    }
}

/* ---- Pulse animation on change ---- */
@keyframes statusPulse {
    0% { opacity: 1; }
    50% { opacity: 0.6; }
    100% { opacity: 1; }
}

.fixed-footer-bar.form-changed .footer-status-text {
    animation: statusPulse 0.6s ease;
}
</style>

<script>
/* ========================================= */
/* FIXED FOOTER BAR - Form Change Detection  */
/* ========================================= */
document.addEventListener('DOMContentLoaded', function() {
    const footerBar = document.getElementById('fixedFooterBar');
    const saveStatus = document.getElementById('save-status');
    const statusIcon = document.getElementById('footer-status-icon');
    const acfForm = document.querySelector('.acf-form');
    const saveBtn = document.getElementById('save-btn');
    
    if (!footerBar || !acfForm) return;
    
    let hasUnsavedChanges = false;
    
    // Detect form changes
    acfForm.addEventListener('input', function() {
        if (!hasUnsavedChanges) {
            hasUnsavedChanges = true;
            updateSaveStatus('unsaved', 'Unsaved Changes');
            footerBar.classList.add('form-changed');
        }
    });
    
    // Handle save button click
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            updateSaveStatus('saving', 'Saving...');
        });
    }
    
    // Listen for ACF form submission
    if (typeof acf !== 'undefined') {
        acf.addAction('submit', function() {
            updateSaveStatus('saving', 'Saving...');
        });
        
        acf.addAction('submit_success', function() {
            hasUnsavedChanges = false;
            updateSaveStatus('saved', 'All changes saved');
            footerBar.classList.remove('form-changed');
            
            setTimeout(function() {
                updateSaveStatus('ready', 'Ready');
            }, 3000);
        });
    }
    
    // Update status display
    function updateSaveStatus(type, message) {
        if (!saveStatus) return;
        
        saveStatus.className = 'footer-status-text ' + type;
        saveStatus.textContent = message;
        
        if (statusIcon) {
            statusIcon.className = 'bx footer-status-icon ';
            switch(type) {
                case 'saved':
                    statusIcon.className += 'bx-check-circle';
                    statusIcon.style.color = '#22c55e';
                    break;
                case 'saving':
                    statusIcon.className += 'bx-loader-circle bx-spin';
                    statusIcon.style.color = '#3b82f6';
                    break;
                case 'unsaved':
                    statusIcon.className += 'bx-error-circle';
                    statusIcon.style.color = '#f59e0b';
                    break;
                default:
                    statusIcon.className += 'bx-check-circle';
                    statusIcon.style.color = '#9ca3af';
            }
        }
    }
    
    // Warn before leaving with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
});
</script>
<?php 
endif; // End $show_fixed_footer check
?>

<!-- Toast Container -->
<!-- Toast Container -->
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <!-- Toasts will be appended here -->
</div>

<?php
astra_body_bottom();
wp_footer();
?>
</body>
</html>
