{include file='../nav.tpl'}

<h2>{$LANG.tld_new_title}</h2>

<p>{$LANG.tld_new_info}</p>

<div id='tabs'>
    <ul class='nav nav-tabs admin-tabs'>
        <li id='tab0' class='tab tabselected'>
            <a href='javascript:;'>
                {$LANG.filter_title}
            </a>
        </li>
    </ul>
</div>

<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.availables}">
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

    <br />

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

                    <input type='submit' value={$LANG.pagination_go} class='btn-small'>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<form action='' method='post'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.create}">
    <table class='datatable' width='100%' border='0' cellspacing='1' cellpadding='3'>
        <thead>
            <tr>
                <th width='1'>
                    <input class='tld_check_all' type='checkbox' />
                </th>

                <th>
                    {$LANG.tld_tld}
                </th>

                <th width='120'>
                    {$LANG.tld_register}
                </th>

                <th width='120'>
                    {$LANG.tld_transfer}
                </th>

                <th width='120'>
                    {$LANG.tld_renew}
                </th>

                <th width='20'>

                </th>
            </tr>
        </thead>

        <tbody>
        {foreach $tlds item=tld}
        <tr>
            <td>
                <input class='tld_checkbox' name='tlds[]' value="{$tld.tld}" type='checkbox' />
            </td>

            <td>
                {$tld.tld}
            </td>

            <td>
                {$tld.register_price} &nbsp;
            </td>

            <td>
                {$tld.transfer_price} &nbsp;
            </td>

            <td>
                {$tld.renew_price} &nbsp;
            <td>
                <image src='images/icons/add.png' class="add_tld" width='16' height='16' border='0' alt='{$LANG.btn_add}' style="cursor: pointer;"/>
            </td>
        </tr>
        {/foreach}
        </tbody>

        <tfoot>
            <tr>
                <th width='1'>
                    <input class='tld_check_all' type='checkbox' />
                </th>

                <th>
                    {$LANG.tld_tld}
                </th>

                <th width='120'>
                    {$LANG.tld_register}
                </th>

                <th width='120'>
                    {$LANG.tld_transfer}
                </th>

                <th width='120'>
                    {$LANG.tld_renew}
                </th>

                <th width='20'>

                </th>
            </tr>
        </tfoot>
    </table>

    <br />
    
    {$LANG.info_with_selected} <button type='submit' name='form_action' value='create' class='btn'>{$LANG.btn_create_selected}</button>
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
    $('.tld_check_all').bind('change', function(e){
        $('.tld_checkbox').prop('checked', $(this).prop('checked'));
        $('.tld_check_all').prop('checked', $(this).prop('checked'));
    });

    function toggleadvsearch() {
        if (document.getElementById('searchbox').style.visibility=="hidden") {
            document.getElementById('searchbox').style.visibility="";
        } else {
            document.getElementById('searchbox').style.visibility="hidden";
        }
    }

    $(document).ready(function() {
        var selectedTab = $('#tab0').attr("id");

        $(".tab").click(function() {
            var elid = $(this).attr("id");
            $(".tab").removeClass("tabselected");
            $("#"+elid).addClass("tabselected");
            $(".tabbox").slideUp();
            if (elid != selectedTab) {
                selectedTab = elid;
                $("#"+elid+"box").slideDown();
            } else {
                selectedTab = null;
                $(".tab").removeClass("tabselected");
            }
            $("#tab").val(elid.substr(3));
        });

        $('.add_tld').on('click', function (e) {
            var img = $(e.target);
            img.parents('table').find('.tld_checkbox').prop('checked', false);
            img.parents('tr').find('.tld_checkbox').prop('checked', true);
            $(e.target).parents('form').submit();
        });
    });
</script>