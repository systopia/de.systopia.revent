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



{foreach from=$groups item=group}
    <p><strong>{$group.name}</strong></p>
    <table>
    {foreach from=$group.fields item=field}
        <tr>
            <td><p><i><b>{$field.name}</b></i></p></td>
        </tr>
        <tr>
            <td><b>default</b></td>
        </tr>
        <tr class="line_{$group.name}_{$field.name}_0">
            <td>
                <div class="crm-section">
                    {* title form element*}
                    {capture assign=title}title__{$group.name}__{$field.name}__0{/capture}
                    {$form.$title.label}
                    {$form.$title.html}
                </div>
            </td>
            <td>
                <div class="crm-section">
                    {* description form element*}
                    {capture assign=description}description__{$group.name}__{$field.name}__0{/capture}
                    {$form.$description.label}
                    {$form.$description.html}
                </div>
            </td>
            <td>
                <div class="crm-section">
                    {* required form element*}
                    {capture assign=required}required__{$group.name}__{$field.name}__0{/capture}
                    {$form.$required.label}
                    {$form.$required.html}
                </div>
            </td>
            <td>
                <div class="crm-section">
                    {* weight form element*}
                    {capture assign=weight}weight__{$group.name}__{$field.name}__0{/capture}
                    {$form.$weight.label}
                    {$form.$weight.html}
                </div>
            </td>
        </tr>
        {foreach from=$field.languages item=language}
            <tr>
                <td><b>{$language}</b></td>
            </tr>
            <tr class="line_{$group.name}_{$field.name}_{$language}">
                <td>
                    <div class="crm-section">
                        {* title form element*}
                        {capture assign=title}title__{$group.name}__{$field.name}__{$language}{/capture}
                        {$form.$title.label}
                        {$form.$title.html}
                    </div>
                </td>
                <td>
                    <div class="crm-section">
                        {* description form element*}
                        {capture assign=description}description__{$group.name}__{$field.name}__{$language}{/capture}
                        {$form.$description.label}
                        {$form.$description.html}
                    </div>
                </td>
                <td>
                    <div class="crm-section">
                        {* required form element*}
                        {capture assign=required}required__{$group.name}__{$field.name}__{$language}{/capture}
                        {$form.$required.label}
                        {$form.$required.html}
                    </div>
                </td>
                <td>
                    <div class="crm-section">
                        {* weight form element*}
                        {capture assign=weight}weight__{$group.name}__{$field.name}__{$language}{/capture}
                        {$form.$weight.label}
                        {$form.$weight.html}
                    </div>
                </td>
            </tr>
        {/foreach} {* languages *}
    {/foreach} {* fields *}
    </table>
{/foreach} {* groups *}

{* FOOTER *}
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{* hidden vars *}
{$form.eid.html}