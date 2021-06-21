<p>{$LANG.tld_new_info}</p>


<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view_index}">
    <div id='tab0box' class='tabbox'>
        <div id='tab_content'>
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td width='15%' class='fieldlabel'>
                            <label for='product_name'>{$LANG.ssl_label_product_name}</label>
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='product_name' value='{$filters.product_name}' />
                        </td>

                        <td width='15%' class='fieldlabel'>
                            <label for='product_name'>{$LANG.ssl_label_product_multi_domain}</label>
                        </td>

                        <td class='fieldarea'>
                            <input type='checkbox' name='product_multi_domain' value='1' {if $filters.product_multi_domain}checked{/if} />
                        </td>

                        <td width='15%' class='fieldlabel'>
                            <label for='product_name'>{$LANG.ssl_label_product_wildcard}</label>
                        </td>

                        <td class='fieldarea'>
                            <input type='checkbox' name='product_wildcard'  value='1' {if $filters.product_wildcard}checked{/if} />
                        </td>

                        <td width='15%' class='fieldlabel'>
                            <label for='product_name'>{$LANG.ssl_label_product_trial}</label>
                        </td>

                        <td class='fieldarea'>
                            <input type='checkbox' name='product_trial'  value='1' {if $filters.product_trial}checked{/if} />
                        </td>
                    </tr>
                </tbody>
            </table>

            <p align='center'>
                <input type='submit' id='search-clients' value='{$LANG.filter_search}' class='button'>
            </p>
        </div>
    </div>
</form>

<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view}">
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

<table class='datatable' width='100%' border='0' cellspacing='1' cellpadding='3'>
    <thead>
        <tr>
            <th>
                {$LANG.ssl_product_id}
            </th>

            <th>
                {$LANG.ssl_product_name}
            </th>

            <th>
                {$LANG.ssl_product_price_create}
            </th>

            <th>
                {$LANG.ssl_product_price_renew}
            </th>
        </tr>
    </thead>

    <tbody>
        {foreach $products item=product}
        <tr>
            <td>
                {$product.dd_product_id}
            </td>

            <td>
                {$product.product_name} &nbsp;
            </td>

            <td>
                {$product.price_create}
            </td>

            <td>
                {$product.price_renew}
            </td>

        </tr>
        {/foreach}
    </tbody>

    <tfoot>
        <tr>
            <th>
                {$LANG.ssl_product_id}
            </th>

            <th>
                {$LANG.ssl_product_name}
            </th>

            <th>
                {$LANG.ssl_product_price_create}
            </th>

            <th>
                {$LANG.ssl_product_price_renew}
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