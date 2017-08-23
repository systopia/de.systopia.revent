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
    <a href={$form_link} target="_blank">registration customisation edit</a>
</div>


<script type="text/javascript">

    var link = "{$form_link}";
    {literal}

    cj('*').filter(function() {
        return cj(this).text() === 'Registration Customisations';
    }).closest('table').hide();

    cj("#registration_form_link").wrap('<tr id="registration_form_link_tr"><td colspan="2">');

    cj("#registration_form_link_tr").insertAfter(cj('*').filter(function() {
        return cj(this).text() === 'Registration Fields';
    }).closest('table'));

    // work on menu link list; hide some entries, add registration customisation
    cj(".crm-event-links-list-inner ul li a").each(function(){
        if ( cj(this).html() != "Informationen und Einstellungen" && cj(this).html() != 'Erinnerungen planen') {
            cj(this).parent().hide();
        }
    });

    var link_entry = '<li><a href="' + link + '" >Registration Customisation</a> </li>'
    cj(".crm-event-links-list-inner ul").append(link_entry);


</script>
{/literal}