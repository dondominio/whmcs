<p>{$LANG.tld_info}</p>

<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.index}">

    <div id='tab0box' class='tabbox'>
        <div id='tab_content'>
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td width='15%' class='fieldlabel'>
                            {$LANG.filter_tld}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='tld' value='{$filters.tld}' />
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
    <input type="hidden" name="__a__" value="">
    <input type='hidden' name='tld' value='{$filters.tld}' />
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

<form action='' method='post'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="">
    <table class='datatable' width='100%' border='0' cellspacing='1' cellpadding='3'>
        <thead>
            <tr>
                <th width='1'>
                    <input class='tld_check_all' type='checkbox' />
                </th>

                <th>
                    {$LANG.tld_tld}
                </th>

                <th width='150'>
                    {$LANG.tld_registrar}
                </th>

                <th width='20'>
                
                </th>
            </tr>
        </thead>

        <tbody>
        {if count($local_tlds) gt 0}
            {foreach $local_tlds item=tld}
            <tr>
                <td>
                    <input class='tld_checkbox' name='tld[]' value="{$tld->extension}" type='checkbox' />
                </td>

                <td>
                    {if empty({$tld->autoreg})}
                        <strong>{$tld->extension}</strong>
                    {else}
                        {$tld->extension}
                    {/if}
                </td>

                <td>
                    {$tld->autoreg}
                </td>

                <td>
                    {if !empty({$tld->autoreg})}
                        <a href='{$links.view_settings}{$tld->extension}'>
                            <img src='images/edit.gif' width='16' height='16' border='0' alt='{$LANG.btn_edit}' />
                        </a>
                    {/if}
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
                <th width='1'>
                    <input class='tld_check_all' type='checkbox' />
                </th>

                <th>
                    {$LANG.tld_tld}
                </th>

                <th width='150'>
                    {$LANG.tld_registrar}
                </th>

                <th width='20'>

                </th>
            </tr>
        </tfoot>
    </table>

    <br />

    {$LANG.info_with_selected} <button type='submit' name='form_action' value='update' class='btn' data-action="{$actions.update_prices}">{$LANG.btn_prices_selected}</button>
    <button type='submit' name='form_action' value='registrar' class='btn' data-action="{$actions.switch_registrar}">{$LANG.btn_registrar_selected}</button>
    <button type='submit' name='form_action' value='reorder' class='btn' data-action="{$actions.reorder_tlds}">{$LANG.btn_reorder_selected}</button>
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
$(document).ready(function() {
    $('.tld_check_all').bind('change', function(e){
        $('.tld_checkbox').prop('checked', $(this).prop('checked'));
        $('.tld_check_all').prop('checked', $(this).prop('checked'));
    });

    $('button[name="form_action"]').on('click', function(e) {
        const form = $(e.target).parents('form');
        $(form).children('[name="__a__"]').val($(e.target).data('action'));
    });
});
</script>