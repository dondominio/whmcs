<h2>{$LANG.menu_whois}</h2>

<div class='contexthelp'>
    <img src='images/icons/reports.png' border='0' align='absmiddle'>&nbsp;
    <a href='https://dev.dondominio.com/whmcs/docs/addon/' target="_blank">
        {$LANG.info_path_moreinfo}
    </a>
</div>

<p>
<ul class='nav nav-tabs admin-tabs' role='tablist'>
    <li class="active">
        <a href="#tab1" role="tab" data-toggle="tab" id="tabLink1" aria-expanded="true">
            {$LANG.new_tld}
        </a>
    </li>
</ul>

<div class='tab-content admin-tabs'>
    <div class='tab-pane active' id='tab1'>
        <form method='post' action=''>
            <input type="hidden" name="module" value="{$module_name}">
            <input type="hidden" name="__c__" value="{$__c__}">
            <input type="hidden" name="__a__" value="{$actions.switch}">
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td width='30%' class='fieldlabel'>
                            {$LANG.new_tld_tld}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='tld' size='30' value='' required='required' />
                            <input type='submit' id='search-clients' value='{$LANG.new_tld_add}' class='button btn btn-default'>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>

<form data-form="delete" method='post' action='' style="display: inline-block">
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.delete}">
</form>

{if $whois_server_file_is_writable}
<p>
    <div class='tab-pane active' id='tab1'>
        <form action='' method='post'>
            <input type="hidden" name="module" value="{$module_name}">
            <input type="hidden" name="__c__" value="{$__c__}">
            <input type="hidden" name="__a__" value="{$actions.switch}">
            
            <input class="btn btn-default" type="submit" value="{$LANG.change_selected_whois}" />
            <a class='btn btn-default' href='{$links.export}'>{$LANG.servers_export}</a>
            <a data-action="delete" class='btn btn-default' href="#">{$LANG.servers_delete}</a>
            
            <table class='datatable' width='100%' border='0' cellspacing='1' cellpadding='3' id='domainpricing'>
                <thead>
                    <tr>
                        <th width='1'>
                            <input data-check='all' type='checkbox' />
                        </th>

                        <th width='50%'>
                            TLD
                        </th>

                        <th width='50%'>
                            Server
                        </th>

                        <th width='1'>
                            &nbsp;
                        </th>
                    </tr>
                </thead>

                <tbody>
                {foreach $whois_items item=entry}
                <tr style="background-color: {$entry.style}">
                    <td style="background-color: {$entry.style}">
                        {if $entry.can_switch}
                            <input data-check name='tld_checkbox[]' value="{$entry.extensions}" type='checkbox' />
                        {/if}
                    </td>

                    <td width='50%' style="background-color: {$entry.style}">
                        {$entry.extensions}
                    </td>

                    <td width='50%' style="background-color: {$entry.style}">
                        {$entry.uri}
                    </td>

                    <td width='1' style="background-color:{$entry.style}">
                    {if $entry.can_switch}
                        <a href='{$links.switch}{$entry.extensions}' class='btn btn-default btn-sm'>
                            {$LANG.config_switch}
                        </a>
                    {else}
                        &nbsp;
                    {/if}		
                    </td>
                </tr>
                {/foreach}
                </tbody>

            </table>
        </form>
    </div>
</p>
{else}
<div class='infobox'><span class='title'>{$LANG.error_servers_no_writable}</span></div>
<p>
    {$LANG.info_path_whois}: <strong>{$whois_server_file_path}</strong>
</p>
{/if}

<script>
$(document).ready(function() {

    $("a[href^='#tab']").click(function() {
        var tabID = $(this).attr('href').substr(4);
        var tabToHide = $('#tab' + tabID);
        if (tabToHide.hasClass('active')) {
            tabToHide.removeClass('active');
        } else {
            tabToHide.addClass('active')
        }
    });

    $('[data-check="all"]').bind('change', function(e) {
        $('[data-check]').prop('checked', $(this).prop('checked'));
    });

    $('[data-action="delete"]').bind('click', function(e) {
        e.preventDefault();
        $('[data-form="delete"]').submit();
    });

});
</script>