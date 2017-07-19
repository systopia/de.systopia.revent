{*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*}

{foreach from=$line_numbers item=line_number_group}
    <table>
        {capture assign=field_lines}line_number_{$line_number_group}{/capture}
        {foreach from=$field_lines item=line_number_field}
            {capture assign=language_lines}line_number_{$line_number_group}_{$line_number_field}{/capture}
            {foreach from=$language_lines item=line_number_language}

                {capture assign=title_field}title_{$line_number}_{$line_number_group}_{$line_number_language}{/capture}
                {capture assign=description_field}description_{$line_number}_{$line_number_group}_{$line_number_language}{$line_number}{/capture}
                {capture assign=required_field}required_{$line_number}_{$line_number_group}_{$line_number_language}{/capture}
                {capture assign=weight_field}weight_{$line_number}_{$line_number_group}_{$line_number_language}{/capture}

                <tr class="line-{$line_number_group}-{$line_number_group}-{$line_number_language}">
                    <td>
                        <div class="crm-section">
                            {$form.$title_field.html}
                        </div>
                    </td>
                    <td>
                        <div class="crm-section">
                            {$form.$description_field.html}
                        </div>
                    </td>
                    <td>
                        <div class="crm-section">
                            {$form.$required_field.html}
                        </div>
                    </td>
                    <td>
                        <div class="crm-section">
                            {$form.$weight_field.html}
                        </div>
                    </td>
                </tr>
            {/foreach}
        {/foreach}
    </table>
{/foreach}


{* FOOTER *}
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{* hidden vars *}
{$form.eid.html}