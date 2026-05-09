{
    "key": "group_ims_f05a",
    "title": "IMS - F-05a: Audit Team Allocation Plan (Stage-2)",
    "fields": [
        { "key": "field_ims_f05a_team", "label": "Team Members", "name": "audit_team", "type": "repeater", "sub_fields": [
            { "key": "field_ims_f05a_name", "label": "Name", "name": "name", "type": "text" }
        ]}
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]]
}
<!-- slide -->
{
    "key": "group_ims_f08a",
    "title": "IMS - F-08a: Audit Schedule (Stage-2)",
    "fields": [
        { "key": "field_ims_f08a_sched", "label": "Stage-2 Schedule", "name": "stage2_schedule", "type": "textarea" }
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]]
}
<!-- slide -->
{
    "key": "group_ims_f15",
    "title": "IMS - F-15: Correspondence & Communication Details",
    "fields": [
        { "key": "field_ims_f15_log", "label": "Log", "name": "comm_log", "type": "textarea" }
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]]
}
<!-- slide -->
{
    "key": "group_ims_sheet6",
    "title": "IMS - Sheet 6: Audit Notification",
    "fields": [
        { "key": "field_ims_sheet6_msg", "label": "Email Message", "name": "email_message", "type": "textarea" }
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]]
}
