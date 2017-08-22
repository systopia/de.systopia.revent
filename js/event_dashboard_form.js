/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
| Author: P. Batroff (batroff@systopia.de)               |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/


var eventIDs = [];
// iterate over all entries and collect Event-IDs
cj(".crm-event-id").each(function(){
    eventIDs.push(cj(this).val("crm-event-id").html());
});

for (i = 0; i < eventIDs.length; ++i) {
    // hide some menu entries
    cj("ul#panel_info_" + eventIDs[i] + " li a").each(function(){
        if (cj(this).attr('title') != "Informationen und Einstellungen" && cj(this).attr('title') != 'Erinnerungen planen') {
            cj(this).hide();
        }
    });

    // add menu entry for
    var url = "__URL__?eid=" + eventIDs[i] + "&reset=1";
    var link_entry = '<li><a title="Registration Customisation" clas="action-item crm-hover-button no-popup enabled" href="' + url + '" >Registration Customisation</a> </li>'
    cj("ul#panel_info_" + eventIDs[i]).append(link_entry);
}