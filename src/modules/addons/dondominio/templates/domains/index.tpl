{include file='../nav.tpl'}

<h2>{$LANG.domains_title} <i class="fad fa-question-circle title-icon" data-toggle="modal" data-target="#domains"></i></h2>

<div class="modal" id="domains" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5  class="modal-title" >{$LANG.domains_title}</h5>
            </div>
            <div class="modal-body">
                <p>{$LANG.domains_info}</p>
                <p>{$LANG.info_too_much_requests}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.close}</button>
            </div>
        </div>
    </div>
</div>

<div id='tabs'>
    <ul class='nav nav-tabs admin-tabs' role='tablist'>
        <li id='tab0' class='tab tabselected'>
            <a href='javascript:;'>{$LANG.filter_title}</a>
        </li>
    </ul>
</div>
<div id='tab0box' class='tabbox'>
    <div id='tab_content'>
        <form action='' method='get'>
            <input type="hidden" name="module" value="{$module_name}">
            <input type="hidden" name="__c__" value="{$__c__}">
            <input type="hidden" name="__a__" value="{$actions.index}">
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
                            <select name='tld' id='tldDropDown'>
                                <option value=''>{$LANG.filter_any}</option>
                                {html_options options=$tlds selected=$filters.tld}
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.filter_registrar}
                        </td>

                        <td class='fieldarea'>
                            <select name='registrar' id='registrarsDropDown'>
                                <option value=''>{$LANG.filter_any}</option>
                                {html_options options=$registrars selected=$filters.registrar}
                            </select>
                        </td>

                        <td class='fieldlabel'>
                            {$LANG.filter_status}
                        </td>

                        <td class='fieldarea'>
                            {html_options name=status options=$statuses selected=$filters.status}
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
    <input type="hidden" name="__a__" value="">
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
                    {$LANG.domains_registrar}
                </th>

                <th width='100'>
                    {$LANG.domains_status}
                </th>

                <th width='20'>
                    &nbsp;
                </th>
            </tr>
        </thead>
        <tbody>

        {if count($domains) gt 0}
            {foreach $domains item=domain}
                <tr>
                    <td>
                        <input class='domain_checkbox' name='domain_checkbox[]' value="{$domain.id}" type='checkbox' />
                    </td>

                    <td>
                        {$domain.domain}
                    </td>

                    <td>
                        {$domain.registrar}
                    </td>
 
                    <td>
                        {assign var='status_class' value=''}

                        {if $domain.status eq 'Active'}
                            {assign var='status_class' value="active"}
                        {/if}
                        {if $domain.status eq 'Pending'}
                            {assign var='status_class' value="pending"}
                        {/if}
                        {if $domain.status eq 'Pending Transfer'}
                            {assign var='status_class' value="pending"}
                        {/if}
                        {if $domain.status eq 'Expired'}
                            {assign var='status_class' value="expired"}
                        {/if}
                        {if $domain.status eq 'Cancelled'}
                            {assign var='status_class' value="cancelled"}
                        {/if}
                        {if $domain.status eq 'Fraud'}
                            {assign var='status_class' value="fraud"}
                        {/if}
                        {if $domain.status eq 'Transferred Away'}
                            {assign var='status_class' value="transferred-away"}
                        {/if}
                        {if $domain.status eq 'Grace'}
                            {assign var='status_class' value="grace"}
                        {/if}
                        {if $domain.status eq 'Redemption'}
                            {assign var='status_class' value="redemption"}
                        {/if}

                        {if strlen($status_class) gt 0}
                            <div style='text-align: center;' class='label {$status_class}'>{$domain.status}</div>
                        {else}
                            <div style='text-align: center;'>{$domain.status}</div>
                        {/if}
                    </td>

                    <td>
                        <a href='{$links.sync_domain}&domain_checkbox[]={$domain.id}'><img src='images/icons/navrotate.png'></a>
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
                    <input class='domains_check_all' type='checkbox' />
                </th>

                <th>
                    {$LANG.domains_domain}
                </th>

                <th width='100'>
                    {$LANG.domains_registrar}
                </th>

                <th width='100'>
                    {$LANG.domains_status}
                </th>

                <th width='20'>
                    &nbsp;
                </th>
            </tr>
        </tfoot>
    </table>

    <br />

    {$LANG.info_with_selected} <button id='domain_dondominio' name='form_action' value='registrar' class='btn' data-action="{$actions.switch_registrar}">{$LANG.domains_set_dondominio}</button>
    <button id='domain_sync' name='form_action' value='sync' class='btn' data-action="{$actions.sync}">{$LANG.domains_sync}</button>
    <button id='domain_price' name='form_action' value='price' class='btn' data-action="{$actions.update_price}">{$LANG.domains_price}</button>

    <br />
    <br />

    <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
        <tbody>
            <tr>
                <td width='20%' class='fieldlabel'>
                    {$LANG.domains_contact_id}
                </td>

                <td class='fieldarea'>
                    <input type='text' name='ddid' size='30' value='{$filters.ddid}' placeholder='XXX-00000' /> <a href='https://dev.mrdomain.com/whmcs/docs/addon/#contacts' target='_api'>{$LANG.link_more_info}</a>
                </td>
            </tr>

            <tr>
                <td class='fieldlabel'>
                    &nbsp;
                </td>

                <td class='fieldarea'>
                    <button id='domain_owner' name='form_action' value='owner' class='btn updateContact' data-action="{$actions.update_contact}">{$LANG.domains_set_owner}</button>
                    <button id='domain_owner' name='form_action' value='admin' class='btn updateContact' data-action="{$actions.update_contact}">{$LANG.domains_set_admin}</button>
                    <button id='domain_owner' name='form_action' value='tech' class='btn updateContact' data-action="{$actions.update_contact}">{$LANG.domains_set_tech}</button>
                    <button id='domain_owner' name='form_action' value='billing' class='btn updateContact' data-action="{$actions.update_contact}">{$LANG.domains_set_billing}</button>
                </td>
            </tr>
        </tbody>
    </table>
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
function toggleadvsearch() {
    if (document.getElementById('searchbox').style.visibility == "hidden") {
        document.getElementById('searchbox').style.visibility = "";
    } else {
        document.getElementById('searchbox').style.visibility = "hidden";
    }
}

$(document).ready(function() {
    var selectedTab = $("#tab0").attr("id");
    
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

    $('.domains_check_all').bind('change', function(e) {
        $('.domain_checkbox').prop('checked', $(this).prop('checked'));
        $('.domains_check_all').prop('checked', $(this).prop('checked'));
    });

    $('button[name="form_action"]').on('click', function(e) {
        const form = $(e.target).parents('form');
        $(form).children('[name="__a__"]').val($(e.target).data('action'));
    });
});
</script>