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


    var event_report_link_56                         = "{$event_report_link_56}";
    var event_report_link_label_56                   = "{$event_report_link_label_56}";
    var event_report_link_61                         = "{$event_report_link_61}";
    var event_report_link_label_61                   = "{$event_report_link_label_61}";
    var event_report_link_62                         = "{$event_report_link_62}";
    var event_report_link_label_62                   = "{$event_report_link_label_62}";
    var event_report_link_63                         = "{$event_report_link_63}";
    var event_report_link_label_63                   = "{$event_report_link_label_63}";
    var event_report_link_74                         = "{$event_report_link_74}";
    var event_report_link_label_74                   = "{$event_report_link_label_74}";
    var event_report_link_64                         = "{$event_report_link_64}";
    var event_report_link_label_64                   = "{$event_report_link_label_64}";
    var event_report_link_66                         = "{$event_report_link_66}";
    var event_report_link_label_66                   = "{$event_report_link_label_66}";
    var event_report_link_67                         = "{$event_report_link_67}";
    var event_report_link_label_67                   = "{$event_report_link_label_67}";
    var event_report_link_68                         = "{$event_report_link_68}";
    var event_report_link_label_68                   = "{$event_report_link_label_68}";
    var event_report_link_69                         = "{$event_report_link_69}";
    var event_report_link_label_69                   = "{$event_report_link_label_69}";
    var event_report_link_70                         = "{$event_report_link_70}";
    var event_report_link_label_70                   = "{$event_report_link_label_70}";
    var event_report_link_71                         = "{$event_report_link_71}";
    var event_report_link_label_71                   = "{$event_report_link_label_71}";
    var event_report_link_72                         = "{$event_report_link_72}";
    var event_report_link_label_72                   = "{$event_report_link_label_72}";
    var event_report_link_73                         = "{$event_report_link_73}";
    var event_report_link_label_73                   = "{$event_report_link_label_73}";
    var event_report_link_77                         = "{$event_report_link_77}";
    var event_report_link_label_77                   = "{$event_report_link_label_77}";

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
    // var event_report_entry = '<li><a target="_blank" class="no-popup" href="' + event_report_link + '" >' + event_report_link_label + '</a> </li>';
    // cj(".crm-event-links-list-inner ul").append(event_report_entry);

    var event_report_entry_56 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_56 + '" >' + event_report_link_label_56 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_56);
    var event_report_entry_61 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_61 + '" >' + event_report_link_label_61 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_61);
    var event_report_entry_62 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_62 + '" >' + event_report_link_label_62 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_62);
    var event_report_entry_63 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_63 + '" >' + event_report_link_label_63 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_63);
    var event_report_entry_74 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_74 + '" >' + event_report_link_label_74 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_74);
    var event_report_entry_64 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_64 + '" >' + event_report_link_label_64 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_64);
    var event_report_entry_66 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_66 + '" >' + event_report_link_label_66 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_66);
    var event_report_entry_67 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_67 + '" >' + event_report_link_label_67 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_67);
    var event_report_entry_68 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_68 + '" >' + event_report_link_label_68 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_68);
    var event_report_entry_69 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_69 + '" >' + event_report_link_label_69 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_69);
    var event_report_entry_70 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_70 + '" >' + event_report_link_label_70 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_70);
    var event_report_entry_71 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_71 + '" >' + event_report_link_label_71 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_71);
    var event_report_entry_72 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_72 + '" >' + event_report_link_label_72 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_72);
    var event_report_entry_73 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_73 + '" >' + event_report_link_label_73 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_73);
    var event_report_entry_77 = '<li><a target="_blank" class="no-popup" href="' + event_report_link_77 + '" >' + event_report_link_label_77 + '</a> </li>';
    cj(".crm-event-links-list-inner ul").append(event_report_entry_77);

</script>
{/literal}