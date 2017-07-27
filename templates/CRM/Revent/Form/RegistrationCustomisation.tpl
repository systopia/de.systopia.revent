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

{foreach from=$groups item=group}
    <div class="crm-accordion-wrapper crm-accordion_title-accordion crm-accordion-processed"
         id="registration_customisation">
        <div class="crm-accordion-header">
            {$group.title}
        </div>
        <div class="crm-accordion-body">
            {foreach from=$group.fields item=field}
                <div class="crm-block crm-form-block" style="margin-left: 4px; margin-right: 4px">
                    <table class="form-layout-compressed">
                        <tr>
                            <td><i><b>{$field.title}</b></i></td>
                        </tr>
                        {assign var="option_condition" value="no"}
                        {foreach from=$field.languages item=language}
                        <tr class="line_{$group.name}_{$field.name}_{$language}">
                            <td>
                                <div class="crm-section">
                                    {* title form element*}
                                    {capture assign=title}title__{$group.name}__{$field.name}__{$language}{/capture}
                                    Titel <i>({$language})</i>
                                    {$form.$title.html}
                                </div>
                            </td>
                            <td>
                                <div class="crm-section">
                                    {* description form element*}
                                    {capture assign=description}description__{$group.name}__{$field.name}__{$language}{/capture}
                                    Beschreibung <i>({$language})</i>
                                    {$form.$description.html}
                                </div>
                            </td>
                            {if $option_condition eq "no"}
                                {assign var="option_condition" value="yes"}
                                <td>
                                    <div class="crm-section">
                                        {capture assign=required}required__{$group.name}__{$field.name}__{$language}{/capture}
                                        {$form.$required.label} {$form.$required.html}
                                    </div>
                                </td>
                                <td>
                                    <div class="crm-section">
                                        {capture assign=weight}weight__{$group.name}__{$field.name}__{$language}{/capture}
                                        {$form.$weight.label} {$form.$weight.html}
                                    </div>
                                </td>
                            {/if}
                            {/foreach} {* languages *}
                    </table>

                    {* get number of options for this field, then create the form elements for each language*}
                    {if count($field.option_count) gt 0}
                        <tr>
                            <div style="text-indent:80px;">
                                <td><i><b>{$field.title} Options</b></i></td>
                            </div>
                        </tr>
                        <table class="form-layout-compressed" style="text-indent:80px;">
                            {foreach from=$field.option_count item=option}
                                <tr>
                                    {foreach from=$field.languages item=language}
                                        <td>
                                            <div>
                                                {capture assign=opt}option__{$group.name}__{$field.name}__{$option}__{$language}{/capture}
                                                {$form.$opt.html} <i>{$language}</i>
                                            </div>
                                        </td>
                                    {/foreach} {* languages *}
                                </tr>
                            {/foreach} {* options *}
                        </table>
                    {/if}
                </div>
            {/foreach} {* fields *}
        </div>
    </div>
{/foreach} {* groups *}

{* FOOTER *}
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{* hidden vars *}
{$form.eid.html}