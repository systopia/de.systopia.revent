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

    var custom_id_reg_address = {$registration_address_custom_id};

    var reload_flag = false;
    {literal}

    //////////// Helper ////////////

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
                console.log("DEBUG: " + cj(this).prev());
                cj(this).prev().find(':input').not('button').not('[type="submit"]').val(null).prop('checked', false);
                cj(this).prev().hide();
            }
        })
        reload_flag = true;
    }

    function revent_fill_address_data(address_data, contact_id) {
        CRM.api3('RemoteRegistration', 'get_custom_group_meta_data', {
            "sequential": 1
        }).done(function(result) {
            if (result.is_error === 1) {
                console.log(result.error_message);
                return;
            }
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
        // TODO: Fix call. Somehow ID isn't passed through
        CRM.api3('Contact', 'getorgnamefromcontact', {
            "sequential": 1,
            "contact_id": contact_id
        }).done(function(result) {
            if (result['is_error'] === 0) {
                cj("[id^='"+ custom_id_reg_address +"']").prev().find("[data-crm-custom$='organisation_name_1']").val(result['values']['master_1']);
                cj("[id^='"+ custom_id_reg_address +"']").prev().find("[data-crm-custom$='organisation_name_2']").val(result['values']['master_2']);
            }
        });
    }

    function revent_hide_custom_groups(){
        // if event is already (pre-)chosen, filter groups as well
        var eId = cj("input[name=event_id]").val();
        if (eId === '') {
            // check for popup
            eId = cj("[id^=crm-ajax-dialog-]").find("input[name=event_id]").val()
        }
        if (eId !== '') {
            CRM.api3('RemoteRegistration', 'get_active_groups', {
                "sequential": 1,
                "event_id": eId
            }).done(function(result) {
                if (result.is_error === 1) {
                    console.log(result.error_message);
                    return;
                }
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
                if (result.is_error === 1) {
                    console.log(result.error_message);
                    return;
                }
                revent_fill_address_data(result, contactId);
            });
        }
    }

    cj(document).ready(function () {

        setTimeout(function(){
            revent_initial_address_data();
            revent_hide_custom_groups();
        }, 500);

        // on change method when eventId is chosen
        cj("input[name=event_id]").on("change", function(){
            if (reload_flag) {
                var event_id = cj(this).val();
                var contact_id = cj("#contact_id").val();
                var queryString = location.search;
                var params = new URLSearchParams(queryString.substring(1)); // substring(1) to drop the leading "?"

                params.set('cid', contact_id);
                params.set('eid', event_id);
                // var eid = parseInt(params.get('eid'));
                // var cid = parseInt(params.get('cid'));
                if (contact_id) {
                    params.set('context', 'participant');
                }
                location.href = location.protocol + "//" + location.host + location.pathname + '?' + params.toString();
            }
            // show all groups again
            cj("[id^='custom_group_']").prev().show();
            var eventId = cj(this).val();
            // hide all groups not associated to this event
            CRM.api3('RemoteRegistration', 'get_active_groups', {
                "sequential": 1,
                "event_id": eventId
            }).done(function(result) {
                if (result.is_error === 1) {
                    console.log(result.error_message);
                    return;
                }
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
                    if (result.is_error === 1) {
                        console.log(result.error_message);
                        return;
                    }
                    fill_change = true;
                    revent_fill_address_data(result, contactId);
                });
            }
        })
    });


</script>
{/literal}