document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.generate-pdf').forEach(button => {
        button.addEventListener('click', function () {
            const postId = this.dataset.postId;
            const scheme = this.dataset.scheme || 'qms';    // e.g. data-scheme="qms"
            const stage = this.dataset.stage || 'f03';    // e.g. data-stage="f03"

            // console.log('Generating PDF click detected', { scheme, stage, postId, time: Date.now() });

            // Prevent double clicks
            if (this.disabled) return;
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating...';

            // console.log('Generating PDF for', scheme, stage, 'post', postId);

            fetch(cpdf_vars.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'generate_pdf',
                    nonce: cpdf_vars.generate_pdf_nonce,
                    post_id: postId,
                    scheme: scheme,
                    stage: stage
                })
            })
                .then(r => {
                    if (!r.ok) throw new Error(`HTTP ${r.status}`);
                    return r.json();
                })
                .then(data => {
                    if (!data.success) throw new Error(data.data.message || 'Unknown');
                    if (typeof showToast === 'function') {
                        // console.log('generate-pdf: calling showToast');
                        showToast('PDF Generated Successfully.', 'success');
                    } else {
                        alert('PDF Ready: ' + data.data.pdf_url);
                    }

                    // Dynamically render "View PDF" button
                    const viewBtn = document.createElement('a');
                    viewBtn.href = data.data.pdf_url;
                    viewBtn.target = '_blank';
                    viewBtn.className = 'footer-btn footer-btn-pdf';
                    viewBtn.innerHTML = '<i class="bx bx-file"></i> <span>View PDF</span>';

                    button.replaceWith(viewBtn);

                    // Update Send Email Button if present
                    const emailBtn = document.querySelector('.send-email-btn');
                    if (emailBtn) {
                        emailBtn.setAttribute('data-pdf-url', data.data.pdf_url);
                        if (data.data.pdf_filename) {
                            emailBtn.setAttribute('data-pdf-filename', data.data.pdf_filename);
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    // Re-enable button
                    button.disabled = false;
                    button.innerHTML = '<i class="fa-solid fa-file-circle-plus"></i>Generate PDF';

                    if (typeof showToast === 'function') {
                        showToast('Error: ' + err.message, 'danger');
                    } else {
                        alert('Error: ' + err.message);
                    }
                });
        });
    });
});
