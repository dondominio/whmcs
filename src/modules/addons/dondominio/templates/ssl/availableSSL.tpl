<form data-ssl-form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view_availabel_ssl}">
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

                        <td class='fieldlabel'>
                            {$LANG.ssl_product_validation_type}
                        </td>

                        <td class='fieldarea'>
                            <select name='product_validation_type'>
                                <option value=''>{$LANG.filter_any}</option>
                                {html_options options=$validation_types selected=$filters.product_validation_type}
                            </select>
                        </td>

                        <td width='15%' class='fieldlabel'>
                            <label for='product_simple'>
                                <input data-dd-product-ype-check="simple" type='checkbox' name='product_simple'
                                    id="product_simple" value='1' {if $filters.product_simple}checked{/if} />
                                {$LANG.ssl_product_simple}
                            </label>
                        </td>

                        <td width='15%' class='fieldlabel'>
                            <label for='product_multi_domain'>
                                <input data-dd-product-ype-check="complex" type='checkbox' name='product_multi_domain'
                                    id="product_multi_domain" value='1' {if
                                    $filters.product_multi_domain}checked{/if} />
                                {$LANG.ssl_label_product_multi_domain}
                            </label>
                        </td>

                        <td width='15%' class='fieldlabel'>
                            <label for='product_wildcard'>
                                <input data-dd-product-ype-check="complex" type='checkbox' name='product_wildcard'
                                    id="product_wildcard" value='1' {if $filters.product_wildcard}checked{/if} />
                                {$LANG.ssl_label_product_wildcard}
                            </label>
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
    <input type="hidden" name="__a__" value="{$actions.view_availabel_ssl}">
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
                {$LANG.ssl_product_name}
            </th>

            <th>
                {$LANG.ssl_product_price_create}
            </th>

            <th>
                {$LANG.ssl_product_price_renew}
            </th>

            <th>
                {$LANG.ssl_product_validation_type}
            </th>

            <th>
                {$LANG.ssl_label_product_multi_domain}
            </th>

            <th>
                {$LANG.ssl_label_product_wildcard}
            </th>

            <th style="width: 20px;"></th>
        </tr>
    </thead>

    <tbody>
        {foreach $products item=product}
        <tr>
            <td>
                {$product.product_name} &nbsp;
            </td>

            <td class="text-right">
                {$product.price_create}
            </td>

            <td class="text-right">
                {$product.price_renew}
            </td>

            <td>
                {$product->getDisplayValidationType()}
            </td>

            <td class="text-center">
                {if $product.is_multi_domain}
                <i class="fad fa-check text-success"></i>
                {else}
                <i class="fad fa-times text-danger"></i>
                {/if}
            </td>

            <td class="text-center">
                {if $product.is_wildcard}
                <i class="fad fa-check text-success"></i>
                {else}
                <i class="fad fa-times text-danger"></i>
                {/if}
            </td>

            <td>
                <a href="{$links.create_whmcs_product}{$product.dd_product_id}">
                    <img src='images/icons/add.png' class="add_tld" width='16' height='16' border='0'
                        alt='{$LANG.btn_add}' style="cursor: pointer;" />
                </a>
            </td>

        </tr>
        {/foreach}
    </tbody>

    <tfoot>
        <tr>
            <th>
                {$LANG.ssl_product_name}
            </th>

            <th>
                {$LANG.ssl_product_price_create}
            </th>

            <th>
                {$LANG.ssl_product_price_renew}
            </th>

            <th>
                {$LANG.ssl_product_validation_type}
            </th>

            <th>
                {$LANG.ssl_label_product_multi_domain}
            </th>

            <th>
                {$LANG.ssl_label_product_wildcard}
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

{literal}
<script>
    $(document).ready(function () {
        $('form[data-ssl-form] select').on('change', function () {
            $('form[data-ssl-form]').submit();
        });

        $('[data-dd-product-ype-check]').on('change', function () {
            let type = $(this).data('dd-product-ype-check');

            if ($(this).is(':checked')) {                
                $('[data-dd-product-ype-check]').each(function (key, element) {
                    let elementType = $(element).data('dd-product-ype-check');

                    if (elementType !== type) {
                        $(element).prop("checked", false);
                    }
                })
            }

            $('form[data-ssl-form]').submit();
        });
    });
</script>
{/literal}