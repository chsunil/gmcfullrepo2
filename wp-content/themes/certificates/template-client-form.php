<?php
/**
 * Template Name: Client Form
 * 
 * A template for creating and editing clients with Sneat UI and tabs-based navigation
 * based on certification stages.
 */

// Ensure ACF form functionality is available
acf_form_head();
// Enqueue ACF frontend assets for date picker functionality
wp_enqueue_script('acf-input');
wp_enqueue_style('acf-input');
get_header();

// Get certification stages
global $certification_stages;
if (!isset($certification_stages)) {
    $certification_stages = get_certification_stages();
}

// Get post ID from URL or create a new post
// $post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ( isset($_GET['new_post_id']) && intval($_GET['new_post_id']) > 0 ) {
  $post_id = intval($_GET['new_post_id']);
  $submit  = 'Update Client';
  $is_new  = false;  // Editing existing client
} else {
  $post_id = 'new_post';
  $submit  = 'Create Client';
  $is_new  = true;   // Creating new client
}
// $is_new = false;

// If no ID provided, create a new client post
// if (!$post_id) {
//     $is_new = true;
//     $post_id = wp_insert_post([
//         'post_title'   => 'New Client',
//         'post_type'    => 'client',
//         'post_status'  => 'publish',
//     ]);
    
//     // Set default stage to 'draft'
//     // update_field('client_stage', 'draft', $post_id);
//     // update_field('certification_type', 'qms', $post_id); // Default to QMS
// }

// Get client data
$client = get_post($post_id);

// Safety check: if client doesn't exist, redirect or show error
if (!$client && !$is_new) {
    echo '<div class="alert alert-danger">Client not found. <a href="' . home_url('/all-clients/') . '">Return to Client List</a></div>';
    get_footer();
    exit;
}
$client_stage = get_field('client_stage', $post_id) ?: 'draft';
$certification_type = get_field('certification_type', $post_id) ?: 'qms';

// Get stages for the current certification type
$stages = isset($certification_stages[$certification_type]) ? $certification_stages[$certification_type] : [];

