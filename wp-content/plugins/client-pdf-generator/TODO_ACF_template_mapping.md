# ACF Field Group to Template Mapping

## Overview
This document maps Advanced Custom Fields (ACF) Field Groups from the export (requirement/acf-export-2025-11-23.json) to corresponding plugin templates located in `templates/`. This mapping helps clarify which templates utilize which field groups and identifies any gaps or missing implementations.

It now also incorporates a mapping to the audit dates CSV rows (from "GMCSPL_AUDIT PACK _QMS_03.08.2024.csv"), reflecting that the ACF field groups are created based on this CSV, and the PDF generation happens via these templates.

---

## ACF Field Groups to Template Mapping

| ACF Field Group Key            | Title                     | Mapped Template(s)                         | CSV Audit Date Rows / Notes                                               |
|-------------------------------|---------------------------|--------------------------------------------|-------------------------------------------------------------------------|
| group_audit_dates_table        | Audit Dates Table         | qms-f11.php                               | Contains audit date fields like Application Date, Review Date, Agreement, Auditor Allocation, and Surveillance dates from CSV |
| group_certificate_details      | Certificate Details       | Possibly qms-f11.php or others             | No direct match found, may need verification                           |
| group_client_details           | Client Details            | qms-f01.php, qms-f03.php, qms-f11.php      | Organization and contact person fields appear in these templates       |
| group_field_group_607b9...     | F-01 QMS Application form | qms-f01.php                               | Direct usage confirmed                                                  |
| group_field_group_607b9...     | F-02 EMS Application Review | qms-f02.php                               | Fields like review_requirements_list used                              |
| group_field_group_607b9...     | F-03 QMS Application Form | qms-f03.php                               | Similar usage confirmed                                                |
| group_field_group_607bb...     | F-11 Audit Plan           | qms-f11.php                               | Audit team and audit dates usage                                      |
| group_field_group_607b8...     | FE-17 Action Plan          | Not clearly mapped                        | Template not identified, may need creation or verification             |
| group_field_group_6088c...     | F-08 QMS Audit Report      | qms-f08.php                               | Matches fields usage in template                                       |
| group_field_group_608d7...     | F-06 EMS Audit Plan        | qms-f06.php                               | Commented out usage found, may be in progress                         |
| group_field_group_6142a...     | FE-19 EMS Action Plan      | Not found                                | Template not found; may require creation                              |
| group_field_group_6142a...     | FE-21 EMS Action Plan      | Not found                                | Template not found; may require creation                              |
| group_field_group_6254e...     | FE 14 QMS Audit Report     | Not found                                | Template not found; may require creation                              |
| group_field_group_62714...     | F 14 QMS Audit Report      | Not found                                | Template not found; may require creation                              |
| group_field_group_628c7...     | FE 13 QMS Audit Report     | Not found                                | Template not found; may require creation                              |
| group_field_group_63320...     | FE 15 EMS Audit Report     | Not found                                | Template not found; may require creation                              |
| group_field_group_6361d...     | F 13 QMS Audit Report      | Not found                                | Template not found; may require creation                              |
| group_field_group_637b7...     | FE 16 QMS Audit Report     | Not found                                | Template not found; may require creation                              |
| group_field_group_63c52...     | FE 17 EMS Audit Report     | Not found                                | Template not found; may require creation                              |
| group_field_group_63cff...     | FE-20 EMS Audit Report     | Not found                                | Template not found; may require creation                              |

---

## Additional Notes:
- The ACF field groups have been created based on the audit dates and other metadata from the CSV file "GMCSPL_AUDIT PACK _QMS_03.08.2024.csv".
- PDF generation uses the templates listed above, pulling data from these ACF groups.
- Missing templates identified need to be created following the CSV-to-ACF structure for full coverage.

---

## Next Steps
- Confirm the missing templates that need to be created from the CSV fields.
- Align the new template creation to ensure it covers all necessary audit dates and related metadata.
- Implement these templates for full audit date PDF generation coverage in the plugin.

---

Please review this updated mapping and advise if I should proceed with creating the missing templates and related implementations.
