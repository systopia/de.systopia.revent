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


{* TODO: get link and custom group name
    --> hide Registration Customisation td in table
        e.g. [custom_field-row custom_19_4-row]
    --> get name variable
    --> add link (probably in table)
*}

<div id="registration_form_link">
    <a href={$form_link} target="_blank">registration customisation edit</a>
</div>


<script type="text/javascript">
    // get variables
    var customizations_field = "{$registration_customisation_field}";
    var group_selector = "{$registration_fields}";

    {literal}

    cj("label[for^='" + customizations_field + "']").parent().parent().hide();
    cj("#registration_form_link").wrap('<tr id="registration_form_link_tr"><td colspan="2">');
    cj("#registration_form_link_tr").insertAfter(cj("label[for^='" + group_selector +"']").parent().parent());


//    cj(":contains('Registration Customisations')").last().closest('table')
</script>
{/literal}
