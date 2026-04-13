<?php

/**
 * certification-stages.php
 *
 * Updated to include a separate “qms” track alongside the existing “ems” track.
 * The EMS part is left untouched; QMS stages are defined in their own sub‐array.
 */

/**
 * Returns all certification stages for each track.
 * 
 * NOTE: The ‘ems’ array below must remain exactly as it was. We’ve simply added
 * a new ‘qms’ key, with its own stages and ACF group keys.
 */
function get_certification_stages() {
    return [
        'ems' => [

            'draft' => ['title'   => 'Draft', 'group'   => 'group_67dc014741369', 'next'    => 'f01',],
            // F-01: EMS Application
            'f01' => ['title' => 'F-01 EMS Application', 'group' => 'group_68222fbec9c41', 'next' => 'f02'],

            // F-02: EMS Application Review
            'f02' => ['title' => 'F-02 EMS Application Review', 'group' => 'group_qms_f02', 'next' => 'f03'],

            // F-03: Certification Agreement
            'f03' => ['title' => 'F-03 Certification Agreement', 'group' => 'group_qms_f03', 'next' => 'f05'],

            // F-05: Audit Team Allocation (Stage 1)
            'f05' => ['title' => 'F-05 Audit Team Allocation', 'group' => 'group_f05_ems_audit_team_allocation_plan_stage_1', 'next' => 'f06'],

            // F-06: Document Review Report
            'f06' => ['title' => 'F-06 Document Review Report', 'group' => 'group_qms_f06', 'next' => 'f07'],

            // F-07: Audit Schedule
            'f07' => ['title' => 'F-07 Audit Schedule', 'group' => 'group_qms_f07', 'next' => 'f08'],

            // F-08: Certificate Issuance
            'f08' => ['title' => 'F-08 Certificate Issuance', 'group' => 'group_qms_f08', 'next' => 'f11'],

            // F-11: Invoice / Billing Details
            'f11' => ['title' => 'F-11 Invoice / Billing Details', 'group' => 'group_qms_f11', 'next' => 'f09'],

            // F-09: Stage 1 Audit Report (no form yet)
            'f09' => [
                'title'   => 'F-09 Stage 1 Audit Report',
                'group'   => '',   // leave blank until an ACF group is created
                'next'    => 'f10',
            ],

            // F-10: Non‐Conformity (uses F-13 fields)
            'f10' => ['title' => 'F-10 Non-Conformity', 'group' => 'group_qms_f13', 'next' => 'f12'],

            // F-12: Scope of Certification (no form yet)
            'f12' => [
                'title'   => 'F-12 Scope of Certification',
                'group'   => '',   // leave blank until an ACF group is created
                'next'    => 'f13',
            ],

            // F-13: Corrective Action Request
            'f13' => ['title' => 'F-13 Corrective Action Request', 'group' => 'group_qms_f13', 'next' => 'f14'],

            // F-14: Conflict of Interest Declaration
            'f14' => ['title' => 'F-14 Conflict of Interest Declaration', 'group' => 'group_67e69bef71256', 'next' => 'sheet6'],

            // Sheet 6: Audit Notification Email (template‐only)
            'sheet6' => [
                'title'     => 'QMS – Audit Notification Email',
                'group'     => '',   // no ACF form; uses subject/message/pdf_field keys
                'next'      => null,
            ],

        ],

        /**
         * ──────────────────────────────────────────────────────────────────────
         * NEW QMS TRACK (DO NOT DISTURB EMS)
         * ──────────────────────────────────────────────────────────────────────
         *
         * These stages use the same form‐keys as above (the “group_qms_fXX” keys),
         * but they live under a separate ‘qms’ key so EMS remains untouched.
         */
        'qms' => [

            'draft' => ['title' => 'Draft', 'group' => 'group_67dc014741369', 'next' => 'f01'],
            'f01' => ['title'   => 'F-01 QMS Application', 'group'   => 'group_68173ed286e57', 'next'    => 'f02'],
            'f02' => ['title' => 'F-02 QMS Application Review', 'group' => 'group_f02_technical_review', 'next' => 'f03'],
            'f03' => ['title' => 'F-03 Certification Agreement', 'group' => 'group_qms_f03', 'next' => 'f05'],
            'f05' => ['title' => 'F-05 Audit Team Allocation', 'group' => 'group_qms_f05', 'next' => 'f14'],
            'f14' => ['title' => 'F-14 Conflict of Interest Declaration', 'group' => 'group_qms_f14', 'next' => 'sheet6'],
            'sheet6' => ['title' => 'Sheet6 QMS – Audit Notification Email', 'group' => 'group_qms_sheet6',  'next' => 'f08'],
            'f08' => ['title' => 'F-08 Certificate Issuance', 'group' => 'group_qms_f08', 'next' => 'f06'],
            'f06' => ['title' => 'F-06 Document Review Report', 'group' => 'group_qms_f06', 'next' => 'f11'],
            'f11' => ['title' => 'F-11 Invoice / Billing Details', 'group' => 'group_qms_f11', 'next' => 'f13'],
            'f13' => ['title' => 'F-13 Corrective Action Request', 'group' => 'group_qms_f13', 'next' => 'f05a'],
            'f05a' => ['title' => 'AUDIT TEAM ALLOCATION PLAN', 'group' => 'group_687a08899558e', 'next' => 'f07'],
            'f07' => ['title' => 'F-07 Audit Schedule', 'group' => 'group_qms_f07', 'next' => 'sheet12'],
            'sheet12' => ['title' => 'Sheet 12 QMS – Stage 1 Audit Notification', 'group' => 'group_6883495ce8192',  'next' => 'f08a'],
            'f08a' => ['title' => 'Audit Schedule', 'group' => 'group_6884e410c0ca6',  'next' => 'f09'],
            'f09' => ['title' => 'F-09 Stage 1 Audit Report', 'group' => 'group_68850e57b5024', 'next' => 'f12'],
            'f12' => ['title' => 'F-12 Scope of Certification', 'group' => 'group_68851d87c8150', 'next' => 'f13a'],
            'f13a' => ['title' => 'ATTENDANCE SHEET', 'group' => 'group_6885acad944c2',  'next' => 'f16'],
            'f16' => ['title' => 'AUDIT PROGRAMME', 'group' => 'group_6885acfbb64a3',  'next' => 'f17'],
            'f17' => ['title' => 'ON GOING SURVEILLANCE PLAN', 'group' => 'group_6885b9609179d',  'next' => 'f24'],
            'f24' => ['title' => 'Customer Feedback Form', 'group' => 'group_692abe113eded',  'next' => 'f48'],
            'f48'   => ['title' => 'Checklist for Completion of Reports', 'group' => 'group_qms_f48', 'next' => 'f19'],
            'f19' => ['title' => 'CHECKLIST FOR CERTIFICATION DECISION', 'group' => 'group_f19_full',  'next' => 'f15'],
            'f15' => ['title' => 'Correspondance & Communication Details', 'group' => 'group_6932f2a846e82',  'next' => 'f25'],
            'f25' => ['title' => 'Assessment Check List', 'group' => 'group_qms_f25',  'next' => 'f10'],
            'f10' => ['title' => 'Non-Conformity Report', 'group' => 'group_696f7f4276d24',  'next' => 'f69s1'],
            'f69s1' => ['title' => 'ISO 9001:2015 Surveillance Audit-1-Reg.', 'group' => 'group_69707ce350599',  'next' => 'f05s1'],
            'f05s1' => ['title' => 'AUDIT TEAM ALLOCATION PLAN', 'group' => 'group_6970b191c8d75',  'next' => 'f14s1'],
            'f14s1' => ['title' => 'CONFIDENTIALITY AND NO CONFLICT OF INTEREST DECLARATION', 'group' => 'group_6970c27c02c1c',  'next' => 'Sheet25'],
            'Sheet25' => ['title' => 'Sub: Surveillance Audit– 1 reg.', 'group' => 'group_6970c5e51b903',  'next' => 'f08s1'],
            'f08s1' => ['title' => 'Audit Schedule', 'group' => 'group_6970cd4a77bdc',  'next' => 'f13s1'],
            'f13s1' => ['title' => 'ATTENDANCE SHEET', 'group' => 'group_6974d1c3c4539',  'next' => 'f21s1'],
            'f21s1' => ['title' => 'Audit  Report', 'group' => 'group_6974d4071dadf',  'next' => 'f16s1'],
            'f16s1' => ['title' => 'AUDIT PROGRAMME', 'group' => 'group_6974d719ef57b',  'next' => 'f17s1'],
            'f17s1' => ['title' => 'ON GOING SURVEILLANCE PLAN', 'group' => 'group_69758381afa73',  'next' => 'f19s1'],
            'f19s1' => ['title' => 'CHECKLIST FOR CERTIFICATION DECISION', 'group' => 'group_6975847c4afca',  'next' => 'f24s1'],
            'f24s1' => ['title' => 'Customer Feedback Form', 'group' => 'group_69758615cba4d',  'next' => 'f15s1'],
            'f15s1' => ['title' => 'Correspondence & Communication Detail', 'group' => 'group_6975912587e24',  'next' => 'f69s2'],
            'f69s2' => ['title' => 'ISO 9001:2015 Surveillance Audit-2-Reg.', 'group' => 'group_955cf0466343', 'next'  => 'f05s2'],
            'f05s2' => ['title' => 'AUDIT TEAM ALLOCATION PLAN', 'group' => 'group_8fa3b08f8716', 'next'  => 'f14s2'],
            'f14s2' => ['title' => 'CONFIDENTIALITY AND NO CONFLICT OF INTEREST DECLARATION', 'group' => 'group_a8577ab07da7', 'next'  => 'sheet36'],    
            'sheet36' => ['title' => 'Surveillance Audit-2 Reg.', 'group' => 'group_bff8a27d39dd', 'next'  => 'f08s2'],        
            'f08s2' => ['title' => 'Audit Schedule', 'group' => 'group_cca0f89f58c2', 'next'  => 'f13s2'],    
            'f13s2' => ['title' => 'ATTENDANCE SHEET', 'group' => 'group_d958093fc637', 'next'  => 'f21s2'],    
            'f21s2' => ['title' => 'Audit Report', 'group' => 'group_65cf5ee61153', 'next'  => 'f16s2'],    
            'f16s2' => ['title' => 'AUDIT PROGRAMME', 'group' => 'group_e18f7c62d39c', 'next'  => 'f17s2'],    
            'f17s2' => ['title' => 'ON GOING SURVEILLANCE PLAN', 'group' => 'group_7ce695799b3a', 'next'  => 'f19s2'],    
            'f19s2' => ['title' => 'CHECKLIST FOR CERTIFICATION DECISION', 'group' => 'group_b133a3da4e9d', 'next'  => 'f15s2'],    
            'f15s2' => ['title' => 'Correspondence & Communication Detail', 'group' => 'group_f5e9a27b67e6', 	'next' 	=>	'f24s2'],    
            'f24s2' => ['title' => 'Customer Feedback Form', 'group' => 'group_d03dadac1197', 'next'  => null],
        ], // end 'qms'

        /**
         * ──────────────────────────────────────────────────────────────────────
         * IMS TRACK (ISO 9001 + ISO 14001 + ISO 45001)
         * ACF group keys are '' until IMS field groups are created.
         * Differences from QMS:
         *   f69s1  → f69      (IMS names it F69, not F69S1)
         *   f21s1  → f09s1    (IMS uses F09 for surveillance audit reports)
         *   f21s2  → f09s2
         * ──────────────────────────────────────────────────────────────────────
         */
        'ims' => [
            // Reuses QMS ACF groups — cert_type='ims' controls conditional logic in PDF templates.
            // f69   → QMS group_69707ce350599 (same form as QMS f69s1, IMS just calls it f69)
            // f09s1 → QMS group_6974d4071dadf (same form as QMS f21s1, IMS calls it f09s1)
            // f09s2 → QMS group_65cf5ee61153  (same form as QMS f21s2, IMS calls it f09s2)
            // f48s2 → QMS group_qms_f48       (same completion checklist, used at end of Surv-2)

            // ── Initial Certification ─────────────────────────────────────────
            'draft'   => ['title' => 'Draft',                                                   'group' => 'group_67dc014741369',   'next' => 'f01'],
            'f01'     => ['title' => 'F-01 IMS Application',                                    'group' => 'group_ims_f01',         'next' => 'f02'],
            'f02'     => ['title' => 'F-02 IMS Application Review',                             'group' => 'group_ims_f02',         'next' => 'f03'],
            'f03'     => ['title' => 'F-03 Certification Agreement',                            'group' => 'group_ims_f03',         'next' => 'f05'],
            'f05'     => ['title' => 'F-05 Audit Team Allocation',                              'group' => 'group_ims_f05',         'next' => 'f14'],
            'f14'     => ['title' => 'F-14 Confidentiality & No Conflict of Interest',          'group' => 'group_ims_f14',         'next' => 'sheet12_ims_s1'],
            'sheet12_ims_s1' => ['title' => 'Sheet 12 IMS – Stage-1 Audit Notification',        'group' => 'group_ims_sheet12',     'next' => 'f08'],
            'f08'     => ['title' => 'F-08 Audit Schedule (Stage-1)',                           'group' => 'group_ims_f08',         'next' => 'f06'],
            'f06'     => ['title' => 'F-06 Document Review Report',                             'group' => 'group_ims_f06',         'next' => 'f11'],
            'f11'     => ['title' => 'F-11 Invoice / Billing Details',                         'group' => 'group_ims_f11',         'next' => 'f13'],
            'f13'     => ['title' => 'F-13 Corrective Action Request',                         'group' => 'group_ims_f13',         'next' => 'f05a'],
            'f05a'    => ['title' => 'F-05a Audit Team Allocation Plan (Stage-2)',              'group' => 'group_ims_f05a',        'next' => 'f07'],
            'f07'     => ['title' => 'F-07 Certification Assessment Plan',                     'group' => 'group_ims_f07',         'next' => 'sheet12_ims_s2'],
            'sheet12_ims_s2' => ['title' => 'Sheet 12 IMS – Stage-2 Audit Notification',        'group' => 'group_ims_sheet12',     'next' => 'f08a'],
            'f08a'    => ['title' => 'F-08a Audit Schedule (Stage-2)',                         'group' => 'group_ims_f08a',        'next' => 'f09'],
            'f09'     => ['title' => 'F-09 Stage-2 Audit Report',                              'group' => 'group_ims_f09',         'next' => 'f12'],
            'f12'     => ['title' => 'F-12 Scope of Certification',                            'group' => 'group_ims_f12',         'next' => 'f13a'],
            'f13a'    => ['title' => 'F-13 Attendance Sheet',                                  'group' => 'group_ims_f13a',        'next' => 'f16'],
            'f16'     => ['title' => 'F-16 Audit Programme',                                   'group' => 'group_ims_f16',         'next' => 'f17'],
            'f17'     => ['title' => 'F-17 Closing Meeting Minutes',                           'group' => 'group_ims_f17',         'next' => 'f24'],
            'f24'     => ['title' => 'F-24 Customer Feedback Form',                            'group' => 'group_ims_f24',         'next' => 'f48'],
            'f48'     => ['title' => 'F-48 Checklist for Completion of Reports',               'group' => 'group_ims_f48',         'next' => 'f19'],
            'f19'     => ['title' => 'F-19 Checklist for Certification Decision',              'group' => 'group_ims_f19',         'next' => 'f15'],
            'f15'     => ['title' => 'F-15 Correspondence & Communication Details',            'group' => 'group_ims_f15',         'next' => 'f25'],
            'f25'     => ['title' => 'F-25 Assessment Check List',                             'group' => 'group_ims_f25',         'next' => 'f10'],
            'f10'     => ['title' => 'F-10 Non-Conformity Report',                             'group' => 'group_ims_f10',         'next' => 'f69'],

            // ── Surveillance Year 1 ───────────────────────────────────────────
            'f69'     => ['title' => 'F-69 IMS Surveillance Audit-1 Reg.',                     'group' => 'group_ims_f69',         'next' => 'f05s1'],
            'f05s1'   => ['title' => 'F-05 Audit Team Allocation Plan (Surv-1)',               'group' => 'group_ims_f05s1',       'next' => 'f14s1'],
            'f14s1'   => ['title' => 'F-14 Confidentiality & No COI (Surv-1)',                 'group' => 'group_ims_f14s1',       'next' => 'sheet25_ims'],
            'sheet25_ims' => ['title' => 'Sheet 25 IMS – Surv-1 Audit Notification',           'group' => 'group_ims_sheet25',     'next' => 'f08s1'],
            'f08s1'   => ['title' => 'F-08 Audit Schedule (Surv-1)',                           'group' => 'group_ims_f08s1',       'next' => 'f13s1'],
            'f13s1'   => ['title' => 'F-13 Attendance Sheet (Surv-1)',                         'group' => 'group_ims_f13s1',       'next' => 'f09s1'],
            'f09s1'   => ['title' => 'F-09 Surveillance Audit Report (Surv-1)',                'group' => 'group_ims_f09s1',       'next' => 'f16s1'],
            'f16s1'   => ['title' => 'F-16 Audit Findings (Surv-1)',                           'group' => 'group_ims_f16s1',       'next' => 'f17s1'],
            'f17s1'   => ['title' => 'F-17 Closing Meeting Minutes (Surv-1)',                  'group' => 'group_ims_f17s1',       'next' => 'f19s1'],
            'f19s1'   => ['title' => 'F-19 Checklist for Certification Decision (Surv-1)',     'group' => 'group_ims_f19s1',       'next' => 'f24s1'],
            'f24s1'   => ['title' => 'F-24 Customer Feedback Form (Surv-1)',                   'group' => 'group_ims_f24s1',       'next' => 'f15s1'],
            'f15s1'   => ['title' => 'F-15 Correspondence & Communication Details (Surv-1)',   'group' => 'group_ims_f15s1',       'next' => 'f69s2'],

            // ── Surveillance Year 2 ───────────────────────────────────────────
            'f69s2'   => ['title' => 'F-69 IMS Surveillance Audit-2 Reg.',                     'group' => 'group_ims_f69s2',       'next' => 'f05s2'],
            'f05s2'   => ['title' => 'F-05 Audit Team Allocation Plan (Surv-2)',               'group' => 'group_ims_f05s2',       'next' => 'f14s2'],
            'f14s2'   => ['title' => 'F-14 Confidentiality & No COI (Surv-2)',                 'group' => 'group_ims_f14s2',       'next' => 'sheet36_ims'],
            'sheet36_ims' => ['title' => 'Sheet 36 IMS – Surv-2 Audit Notification',           'group' => 'group_ims_sheet36',     'next' => 'f08s2'],
            'f08s2'   => ['title' => 'F-08 Audit Schedule (Surv-2)',                           'group' => 'group_ims_f08s2',       'next' => 'f13s2'],
            'f13s2'   => ['title' => 'F-13 Attendance Sheet (Surv-2)',                         'group' => 'group_ims_f13s2',       'next' => 'f09s2'],
            'f09s2'   => ['title' => 'F-09 Surveillance Audit Report (Surv-2)',                'group' => 'group_ims_f09s2',       'next' => 'f16s2'],
            'f16s2'   => ['title' => 'F-16 Audit Findings (Surv-2)',                           'group' => 'group_ims_f16s2',       'next' => 'f17s2'],
            'f17s2'   => ['title' => 'F-17 Closing Meeting Minutes (Surv-2)',                  'group' => 'group_ims_f17s2',       'next' => 'f19s2'],
            'f19s2'   => ['title' => 'F-19 Checklist for Certification Decision (Surv-2)',     'group' => 'group_ims_f19s2',       'next' => 'f15s2'],
            'f15s2'   => ['title' => 'F-15 Correspondence & Communication Details (Surv-2)',   'group' => 'group_ims_f15s2',       'next' => 'f24s2'],
            'f24s2'   => ['title' => 'F-24 Customer Feedback Form (Surv-2)',                   'group' => 'group_ims_f24s2',       'next' => 'f48s2'],

            // ── Final ─────────────────────────────────────────────────────────
            'f48s2'   => ['title' => 'F-48 Checklist for Completion of Reports (Surv-2)',      'group' => 'group_ims_f48s2',       'next' => null],

        ], // end 'ims'

        'ohsms' => [
            'draft' => ['title' => 'Draft', 'group' => 'group_67dc014741369', 'next' => 'f01'],
            'f01'   => ['title' => 'F-01 OHSMS Application', 'group' => 'group_68173ed286e57', 'next' => 'f02'],
            // ... (rest of OHSMS stages follow QMS pattern for now)
        ],

        'mdqms' => [
            'draft' => ['title' => 'Draft', 'group' => 'group_67dc014741369', 'next' => 'f01'],
            'f01'   => ['title' => 'F-01 MDQMS Application', 'group' => 'group_68173ed286e57', 'next' => 'f02'],
        ],

        'isms' => [
            'draft' => ['title' => 'Draft', 'group' => 'group_67dc014741369', 'next' => 'f01'],
            'f01'   => ['title' => 'F-01 ISMS Application', 'group' => 'group_68173ed286e57', 'next' => 'f02'],
        ],

    ];
}
/**
 * Returns all email templates for each certification track.
 * 
 * We add a new 'qms' key without modifying the existing 'ems' definitions.
 */
