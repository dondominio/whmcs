<p>{$LANG.transfer_info}</p>

<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view_transfer}">
    <table width='100%' border='0' cellpadding='3' cellspacing='0'>
        <tbody>
            <tr>
                <td width='50%' align='left'>
                    {$PAGINATION.total} {$LANG.pagination_results_found}, {$LANG.pagination_page} {$PAGINATION.page} {$LANG.pagination_of} {$PAGINATION.total_pages}
                </td>

                <td width='50%' align='right'>
                    {$LANG.pagination_go_to}
                    <select name='page' onchange='submit()'>
                        {html_options options=$pagination_select selected=$PAGINATION.page}
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
    <input type="hidden" name="__a__" value="{$actions.transfer_domains}">
    <table class='datatable' width='100%' border='0' cellspacing='1' cellpadding='3'>
        <thead>
            <tr>
                <th width='1'>
                    <input class='domains_check_all' type='checkbox' />
                </th>

                <th>
                    {$LANG.transfer_domain}
                </th>

                <th width='100'>
                    {$LANG.transfer_authcode}
                </th>
            </tr>
        </thead>

        <tbody>
        {if count($domains) gt 0}
            {foreach from=$domains item=$domain name=domains_list}
            <tr>
                <td>
                    <input class='domain_checkbox' name='domain_checkbox[{$smarty.foreach.domains_list.index}]' value="{$domain.id}" type='checkbox' />
                </td>

                <td>
                    {$domain.domain}
                </td>

                <td>
                    <input type='text' name='authcode[{$smarty.foreach.domains_list.index}]' value='' width='100%' />
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
                    {$LANG.transfer_domain}
                </th>

                <th>
                    {$LANG.transfer_authcode}
                </th>
            </tr>
        </tfoot>
    </table>

    <br />

    {$LANG.info_with_selected} <button type='submit' name='form_action' value='transfer' class='btn'>{$LANG.btn_transfer}</button>
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
    $('.domains_check_all').bind('change', function(e) {
        $('.domain_checkbox').prop('checked', $(this).prop('checked'));
        $('.domains_check_all').prop('checked', $(this).prop('checked'));
    });
</script>