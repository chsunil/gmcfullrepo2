# Client PDF Generator Plugin - Template Development To-Do List

## Existing Templates in Plugin (plugin/templates folder):
- qms-f01.php
- qms-f02.php
- qms-f03.php
- qms-f05.php
- qms-f06.php
- qms-f08.php
- qms-f11.php
- qms-f14.php
- qms-sheet6.php
- qms-sheet12.php

## Requirement CSV Files in requirement/csv:
- f01.csv
- f02.csv
- f03.csv
- f05.csv
- f05a.csv
- f05s1.csv
- f05s2.csv
- f06.csv
- f07.csv
- f08.csv
- f08a.csv
- f08s1.csv
- f08s2.csv
- f09.csv
- f10.csv
- f11.csv
- f12.csv
- f13.csv
- f13a.csv
- f13s1.csv
- f13s2.csv
- f14.csv
- f14s1.csv
- f14s2.csv
- f15.csv
- f15s1.csv
- f15s2.csv
- f16.csv
- f16s1.csv
- f16s2.csv
- f17.csv
- f17s1.csv
- f17s2.csv
- f19.csv
- f19s1.csv
- f19s2.csv
- f21s1.csv
- f21s2.csv
- f24.csv
- f24s1.csv
- f24s2.csv
- f25.csv
- f48.csv
- f69s1.csv
- f69s2.csv
- GMCSPL_AUDIT PACK _QMS_03.08.2024.csv
- sheet6.csv
- sheet12.csv
- sheet36.csv
- shwwt25.csv

## Pending Template Development:
The existing plugin templates cover some of the base forms (f01, f02, f03, f05, f06, f08, f11, f14) and some sheets (sheet6, sheet12).

Pending tasks:
- Create templates for other CSV forms not currently covered, including but not limited to:
  - f07, f09 through f13 series (f09.csv, f10.csv, f11.csv confirmed)
  - All 'a' and 's' subset files (f05a, f05s1, f05s2, etc.)
  - Later forms and sheets such as f15, f16, f17, f19, f21, f24, f25, f48, f69 series
  - Additional sheets like sheet36.csv and shwwt25.csv
  - Confirm and map requirements from GMCSPL_AUDIT PACK _QMS_03.08.2024.csv, if applicable.

## Next Steps / To-Do:
- Review existing CSV files contents to finalize specific field mappings per missing template.
- Develop PHP template files under plugin/templates for each missing CSV form.
- Integrate new templates with plugin rendering and AJAX PDF generation.
- Test all newly developed templates for accuracy and completeness.

---
This To-Do list should serve as a guide to complete template development based on the auditing requirement CSVs provided.
