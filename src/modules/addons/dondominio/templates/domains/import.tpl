<p>{$LANG.import_info}</p>

<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view_import}">
    <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
        <tbody>
            <tr>
                <td width='15%' class='fieldlabel'>
                    {$LANG.filter_domain}
                </td>

                <td class='fieldarea'>
                    <input type='text' name='domain' size='30' value='{$filters.domain}'>
                </td>

                <td width='15%' class='fieldlabel'>
                    {$LANG.filter_tld}
                </td>

                <td class='fieldarea'>
                    <input type='text' name='tld' size='30' value='{$filters.tld}'>
                </td>
            </tr>
        </tbody>
    </table>

    <p align='center'>
        <input type='submit' id='search-clients' value='{$LANG.filter_search}' class='button'>
    </p>
</form>


<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view_import}">
    <table width='100%' border='0' cellpadding='3' cellspacing='0'>
        <tbody>
            <tr>
                <td width='50%' align='left'>
                    {$pagination.total} {$LANG.pagination_results_found}, {$LANG.pagination_page} {$pagination.page}
                    {$LANG.pagination_of} {$pagination.total_pages}
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

<form action='' method='post'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.import_domains}">
    <table class='datatable' width='100%' border='0' cellspacing='1' cellpadding='3'>
        <thead>
            <tr>
                <th width='1'>
                    <input class='domains_check_all' type='checkbox' />
                </th>

                <th>
                    {$LANG.domains_domain}
                </th>

                <th width='100'>
                    {$LANG.domains_status}
                </th>
            </tr>
        </thead>
        <tbody>
            {if count($domains) gt 0}
            {foreach $domains item=domain}
            <tr>
                <td>
                    <input class='domain_checkbox' name='domain_checkbox[]' value='{$domain.name}' type='checkbox' />
                </td>

                <td>
                    {$domain.name}
                </td>

                <td>
                    {if $domain.domain_found}
                    <div style='text-align: center;' class='label active'>{$LANG.import_imported}</div>
                    {else}
                    <div style='text-align: center;' class='label cancelled'>{$LANG.import_not_imported}</div>
                    {/if}
                </td>
            </tr>
            {/foreach}
            {else}
            <tr>
                <td colspan='2'>
                    {$LANG.info_no_results}
                </td>
            </tr>
            {/if}

        </tbody>
        <tfoot>
            <tr>
                <th width='1'>
                    <input class='domains_check_all' type='checkbox' />
                </th>

                <th>
                    {$LANG.domains_domain}
                </th>

                <th width='100'>
                    {$LANG.domains_status}
                </th>
            </tr>
        </tfoot>
    </table>

    <br />
    {$LANG.info_with_selected}

    <select name='customer' id='import_customer'>
        {foreach $customers item=customer}
        <option value="{$customer->id}">{$customer->firstname} {$customer->lastname}</option>
        {/foreach}
    </select>

    <button id='import_import' name='form_action' value='import' class='btn'>{$LANG.import_btn_import}</button>
</form>

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

<script type='text/javascript'>
    $('.domains_check_all').bind('change', function (e) {
        $('.domain_checkbox').prop('checked', $(this).prop('checked'));
        $('.domains_check_all').prop('checked', $(this).prop('checked'));
    });
</script>