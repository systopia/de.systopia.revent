{*-------------------------------------------------------+*}
{*| SYSTOPIA REMOTE EVENT REGISTRATION                     |*}
{*| Copyright (C) 2017 SYSTOPIA                            |*}
{*| Author: B. Endres  (endres@systopia.de)                |*}
{*| Author: P. Batroff (batroff@systopia.de)               |*}
{*+--------------------------------------------------------+*}
{*| This program is released as free software under the    |*}
{*| Affero GPL license. You can redistribute it and/or     |*}
{*| modify it under the terms of this license which you    |*}
{*| can read by viewing the included agpl.txt or online    |*}
{*| at www.gnu.org/licenses/agpl.html. Removal of this     |*}
{*| copyright header is strictly prohibited without        |*}
{*| written permission from the original author(s).        |*}
{*+-------------------------------------------------------*}

{*<script type="text/javascript">*}
    {*// get variables*}

    {*var group_ids = {$active_group_ids};*}

    {*{literal}*}

    {*function revent_custom_data_mods() {*}
        {*cj("[id^='custom_group_']").each(function() {*}
            {*var pattern = /custom_group_([0-9]*?)_/;*}
            {*var custom_group_id = parseInt(pattern.exec(cj(this).attr("id"))[1]);*}

            {*if (cj.inArray(custom_group_id, group_ids) === -1) {*}
                {*// is not in array of valid custom groups, we shall hide it now*}
                {*cj(this).prev().hide();*}
            {*}*}
        {*})*}
    {*}*}

    {*cj(document).ready(function () {*}
        {*// call adjustment once*}
        {*revent_custom_data_mods();*}

        {*// inject data dependency*}
        {*cj(document).bind("ajaxComplete", revent_custom_data_mods);*}
    {*});*}


{*</script>*}
{*{/literal}*}

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



    {literal}

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

    cj(document).ready(function () {

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
    });

</script>
{/literal}