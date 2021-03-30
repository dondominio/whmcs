<h2>{$LANG.deleted_domains_title}</h2>

<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view_deleted}">
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

<table class='datatable' width='100%' border='0' cellspacing='1' cellpadding='3'>
    <thead>
        <tr>
            <th>
                {$LANG.domains_domain}
            </th>

            <th>
                {$LANG.tld_tld}
            </th>

            <th>
                {$LANG.deleted_domains_ts}
            </th>

            <th>
                {$LANG.deleted_domains_info}
            </th>
        </tr>
    </thead>
    <tbody>

        {if count($domains) gt 0}
        {foreach $domains item=domain}
        <tr>
            <td>
                {$domain.name}
            </td>

            <td>
                {$domain.tld}
            </td>

            <td>
                {$domain.tsDeleted}
            </td>

            <td>
                {$domain.info}
            </td>
        </tr>
        {/foreach}
        {else}
        <tr>
            <td colspan='4'>
                {$LANG.info_no_results}
            </td>
        </tr>
        {/if}

    </tbody>
    <tfoot>
        <tr>
            <th>
                {$LANG.domains_domain}
            </th>

            <th>
                {$LANG.tld_tld}
            </th>

            <th>
                {$LANG.deleted_domains_ts}
            </th>

            <th>
                {$LANG.deleted_domains_info}
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