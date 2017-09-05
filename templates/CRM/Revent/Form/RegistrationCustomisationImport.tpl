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

{capture assign=introduction}{$introduction}{/capture}
{capture assign=warning}{$warning}{/capture}

<p>{$introduction}</p>
<p><span style="color:#FF0000;"><strong>{$warning}</strong></span></p>

<p>
    <div class="crm-section">
        {$form.events.label}
        {$form.events.html}
    </div>
</p>

{* FOOTER *}
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{* hidden vars *}
{$form.eid.html}