// Get current stage data
$current_stage_data = isset($stages[$client_stage]) ? $stages[$client_stage] : null;
$next_stage = $current_stage_data && isset($current_stage_data['next']) ? $current_stage_data['next'] : null;
?>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Sidebar -->
        <?php get_sidebar('custom'); ?>
        
        <!-- Layout container -->
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="flex-grow-1">
                  
                    <!-- Client Form Content -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?php echo $is_new ? 'Create New Client' : 'Edit Client: ' . esc_html($client->post_title); ?></h5>
                                 <small> <?php echo $_GET['new_post_id']; ?></small>
                                </div>
                                <div class="card-body">
                                 
                                    <!-- Tabs Navigation -->
                                    <ul class="nav nav-pills mb-3 mx-0 d-none">
                                        <?php 
                                        $active_set = false;
                                        $available_stages = [];
                                        
                                        // First, determine which stages should be available
                                        // Start with draft and follow the chain up to current stage
                                        $available_stages[] = 'draft';
                                        $next_key = isset($stages['draft']['next']) ? $stages['draft']['next'] : null;
                                        
                                        while ($next_key && $next_key !== $client_stage) {
                                            $available_stages[] = $next_key;
                                            $next_key = isset($stages[$next_key]['next']) ? $stages[$next_key]['next'] : null;
                                        }
                                        
                                        // Add current stage
                                        if (!in_array($client_stage, $available_stages)) {
                                            $available_stages[] = $client_stage;
                                        }
                                        
                                        // Now display tabs for available stages
                                        foreach ($stages as $stage_key => $stage) : 
                                            // Skip stages with no group defined or not available yet
                                            if (empty($stage['group']) || !in_array($stage_key, $available_stages)) continue;
                                            
                                            // Determine if this tab should be active
                                            $is_active = (!$active_set && ($stage_key === $client_stage || $is_new && $stage_key === 'draft'));
                                            if ($is_active) $active_set = true;
                                        ?>
                                        <li class="nav-item" role="presentation">
                                            <!-- <button class="nav-link text-capitalize <?php echo $is_active ? 'active' : ''; ?>" 
                                                    id="<?php echo esc_attr($stage_key); ?>-tab" 
                                                    data-bs-target="#<?php echo esc_attr($stage_key); ?>-pane" 
                                                    type="button" 
                                                    role="tab" 
                                                    aria-controls="<?php echo esc_attr($stage_key); ?>-pane" 
                                                    aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                                                    data-tab="<?php echo esc_attr($stage_key); ?>">
                                                <i class="bx bx-file me-1"></i>
                                                <?php echo esc_html($stage_key); ?>
                                            </button> -->
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    
                                    <!-- Tab Content -->
                                    <div class="tab-content p-0 border-0" style="box-shadow: unset;" id="clientTabContent">
                                        <?php 
                                        $certification_emails = get_certification_emails();
                                        $emails = isset($certification_emails[$certification_type]) ? $certification_emails[$certification_type] : [];
                                        
                                        // PERFORMANCE OPTIMIZATION: Only load the current stage, not all stages
                                        // Determine which stage to show
                                        $current_stage_key = isset($_GET['stage']) ? sanitize_text_field($_GET['stage']) : $client_stage;
                                        
                                        // For new clients, always show draft
                                        if ($is_new) {
                                            $current_stage_key = 'draft';
                                        }
                                        
                                        // Get the stage data
                                        $stage = isset($stages[$current_stage_key]) ? $stages[$current_stage_key] : null;
                                        $stage_key = $current_stage_key;
                                        
                                        if ($stage && !empty($stage['group'])) :
                                            // Check if email template exists for this stage
                                            $has_email = isset($emails[$stage_key]);
                                            $pdf_field = $has_email && !empty($emails[$stage_key]['pdf_field']) ? $emails[$stage_key]['pdf_field'] : '';
                                            $has_pdf = $pdf_field && get_field($pdf_field, $post_id);
                                        ?>
                                        <div class="tab-pane fade show active" 
                                             id="<?php echo esc_attr($stage_key); ?>-pane" 
                                             role="tabpanel" 
                                             aria-labelledby="<?php echo esc_attr($stage_key); ?>-tab" 
                                             tabindex="0">
                                            
                                             <h5><?php echo esc_html($stage['title']); ?></h5>
                                            
                                            <div class="acf-fields-container">
                                                <?php 
                                                $check_post_id =  isset($_GET['new_post_id']) ? intval($_GET['new_post_id']) : 0;
                                                if ($check_post_id) {
                                                    $newpost = true;
                                                }
                                                else{
                                                    $newpost = false;
                                                }
                                                $return_update = add_query_arg([
                                                'new_post_id' => '%post_id%',
                                                'stage'       => $stage_key,
                                            ], get_permalink());

                                            // when creating for the first time → go to All Clients
                                            $return_new = home_url( '/all-clients/' );
                                                
                                                // Check if ACF field group exists before rendering
                                                $acf_group = acf_get_field_group($stage['group']);
                                                
                                                if ($acf_group) {
                                                    // Render ACF form for this stage
                                                    acf_form([
                                                        'post_id'       => $post_id,
                                                        'field_groups'  => [$stage['group']],
                                                        'form'          => true,
                                                        // 'return'        => add_query_arg(['id' => $post_id,  'stage' => $stage_key,], get_permalink()),
                                                        'return'       => $is_new ? $return_new : $return_update,
                                                        'submit_value'  => $submit . ' ' . $stage_key,
                                                        'html_before_fields' => '<div class="acf-fields-wrapper">',
                                                        'html_after_fields'  => '</div>',
                                                        'instruction_placement' => 'field',
                                                        'new_post'     => [
                                                            'post_type'   => 'client',
                                                            'post_status' => 'publish',
                                                            'meta_input'  => [
                                                            'client_stage' => 'draft',
                                                        ],
                                                        ],
                                                        'updated_message' => 'Client information updated successfully',
                                                    ]);
                                                } else {
                                                    // Show error message if group doesn't exist
                                                    echo '<div class="alert alert-warning">';
                                                    echo '<strong>⚠️ ACF Field Group Not Found</strong><br>';
                                                    echo 'The field group <code>' . esc_html($stage['group']) . '</code> for stage <strong>' . esc_html($stage_key) . '</strong> does not exist.<br>';
                                                    echo 'Please create this field group in WordPress Admin → Custom Fields.';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                            
                                            <!-- Hide ACF's built-in submit button — fixed bar handles save -->
                                            <style>.acf-form .acf-form-submit { visibility: hidden !important; height: 0 !important; overflow: hidden !important; margin: 0 !important; padding: 0 !important; }</style>
                                            
                                            <!-- Fixed Action Bar -->
                                            <div class="fixed-bottom d-flex justify-content-end align-items-center gap-2 px-4 py-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-top: 2px dashed #5a8dee; z-index: 1040;">
                                                <!-- Save / Update Client Button -->
                                                <button type="button" class="btn btn-secondary" id="footer-save-btn" onclick="var s=document.querySelector('.acf-form input[type=submit]'); if(s) s.click();">
                                                    <i class="bx bx-save me-1"></i> <?php echo esc_html($submit . ' ' . $stage_key); ?>
                                                </button>

                                                <?php 
                                                 $newpdf_field = $stage_key.'_pdf';
                                                 $isithas_pdf = get_field($newpdf_field, $post_id);
                                                ?>
                                                <?php if ($stage_key !== 'draft' && !$isithas_pdf): ?>
                                                <button type="button" class="btn btn-outline-secondary generate-pdf" 
                                                        data-scheme="qms" data-stage="<?php echo esc_attr($stage_key); ?>" 
                                                        data-post-id="<?php echo esc_attr($post_id); ?>">
                                                    <i class="bx bx-file-blank me-1"></i> Generate PDF
                                                </button>
                                                <?php elseif ($stage_key !== 'draft' && $isithas_pdf) : ?>
                                                <div class="d-inline-flex align-items-center gap-2">
                                                    <a class="btn btn-outline-primary" href="<?php echo $isithas_pdf; ?>" target="_blank"><i class="bx bx-file me-1"></i>View PDF</a>
                                                    <button type="button" class="btn btn-outline-danger delete-pdf" 
                                                            data-post-id="<?php echo esc_attr($post_id); ?>" 
                                                            data-stage="<?php echo esc_attr($stage_key); ?>" 
                                                            title="Delete & Regenerate">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <?php if ($has_email) : ?>
                                                <button type="button" class="btn btn-outline-primary send-email-btn text-capitalize" 
                                                        data-bs-toggle="modal" data-bs-target="#sendEmailModal"
                                                        data-post-id="<?php echo esc_attr($post_id); ?>"
                                                        data-client-name="<?php echo esc_attr(get_the_title($post_id)); ?>"
                                                        data-email="<?php echo esc_attr(get_field('contact_person_contact_email_new', $post_id)); ?>"
                                                        data-pdf-url="<?php echo esc_url(get_field($pdf_field, $post_id)); ?>"
                                                        data-pdf-filename="<?php echo esc_attr(basename(get_field($pdf_field, $post_id))); ?>"
                                                        data-stage="<?php echo esc_attr($stage_key); ?>">
                                                    <i class="bx bx-envelope me-1"></i> Send Email
                                                </button>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($stage['next'])) : ?>
                                                <button type="button" class="btn btn-primary next-stage-btn text-capitalize" 
                                                        data-current="<?php echo esc_attr($stage_key); ?>" 
                                                        data-next="<?php echo esc_attr($stage['next']); ?>">
                                                    Next: <?php echo esc_html($stage['next']); ?>
                                                    <i class="bx bx-right-arrow-alt ms-1"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                            <!-- Spacer for fixed bar -->
                                            <div style="height: 80px;"></div>
                                        </div>
                                        <?php 
                                        // Include send email modal for stages that have PDF
                                        if ($has_pdf) {
                                            set_query_var('send_email_args', [
                                                'post_id'       => $post_id,
                                                'pdf_url'       => get_field($pdf_field, $post_id),
                                                'contact_email' => get_field('contact_person_contact_email_new', $post_id),
                                                'client_name'   => get_the_title($post_id),
                                            ]);
                                        }
                                        else {
                                            // echo '<div class="alert alert-danger">Stage not found or has no ACF group defined.</div>';
                                            }
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

               

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<?php 
// Include the working send email modal
get_template_part('template-parts/client/send-email-modal');
?>

