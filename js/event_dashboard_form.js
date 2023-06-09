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

var pattern_1 = /civicrm\/event\/manage\/settings/;
var pattern_2 = /civicrm\/event\/manage\/reminder/;
// noinspection JSAnnotator
for (i = 0; i < eventIDs.length; ++i) {
    // hide some menu entries
    cj("ul#panel_info_" + eventIDs[i] + " li a").each(function(){
        if (!pattern_1.test(cj(this).attr("href")) && !pattern_2.test(cj(this).attr("href"))) {
            cj(this).hide();
        }
    });
    // add menu entry for
    var url = "__URL__?eid=" + eventIDs[i] + "&reset=1";
    var link_entry = '<li><a title="__Registration-Customisation__" class="action-item crm-hover-button no-popup enabled" href="' + url + '" >__Registration-Customisation__</a> </li>'

    var import_url = "__URL-import__?eid=" + eventIDs[i] + "&reset=1";
    var import_link_entry = '<li><a title="__Registration-Customisation-Import__" class="action-item crm-hover-button crm-popup enabled" href="' + import_url + '" >__Registration-Customisation-import__</a> </li>'



    cj("ul#panel_info_" + eventIDs[i]).append(link_entry);
    cj("ul#panel_info_" + eventIDs[i]).append(import_link_entry);

    // #6330 Event Report Link
    var event_report_url56 = "__REPORT-URL-56__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry56 = '<li><a target="_blank" title="__REPORT-URL-LABEL-56__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url56 + '" >__REPORT-URL-LABEL-56__</a> </li>'

    var event_report_url61 = "__REPORT-URL-61__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry61= '<li><a target="_blank" title="__REPORT-URL-LABEL-61__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url61 + '" >__REPORT-URL-LABEL-61__</a> </li>'

    var event_report_url62 = "__REPORT-URL-62__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry62 = '<li><a target="_blank" title="__REPORT-URL-LABEL-62__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url62 + '" >__REPORT-URL-LABEL-62__</a> </li>'

    var event_report_url63 = "__REPORT-URL-63__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry63 = '<li><a target="_blank" title="__REPORT-URL-LABEL-63__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url63 + '" >__REPORT-URL-LABEL-63__</a> </li>'

    var event_report_url74 = "__REPORT-URL-74__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry74 = '<li><a target="_blank" title="__REPORT-URL-LABEL-74__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url74 + '" >__REPORT-URL-LABEL-74__</a> </li>'

    var event_report_url64 = "__REPORT-URL-64__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry64 = '<li><a target="_blank" title="__REPORT-URL-LABEL-64__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url64 + '" >__REPORT-URL-LABEL-64__</a> </li>'

    var event_report_url66 = "__REPORT-URL-66__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry66 = '<li><a target="_blank" title="__REPORT-URL-LABEL-66__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url66 + '" >__REPORT-URL-LABEL-66__</a> </li>'

    var event_report_url67 = "__REPORT-URL-67__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry67 = '<li><a target="_blank" title="__REPORT-URL-LABEL-67__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url67 + '" >__REPORT-URL-LABEL-67__</a> </li>'

    var event_report_url68 = "__REPORT-URL-68__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry68 = '<li><a target="_blank" title="__REPORT-URL-LABEL-68__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url68 + '" >__REPORT-URL-LABEL-68__</a> </li>'

    var event_report_url69 = "__REPORT-URL-69__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry69 = '<li><a target="_blank" title="__REPORT-URL-LABEL-69__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url69 + '" >__REPORT-URL-LABEL-69__</a> </li>'

    var event_report_url70 = "__REPORT-URL-70__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry70 = '<li><a target="_blank" title="__REPORT-URL-LABEL-70__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url70 + '" >__REPORT-URL-LABEL-70__</a> </li>'

    var event_report_url71 = "__REPORT-URL-71__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry71 = '<li><a target="_blank" title="__REPORT-URL-LABEL-71__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url71 + '" >__REPORT-URL-LABEL-71__</a> </li>'

    var event_report_url72 = "__REPORT-URL-72__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry72 = '<li><a target="_blank" title="__REPORT-URL-LABEL-72__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url72 + '" >__REPORT-URL-LABEL-72__</a> </li>'

    var event_report_url73 = "__REPORT-URL-73__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry73 = '<li><a target="_blank" title="__REPORT-URL-LABEL-73__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url73 + '" >__REPORT-URL-LABEL-73__</a> </li>'

    var event_report_url77 = "__REPORT-URL-77__?reset=1&force=1&event_id_op=in&event_id_value=" + eventIDs[i];
    var event_report_link_entry77 = '<li><a target="_blank" title="__REPORT-URL-LABEL-77__" class="action-item crm-hover-button no-popup enabled" href="' + event_report_url77 + '" >__REPORT-URL-LABEL-77__</a> </li>'


    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry56);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry61);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry62);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry63);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry74);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry64);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry66);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry67);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry68);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry69);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry70);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry71);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry72);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry73);
    cj("ul#panel_info_" + eventIDs[i]).append(event_report_link_entry77);
}