<form data-ssl-form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view_certificates}">
    <div id='tab0box' class='tabbox'>
        <div id='tab_content'>
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td width='15%' class='fieldlabel'>
                            <label for='product_name'>Common Name</label>
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='certificate_common_name' value='{$filters.commonName}' />
                        </td>

                        <td width='15%' class='fieldlabel'>
                            <label for='product_name'>Estado</label>
                        </td>

                        <td class='fieldarea'>
                            <select type='text' name='certificate_status' value='{$filters.status}'>
                                <option value=''>{$LANG.filter_any}</option>
                                {html_options options=$certificates_status selected=$filters.status}
                            <select>
                        </td>

                        <td width='15%' class='fieldlabel'>
                            <label for='product_name'>Renovable</label>
                        </td>

                        <td class='fieldarea'>
                            <select type='text' name='certificate_renewable' value='{$filters.renewable}'>
                                <option value=''>{$LANG.filter_any}</option>
                                {html_options options=$certificates_renewable selected=$filters.renewable}
                            <select>
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
    <input type="hidden" name="__a__" value="{$actions.view_certificates}">
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
                {$LANG.ssl_certificate_id}
            </th>
        
            <th>
                {$LANG.ssl_certificate_common_name}
            </th>
        
            <th>
                {$LANG.ssl_certificate_status}
            </th>
        
            <th>
                {$LANG.ssl_certificate_product}
            </th>
        
            <th>
                {$LANG.ssl_certificate_ts_ini}
            </th>
        
            <th>
                {$LANG.ssl_certificate_ts_end}
            </th>

            <th>{$LANG.ssl_certificate_order}</th>
        </tr>
    </thead>

    <tbody>
        {foreach $certificates item=certificate}
        <tr>
            <td class="text-right">
                <a href="{$links.view_certificate}{$certificate.certificateID}">{$certificate.certificateID}</a>
            </td>

            <td>
                {$certificate.commonName}
            </td>

            <td>
                {$certificate.displayStatus}
            </td>

            <td>
                {$certificate.productName}
            </td>
        
            <td class="text-right">
                {$certificate.tsCreate}
            </td>
        
            <td class="text-right">
                {$certificate.tsExpir}
            </td>

            <td class="text-center">
                {if $certificate.order_id}
                <a target="_blank" href="{$links.whmcs_order}{$certificate.order_id}"><i class="fab fa-whmcs text-success"></i></a> 
                {/if}
            </td>
        </tr>

        {/foreach}
    </tbody>

    <tfoot>
        <tr>
            <th>
                {$LANG.ssl_certificate_id}
            </th>
        
            <th>
                {$LANG.ssl_certificate_common_name}
            </th>
        
            <th>
                {$LANG.ssl_certificate_status}
            </th>
        
            <th>
                {$LANG.ssl_certificate_product}
            </th>
        
            <th>
                {$LANG.ssl_certificate_ts_ini}
            </th>
        
            <th>
                {$LANG.ssl_certificate_ts_end}
            </th>

            <th>{$LANG.ssl_certificate_order}</th>
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
        $('form[data-ssl-form] input[type="checkbox"], form[data-ssl-form] select').on('change', function (event) {
            $('form[data-ssl-form]').submit();
        });
    });
</script>
{/literal}