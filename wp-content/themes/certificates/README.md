# GMC Certificates Theme

**Author:** Sunil  
**Theme Base:** Astra Child  
**Current Version:** 1.2.0

---

## Changelog

### v1.2.0 — 2026-02-24

#### Invoices Table Enhancements

**New Features**
- **Row expand/collapse** — Click the +/- icon to reveal line items, GST breakdown, payment history, and a summary bar (Paid / Balance / Status).
- **Edit / Delete payments** — Edit or delete individual payment entries directly from the expanded row. Totals and status update automatically.
- **Two date columns** — Replaced single "Date" column with:
  - **Created** — Invoice creation date.
  - **Last Payment** — Most recent payment received date (shows `—` if none).
- **Date range filter** — Calendar icon (📅) in the "Created" header toggles a date-range filter with **flatpickr** date pickers. Filter by From/To and click Apply.
- **Status column** — Kept visible in the main table for quick scanning (Paid / Partial / Unpaid badges).

**Columns moved to expandable panel**
- Paid amount, Balance, and detailed payment history are now shown inside the expandable row to keep the main table clean.

**Backend (functions.php)**
- `gmc_update_payment` — AJAX handler to edit a payment entry by index with automatic status recalculation.
- `gmc_delete_payment` — AJAX handler to remove a payment entry by index with automatic status recalculation.

**Dependencies Added**
- [Flatpickr](https://flatpickr.js.org/) v4.6.13 (CDN) — Date picker for the filter inputs.

---

### v1.1.1 — Previous

- Initial theme setup with sidebar, ACF tabs, workflow menu, sticky action bar.
- Invoice CRUD, PDF generation, email sending.
- Client management with workflow-based navigation.
