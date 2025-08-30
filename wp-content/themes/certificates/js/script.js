document.addEventListener("DOMContentLoaded", function () {
    // --------------------------------------
    // ACF Tabs: Activate based on ?stage
    // --------------------------------------
    const urlParams = new URLSearchParams(window.location.search);
    const stageParam = urlParams.get('stage');
    const postIdParam = urlParams.get('new_post_id'); // We check for new_post_id in the URL

    const tabs = document.querySelectorAll(".nav-tabs .nav-link");
    const tabPanes = document.querySelectorAll(".tab-content .tab-pane");

    // Tab header click: show content, do not change status or reload
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            // Don't prevent default to allow Bootstrap's native tab behavior to work
            const slug = tab.getAttribute('data-tab') || tab.getAttribute('data-bs-target')?.substring(1) || tab.getAttribute('href')?.substring(1);
            if (!slug) return;
            
            // Let Bootstrap handle the tab activation
            // This ensures proper initialization of ACF fields in the tab
            setTimeout(() => {
                // Trigger ACF to refresh fields in the newly activated tab
                if (typeof acf !== 'undefined') {
                    acf.doAction('ready');
                    
                    // Get the tab pane
                    const tabPane = document.getElementById(slug);
                    if (tabPane) {
                        // Force ACF to show field groups in this tab
                        const acfFieldGroups = tabPane.querySelectorAll('.acf-field-group');
                        acfFieldGroups.forEach(group => {
                            if (group) {
                                group.style.display = 'block';
                            }
                        });
                        
                        // Make sure empty containers are still visible
                        const emptyContainers = tabPane.querySelectorAll('.acf-fields-container');
                        emptyContainers.forEach(container => {
                            if (container && !container.innerHTML.trim()) {
                                const message = document.createElement('div');
                                message.className = 'alert alert-info';
                                message.innerHTML = 'Please fill out the form fields below.';
                                container.appendChild(message);
                            }
                        });
                    }
                }
            }, 100);
        });
    });

    if (stageParam) {
        tabs.forEach(tab => {
            const slug = tab.getAttribute("data-tab") || tab.getAttribute("data-bs-target")?.substring(1) || tab.getAttribute("href")?.substring(1);
            if (slug === stageParam) {
                // Use Bootstrap's Tab API to properly activate the tab
                const bsTab = new bootstrap.Tab(tab);
                bsTab.show();
            }
        });
    }

    // --------------------------------------
    // Multi-Step ACF Save + Next logic
    // --------------------------------------
    document.querySelectorAll(".acf-step-form").forEach((form) => {
        const saveBtn = form.querySelector(".save-button");
        const nextBtn = form.querySelector(".next-button");

        form.addEventListener("submit", function (e) {
            const isSave = e.submitter === saveBtn;
            const postIdField = form.querySelector('input[name="_acf_post_id"]');
            const postId = postIdField?.value;
            const nextStage = form.dataset.nextStage;

            if (isSave) {
                e.preventDefault(); // Only prevent default for save button to allow form save
            }

            acf.doAction('submit', form); // trigger ACF save

            setTimeout(() => {
                if (isSave && postId && nextBtn) {
                    if (!form.querySelector('.alert-success')) {
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success mt-3';
                        alert.textContent = 'Saved successfully!';
                        form.appendChild(alert);
                        alert.scrollIntoView({ behavior: 'smooth' });
                    }
                    nextBtn.classList.remove('d-none', 'hidden');
                    saveBtn.classList.add('d-none', 'hidden');
                }

                if (!isSave && postId && nextStage) {
                    // Handle first step: if no post_id, simulate creating new post and navigating to next stage
                    if (!postId && !postIdParam) {
                        // Create a new draft post via AJAX
                        fetch(ajaxurl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                action: 'create_new_client_post'
                            })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    const newPostId = data.post_id; // Get new post ID
                                    const url = new URL(window.location.href);
                                    url.searchParams.set('new_post_id', newPostId);  // Set new post ID
                                    url.searchParams.set('stage', 'draft');  // Set initial stage as 'draft'
                                    window.location.href = url.toString();  // Reload the page with new URL
                                }
                            });
                    } else {
                        // Save the form before transitioning to next stage
                        acf.doAction('submit', form);

                        setTimeout(() => {
                            fetch(ajaxurl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams({
                                    action: 'update_client_stage',
                                    post_id: postId,
                                    next_stage: nextStage
                                })
                            })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        // Update URL with the next stage and post ID
                                        const url = new URL(window.location.href);
                                        url.searchParams.set('stage', nextStage);  // Update the stage
                                        url.searchParams.set('new_post_id', postId);  // Update post ID
                                        window.location.href = url.toString();  // Reload page
                                    }
                                });
                        }, 800); // wait briefly to let ACF save before redirect
                    }
                }

            }, 900);
        });
    });
});

