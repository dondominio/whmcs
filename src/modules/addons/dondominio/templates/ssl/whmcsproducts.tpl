{if $products|count gt 0}
<form data-ssl-form action='' method='get'>
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
                            <input type='text' name='whmcs_product_name' value='{$filters.whmcs_product_name}' />
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
                {$LANG.ssl_whmcs_product_name}
            </th>
            
            <th>
                {$LANG.ssl_whmcs_product_price}
            </th>

            <th>
                {$LANG.ssl_whmcs_base_price}
            </th>

            <th>
                {$LANG.ssl_product_price_increase}
            </th>

            <th style="width: 20px;"></th>
        </tr>
    </thead>

    <tbody>
        {foreach $products item=product}
        <tr>
            <td>
                {$product->getWhmcsProduct()->name} &nbsp;
            </td>

            <td class="text-right">
                {$product->getWhmcsProductAnnuallyPrice()}
            </td>

            <td class="text-right">
                {$product.price_create}
            </td>

            <td class="text-right">
                {$product->getDisplayPriceIncrement()}
            </td>

            <td>
                <a href="{$links.create_whmcs_product}{$product.dd_product_id}">
                    <img src='images/edit.gif' class="add_tld" width='16' height='16' border='0'
                        alt='{$LANG.btn_edit}' style="cursor: pointer;" />
                </a>
            </td>

        </tr>
        {/foreach}
    </tbody>

    <tfoot>
        <tr>
            <th>
                {$LANG.ssl_whmcs_product_name}
            </th>
            
            <th>
                {$LANG.ssl_whmcs_product_price}
            </th>

            <th>
                {$LANG.ssl_whmcs_base_price}
            </th>

            <th>
                {$LANG.ssl_product_price_increase}
            </th>

            <th style="width: 20px;"></th>
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

{else}
<div class="panel panel-default">
    <div class="panel-body text-center">
        <p>{$LANG.ssl_whmcs_products_not_found}</p>
        <a class="btn btn-success" href='{$links.available_products}'>{$LANG.ssl_add_product}</a>
    </div>
</div>
{/if}

{literal}
<script>
    $(document).ready(function () {
        $('form[data-ssl-form] input[type="checkbox"], form[data-ssl-form] select').on('change', function (event) {
            $('form[data-ssl-form]').submit();
        });
    });
</script>
{/literal}