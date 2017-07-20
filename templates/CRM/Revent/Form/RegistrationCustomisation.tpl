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
    <table>
    {foreach from=$group.fields item=field}
        <tr class="line_{$group}_{$field}_0">
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
            <tr class="line_{$group}_{$field}_{$language}">
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

{*old stuff ...
{foreach from=$line_numbers item=line_number_group}
    <table>
        {capture assign=field_lines}line_numbers_{$line_number_group}{/capture}
        <p> Field lines: {$field_lines} , line Number: {$line_number_group}</p>
        {foreach from=$$field_lines item=line_number_field}
            <p> line_number Field : {$line_number_field} </p>
            {capture assign=language_lines}{$field_lines}_{$line_number_field}{/capture}
            <p> language lines : {$language_lines} </p>
            {foreach from=$language_lines item=line_number_language}

                {capture assign=title_field}title_{$line_number}_{$line_number_group}_{$line_number_language}{/capture}
                {capture assign=description_field}description_{$line_number}_{$line_number_group}_{$line_number_language}{$line_number}{/capture}
                {capture assign=required_field}required_{$line_number}_{$line_number_group}_{$line_number_language}{/capture}
                {capture assign=weight_field}weight_{$line_number}_{$line_number_group}_{$line_number_language}{/capture}

                <tr class="line-{$line_number_group}-field_{$line_number_field}-language_{$line_number_language}">
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
*}

{* FOOTER *}
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{* hidden vars *}
{$form.eid.html}