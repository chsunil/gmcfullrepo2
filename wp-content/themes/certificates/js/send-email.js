// File: js/send-email.js

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // CACHE DOM ELEMENTS
  const modalEl = document.getElementById('sendEmailModal');
  const formEl = document.getElementById('sendEmailForm');
  const toEmailEl = modalEl.querySelector('#toEmail');
  const subjectEl = modalEl.querySelector('#subject');
  const messageEl = modalEl.querySelector('#message');
  const pdfInputEl = modalEl.querySelector('#pdfAttachment');
  const filenameEl = modalEl.querySelector('#pdfFilename');
  const clientNameEl = modalEl.querySelector('#clientname');

  const postIdEl = modalEl.querySelector('#emailPostId');
  const stageEl = modalEl.querySelector('#emailStage');

  // 1) Populate modal fields when it is shown
  modalEl.addEventListener('show.bs.modal', function (event) {

    const btn = event.relatedTarget;
    const client = btn.getAttribute('data-client-name') || 'name';
    const email = btn.getAttribute('data-email') || 'email';
    const pdfUrl = btn.getAttribute('data-pdf-url') || 'url';
    const pdfFile = btn.getAttribute('data-pdf-filename') || 'file';

    // Capture post_id and stage
    const postId = btn.getAttribute('data-post-id') || '';
    const stage = btn.getAttribute('data-stage') || '';

    clientNameEl.textContent = client;
    toEmailEl.value = email;
    pdfInputEl.value = pdfUrl;
    filenameEl.textContent = pdfFile;

    // Set hidden inputs
    if (postIdEl) postIdEl.value = postId;
    if (stageEl) stageEl.value = stage;

    // Optional: initialize Froala if loaded
    if (typeof FroalaEditor !== 'undefined' && !messageEl.classList.contains('fr-box')) {
      new FroalaEditor('#message', {
        theme: 'royal',
        height: 250,
        toolbarButtons: [
          ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript'],
          ['fontFamily', 'fontSize', 'textColor', 'backgroundColor'],
          ['inlineClass', 'inlineStyle', 'clearFormatting']
        ]
      });
    }
  });

  // 2) Handle form submission via Event Delegation (Fixes "element not found" or "replaced element" issues)
  document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'sendEmailForm') {
      e.preventDefault();

      const form = e.target;

      // Get values dynamically from the submitted form to ensure we have the latest data
      const toEmail = form.querySelector('#toEmail').value;
      const subject = form.querySelector('#subject').value;
      const message = form.querySelector('#message').value;
      const pdfUrl = form.querySelector('#pdfAttachment').value;
      const postId = form.querySelector('#emailPostId').value;
      const stage = form.querySelector('#emailStage').value;

      console.log('📧 Sending Email Request (Event Delegation)...', {
        to: toEmail,
        subject: subject,
        post_id: postId,
        stage: stage
      });

      const params = new URLSearchParams({
        action: 'send_client_email',
        post_id: postId,
        stage: stage,
        to: toEmail,
        subject: subject,
        message: message,
        nonce: wp_vars.send_client_email_nonce
      });

      // Disable button to prevent double-submit
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Send';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
      }

      fetch(wp_vars.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
      })
        .then(response => response.json())
        .then(json => {
          if (json.success) {
            console.log('✅ Email Sent Successfully.');
            // Hide the modal
            const modalEl = document.getElementById('sendEmailModal');
            const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            bsModal.hide();

            if (typeof showToast === 'function') {
              showToast('Email sent successfully!', 'success');
            } else {
              alert('Email sent successfully!');
            }
          } else {
            throw new Error((json.data && json.data.message) || json.message || 'Unknown error');
          }
        })
        .catch(err => {
          console.error('❌ Email Failed:', err);
          const errMsg = err.message || err;
          if (typeof showToast === 'function') {
            showToast('Error sending email: ' + errMsg, 'danger');
          } else {
            alert('Error sending email: ' + errMsg);
          }
        })
        .finally(() => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        });
    }
  });
});
