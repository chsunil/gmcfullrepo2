{
    "key": "group_ims_f08",
    "title": "IMS - F-08: Audit Schedule (Stage-1)",
    "fields": [
        { "key": "field_ims_f08_plan", "label": "Audit Plan Details", "name": "audit_plan", "type": "textarea" }
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]]
}
<!-- slide -->
{
    "key": "group_ims_f15",
    "title": "IMS - F-15: Correspondence & Communication Details",
    "fields": [
        { "key": "field_ims_f15_log", "label": "Communication Log", "name": "comm_log", "type": "repeater", "sub_fields": [
            { "key": "field_ims_f15_date", "label": "Date", "name": "date", "type": "date_picker" },
            { "key": "field_ims_f15_subj", "label": "Subject", "name": "subject", "type": "text" }
        ]}
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]]
}
