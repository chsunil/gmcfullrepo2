/**
 * Toast Helper for Sneat / Bootstrap 5
 * Dynamically creates and shows toast notifications.
 */
'use strict';

function showToast(message, type = 'info') {
  // Types: 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'
  // If type is just 'error', map it to 'danger'
  if (type === 'error') type = 'danger';

  const container = document.getElementById('toast-container');
  if (!container) {
    console.error('Toast container not found!');
    alert(message); // Fallback if container is missing
    return;
  }

  // console.log('showToast called:', message, type);

  // Create toast element
  const toastEl = document.createElement('div');
  toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
  toastEl.role = 'alert';
  toastEl.ariaLive = 'assertive';
  toastEl.ariaAtomic = 'true';

  // Icon mapping
  let iconClass = 'bx-bell';
  if (type === 'success') iconClass = 'bx-check-circle';
  if (type === 'danger') iconClass = 'bx-error';
  if (type === 'warning') iconClass = 'bx-error-circle';

  toastEl.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">
        <i class="bx ${iconClass} me-2"></i> ${message}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;

  // Append to container
  container.appendChild(toastEl);

  // Check for Bootstrap
  if (typeof bootstrap === 'undefined') {
    console.error('Bootstrap 5 not found! Falling back to alert.');
    alert(message);
    return;
  }

  // Initialize Bootstrap Toast
  const toast = new bootstrap.Toast(toastEl, {
    delay: 5000,
    animation: true
  });

  // Show it
  toast.show();

  // Remove from DOM after hidden
  toastEl.addEventListener('hidden.bs.toast', function () {
    toastEl.remove();
  });
}

/**
 * Confirmation modal with Confirm / Cancel buttons.
 * @param {string}   message   - Question to display in the modal body
 * @param {Function} onConfirm - Called when user clicks Confirm
 * @param {string}   title     - Modal title (default 'Confirm')
 * @param {string}   type      - Bootstrap colour for the Confirm button (default 'danger')
 */
function showConfirmModal(message, onConfirm, title = 'Confirm', type = 'danger') {
  if (typeof bootstrap === 'undefined') {
    if (confirm(message)) onConfirm();
    return;
  }

  // Reuse existing modal element or create once
  let modalEl = document.getElementById('gmc-confirm-modal');
  if (!modalEl) {
    modalEl = document.createElement('div');
    modalEl.id = 'gmc-confirm-modal';
    modalEl.className = 'modal fade';
    modalEl.tabIndex = -1;
    modalEl.setAttribute('aria-hidden', 'true');
    modalEl.innerHTML = `
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn modal-confirm-btn">Confirm</button>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(modalEl);
  }

  // Update content and button style
  modalEl.querySelector('.modal-title').textContent = title;
  modalEl.querySelector('.modal-body').textContent = message;
  const confirmBtn = modalEl.querySelector('.modal-confirm-btn');
  confirmBtn.className = `btn btn-${type} modal-confirm-btn`;

  const modal = new bootstrap.Modal(modalEl);
  modal.show();

  // Attach one-time confirm handler
  const handler = function () {
    modal.hide();
    onConfirm();
  };
  confirmBtn.addEventListener('click', handler, { once: true });
}
