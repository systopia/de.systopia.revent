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

    var group_names = {$active_group_ids};

{literal}

    function revent_participant_view_mods() {
        cj(".no-border").each(function() {
            // hide all groups first
            cj(this).hide();
        })
        for (var i = 0; i < group_names.length; i++) {
            // make active groups visible
            var tmp_selector = "[id^='" + group_names[i] + "']";
            cj(tmp_selector).parent().parent().parent().show();
        }

    }


    cj(document).ready(function () {
        // call adjustment once
        revent_participant_view_mods();

        // inject data dependency
        cj(document).bind("ajaxComplete", revent_participant_view_mods);
    });

</script>
{/literal}