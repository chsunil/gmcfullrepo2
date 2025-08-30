<?php
/**
 * Partial: Send Email Modal
 *
 * Expects query var 'send_email_args' => [
 *   'post_id'       => int,
 *   'pdf_url'       => string,
 *   'contact_email' => string,
 *   'client_name'   => string,
 * ]
 */
$args          = get_query_var('send_email_args', []);
$post_id       = intval(   $args['post_id']       ?? 0 );
$pdf_url       = esc_url(  $args['pdf_url']       ?? '' );
$contact_email = sanitize_email( $args['contact_email'] ?? '' );
$client_name   = sanitize_text_field( $args['client_name'] ?? '' );
?>
<!-- Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Send PDF to <?php echo esc_html($client_name); ?></h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="row">
          
          <div class="col-12">
            <form method="post">
              <?php wp_nonce_field('send_email_action','send_email_nonce'); ?>
              <input type="hidden" name="send_email" value="1">
              
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="to-email">To</label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                    <input
                      type="email"
                      id="to-email"
                      name="to_email"
                      class="form-control"
                      placeholder="Client's Email"
                      required
                      value="<?php echo esc_attr($contact_email); ?>"
                    />
                  </div>
                </div>
              </div>
              
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="subject">Subject</label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-comment"></i></span>
                    <input
                      type="text"
                      id="subject"
                      name="subject"
                      class="form-control"
                      placeholder="Email Subject"
                      required
                      value="Your certificate is ready"
                    />
                  </div>
                </div>
              </div>
              
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="message">Message</label>
                <div class="col-sm-10">
                  <textarea
                    id="message"
                    name="message"
                    class="form-control"
                    rows="4"
                    required
                  ><?php echo "Hi {$client_name},\n\nPlease find your certificate attached.\n\nRegards,"; ?></textarea>
                </div>
              </div>
              
              <input type="hidden" name="pdf_attachment" value="<?php echo $pdf_url; ?>">
              
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Attachment</label>
                <div class="col-sm-10">
                  <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-file"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      readonly
                      value="<?php echo esc_html(basename($pdf_url)); ?>"
                    />
                  </div>
                </div>
              </div>
              
              <div class="row justify-content-end">
                <div class="col-sm-10">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancel
                  </button>
                  <button type="submit" class="btn btn-primary">Send Email</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>