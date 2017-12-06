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

<script type="text/javascript">
    var initial_contact_id = {$form_contact_id};
    var registration_address_custom_id = '0';


    {literal}

    //////////// Helper ////////////
    function revent_get_custom_group_registration_address_id() {
        CRM.api3('CustomGroup', 'getsingle', {
            "sequential": 1,
            "title": "Registration Address"
        }).done(function(result) {
            // do something
            registration_address_custom_id = parseInt(result['id']);
        });
    }

    function revent_find_in_array(value, search_array) {
        for (key in search_array) {
            if (key === value) {
                return true;
            }
        }
        return false;
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function revent_custom_data_mods(event_custom_group_ids) {
        cj("[id^='custom_group_']").each(function() {
            var pattern = /custom_group_([0-9]*?)_/;
            var custom_group_id = parseInt(pattern.exec(cj(this).attr("id"))[1]);
            if (cj.inArray(custom_group_id, event_custom_group_ids) === -1) {
                // is not in array of valid custom groups, we shall hide it now
                cj(this).prev().hide();
            }
        })
    }

    function revent_fill_address_data(address_data) {
        CRM.api3('CustomField', 'get', {
            "sequential": 1,
            "custom_group_id": "registration_address"
        }).done(function(result) {
            for (i=0; i < result.values.length; i++) {
                if (revent_find_in_array(result.values[i]['name'], address_data)) {
                    if (result.values[i]['name'] === "country_id") {
                        var indexString_country = "select[name^=custom_" + result.values[i]['id'] + "]";
                        cj(indexString_country).val(address_data[result.values[i]['name']]).trigger('change');
                    }
                    var indexString = "input#custom_" + result.values[i]['id'] + "_-1";
                    cj(indexString).val(address_data[result.values[i]['name']]);
                }
            }
        });
    }

    function revent_hide_custom_groups(){
        // if event is already (pre-)chosen, filter groups as well
        var eId = cj("input[name=event_id]").val();
        if (eId !== '') {
            CRM.api3('RemoteRegistration', 'get_active_groups', {
                "sequential": 1,
                "event_id": eId
            }).done(function(result) {
                revent_custom_data_mods(result.values);
            });
        }
    }

    function revent_initial_address_data() {
        var contactId = cj("input[name=contact_id]").val();
        if (initial_contact_id !== 0) {
            contactId = initial_contact_id;
        }
        if (contactId !== '') {
            CRM.api3('Address', 'getsingle', {
                "sequential": 1,
                "contact_id": contactId,
                "is_primary": 1
            }).done(function (result) {
                revent_fill_address_data(result);
            });
        }
    }

    cj(document).ready(function () {
        // call adjustment once
        revent_get_custom_group_registration_address_id();
        revent_initial_address_data();
        revent_hide_custom_groups();
        // TODO: if this doesn't work, we must use a timeout here for the initial hide action
        // ajax complete doesn't work, b/c API-call ends in ajaxcomplete event, thus endless loop
        // cj(document).bind("ajaxComplete", revent_initial_address_data);
        // cj(document).bind("ajaxComplete", revent_hide_custom_groups);

        // on change method when eventId is chosen
        cj("input[name=event_id]").on("change", function(){
            // show all groups again
            cj("[id^='custom_group_']").prev().show();
            var eventId = cj(this).val();
            // hide all groups not associated to this event
            CRM.api3('RemoteRegistration', 'get_active_groups', {
                "sequential": 1,
                "event_id": eventId
            }).done(function(result) {
                revent_custom_data_mods(result.values);
            });
        });

        cj("input[name=contact_id]").on("change", function(){
            var contactId = cj(this).val();
            if (contactId !== '') {
                CRM.api3('Address', 'getsingle', {
                    "sequential": 1,
                    "contact_id": contactId,
                    "is_primary": 1
                }).done(function (result) {
                    fill_change = true;
                    revent_fill_address_data(result);
                });
            }
        })
    });


</script>
{/literal}