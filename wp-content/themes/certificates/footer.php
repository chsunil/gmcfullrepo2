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
 * Sticky Action Bar - Shows on client form pages only
 */
if (is_page_template('template-client-form.php') && !empty($GLOBALS['client_form_data'])) :
    $data = $GLOBALS['client_form_data'];
    
    // Extract variables
    $post_id = $data['post_id'] ?? 0;
    $is_new = $data['is_new'] ?? true;
    $stage_key = $data['stage_key'] ?? '';
    $stages = $data['stages'] ?? [];
    $client_stage = $data['client_stage'] ?? '';
    $certification_type = $data['certification_type'] ?? 'qms';
    
    if (!$is_new && $post_id > 0) :
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
?>
<!-- ===================================== -->
<!-- STICKY ACTION BAR (Sneat Styled)      -->
<!-- ===================================== -->
<div class="sticky-action-bar">
    <div class="container-xxl">
        <div class="card mb-0 shadow-lg">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">
                    <!-- Left: Status Indicator -->
                    <div class="col-auto">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-info-circle text-warning fs-5 me-2"></i>
                            <div>
                                <div class="text-warning fw-semibold" id="save-status">Ready to save</div>
                                <small class="text-muted d-none d-md-block">
                                    Stage: <?php echo esc_html($stage['title'] ?? $stage_key); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right: Action Buttons -->
                    <div class="col text-end">
                        <div class="btn-group" role="group">
                            <!-- Save Button -->
                            <button type="submit" class="btn btn-secondary" id="save-btn" form="acf-form">
                                <i class="bx bx-save"></i>
                                <span class="d-none d-sm-inline ms-1">Save</span>
                            </button>
                            
                            <!-- PDF Button -->
                            <?php if ($stage_key !== 'draft') : 
                                $newpdf_field = $stage_key.'_pdf';
                                $isithas_pdf = get_field($newpdf_field, $post_id);
                            ?>
                                <?php if (!$isithas_pdf) : ?>
                                <button type="button" class="btn btn-outline-info generate-pdf" 
                                        data-scheme="<?php echo esc_attr($certification_type); ?>" 
                                        data-stage="<?php echo esc_attr($stage_key); ?>" 
                                        data-post-id="<?php echo esc_attr($post_id); ?>">
                                    <i class="bx bx-file-blank"></i>
                                    <span class="d-none d-md-inline ms-1">Generate PDF</span>
                                </button>
                                <?php else : ?>
                                <a href="<?php echo esc_url($isithas_pdf); ?>" 
                                   target="_blank" 
                                   class="btn btn-outline-info">
                                    <i class="bx bx-file"></i>
                                    <span class="d-none d-md-inline ms-1">View PDF</span>
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <!-- Email Button -->
                            <?php if ($has_email && $has_pdf) : ?>
                            <button type="button" class="btn btn-outline-success send-email-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#sendEmailModal"
                                    data-post-id="<?php echo esc_attr($post_id); ?>"
                                    data-client-name="<?php echo esc_attr(get_the_title($post_id)); ?>"
                                    data-email="<?php echo esc_attr(get_field('contact_person_contact_email_new', $post_id)); ?>"
                                    data-pdf-url="<?php echo esc_url(get_field($pdf_field, $post_id)); ?>"
                                    data-pdf-filename="<?php echo esc_attr(basename(get_field($pdf_field, $post_id))); ?>"
                                    data-stage="<?php echo esc_attr($stage_key); ?>">
                                <i class="bx bx-envelope"></i>
                                <span class="d-none d-md-inline ms-1">Send Email</span>
                            </button>
                            <?php endif; ?>
                            
                            <!-- Next Stage Button -->
                            <?php if ($stage && !empty($stage['next'])) : 
                                $next_stage_data = isset($stages[$stage['next']]) ? $stages[$stage['next']] : null;
                                $next_stage_title = $next_stage_data ? $next_stage_data['title'] : $stage['next'];
                            ?>
                            <button type="button" class="btn btn-primary next-stage-btn fw-semibold" 
                                    data-current="<?php echo esc_attr($stage_key); ?>" 
                                    data-next="<?php echo esc_attr($stage['next']); ?>">
                                <span class="d-none d-sm-inline">Next: </span>
                                <?php echo esc_html($next_stage_title); ?>
                                <i class="bx bx-right-arrow-alt ms-1"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================= */
/* STICKY ACTION BAR - Sneat Style          */
/* ========================================= */
.sticky-action-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1040;
    background: transparent;
    padding: 0;
    transform: translateY(0);
    transition: transform 0.3s ease;
}

@media (min-width: 1200px) {
    .sticky-action-bar {
        left: 260px; /* Sneat sidebar width */
    }
}

.sticky-action-bar .card {
    border-radius: 0;
    border-top: 2px solid var(--bs-primary);
    box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.1) !important;
}

#save-status {
    font-size: 14px;
    line-height: 1.2;
}

#save-status.saved {
    color: var(--bs-success) !important;
}

#save-status.saving {
    color: var(--bs-info) !important;
}

#save-status.unsaved {
    color: var(--bs-warning) !important;
}

.sticky-action-bar .btn-group {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.sticky-action-bar .btn {
    font-weight: 500;
    padding: 0.5rem 1rem;
}

@media (max-width: 767px) {
    .sticky-action-bar .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .sticky-action-bar .card-body {
        padding: 0.75rem !important;
    }
}

.sticky-action-placeholder {
    display: block;
    margin-top: 2rem;
    height: 80px;
}

@media (max-width: 767px) {
    .sticky-action-placeholder {
        height: 60px;
    }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.sticky-action-bar.form-changed #save-status {
    animation: pulse 0.5s ease;
}
</style>

<script>
/* ========================================= */
/* STICKY ACTION BAR - Form Change Detection */
/* ========================================= */
document.addEventListener('DOMContentLoaded', function() {
    const stickyBar = document.querySelector('.sticky-action-bar');
    const saveStatus = document.getElementById('save-status');
    const acfForm = document.querySelector('.acf-form');
    const saveBtn = document.getElementById('save-btn');
    
    if (!stickyBar || !acfForm) return;
    
    let hasUnsavedChanges = false;
    
    // Detect form changes
    acfForm.addEventListener('input', function() {
        if (!hasUnsavedChanges) {
            hasUnsavedChanges = true;
            updateSaveStatus('unsaved', 'Unsaved changes');
            stickyBar.classList.add('form-changed');
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
            stickyBar.classList.remove('form-changed');
            
            setTimeout(function() {
                updateSaveStatus('ready', 'Ready to save');
            }, 3000);
        });
    }
    
    // Update status display
    function updateSaveStatus(type, message) {
        if (!saveStatus) return;
        
        saveStatus.className = 'fw-semibold ' + type;
        saveStatus.textContent = message;
        
        const icon = saveStatus.previousElementSibling;
        if (icon) {
            icon.className = 'bx fs-5 me-2 ';
            switch(type) {
                case 'saved':
                    icon.className += 'bx-check-circle text-success';
                    break;
                case 'saving':
                    icon.className += 'bx-loader-circle bx-spin text-info';
                    break;
                case 'unsaved':
                    icon.className += 'bx-info-circle text-warning';
                    break;
                default:
                    icon.className += 'bx-info-circle text-muted';
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
    endif; // End !$is_new check
endif; // End client_form_data check
?>

<?php
astra_body_bottom();
wp_footer();
?>
</body>

</html>
