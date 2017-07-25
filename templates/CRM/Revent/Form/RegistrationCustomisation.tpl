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
    <div class="crm-accordion-wrapper crm-accordion_title-accordion crm-accordion-processed"
         id="registration_customisation">
        <div class="crm-accordion-header">
            {$group.name}
        </div>
        <div class="crm-accordion-body">
            <table class="form-layout-compressed">
                {foreach from=$group.fields item=field}
                    <tr>
                        <td><i><b>{$field.name}</b></i></td>
                    </tr>
                    {assign var="option_condition" value="no"}
                    {foreach from=$field.languages item=language}
                        <tr class="line_{$group.name}_{$field.name}_{$language}">
                            <td>
                                <div class="crm-section">
                                    {* title form element*}
                                    {capture assign=title}title__{$group.name}__{$field.name}__{$language}{/capture}
                                    {$form.$title.html} {$language}
                                </div>
                            </td>
                            <td>
                                <div class="crm-section">
                                    {* description form element*}
                                    {capture assign=description}description__{$group.name}__{$field.name}__{$language}{/capture}
                                    {$form.$description.html} {$language}
                                </div>
                            </td>
                            {if $option_condition eq "no"}
                                {assign var="option_condition" value="yes"}
                                <td>
                                    <div class="crm-section">
                                        {* required form element*}
                                        {capture assign=required}required__{$group.name}__{$field.name}__{$language}{/capture}
                                        {$form.$required.label} {$form.$required.html}
                                    </div>
                                </td>
                                <td>
                                    <div class="crm-section">
                                        {* weight form element*}
                                        {capture assign=weight}weight__{$group.name}__{$field.name}__{$language}{/capture}
                                        {$form.$weight.label} {$form.$weight.html}
                                    </div>
                                </td>
                            {/if}
                        </tr>
                    {/foreach} {* languages *}

                    {* get number of options for this field, then create the form elements for each language*}
                    <td><i><b>{$field.name} Options</b></i></td>
                    {foreach from=$field.option_count item=option}
                        <table class="form-layout-compressed">
                            <tr>
                                {foreach from=$field.languages item=language}
                                    <td>
                                        <div class="crm-section">
                                            {capture assign=opt}option__{$group.name}__{$field.name}__{$option}__{$language}{/capture}
                                            {$form.$opt.html} {$language}
                                        </div>
                                    </td>
                                {/foreach} {* languages *}
                            </tr>
                        </table>
                    {/foreach} {* options *}
                {/foreach} {* fields *}
            </table>
        </div>
    </div>
{/foreach} {* groups *}

{* FOOTER *}
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{* hidden vars *}
{$form.eid.html}