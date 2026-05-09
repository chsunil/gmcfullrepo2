document.addEventListener("DOMContentLoaded", function () {
    // ── Handle Generate PDF (event delegation — works for dynamically created buttons too) ──
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.generate-pdf');
        if (!button) return;

            const postId = button.dataset.postId;
            const scheme = button.dataset.scheme || 'qms';    // e.g. data-scheme="qms"
            const stage = button.dataset.stage || 'f03';    // e.g. data-stage="f03"

            // Prevent double clicks
            if (button.disabled) return;
            button.disabled = true;
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating...';

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
                        showToast('PDF Generated Successfully.', 'success');
                    } else {
                        alert('PDF Ready: ' + data.data.pdf_url);
                    }

                    // Dynamically render "View PDF" and "Delete" button
                    const wrapper = document.createElement('div');
                    wrapper.className = 'd-inline-flex align-items-center gap-2';

                    const viewBtn = document.createElement('a');
                    viewBtn.href = data.data.pdf_url;
                    viewBtn.target = '_blank';
                    viewBtn.className = 'btn btn-primary btn-sm';
                    viewBtn.innerHTML = '<i class="bx bx-file"></i> <span>View PDF</span>';

                    const delBtn = document.createElement('button');
                    delBtn.className = 'btn btn-danger btn-sm delete-pdf';
                    delBtn.dataset.postId = postId;
                    delBtn.dataset.stage = stage;
                    delBtn.innerHTML = '<i class="bx bx-trash"></i>';
                    delBtn.title = 'Delete & Regenerate';

                    wrapper.appendChild(viewBtn);
                    wrapper.appendChild(delBtn);

                    button.replaceWith(wrapper);

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

    // ── Handle Delete PDF ───────────────────────────────────────────
    document.addEventListener('click', function (e) {
        if (e.target.closest('.delete-pdf')) {
            const btn = e.target.closest('.delete-pdf');
            if (btn.disabled) return;

            const postId = btn.dataset.postId;
            const stage = btn.dataset.stage;

            const doDelete = () => {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                fetch(cpdf_vars.ajax_url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'delete_pdf',
                        nonce: cpdf_vars.generate_pdf_nonce,
                        post_id: postId,
                        stage: stage
                    })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            if (typeof showToast === 'function') {
                                showToast('PDF deleted. You can now regenerate it.', 'info');
                            }

                            // Create fresh "Generate" button
                            const genBtn = document.createElement('button');
                            genBtn.className = 'btn btn-success btn-sm generate-pdf';
                            genBtn.dataset.postId = postId;
                            genBtn.dataset.stage = stage;
                            genBtn.innerHTML = '<i class="bx bx-file-blank me-1"></i> Generate PDF';

                            // Replace the wrapper
                            btn.closest('div').replaceWith(genBtn);
                        } else {
                            throw new Error(data.data.message || 'Deletion failed');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bx bx-trash"></i>';
                        if (typeof showToast === 'function') {
                            showToast('Error deleting PDF: ' + err.message, 'danger');
                        }
                    });
            };

            if (typeof showConfirmModal === 'function') {
                showConfirmModal('Are you sure you want to delete this PDF? You can regenerate it afterwards.', doDelete, 'Delete PDF');
            } else {
                if (confirm('Are you sure you want to delete this PDF and regenerate it?')) doDelete();
            }
        }
    });
});
