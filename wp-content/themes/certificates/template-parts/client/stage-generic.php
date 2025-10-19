<?php

/**
 * /template-parts/client/stage-generic.php
 */
/**
 * Generic per-stage form + PDF + Email + Next link.
 * Expects query var 'client_stage_args' => [
 *   'acf_args'     => array for acf_form(),
 *   'step_key'     => string, e.g. 'f03',
 *   'next_stage'   => string, e.g. 'f04',
 *   'group'        => ACF field‐group key,
 *   'real_post_id' => integer (0 if still creating)
 * ]
 */
$args         = get_query_var('client_stage_args', []);
$acf_args     = $args['acf_args']      ?? [];
$group        = $args['group']         ?? '';
$next_stage   = $args['next_stage']    ?? '';
$real_post_id = intval($args['real_post_id'] ?? 0);
$scheme     = get_field('certification_type', $real_post_id) ?: 'ems';
$step_key = sanitize_text_field($_GET['stage'] ?? 'draft');

// 1) Render only the "Save" button via ACF and dont show if we are doing PDF
if ($step_key != 'f03') {
  echo '<div class="acf-form-wrapper">';
  
  // Add Sneat form styling to ACF form
  $acf_args['field_el'] = 'div';
  $acf_args['instruction_placement'] = 'label';
  $acf_args['html_before_fields'] = '<div class="row">';
  $acf_args['html_after_fields'] = '</div>';
  $acf_args['html_submit_button'] = '<input type="submit" class="btn btn-primary" value="%s" />';
  
  acf_form($acf_args);
  echo '</div>';
  
  // Add a fallback message if the form might be empty
  echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
      const formWrapper = document.querySelector(".acf-form-wrapper");
      if (formWrapper && !formWrapper.querySelector(".acf-fields")) {
        const message = document.createElement("div");
        message.className = "alert alert-info alert-dismissible mb-3";
        message.innerHTML = "<h6 class=\"alert-heading fw-bold mb-1\">Please complete the form fields</h6><p class=\"mb-0\">Fill out all required fields in this section.</p>";
        formWrapper.appendChild(message);
      }
      
      // Apply Sneat styling to ACF fields
      const acfFields = document.querySelectorAll(".acf-field");
      acfFields.forEach(field => {
        field.classList.add("mb-3");
        
        // Style inputs
        const inputs = field.querySelectorAll("input[type=\"text\"], input[type=\"email\"], input[type=\"number\"], textarea, select");
        inputs.forEach(input => {
          input.classList.add("form-control");
        });
        
        // Style checkboxes and radio buttons
        const checkboxes = field.querySelectorAll("input[type=\"checkbox\"], input[type=\"radio\"]");
        checkboxes.forEach(checkbox => {
          const wrapper = checkbox.closest("label");
          if (wrapper) {
            wrapper.classList.add("form-check");
            checkbox.classList.add("form-check-input");
          }
        });
      });
    });
  </script>';
}

echo '<div class="mt-4">';

// 3) PDF‐generation block
if ($real_post_id) {
  $pdf_stages = get_certification_pdf()[$scheme] ?? [];

  if (in_array($step_key, $pdf_stages, true)) {
    // ACF stores URL in a field named {step_key}_pdf
    $field_key = "{$step_key}_pdf";
    $pdf_url   = get_field($field_key, $real_post_id);

    if ($pdf_url) {
      echo '<a target="_blank" class="btn btn-primary btn-sm" href="'.$pdf_url.'"><i class="bx bx-file me-1"></i> View PDF stage generic</a>';
    } else {
      echo '<button class="btn btn-primary btn-sm generate-pdf" data-post-id="' . $real_post_id . '" data-scheme="qms" data-stage="' . $step_key . '">
              <i class="bx bx-file-blank me-1"></i> Generate PDF for '. $step_key.'
            </button>';
    }

    // Send Email button (only if we have a template defined)
    if ($pdf_url) {
      $templates = get_certification_emails()[$scheme][$step_key] ?? null;
      $contact_email = get_field('contact_person_contact_email_new',$real_post_id);
      if ($templates) {
        ?>
      <button
        id="send-email-btn"
        class="btn btn-warning btn-sm send-email-btn ms-2"
        data-bs-toggle="modal"
        data-bs-target="#sendEmailModal"
        data-post-id="<?php echo esc_attr($real_post_id);?>"
        data-client-name="<?php echo esc_attr(get_the_title($real_post_id));?>"
        data-email="<?php echo esc_attr($contact_email);?>"
        data-pdf-url="<?php echo esc_url($pdf_url);?>"
        data-pdf-filename="<?php echo esc_attr(basename($pdf_url));?>">
        <i class="bx bx-envelope me-1"></i> Send Email
      </button>
      </div>
<?php
if ( $pdf_url && $templates ) {
  // gather modal data
  set_query_var('send_email_args', [
    'post_id'       => $real_post_id,
    'pdf_url'       => $pdf_url,
    'contact_email' => get_field('contact_person_contact_email_new',$real_post_id),
    'client_name'   => get_the_title($real_post_id),
  ]);
  // include the modal
  get_template_part('template-parts/client/send-email-modal');
}
?>

                   <?php 
      }
    }
  }
}

// 2) If we have a real post ID (i.e. the form has been saved at least once),
//    show a Next button that links to the next-stage URL.
if ($real_post_id && $next_stage):
  $next_url = add_query_arg(
    ['new_post_id' => $real_post_id, 'stage' => $next_stage],
    get_permalink()
  );
?>
  <div class="next-button-wrapper mt-3">
    <a href="<?php echo esc_url($next_url); ?>" class="btn btn-primary">
      <i class="bx bx-right-arrow-alt me-1"></i> Next: <?php echo esc_html($next_stage); ?>
    </a>
  </div>
<?php endif; ?>