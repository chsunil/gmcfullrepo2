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
