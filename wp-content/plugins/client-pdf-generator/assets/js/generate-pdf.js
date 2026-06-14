document.addEventListener("DOMContentLoaded", function () {

    // ── Handle Generate PDF (event delegation — works for dynamically created buttons too) ──
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.generate-pdf');
        if (!button) return;

            const postId = button.dataset.postId;
            const scheme = button.dataset.scheme || 'qms';    // e.g. data-scheme="qms"
            const stage = button.dataset.stage || 'f03';    // e.g. data-stage="f03"
            const variant = button.dataset.variant || '';     // F-25 only: 'initial'|'surv1'|'surv2'
            const f25Stage = button.dataset.f25Stage || '';   // F-25 only: full stage key for backend

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
                    stage: stage,
                    print_stages: f25Stage
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
                    wrapper.className = stage === 'f25' ? 'd-flex align-items-center gap-1' : 'd-inline-flex align-items-center gap-2';

                    const viewBtn = document.createElement('a');
                    viewBtn.href = data.data.pdf_url;
                    viewBtn.target = '_blank';
                    viewBtn.className = stage === 'f25' ? 'btn btn-outline-primary btn-sm flex-grow-1' : 'btn btn-primary btn-sm';
                    viewBtn.innerHTML = '<i class="bx bx-file me-1"></i>View PDF';

                    const delBtn = document.createElement('button');
                    delBtn.className = stage === 'f25' ? 'btn btn-outline-danger btn-sm delete-pdf' : 'btn btn-danger btn-sm delete-pdf';
                    delBtn.dataset.postId = postId;
                    delBtn.dataset.stage = stage;
                    if (stage === 'f25') {
                        delBtn.dataset.variant = variant;
                        delBtn.dataset.f25Stage = f25Stage;
                    }
                    delBtn.innerHTML = '<i class="bx bx-trash"></i>';
                    delBtn.title = 'Delete & Regenerate';
                    delBtn.setAttribute('aria-label', 'Delete & regenerate PDF');

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
                    button.innerHTML = '<i class="fa-solid fa-file-circle-plus"></i> Generate PDF';

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
            const variant = btn.dataset.variant || '';
            const f25Stage = btn.dataset.f25Stage || '';

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
                        stage: stage,
                        variant: variant
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
                            genBtn.dataset.postId = postId;
                            genBtn.dataset.stage = stage;
                            genBtn.dataset.scheme = btn.dataset.scheme || 'qms';

                            if (stage === 'f25') {
                                genBtn.className = 'btn btn-outline-secondary btn-sm generate-pdf w-100';
                                genBtn.dataset.variant = variant;
                                genBtn.dataset.f25Stage = f25Stage;
                            } else {
                                genBtn.className = 'btn btn-outline-secondary generate-pdf';
                            }
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
