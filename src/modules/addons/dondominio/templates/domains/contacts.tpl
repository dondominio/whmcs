<div id='tab0box' class='tabbox'>
    <div id='tab_content'>
        <form action='' method='get'>
            <input type="hidden" name="module" value="{$module_name}">
            <input type="hidden" name="__c__" value="{$__c__}">
            <input type="hidden" name="__a__" value="{$actions.view_contacts}">
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td width='15%' class='fieldlabel'>
                            {$LANG.contact_name}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='name' size='30' value='{$filters.name}'>
                        </td>

                        <td width='15%' class='fieldlabel'>
                            {$LANG.contact_email}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='email' size='30' value='{$filters.email}'>
                        </td>
                    </tr>
                    <tr>
                        <td width='15%' class='fieldlabel'>
                            {$LANG.contact_verification}
                        </td>

                        <td class='fieldarea'>
                            <select name='verification' id='tldDropDown'>
                                <option value=''>{$LANG.filter_any}</option>
                                {html_options options=$options.verification selected=$filters.verification}
                            </select>
                        </td>

                        <td width='15%' class='fieldlabel'>
                            {$LANG.contact_daaccepted}
                        </td>

                        <td class='fieldarea'>
                            <select name='daaccepted' id='tldDropDown'>
                                <option value=''>{$LANG.filter_any}</option>
                                {html_options options=$options.daaccepted selected=$filters.daaccepted}
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p align='center'>
                <input type='submit' id='search-clients' value='{$LANG.filter_search}' class='button'>
            </p>
        </form>
    </div>
</div>

<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view_contacts}">
    <input type='hidden' name='name' size='30' value='{$filters.name}'>
    <input type='hidden' name='email' size='30' value='{$filters.email}'>
    <input type='hidden' name='verification' size='30' value='{$filters.verification}'>
    <input type='hidden' name='daaccepted' size='30' value='{$filters.daaccepted}'>
    <table width='100%' border='0' cellpadding='3' cellspacing='0'>
        <tbody>
            <tr>
                <td width='50%' align='left'>
                    {$pagination.total} {$LANG.pagination_results_found}, {$LANG.pagination_page} {$pagination.page} {$LANG.pagination_of} {$pagination.total_pages}
                </td>

                <td width='50%' align='right'>
                    {$LANG.pagination_go_to}
                    <select name='page' onchange='submit()'>
                        {html_options options=$pagination_select selected=$pagination.page}
                    </select>

                    <input type='submit' value='{$LANG.pagination_go}' class='btn-small'>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<table style="table-layout: fixed;" class='datatable' width='100%' border='0' cellspacing='1' cellpadding='3'>
    <thead>
        <tr>
            <th>
                {$LANG.contact_id}
            </th>

            <th>
                {$LANG.contact_type}
            </th>

            <th>
                {$LANG.contact_name}
            </th>

            <th>
                {$LANG.contact_email}
            </th>

            <th>
                {$LANG.contact_country}
            </th>

            <th>
                {$LANG.contact_verification}
            </th>

            <th>
                {$LANG.contact_daaccepted}
            </th>
        </tr>
    </thead>
    <tbody>
        {if count($contacts) gt 0}
        {foreach $contacts item=contact}
        <tr>
            <td>
                <a href="{$links.contact}&contact_id={$contact.contactID}">
                    {$contact.contactID}
                </a>
            </td>
            <td>
                {if $contact.contactType eq 'organization'}
                    {$LANG.contact_type_organization}
                {/if}
                {if $contact.contactType eq 'individual'}
                    {$LANG.contact_type_individual}
                {/if}
            </td>
            <td>
                {$contact.contactName}
            </td>
            <td>
                {$contact.email}
            </td>
            <td>
                {$contact.country}
            </td>
            <td>
                {if $contact.verificationstatus eq 'verified'}
                    {$LANG.contact_ver_verified}
                {/if}
                {if $contact.verificationstatus eq 'inprocess'}
                    {$LANG.contact_ver_inprocess}
                {/if}
                {if $contact.verificationstatus eq 'failed'}
                    {$LANG.contact_ver_failed}
                {/if}
                {if $contact.verificationstatus eq 'notapplicable'}
                    {$LANG.contact_ver_notapplicable}
                {/if}
            </td>
            <td>
                {if $contact.daaccepted eq true}
                    {$LANG.contact_da_accepted}
                {else}
                    {$LANG.contact_da_no_accepted}
                {/if}
            </td>
        </tr>
        {/foreach}
        {else}
        <tr>
            <td colspan='5'>
                {$LANG.info_no_results}
            </td>
        </tr>
        {/if}

    </tbody>
    <tfoot>
        <tr>
            <th>
                {$LANG.contact_id}
            </th>

            <th>
                {$LANG.contact_type}
            </th>

            <th>
                {$LANG.contact_name}
            </th>

            <th>
                {$LANG.contact_email}
            </th>

            <th>
                {$LANG.contact_country}
            </th>

            <th>
                {$LANG.contact_verification}
            </th>

            <th>
                {$LANG.contact_daaccepted}
            </th>
        </tr>
    </tfoot>
</table>

<p align='center'>
    {if $pagination.page gt 1}
        <a href='{$links.prev_page}'>« {$LANG.pagination_previous}</a>
    {else}
        « {$LANG.pagination_previous}
    {/if}

    &nbsp;
    {if $pagination.page lt $pagination.total_pages}
        <a href='{$links.next_page}'>{$LANG.pagination_next} »</a>
    {else}
        {$LANG.pagination_next} »
    {/if}
</p>