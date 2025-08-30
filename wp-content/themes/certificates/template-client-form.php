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
} else {
  $post_id = 'new_post';
  $submit  = 'Create Client';
  $is_new = ( $post_id === 'new_post' );
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
                <div class="container-xxl flex-grow-1 container-p-y">
                  
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
                                    <ul class="nav nav-pills mb-3 mx-0" id="clientTab" role="tablist">
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
                                            <button class="nav-link text-capitalize <?php echo $is_active ? 'active' : ''; ?>" 
                                                    id="<?php echo esc_attr($stage_key); ?>-tab" 
                                                    data-bs-toggle="pill" 
                                                    data-bs-target="#<?php echo esc_attr($stage_key); ?>-pane" 
                                                    type="button" 
                                                    role="tab" 
                                                    aria-controls="<?php echo esc_attr($stage_key); ?>-pane" 
                                                    aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                                                    data-tab="<?php echo esc_attr($stage_key); ?>">
                                                <!-- <i class="bx bx-file me-1"></i> -->
                                                <?php echo esc_html($stage_key); ?>
                                            </button>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    
                                    <!-- Tab Content -->
                                    <div class="tab-content p-0 border-0" style="box-shadow: unset;" id="clientTabContent">
                                        <?php 
                                        $active_set = false;
                                        $certification_emails = get_certification_emails();
                                        $emails = isset($certification_emails[$certification_type]) ? $certification_emails[$certification_type] : [];
                                        
                                        foreach ($stages as $stage_key => $stage) : 
                                            // Skip stages with no group defined or not available yet
                                            if (empty($stage['group']) || !in_array($stage_key, $available_stages)) continue;
                                            
                                            // Determine if this tab should be active
                                            $is_active = (!$active_set && ($stage_key === $client_stage || $is_new && $stage_key === 'draft'));
                                            if ($is_active) $active_set = true;
                                            
                                            // Check if email template exists for this stage
                                            $has_email = isset($emails[$stage_key]);
                                            $pdf_field = $has_email && !empty($emails[$stage_key]['pdf_field']) ? $emails[$stage_key]['pdf_field'] : '';
                                            $has_pdf = $pdf_field && get_field($pdf_field, $post_id);
                                        ?>
                                        <div class="tab-pane fade <?php echo $is_active ? 'show active' : ''; ?>" 
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
                                                'stage'       => $stage,
                                            ], get_permalink());

                                            // when creating for the first time → go to All Clients
                                            $return_new = home_url( '/all-clients/' );
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
                                                ?>
                                            </div>
                                            
                                            <!-- Action buttons in a single row -->
                                            <div class="mt-4 d-flex justify-content-end align-items-center">
                                                <?php if ($has_pdf): ?>
                                                <a href="<?php echo esc_url(get_field($pdf_field, $post_id)); ?>" target="_blank" class="btn btn-outline-primary me-2">
                                                    <i class="bx bx-file-report me-1"></i> View PDF
                                                </a>
                                                <?php endif; ?>
                                                <?php 
                                                 $newpdf_field = $stage_key.'_pdf';
                                             $isithas_pdf = get_field($newpdf_field, $post_id);
                                                ?>
                                                <?php if ($stage_key !== 'draft' && !$isithas_pdf): ?>
                                                <button type="button" class="btn btn-outline-secondary generate-pdf me-2" 
                                                        data-scheme="qms" data-stage="<?php echo esc_attr($stage_key); ?>" 
                                                        data-post-id="<?php echo esc_attr($post_id); ?>">
                                                    <i class="bx bx-file-blank me-1"></i> Generate PDF
                                                </button>
                                                <?php else : ?>
                                                <a class="btn btn-outline-primary me-2" href="<?php echo $isithas_pdf; ?>" target="_blank"><i class="bx bx-file me-1"></i>View PDF</a>
                                                <?php endif; ?>
                                                
                                                <?php if ($has_email) : ?>
                                                <button type="button" class="btn btn-outline-primary send-email-btn text-capitalize me-2" 
                                                        data-bs-toggle="modal" data-bs-target="#sendEmailModal"
                                                        data-post-id="<?php echo esc_attr($post_id); ?>"
                                                        data-client-name="<?php echo esc_attr(get_the_title($post_id)); ?>"
                                                        data-email="<?php echo esc_attr(get_field('contact_person_contact_email_new', $post_id)); ?>"
                                                        data-pdf-url="<?php echo esc_url(get_field($pdf_field, $post_id)); ?>"
                                                        data-pdf-filename="<?php echo esc_attr(basename(get_field($pdf_field, $post_id))); ?>">
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
                                        endforeach; 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            © <?php echo date('Y'); ?> GMC
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->

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
                    // Reload the page to show the updated tabs
                    window.location.reload();
                } else {
                    console.error('Error updating client stage:', response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    });
    
    // Handle generate PDF button clicks
    $(document).on('click', '.generate-pdf-btn', function() {
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
    
    // Handle send email button clicks
    $(document).on('click', '.send-email-btn', function() {
        const stage = $(this).data('stage');
        const postId = $(this).data('post-id');
        
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
                    $('#email_to').val(response.data.to_email);
                    $('#email_subject').val(response.data.subject);
                    $('#email_message').val(response.data.message);
                    
                    // Show the modal
                    const emailModal = new bootstrap.Modal(document.getElementById('emailModal'));
                    emailModal.show();
                } else {
                    console.error('Error getting email template:', response.data);
                    alert('Error getting email template. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('Error getting email template. Please try again.');
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
                    bootstrap.Modal.getInstance(document.getElementById('emailModal')).hide();
                    
                    // Show success message
                    alert('Email sent successfully!');
                } else {
                    console.error('Error sending email:', response.data);
                    alert('Error sending email: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('Error sending email. Please try again.');
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
add_action('wp_ajax_update_client_stage', function() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'update_client_stage_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Get parameters
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $next_stage = isset($_POST['next_stage']) ? sanitize_text_field($_POST['next_stage']) : '';
    
    if (!$post_id || !$next_stage) {
        wp_send_json_error('Missing parameters');
    }
    
    // Update client stage
    update_field('client_stage', $next_stage, $post_id);
    
    wp_send_json_success(['message' => 'Client stage updated', 'stage' => $next_stage]);
});

add_action('wp_ajax_update_certification_type', function() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'update_certification_type_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Get parameters
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    
    if (!$post_id || !$type || !in_array($type, ['qms', 'ems'])) {
        wp_send_json_error('Invalid parameters');
    }
    
    // Update certification type
    update_field('certification_type', $type, $post_id);
    
    // Reset stage to draft when changing certification type
    update_field('client_stage', 'draft', $post_id);
    
    wp_send_json_success(['message' => 'Certification type updated', 'type' => $type]);
});

// Get email template data
add_action('wp_ajax_get_email_template', function() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_email_template_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Get parameters
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $stage = isset($_POST['stage']) ? sanitize_text_field($_POST['stage']) : '';
    
    if (!$post_id || !$stage) {
        wp_send_json_error('Missing parameters');
    }
    
    // Get client data
    $client = get_post($post_id);
    $client_name = $client ? $client->post_title : 'Client';
    $certification_type = get_field('certification_type', $post_id) ?: 'qms';
    
    // Get email templates
    $certification_emails = get_certification_emails();
    $emails = isset($certification_emails[$certification_type]) ? $certification_emails[$certification_type] : [];
    
    if (!isset($emails[$stage])) {
        wp_send_json_error('Email template not found for this stage');
    }
    
    $email_template = $emails[$stage];
    
    // Get PDF URL if applicable
    $pdf_url = '';
    $pdf_name = '';
    if (!empty($email_template['pdf_field'])) {
        $pdf_url = get_field($email_template['pdf_field'], $post_id);
        if ($pdf_url) {
            $pdf_name = basename($pdf_url);
        }
    }
    
    // Get client contact email
    $to_email = get_field('top_management_contact_person_contact_email', $post_id) ?: '';
    
    // Replace placeholders in subject and message
    $subject = $email_template['subject'];
    $message = $email_template['message'];
    
    // Replace placeholders
    $replacements = [
        '{{client_name}}' => $client_name,
        '{{pdf_link}}' => $pdf_url,
        '{{pdf_name}}' => $pdf_name,
    ];
    
    foreach ($replacements as $placeholder => $value) {
        $subject = str_replace($placeholder, $value, $subject);
        $message = str_replace($placeholder, $value, $message);
    }
    
    wp_send_json_success([
        'to_email' => $to_email,
        'subject' => $subject,
        'message' => $message,
        'pdf_url' => $pdf_url,
        'pdf_name' => $pdf_name,
    ]);
});

// Generate client PDF
add_action('wp_ajax_generate_client_pdf', function() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'generate_client_pdf_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Get parameters
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $stage = isset($_POST['stage']) ? sanitize_text_field($_POST['stage']) : '';
    
    if (!$post_id || !$stage) {
        wp_send_json_error('Missing parameters');
    }
    
    // Get client data
    $client = get_post($post_id);
    if (!$client) {
        wp_send_json_error('Client not found');
    }
    
    // Get certification type
    $certification_type = get_field('certification_type', $post_id) ?: 'qms';
    
    // Get certification emails to find PDF field name
    $certification_emails = get_certification_emails();
    $emails = isset($certification_emails[$certification_type]) ? $certification_emails[$certification_type] : [];
    $pdf_field = isset($emails[$stage]['pdf_field']) ? $emails[$stage]['pdf_field'] : '';
    
    if (empty($pdf_field)) {
        // If no PDF field is defined for this stage, use a default naming convention
        $pdf_field = $stage . '_pdf';
    }
    
    // Generate PDF using TCPDF or other PDF library
    // This is a placeholder - you'll need to implement the actual PDF generation
    // based on your existing PDF generation code
    
    // For demonstration, we'll create a simple PDF using TCPDF if available
    if (class_exists('TCPDF')) {
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('GMC');
        $pdf->SetAuthor('GMC');
        $pdf->SetTitle($stage . ' - ' . $client->post_title);
        $pdf->SetSubject($stage . ' Form');
        
        // Set default header data
        $pdf->SetHeaderData('', 0, $stage . ' - ' . $client->post_title, 'Generated on ' . date('Y-m-d H:i:s'));
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 25);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 10);
        
        // Add content
        $html = '<h1>' . $stage . ' - ' . $client->post_title . '</h1>';
        $html .= '<p>Generated on ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '<hr>';
        
        // Add form fields based on stage
        $field_group = isset($certification_stages[$certification_type][$stage]['group']) ? $certification_stages[$certification_type][$stage]['group'] : '';
        if ($field_group) {
            $fields = acf_get_fields($field_group);
            if ($fields) {
                $html .= '<table border="1" cellpadding="5">';
                foreach ($fields as $field) {
                    $value = get_field($field['name'], $post_id);
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    $html .= '<tr><th>' . $field['label'] . '</th><td>' . $value . '</td></tr>';
                }
                $html .= '</table>';
            }
        }
        
        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Close and output PDF document
        $upload_dir = wp_upload_dir();
        $pdf_dir = $upload_dir['basedir'] . '/client-pdfs/' . $post_id;
        
        // Create directory if it doesn't exist
        if (!file_exists($pdf_dir)) {
            wp_mkdir_p($pdf_dir);
        }
        
        // Generate filename
        $filename = $stage . '-' . sanitize_title($client->post_title) . '-' . date('Ymd-His') . '.pdf';
        $pdf_path = $pdf_dir . '/' . $filename;
        $pdf_url = $upload_dir['baseurl'] . '/client-pdfs/' . $post_id . '/' . $filename;
        
        // Save PDF to file
        $pdf->Output($pdf_path, 'F');
        
        // Update ACF field with PDF URL
        update_field($pdf_field, $pdf_url, $post_id);
        
        wp_send_json_success([
            'message' => 'PDF generated successfully',
            'pdf_url' => $pdf_url
        ]);
    } else {
        // If TCPDF is not available, return an error
        wp_send_json_error('PDF generation library not available');
    }
});