<!-- Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Send Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="emailForm">
                    <input type="hidden" id="email_stage" name="stage">
                    <input type="hidden" id="email_post_id" name="post_id" value="<?php echo $post_id; ?>">
                    
                    <div class="mb-3">
                        <label for="email_to" class="form-label">To</label>
                        <input type="email" class="form-control" id="email_to" name="to" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email_subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="email_subject" name="subject" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email_message" class="form-label">Message</label>
                        <textarea class="form-control" id="email_message" name="message" rows="5" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendEmailBtn">Send Email</button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Base AJAX URL for front-end (ensure `ajaxurl` is available)
    const ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

    // Disable ACF's "unsaved changes" warning when form is submitted
    // This prevents the "Leave site?" dialog on form submit
    $(document).on('submit', '.acf-form', function() {
        // Remove the beforeunload event that ACF sets
        $(window).off('beforeunload');
        // Also set ACF's internal unload flag to false if available
        if (typeof acf !== 'undefined' && acf.unload) {
            acf.unload.active = false;
        }
    });

    // Also handle click on ACF submit buttons
    $(document).on('click', '.acf-form input[type="submit"]', function() {
        $(window).off('beforeunload');
        if (typeof acf !== 'undefined' && acf.unload) {
            acf.unload.active = false;
        }
    });

    // Handle certification type change
    $('#certification_type_selector').on('change', function() {
        const type = $(this).val();
        const postId = <?php echo $post_id; ?>;
        
        // Update certification type via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_certification_type',
                post_id: postId,
                type: type,
                nonce: '<?php echo wp_create_nonce('update_certification_type_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Reload the page to show the new certification type's tabs
                    window.location.reload();
                } else {
                    console.error('Error updating certification type:', response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    });
    
    // Handle next stage button clicks
    $('.next-stage-btn').on('click', function() {
        const currentStage = $(this).data('current');
        const nextStage = $(this).data('next');
        const postId = <?php echo $post_id; ?>;
        const $btn = $(this);

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Moving...');

        // Update client stage via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_client_stage',
                post_id: postId,
                next_stage: nextStage,
                nonce: '<?php echo wp_create_nonce('update_client_stage_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Navigate to the next stage URL (updates ?stage= param)
                    const nextUrl = new URL(window.location.href);
                    nextUrl.searchParams.set('stage', nextStage);
                    nextUrl.searchParams.set('new_post_id', postId);
                    window.location.href = nextUrl.toString();
                } else {
                    console.error('Error updating client stage:', response.data);
                    $btn.prop('disabled', false).html('Next: ' + nextStage + ' <i class="bx bx-right-arrow-alt ms-1"></i>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                $btn.prop('disabled', false).html('Next: ' + nextStage + ' <i class="bx bx-right-arrow-alt ms-1"></i>');
            }
        });
    });
    
    // Handle generate PDF button clicks
    // Buttons use class `generate-pdf` in markup so listen for that
    /*
    // DISABLED: Handled by client-pdf-generator plugin (js/generate-pdf.js)
    $(document).on('click', '.generate-pdf', function() {
        const stage = $(this).data('stage');
        const postId = $(this).data('post-id');
        const $btn = $(this);
        
        // Show loading state
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...');
        
        // Generate PDF via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'generate_client_pdf',
                post_id: postId,
                stage: stage,
                nonce: '<?php echo wp_create_nonce('generate_client_pdf_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert('PDF generated successfully!');
                    
                    // If PDF URL is returned, open it in a new tab
                    if (response.data && response.data.pdf_url) {
                        window.open(response.data.pdf_url, '_blank');
                    }
                    
                    // Reload the page to update PDF links
                    window.location.reload();
                } else {
                    console.error('Error generating PDF:', response.data);
                    alert('Error generating PDF: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('Error generating PDF. Please try again.');
            },
            complete: function() {
                // Restore button state
                $btn.prop('disabled', false).html('<i class="bx bx-file-blank me-1"></i> Generate PDF');
            }
        });
    });
    */
    
    // Handle send email button clicks
    $(document).on('click', '.send-email-btn', function() {
        const $btn = $(this);
        const stage = $btn.data('stage');
        const postId = $btn.data('post-id');
        const fallbackEmail = $btn.data('email') || '';
        const fallbackPdfUrl = $btn.data('pdf-url') || '';
        const fallbackPdfFilename = $btn.data('pdf-filename') || '';
        
        // Disable button while loading template
        $btn.prop('disabled', true);
        const originalText = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

        console.log('Sending email request:', {
            stage: stage,
            postId: postId,
            fallbackEmail: fallbackEmail
        });

        // Get email template data via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_email_template',
                post_id: postId,
                stage: stage,
                nonce: '<?php echo wp_create_nonce('get_email_template_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Populate the email modal with template data
                    $('#email_stage').val(stage);
                    $('#email_post_id').val(postId);

                    // Use server-provided to_email or fall back to button data
                    var toEmail = (response.data && response.data.to_email) ? response.data.to_email : fallbackEmail;
                    $('#email_to').val(toEmail);

                    var subject = (response.data && response.data.subject) ? response.data.subject : '';
                    $('#email_subject').val(subject);

                    var message = (response.data && response.data.message) ? response.data.message : '';
                    $('#email_message').val(message);

                    // If server didn't return pdf info, try button data (used in message placeholders if needed)
                    if ((!response.data || !response.data.pdf_url) && fallbackPdfUrl) {
                        // If message had {{pdf_link}} placeholder, replace it client-side as a best-effort
                        var pdfLink = '<a href="' + fallbackPdfUrl + '">' + (fallbackPdfFilename || fallbackPdfUrl.split('/').pop()) + '</a>';
                        var currentMessage = $('#email_message').val();
                        if (currentMessage && currentMessage.indexOf('{{pdf_link}}') !== -1) {
                            $('#email_message').val(currentMessage.replace(/{{pdf_link}}/g, pdfLink));
                        }
                    }

                    // Show the modal (use the correct ID `sendEmailModal`)
                    const emailModal = new bootstrap.Modal(document.getElementById('sendEmailModal'));
                    emailModal.show();
                } else {
                    console.error('Error getting email template:', response.data);
                    if (typeof showToast === 'function') {
                        showToast('Error getting email template. Please try again.', 'danger');
                    } else {
                        alert('Error getting email template. Please try again.');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                if (typeof showToast === 'function') {
                    showToast('Error getting email template. Please try again.', 'danger');
                } else {
                    alert('Error getting email template. Please try again.');
                }
            },
            complete: function() {
                // Restore button state
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Handle send email form submission
    $('#sendEmailBtn').on('click', function() {
        const form = $('#emailForm')[0];
        
        // Basic form validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        const stage = $('#email_stage').val();
        const postId = $('#email_post_id').val();
        const to = $('#email_to').val();
        const subject = $('#email_subject').val();
        const message = $('#email_message').val();
        
        // Send email via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'send_client_email',
                post_id: postId,
                stage: stage,
                to: to,
                subject: subject,
                message: message,
                nonce: '<?php echo wp_create_nonce('send_client_email_nonce'); ?>'
            },
            beforeSend: function() {
                $('#sendEmailBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
            },
            success: function(response) {
                if (response.success) {
                    // Close the modal
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('sendEmailModal'));
                    if (modalInstance) modalInstance.hide();
                    
                    // Show success message
                    if (typeof showToast === 'function') {
                        showToast('Email sent successfully!', 'success');
                    } else {
                        alert('Email sent successfully!');
                    }
                } else {
                    console.error('Error sending email:', response.data);
                    if (typeof showToast === 'function') {
                        showToast('Error sending email: ' + response.data, 'danger');
                    } else {
                        alert('Error sending email: ' + response.data);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                if (typeof showToast === 'function') {
                    showToast('Error sending email. Please try again.', 'danger');
                } else {
                    alert('Error sending email. Please try again.');
                }
            },
            complete: function() {
                $('#sendEmailBtn').prop('disabled', false).text('Send Email');
            }
        });
    });
    
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Fix ACF field visibility in tabs
    function fixAcfFieldVisibility() {
        // Force all ACF field groups to be visible
        $('.acf-field-group, .acf-fields').css('display', 'block');
        
        // Apply Sneat styling to ACF fields
        $('.acf-field').addClass('mb-3');
        $('.acf-field input[type="text"], .acf-field input[type="email"], .acf-field input[type="number"], .acf-field textarea, .acf-field select').addClass('form-control');
        $('.acf-field input[type="checkbox"], .acf-field input[type="radio"]').addClass('form-check-input');
        $('.acf-field .acf-label').addClass('form-label');
    }
    
    // Run when ACF is ready
    if (typeof acf !== 'undefined') {
        acf.addAction('ready', fixAcfFieldVisibility);
        
        // Run again after a short delay to catch any late-loading fields
        setTimeout(fixAcfFieldVisibility, 500);
    }
});
</script>

<?php
// Add AJAX handlers for client stage and certification type updates
// AJAX handlers have been moved to functions.php to ensure they load correctly.
// See functions.php for:
// - update_client_stage
// - update_certification_type
// - get_email_template
// - send_client_email




get_footer();
?>