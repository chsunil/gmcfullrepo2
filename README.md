# GMC Client Management System

## Overview
A WordPress-based client management system for handling certifications, QMS (Quality Management System) audits, client information, invoicing, and PDF document generation.

## Technical Stack
- **Platform:** WordPress (Local WP development environment)
- **Database:** MySQL 5.7.28
- **Theme:** Certificates (Astra Child)
- **Git Branch:** feb2026

## Custom Theme: Certificates (v1.2.0)

A child theme of Astra with full custom functionality.

### Key Pages & Templates
- `page-dashboard.php` — Main client dashboard
- `page-client-single.php` — Individual client view with certification workflow
- `page-invoices.php` — Invoice listing with expand/collapse rows
- `page-invoice-form.php` — Invoice creation/edit form
- `page-audit_dates.php` — Audit scheduling view
- `template-client-form.php` — Client data entry form
- `template-user-add/edit/view.php` — User management templates

### Theme Includes
- `includes/class-client-list-table.php` — WP_List_Table extension for client listing
- `includes/class-gmc-invoice.php` — Invoice data model and CRUD
- `includes/reports.php` — Reporting logic
- `certification-stages.php` — Defines certification workflow stages

### AJAX Handlers (functions.php)
- `gmc_update_payment` — Edit a payment entry with status recalculation
- `gmc_delete_payment` — Remove a payment entry with status recalculation

---

## Custom Plugin: Client PDF Generator (v1.2)

Generates QMS/EMS PDFs via AJAX using the DOMPDF library.

### How It Works
1. Frontend triggers AJAX with `post_id`, `scheme` (e.g. `qms`), and `stage` (e.g. `f01`)
2. Plugin loads the matching PHP template from `templates/{scheme}-{stage}.php`
3. DOMPDF renders it to PDF, saves to `wp-content/uploads/client_pdfs/`
4. URL stored in ACF field `{stage}_pdf` on the client post
5. Success response returns the PDF URL

### AJAX Actions
- `wp_ajax_generate_pdf` — Generate and save a PDF
- `wp_ajax_delete_pdf` — Delete PDF file and clear ACF field

### Templates Available (`templates/`)
**QMS Forms:**
`qms-f01` through `qms-f17`, `qms-f25`, `qms-f69s1`, plus variants:
`qms-f05a`, `qms-f05s1`, `qms-f08a`, `qms-f08s1`, `qms-f13a`, `qms-f14s1`, `qms-f14s2`

**QMS Sheets:**
`qms-sheet6`, `qms-sheet12`, `qms-sheet25`

**EMS Templates:**
Located in `templates/ems/` subdirectory

### Plugin Directory Structure
```
client-pdf-generator/
├── client-pdf-generator.php   # Main plugin file (AJAX handlers, enqueue)
├── includes/
│   ├── class-certification-templates.php
│   ├── class-dynamic-fields.php
│   └── class-pdf-generator.php
├── templates/                 # PHP/HTML templates per form type
│   ├── qms-*.php
│   └── ems/
├── assets/js/generate-pdf.js  # Frontend AJAX trigger
└── dompdf/                    # Bundled PDF library
```

---

## Active Plugins

| Plugin | Purpose |
|--------|---------|
| Advanced Custom Fields Pro | Custom fields for client posts |
| ACF Matrix Field | Matrix/table field type for ACF |
| Calculated Fields for ACF | Dynamic field calculations |
| Client PDF Generator | QMS/EMS PDF generation |
| Admin Site Enhancements | Admin UI improvements |
| Backuply / Backuply Pro | Site backup management |
| TRX Addons | Theme extensions |
| User Role Editor | Role-based access control |
| WP Mail SMTP | SMTP email configuration |

---

## Development

- **Local environment:** Local WP
- **Git repo:** Yes (current branch: `feb2026`, main: `master`)
- **Custom post type:** `client`
- **Generated PDFs saved to:** `wp-content/uploads/client_pdfs/`

## Security
- Role-based access control via User Role Editor
- SMTP email via WP Mail SMTP
- Nonce verification on all AJAX endpoints