// Send client email
add_action('wp_ajax_send_client_email', function() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'send_client_email_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Get parameters
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $stage = isset($_POST['stage']) ? sanitize_text_field($_POST['stage']) : '';
    $to = isset($_POST['to']) ? sanitize_email($_POST['to']) : '';
    $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
    $message = isset($_POST['message']) ? wp_kses_post($_POST['message']) : '';
    
    if (!$post_id || !$stage || !$to || !$subject || !$message) {
        wp_send_json_error('Missing parameters');
    }
    
    // Get certification type
    $certification_type = get_field('certification_type', $post_id) ?: 'qms';
    
    // Get email templates
    $certification_emails = get_certification_emails();
    $emails = isset($certification_emails[$certification_type]) ? $certification_emails[$certification_type] : [];
    
    if (!isset($emails[$stage])) {
        wp_send_json_error('Email template not found for this stage');
    }
    
    $email_template = $emails[$stage];
    
    // Get PDF attachment if applicable
    $attachments = [];
    if (!empty($email_template['pdf_field'])) {
        $pdf_url = get_field($email_template['pdf_field'], $post_id);
        if ($pdf_url) {
            // Convert URL to server path
            $upload_dir = wp_upload_dir();
            $pdf_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $pdf_url);
            
            if (file_exists($pdf_path)) {
                $attachments[] = $pdf_path;
            }
        }
    }
    
    // Set email headers
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
    ];
    
    // Send email
    $sent = wp_mail($to, $subject, $message, $headers, $attachments);
    
    if ($sent) {
        // Log email sent
        update_post_meta($post_id, '_email_sent_' . $stage, current_time('mysql'));
        wp_send_json_success(['message' => 'Email sent successfully']);
    } else {
        wp_send_json_error('Failed to send email');
    }
});

get_footer();
?>