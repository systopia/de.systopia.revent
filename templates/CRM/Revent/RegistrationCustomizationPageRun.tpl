{*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres  (endres@systopia.de)                |
| Author: P. Batroff (batroff@systopia.de)               |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*}

<div id="registration_form_link">
    <a href={$form_link} target="_blank">{$registration_form_link_label}</a>
</div>


<script type="text/javascript">

    var link                                = "{$form_link}";
    var reg_customization_label             = "{$reg_customization_label}";
    var reg_customisation_fields_label      = "{$reg_customisation_fields_label}";

    var reg_customization_label             = "{$registration_customisation_import}";
    var import_link                         = "{$import_form_link}";

    var reg_customization_link_label        = "{$registration_customisation}";

    var event_report_link                   = "{$event_report_link}";
    var event_report_link_label             = "{$event_report_link_label}";

    {literal}

    cj('*').filter(function() {
        return cj(this).text() === reg_customization_label;
    }).closest('table').hide();

    cj("#registration_form_link").wrap('<tr id="registration_form_link_tr"><td colspan="2">');

    cj("#registration_form_link_tr").insertAfter(cj('*').filter(function() {
        return cj(this).text() === reg_customisation_fields_label;
    }).closest('table'));

    // work on menu link list; hide some entries, add registration customisation
    var pattern_1 = /civicrm\/event\/manage\/settings/;
    var pattern_2 = /civicrm\/event\/manage\/reminder/;

    cj(".crm-event-links-list-inner ul li a").each(function(){
        if (!pattern_1.test(cj(this).attr("href")) && !pattern_2.test(cj(this).attr("href"))) {
            cj(this).parent().hide();
        }
    });

    var link_entry = '<li><a href="' + link + '" >' + reg_customization_link_label + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(link_entry);

    var link_import_entry = '<li><a class="crm-popup" href="' + import_link + '" >' + reg_customization_label + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(link_import_entry);

    // #6330 Event Report Link
    var event_report_entry = '<li><a class="crm-popup" href="' + event_report_link + '" >' + event_report_link_label + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry);


</script>
{/literal}