function get_certification_emails() {
    return [

        /**
         * ──────────────────────────────────────────────────────────────────────
         * EXISTING EMS EMAIL TEMPLATES (UNCHANGED)
         * ──────────────────────────────────────────────────────────────────────
         */
        'ems' => [

            'f01'   => [
                'subject'   => 'EMS Application Received',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Thank you for submitting your EMS Application (F-01). Our team will review it and get back to you shortly.</p>
                    <p>Best regards,<br/>Certifications Team</p>
                ',
                'pdf_field' => '', // no PDF for F-01 in EMS
            ],

            'f02'   => [
                'subject'   => 'EMS Application Review Completed',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Your EMS Application Review (F-02) is complete. Please see the attached review summary.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Regards,<br/>Certifications Team</p>
                ',
                'pdf_field' => 'f02_pdf',
            ],

            // … (other existing EMS email templates for f03, f05, f06, etc.) …

            'f13'   => [
                'subject'   => 'EMS Corrective Action Request (F-13)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>A Corrective Action Request (F-13) has been generated. Please review the attached document and address the non-conformities.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Thank you,<br/>Certifications Team</p>
                ',
                'pdf_field' => 'f13_pdf',
            ],

            'sheet6' => [
                'subject'   => 'EMS Attendance Sheet Stage 2',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Your EMS Attendance Sheet (Stage 2) is attached.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Regards,<br/>Certifications Team</p>
                ',
                'pdf_field' => 'sheet6_pdf',
            ],

            // --- ADDED MISSING EMS STAGES ---
            'f03' => ['subject' => 'EMS Certification Agreement (F-03)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Certification Agreement (F-03).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Certifications Team</p>', 'pdf_field' => 'f03_pdf'],
            'f05' => ['subject' => 'EMS Audit Team Allocation (F-05)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Team Allocation (F-05).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Certifications Team</p>', 'pdf_field' => 'f05_pdf'],
            'f06' => ['subject' => 'EMS Document Review Report (F-06)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Document Review Report (F-06).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Certifications Team</p>', 'pdf_field' => 'f06_pdf'],
            'f07' => ['subject' => 'EMS Audit Schedule (F-07)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Schedule (F-07).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Certifications Team</p>', 'pdf_field' => 'f07_pdf'],
            'f08' => ['subject' => 'EMS Certificate Issuance (F-08)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Certificate (F-08).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Certifications Team</p>', 'pdf_field' => 'f08_pdf'],
            'f11' => ['subject' => 'EMS Invoice / Billing Details (F-11)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Invoice/Billing Details (F-11).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Billing Team</p>', 'pdf_field' => 'f11_pdf'],
            'f09' => ['subject' => 'EMS Stage 1 Audit Report (F-09)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Stage 1 Audit Report (F-09).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Certifications Team</p>', 'pdf_field' => 'f09_pdf'],
            'f10' => ['subject' => 'EMS Non-Conformity (F-10)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Non-Conformity Report (F-10).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Certifications Team</p>', 'pdf_field' => 'f10_pdf'],
            'f12' => ['subject' => 'EMS Scope of Certification (F-12)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Scope of Certification (F-12).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Certifications Team</p>', 'pdf_field' => 'f12_pdf'],
            'f14' => ['subject' => 'EMS Conflict of Interest Declaration (F-14)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Conflict of Interest Declaration (F-14).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>Certifications Team</p>', 'pdf_field' => 'f14_pdf'],

        ], // end 'ems'


        /**
         * ──────────────────────────────────────────────────────────────────────
         * NEW QMS EMAIL TEMPLATES (DO NOT DISTURB EMS)
         * ──────────────────────────────────────────────────────────────────────
         */
        'qms' => [


            'f03'   => [
                'subject'   => 'QMS Certification Agreement (F-03)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Your Certification Agreement (F-03) has been processed. Please review and sign the attached agreement.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Thank you,<br/>QMS Certifications Team</p>
                ',
                'pdf_field' => 'f03_pdf',
            ],

            'f05'   => [
                'subject'   => 'QMS Audit Team Allocation (F-05)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Your Audit Team has been allocated. Find the details in the attached F-05 form.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Regards,<br/>QMS Certifications Team</p>
                ',
                'pdf_field' => 'f05_pdf',
            ],

            'f06'   => [
                'subject'   => 'QMS Document Review Report (F-06)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>The Document Review Report (F-06) is ready. Please see the attached document for details.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Thank you,<br/>QMS Certifications Team</p>
                ',
                'pdf_field' => 'f06_pdf',
            ],

            'f07'   => [
                'subject'   => 'QMS Audit Schedule (F-07)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Your Audit Schedule (F-07) has been finalized. Please review the attached schedule.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Regards,<br/>QMS Certifications Team</p>
                ',
                'pdf_field' => 'f07_pdf',
            ],

            'f08'   => [
                'subject'   => 'QMS Certificate Issuance (F-08)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Your QMS Certificate (F-08) has been issued. Please download the certificate using the link below.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Thank you,<br/>QMS Certifications Team</p>
                ',
                'pdf_field' => 'f08_pdf',
            ],

            'f11'   => [
                'subject'   => 'QMS Invoice / Billing Details (F-11)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Your invoice (F-11) is attached. Please arrange payment as per the due date.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Regards,<br/>QMS Billing Team</p>
                ',
                'pdf_field' => 'f11_pdf',
            ],

            'f13'   => [
                'subject'   => 'QMS Corrective Action Request (F-13)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>A Corrective Action Request (F-13) has been raised. Please review the attached request and close all non-conformities.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Thank you,<br/>QMS Certifications Team</p>
                ',
                'pdf_field' => 'f13_pdf',
            ],

            'sheet6' => [
                'subject'   => 'QMS Audit Notification (Sheet 6)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Your QMS Audit Notification (Sheet 6) is attached. Please review the audit details.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Regards,<br/>QMS Certifications Team</p>
                ',
                'pdf_field' => 'sheet6_pdf',
            ],

            'f15'   => [
                'subject'   => 'QMS Correspondence & Communication Details (F-15)',
                'message'   => '
                    <p>Dear {{client_name}},</p>
                    <p>Please update and return the Correspondence &amp; Communication Details form (F-15) at your earliest convenience.</p>
                    <p>This will ensure we have your correct contact information for all future audit and certification communications.</p>
                    <p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p>
                    <p>Thank you,<br/>QMS Certifications Team</p>
                ',
                'pdf_field' => 'f15_pdf',
            ],

            // --- ADDED MISSING QMS STAGES ---
            'f01' => ['subject' => 'QMS Application (F-01)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the QMS Application (F-01).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f01_pdf'],
            'f02' => ['subject' => 'QMS Application Review (F-02)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Application Review (F-02).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f02_pdf'],
            'f14' => ['subject' => 'QMS Conflict of Interest (F-14)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Conflict of Interest Declaration (F-14).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f14_pdf'],
            'f05a' => ['subject' => 'Audit Team Allocation Plan', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Team Allocation Plan.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f05a_pdf'],
            'sheet12' => ['subject' => 'QMS Audit Notification Email (Sheet 12)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Notification.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'sheet12_pdf'],
            'f08a' => ['subject' => 'Audit Schedule', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Schedule.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f08a_pdf'],
            'f09' => ['subject' => 'Stage 1 Audit Report (F-09)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Stage 1 Audit Report (F-09).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f09_pdf'],
            'f12' => ['subject' => 'Scope of Certification (F-12)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Scope of Certification (F-12).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f12_pdf'],
            'f13a' => ['subject' => 'Attendance Sheet', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Attendance Sheet.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f13a_pdf'],
            'f16' => ['subject' => 'Audit Programme', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Programme.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f16_pdf'],
            'f17' => ['subject' => 'On Going Surveillance Plan', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the On Going Surveillance Plan.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f17_pdf'],
            'f24' => ['subject' => 'Customer Feedback Form', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Customer Feedback Form.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f24_pdf'],
            'f19' => ['subject' => 'Checklist for Certification Decision', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Checklist for Certification Decision.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f19_pdf'],
            'f25' => ['subject' => 'Assessment Check List', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Assessment Check List.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f25_pdf'],
            'f10' => ['subject' => 'Non-Conformity Report', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Non-Conformity Report.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f10_pdf'],

            // Surveillance Years
            'f69s1' => ['subject' => 'Surveillance Audit 1 Reg (F69S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surveillance Audit 1 Registration.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f69s1_pdf'],
            'f05s1' => ['subject' => 'Audit Team Allocation Plan (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Team Allocation Plan (S1).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f05s1_pdf'],
            'f14s1' => ['subject' => 'Conflict of Interest Declaration (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S1 Declaration.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f14s1_pdf'],
            'Sheet25' => ['subject' => 'Surveillance Audit 1 Reg Sub', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the document.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'Sheet25_pdf'],
            'f08s1' => ['subject' => 'Audit Schedule (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S1 Audit Schedule.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f08s1_pdf'],
            'f13s1' => ['subject' => 'Attendance Sheet (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S1 Attendance Sheet.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f13s1_pdf'],
            'f21s1' => ['subject' => 'Audit Report (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S1 Audit Report.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f21s1_pdf'],
            'f16s1' => ['subject' => 'Audit Programme (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S1 Audit Programme.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f16s1_pdf'],
            'f17s1' => ['subject' => 'On Going Surveillance Plan (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S1 Surveillance Plan.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f17s1_pdf'],
            'f19s1' => ['subject' => 'Checklist for Certification Decision (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S1 Checklist.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f19s1_pdf'],
            'f24s1' => ['subject' => 'Customer Feedback Form (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S1 Customer Feedback Form.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f24s1_pdf'],
            'f15s1' => ['subject' => 'Correspondence Details (S1)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S1 Correspondence Details.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f15s1_pdf'],

            'f69s2' => ['subject' => 'Surveillance Audit 2 Reg (F69S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surveillance Audit 2 Registration.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f69s2_pdf'],
            'f05s2' => ['subject' => 'Audit Team Allocation Plan (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Team Allocation Plan (S2).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f05s2_pdf'],
            'f14s2' => ['subject' => 'Conflict of Interest Declaration (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S2 Declaration.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f14s2_pdf'],
            'sheet36' => ['subject' => 'Surveillance Audit 2 Reg Sub', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the document.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'sheet36_pdf'],
            'f08s2' => ['subject' => 'Audit Schedule (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S2 Audit Schedule.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f08s2_pdf'],
            'f13s2' => ['subject' => 'Attendance Sheet (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S2 Attendance Sheet.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f13s2_pdf'],
            'f21s2' => ['subject' => 'Audit Report (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S2 Audit Report.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f21s2_pdf'],
            'f16s2' => ['subject' => 'Audit Programme (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S2 Audit Programme.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f16s2_pdf'],
            'f17s2' => ['subject' => 'On Going Surveillance Plan (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S2 Surveillance Plan.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f17s2_pdf'],
            'f19s2' => ['subject' => 'Checklist for Certification Decision (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S2 Checklist.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f19s2_pdf'],
            'f15s2' => ['subject' => 'Correspondence Details (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S2 Correspondence Details.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f15s2_pdf'],
            'f24s2' => ['subject' => 'Customer Feedback Form (S2)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the S2 Customer Feedback Form.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>QMS Team</p>', 'pdf_field' => 'f24s2_pdf'],

        ], // end 'qms'

        /**
         * ──────────────────────────────────────────────────────────────────────
         * IMS EMAIL TEMPLATES
         * ──────────────────────────────────────────────────────────────────────
         */
        'ims' => [
            'f01'     => ['subject' => 'IMS Application (F-01)',                              'message' => '<p>Dear {{client_name}},</p><p>Please find attached the IMS Application (F-01).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                              'pdf_field' => 'f01_pdf'],
            'f02'     => ['subject' => 'IMS Application Review (F-02)',                       'message' => '<p>Dear {{client_name}},</p><p>Please find attached the IMS Application Review (F-02).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                       'pdf_field' => 'f02_pdf'],
            'f03'     => ['subject' => 'IMS Certification Agreement (F-03)',                  'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Certification Agreement (F-03).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                  'pdf_field' => 'f03_pdf'],
            'f05'     => ['subject' => 'IMS Audit Team Allocation (F-05)',                    'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Team Allocation (F-05).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                    'pdf_field' => 'f05_pdf'],
            'f14'     => ['subject' => 'IMS Conflict of Interest Declaration (F-14)',         'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Conflict of Interest Declaration (F-14).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',         'pdf_field' => 'f14_pdf'],
            'sheet6'  => ['subject' => 'IMS Stage-1 Audit Notification',                     'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Stage-1 Audit Notification.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                     'pdf_field' => 'sheet6_pdf'],
            'f08'     => ['subject' => 'IMS Audit Schedule Stage-1 (F-08)',                  'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Schedule (F-08).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                  'pdf_field' => 'f08_pdf'],
            'f06'     => ['subject' => 'IMS Document Review Report (F-06)',                  'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Document Review Report (F-06).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                  'pdf_field' => 'f06_pdf'],
            'f11'     => ['subject' => 'IMS Invoice / Billing Details (F-11)',               'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Invoice/Billing Details (F-11).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Billing Team</p>',               'pdf_field' => 'f11_pdf'],
            'f13'     => ['subject' => 'IMS Corrective Action Request (F-13)',               'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Corrective Action Request (F-13).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',               'pdf_field' => 'f13_pdf'],
            'f05a'    => ['subject' => 'IMS Audit Team Allocation Plan Stage-2',             'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Team Allocation Plan (Stage-2).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',             'pdf_field' => 'f05a_pdf'],
            'f07'     => ['subject' => 'IMS Certification Assessment Plan (F-07)',           'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Certification Assessment Plan (F-07).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',           'pdf_field' => 'f07_pdf'],
            'sheet12_ims_s1' => ['subject' => 'IMS Stage-1 Audit Notification (Sheet 12)',         'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Stage-1 Audit Notification.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                     'pdf_field' => 'sheet12_pdf'],
            'sheet12_ims_s2' => ['subject' => 'IMS Stage-2 Audit Notification (Sheet 12)',         'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Stage-2 Audit Notification.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                     'pdf_field' => 'sheet12_pdf'],
            'f08a'    => ['subject' => 'IMS Audit Schedule Stage-2 (F-08a)',                 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Schedule (F-08a).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                 'pdf_field' => 'f08a_pdf'],
            'f09'     => ['subject' => 'IMS Stage-2 Audit Report (F-09)',                    'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Stage-2 Audit Report (F-09).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                    'pdf_field' => 'f09_pdf'],
            'f12'     => ['subject' => 'IMS Scope of Certification (F-12)',                  'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Scope of Certification (F-12).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                  'pdf_field' => 'f12_pdf'],
            'f13a'    => ['subject' => 'IMS Attendance Sheet (F-13a)',                       'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Attendance Sheet.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                       'pdf_field' => 'f13a_pdf'],
            'f16'     => ['subject' => 'IMS Audit Findings (F-16)',                          'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Findings (F-16).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                          'pdf_field' => 'f16_pdf'],
            'f17'     => ['subject' => 'IMS Closing Meeting Minutes (F-17)',                 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Closing Meeting Minutes (F-17).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                 'pdf_field' => 'f17_pdf'],
            'f24'     => ['subject' => 'IMS Customer Feedback Form (F-24)',                  'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Customer Feedback Form (F-24).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                  'pdf_field' => 'f24_pdf'],
            'f48'     => ['subject' => 'IMS Checklist for Completion of Reports (F-48)',     'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Checklist for Completion of Reports (F-48).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',     'pdf_field' => 'f48_pdf'],
            'f19'     => ['subject' => 'IMS Checklist for Certification Decision (F-19)',    'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Checklist for Certification Decision (F-19).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',    'pdf_field' => 'f19_pdf'],
            'f15'     => ['subject' => 'IMS Correspondence & Communication Details (F-15)',  'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Correspondence & Communication Details (F-15).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',  'pdf_field' => 'f15_pdf'],
            'f25'     => ['subject' => 'IMS Assessment Check List (F-25)',                   'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Assessment Check List (F-25).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                   'pdf_field' => 'f25_pdf'],
            'f10'     => ['subject' => 'IMS Non-Conformity Report (F-10)',                   'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Non-Conformity Report (F-10).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                   'pdf_field' => 'f10_pdf'],
            // Surveillance Year 1
            'f69'     => ['subject' => 'IMS Surveillance Audit-1 Reg. (F-69)',              'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surveillance Audit-1 Registration.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',              'pdf_field' => 'f69_pdf'],
            'f05s1'   => ['subject' => 'IMS Audit Team Allocation Plan (Surv-1)',            'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Team Allocation Plan (Surv-1).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',            'pdf_field' => 'f05s1_pdf'],
            'f14s1'   => ['subject' => 'IMS Confidentiality Declaration (Surv-1)',           'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Confidentiality Declaration.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',           'pdf_field' => 'f14s1_pdf'],
            'sheet25_ims' => ['subject' => 'IMS Surv-1 Audit Notification (Sheet 25)',          'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Audit Notification.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                      'pdf_field' => 'Sheet25_pdf'],
            'f08s1'   => ['subject' => 'IMS Audit Schedule (Surv-1)',                        'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Audit Schedule.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                        'pdf_field' => 'f08s1_pdf'],
            'f13s1'   => ['subject' => 'IMS Attendance Sheet (Surv-1)',                      'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Attendance Sheet.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                      'pdf_field' => 'f13s1_pdf'],
            'f09s1'   => ['subject' => 'IMS Surveillance Audit Report (Surv-1)',             'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Audit Report.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',             'pdf_field' => 'f09s1_pdf'],
            'f16s1'   => ['subject' => 'IMS Audit Findings (Surv-1)',                        'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Audit Findings.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                        'pdf_field' => 'f16s1_pdf'],
            'f17s1'   => ['subject' => 'IMS Closing Meeting Minutes (Surv-1)',               'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Closing Meeting Minutes.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',               'pdf_field' => 'f17s1_pdf'],
            'f19s1'   => ['subject' => 'IMS Checklist for Certification Decision (Surv-1)',  'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Certification Decision Checklist.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',  'pdf_field' => 'f19s1_pdf'],
            'f24s1'   => ['subject' => 'IMS Customer Feedback Form (Surv-1)',                'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Customer Feedback Form.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                'pdf_field' => 'f24s1_pdf'],
            'f15s1'   => ['subject' => 'IMS Correspondence & Communication Details (Surv-1)','message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-1 Correspondence Details.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>','pdf_field' => 'f15s1_pdf'],
            // Surveillance Year 2
            'f69s2'   => ['subject' => 'IMS Surveillance Audit-2 Reg. (F-69S2)',            'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surveillance Audit-2 Registration.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',            'pdf_field' => 'f69s2_pdf'],
            'f05s2'   => ['subject' => 'IMS Audit Team Allocation Plan (Surv-2)',            'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Audit Team Allocation Plan (Surv-2).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',            'pdf_field' => 'f05s2_pdf'],
            'f14s2'   => ['subject' => 'IMS Confidentiality Declaration (Surv-2)',           'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Confidentiality Declaration.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',           'pdf_field' => 'f14s2_pdf'],
            'sheet36_ims' => ['subject' => 'IMS Surv-2 Audit Notification (Sheet 36)',          'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Audit Notification.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                      'pdf_field' => 'sheet36_pdf'],
            'f08s2'   => ['subject' => 'IMS Audit Schedule (Surv-2)',                        'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Audit Schedule.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                        'pdf_field' => 'f08s2_pdf'],
            'f13s2'   => ['subject' => 'IMS Attendance Sheet (Surv-2)',                      'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Attendance Sheet.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                      'pdf_field' => 'f13s2_pdf'],
            'f09s2'   => ['subject' => 'IMS Surveillance Audit Report (Surv-2)',             'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Audit Report.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',             'pdf_field' => 'f09s2_pdf'],
            'f16s2'   => ['subject' => 'IMS Audit Findings (Surv-2)',                        'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Audit Findings.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                        'pdf_field' => 'f16s2_pdf'],
            'f17s2'   => ['subject' => 'IMS Closing Meeting Minutes (Surv-2)',               'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Closing Meeting Minutes.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',               'pdf_field' => 'f17s2_pdf'],
            'f19s2'   => ['subject' => 'IMS Checklist for Certification Decision (Surv-2)',  'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Certification Decision Checklist.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',  'pdf_field' => 'f19s2_pdf'],
            'f15s2'   => ['subject' => 'IMS Correspondence & Communication Details (Surv-2)','message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Correspondence Details.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>','pdf_field' => 'f15s2_pdf'],
            'f24s2'   => ['subject' => 'IMS Customer Feedback Form (Surv-2)',                'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Surv-2 Customer Feedback Form.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',                'pdf_field' => 'f24s2_pdf'],
            'f48s2'   => ['subject' => 'IMS Checklist for Completion of Reports (Surv-2)',   'message' => '<p>Dear {{client_name}},</p><p>Please find attached the Checklist for Completion of Reports (Surv-2).</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>IMS Team</p>',   'pdf_field' => 'f48s2_pdf'],

        ], // end 'ims'

        'ohsms' => [
            'f01' => ['subject' => 'OHSMS Application (F-01)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the OHSMS Application.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>OHSMS Team</p>', 'pdf_field' => 'f01_pdf'],
        ],

        'mdqms' => [
            'f01' => ['subject' => 'MDQMS Application (F-01)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the MDQMS Application.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>MDQMS Team</p>', 'pdf_field' => 'f01_pdf'],
        ],

        'isms' => [
            'f01' => ['subject' => 'ISMS Application (F-01)', 'message' => '<p>Dear {{client_name}},</p><p>Please find attached the ISMS Application.</p><p>Download: <a href="{{pdf_link}}">{{pdf_name}}</a></p><p>Regards,<br/>ISMS Team</p>', 'pdf_field' => 'f01_pdf'],
        ],

    ];
}

function get_certification_pdf() {
    return [
        'ems' => [
            'f01', 'f02', 'f03', 'f05', 'f06', 'f07', 'f08', 'f11', 'f09', 'f10', 'f12', 'f13', 'f14', 'sheet6'
        ],
        'qms' => [
            'f01', 'f02', 'f03', 'f05', 'f14', 'sheet6', 'f08', 'f06', 'f11', 'f13', 'f05a', 'f07', 'sheet12', 'f08a', 'f09', 'f12', 'f13a', 'f16', 'f17', 'f24', 'f19', 'f15', 'f25', 'f10',
            // Surveillance S1
            'f69s1', 'f05s1', 'f14s1', 'Sheet25', 'f08s1', 'f13s1', 'f21s1', 'f16s1', 'f17s1', 'f19s1', 'f24s1', 'f15s1',
            // Surveillance S2
            'f69s2', 'f05s2', 'f14s2', 'sheet36', 'f08s2', 'f13s2', 'f21s2', 'f16s2', 'f17s2', 'f19s2', 'f15s2', 'f24s2',
            // Final completion checklist
            'f48'
        ],
        // IMS: same order as QMS stages, f69s1→f69, f21s1→f09s1, f21s2→f09s2
        'ims' => [
            'f01', 'f02', 'f03', 'f05', 'f14', 'sheet6', 'f08', 'f06', 'f11', 'f13', 'f05a', 'f07', 'sheet12', 'f08a', 'f09', 'f12', 'f13a', 'f16', 'f17', 'f24', 'f48', 'f19', 'f15', 'f25', 'f10',
            // Surveillance S1 — note: f69 (not f69s1), f09s1 (not f21s1)
            'f69', 'f05s1', 'f14s1', 'sheet25_ims', 'f08s1', 'f13s1', 'f09s1', 'f16s1', 'f17s1', 'f19s1', 'f24s1', 'f15s1',
            // Surveillance S2 — note: f09s2 (not f21s2)
            'f69s2', 'f05s2', 'f14s2', 'sheet36_ims', 'f08s2', 'f13s2', 'f09s2', 'f16s2', 'f17s2', 'f19s2', 'f15s2', 'f24s2',
            // Final completion checklist
            'f48s2'
        ],
        'ohsms' => [ 'f01' ],
        'mdqms' => [ 'f01' ],
        'isms'  => [ 'f01' ],
    ];
}
