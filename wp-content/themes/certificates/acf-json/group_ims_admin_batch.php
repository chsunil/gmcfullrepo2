{
    "key": "group_ims_f01",
    "title": "IMS - F-01: Application Form",
    "fields": [
        {
            "key": "field_ims_f01_manidata",
            "label": "Basic Information",
            "name": "manidata",
            "type": "clone",
            "clone": ["group_67dc014741369"],
            "display": "seamless"
        },
        {
            "key": "field_ims_f01_scope",
            "label": "Desired Scope of Certification",
            "name": "scope_of_certification",
            "type": "text"
        }
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]],
    "style": "default"
}
<!-- slide -->
{
    "key": "group_ims_f02",
    "title": "IMS - F-02: Application Technical Review",
    "fields": [
        {
            "key": "field_ims_f02_review",
            "label": "Review Details",
            "name": "review_details",
            "type": "textarea"
        }
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]]
}
<!-- slide -->
{
    "key": "group_ims_f03",
    "title": "IMS - F-03: Certification Agreement",
    "fields": [
        {
            "key": "field_ims_f03_agreement",
            "label": "Agreement Text",
            "name": "agreement_text",
            "type": "message",
            "message": "Standard IMS Certification Agreement content..."
        }
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]]
}
<!-- slide -->
{
    "key": "group_ims_f05",
    "title": "IMS - F-05: Audit Team Allocation",
    "fields": [
        {
            "key": "field_ims_f05_team",
            "label": "Team Members",
            "name": "audit_team",
            "type": "repeater",
            "sub_fields": [
                { "key": "field_ims_f05_name", "label": "Name", "name": "name", "type": "text" },
                { "key": "field_ims_f05_role", "label": "Role", "name": "role", "type": "select", "choices": { "Lead Auditor": "Lead Auditor", "Auditor": "Auditor" } }
            ]
        }
    ],
    "location": [[{"param": "post_type", "operator": "==", "value": "client"}]]
}
