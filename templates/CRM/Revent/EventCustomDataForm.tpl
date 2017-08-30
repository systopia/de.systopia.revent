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
    // get variables

    var group_ids = {$active_group_ids};

    {literal}

    function revent_custom_data_mods() {
        console.log("group_ids: " + group_ids);

        cj("[id^='custom_group_']").each(function() {
            var pattern = /custom_group_([0-9]*?)_/;
            var custom_group_id = parseInt(pattern.exec(cj(this).attr("id"))[1]);

            if (cj.inArray(custom_group_id, group_ids) === -1) {
                // is not in array of valid custom groups, we shall hide it now
                cj(this).prev().hide();
            }
        })
    }

    cj(document).ready(function () {
        // call adjustment once
        revent_custom_data_mods();

        // inject data dependency
        cj(document).bind("ajaxComplete", revent_custom_data_mods);
    });


</script>
{/literal}