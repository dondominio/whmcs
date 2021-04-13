<div id='tabs'>
    <ul class='nav nav-tabs admin-tabs' role='tablist'>
        <li id='tab0' class='tab active'>
            <a href='javascript:;'>{$LANG.settings_api_title}</a>
        </li>

        <li id='tab1' class='tab'>
            <a href='javascript:;'>{$LANG.settings_prices_title}</a>
        </li>

        <li id='tab2' class='tab'>
            <a href='javascript:;'>{$LANG.settings_notifications_title}</a>
        </li>

        <li id='tab3' class='tab'>
            <a href='javascript:;'>{$LANG.settings_cache_title}</a>
        </li>

        <li id='tab4' class='tab'>
            <a href='javascript:;'>{$LANG.settings_whois_title}</a>
        </li>
    </ul>
</div>

<!-- API Settings -->
<div id='tab0box' class='tabbox tab-content'>
    <div id='tab_content'>
        <form method='post' action="#tab0box">
            <input type="hidden" name="module" value="{$module_name}">
            <input type="hidden" name="__c__" value="{$__c__}">
            <input type="hidden" name="__a__" value="{$actions.savecredentials}">
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_api_username}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='api_username' value="{$api_username}" required="required" />
                            {$LANG.settings_api_username_info}
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_api_password}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='api_password' value="{$api_password}" required="required" />
                            {$LANG.settings_api_password_info}
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="text-align: center;">
                <button action='submit' name='submit_button' id='settings_submit' class='btn'>{$LANG.btn_save}</button>
            </p>
        </form>
    </div>
</div>

<!-- Price Adjustment -->
<div id='tab1box' class='tabbox tab-content'>
    <div id='tab_content'>
        <form method='post' action="#tab1box">
            <input type="hidden" name="module" value="{$module_name}">
            <input type="hidden" name="__c__" value="{$__c__}">
            <input type="hidden" name="__a__" value="{$actions.savepriceadjustment}">
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_prices_update_cron}
                        </td>

                        <td class='fieldarea'>
                            <input type='checkbox' name='prices_update_cron' {$prices_update_cron} />
                            {$LANG.settings_prices_update_cron_info}
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_prices_register_add}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='prices_register_add' size='20' value='{$register_increase}' />

                            <label><input type='radio' name='prices_register_type' value='fixed'
                                    {$register_increase_type_fixed}> {$LANG.settings_prices_type_fixed}</label>
                            <label><input type='radio' name='prices_register_type' value='percent'
                                    {$register_increase_type_percent}>{$LANG.settings_prices_type_percent}</label>
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_prices_transfer_add}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='prices_transfer_add' size='20' value="{$transfer_increase}" />

                            <label><input type='radio' name='prices_transfer_type' value='fixed'
                                    {$transfer_increase_type_fixed}> {$LANG.settings_prices_type_fixed}</label>
                            <label><input type='radio' name='prices_transfer_type' value='percent'
                                    {$transfer_increase_type_percent}> {$LANG.settings_prices_type_percent}</label>
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_prices_renew_add}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='prices_renew_add' size='20' value="{$renew_increase}" />

                            <label><input type='radio' name='prices_renew_type' value='fixed'
                                    {$renew_increase_type_fixed}> {$LANG.settings_prices_type_fixed}</label>
                            <label><input type='radio' name='prices_renew_type' value='percent'
                                    {$renew_increase_type_percent}> {$LANG.settings_prices_type_percent}</label>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="text-align: center;">
                <button action='submit' name='submit_button' id='settings_submit' class='btn'>{$LANG.btn_save}</button>
            </p>
        </form>
    </div>
</div>

<!-- Notifications -->
<div id='tab2box' class='tabbox tab-content'>
    <div id='tab_content'>
        <form id='notifications_settings' method='post' action="#tab2box">
            <input type="hidden" name="module" value="{$module_name}">
            <input type="hidden" name="__c__" value="{$__c__}">
            <input type="hidden" name="__a__" value="{$actions.saveautomaticnotifications}">
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_notifications_enable}
                        </td>

                        <td class='fieldarea'>
                            <input type='checkbox' name='notifications_enabled' {$notifications_enabled_checkbox} />
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_notifications_email}
                        </td>

                        <td class='fieldarea'>
                            <input type='email' name='notifications_email' value='{$notifications_email}' size='35' />
                            {$LANG.settings_notifications_email_info}
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldarea' colspan='2'>
                            &nbsp;
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_notifications_select}
                        </td>

                        <td class='fieldarea'>
                            <label><input type='checkbox' name='notifications_new_tld'
                                    {$notifications_new_tlds_checkbox} />
                                {$LANG.settings_notifications_new_tld}</label><br />
                            <label><input type='checkbox' name='notifications_prices'
                                    {$notifications_prices_checkbox} />
                                {$LANG.settings_notifications_prices_updated}</label>
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_watch_ignore}
                        </td>

                        <td class='fieldarea'>
                            <label><input type='radio' name='watchlist' value="disable" {$watchlist_is_disable} />
                                {$LANG.settings_watch_ignore_disable}</label>
                            <label><input type='radio' name='watchlist' value="watch" {$watchlist_is_watch} />
                                {$LANG.settings_watch_ignore_watch}</label>
                            <label><input type='radio' name='watchlist' value="ignore" {$watchlist_is_ignore} />
                                {$LANG.settings_watch_ignore_ignore}</label>

                            <br /><br />

                            <table border='0'>
                                <tbody>
                                    <tr>
                                        <td width='200'>
                                            {$LANG.settings_watch_ignore_available}:<br /><br />
                                            <select id='tlds_available' name='all_tlds[]' multiple size='20'
                                                style='width: 100%;'>
                                                {foreach from=$tlds item=$available_tld}
                                                <option value='{$available_tld}'>{$available_tld}</option>
                                                {/foreach}
                                            </select>
                                        </td>

                                        <td style='vertical-align: middle; padding: 10px;'>
                                            <input id='to_the_right' type='button' value='>>' />
                                            <br /><br />
                                            <input id='to_the_left' type='button' value='<<' />
                                        </td>

                                        <td width='200'>
                                            {$LANG.settings_watch_ignore_active}:<br /><br />
                                            <select id='watchlisted_tlds' name='watchlisted_tlds[]' multiple size='20'
                                                style='width: 100%;'>
                                                {foreach from=$watchlisted_tlds item=$available_tld}
                                                <option value='{$available_tld}'>{$available_tld}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="text-align: center;">
                <button action='submit' name='submit_button' id='submit_button' class='btn'>{$LANG.btn_save}</button>
            </p>
        </form>
    </div>
</div>

<!-- Cache -->
<div id='tab3box' class='tabbox tab-content'>
    <div id='tab_content'>
        <form method='post' action="#tab3box">
            <input type="hidden" name="module" value="{$module_name}">
            <input type="hidden" name="__c__" value="{$__c__}">
            <input type="hidden" name="__a__" value="{$actions.synctlds}">
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_cache_last_update}
                        </td>

                        <td class='fieldarea'>
                            {$last_update}
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_cache_total}
                        </td>

                        <td class='fieldarea'>
                            {$total_tlds}
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel'>
                            {$LANG.settings_cache_rebuild}
                        </td>

                        <td class='fieldarea'>
                            <input type='checkbox' name='cache_rebuild' />
                            {$LANG.settings_cache_rebuild_info}
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="text-align: center;">
                <button action='submit' name='submit_button' id='settings_submit' class='btn'>{$LANG.btn_save}</button>
            </p>
        </form>
    </div>
</div>

<div id='tab4box' class='tabbox tab-content'>
    <div id='tab_content'>
        <form method='post' action="#tab4box">
            <input type="hidden" name="module" value="{$module_name}">
            <input type="hidden" name="__c__" value="{$__c__}">
            <input type="hidden" name="__a__" value="{$actions.savewhoisproxy}">
            <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
                <tbody>
                    <tr>
                        <td class='fieldlabel' width='200'>
                            {$LANG.settings_whois_domain}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='domain' value='{$whois_domain}' size='35'
                                placeholder={$whois_domain_placeholder}><br />{$lang.config_domain_info}
                        </td>
                    </tr>

                    <tr>
                        <td class='fieldlabel' width='200'>
                            {$LANG.settings_whois_ip}
                        </td>

                        <td class='fieldarea'>
                            <input type='text' name='ip' value='{$whois_ip}' size='35'
                                placeholder={$whois_ip_placeholder}><br />{$lang.config_ip_info}
                            <span class='help'>{$LANG.settings_whois_ip_info}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="text-align: center;">
                <button action='submit' name='submit_button' id='settings_submit' class='btn'>{$LANG.btn_save}</button>
            </p>
        </form>
    </div>
</div>

<script>
    $('#to_the_right').click(function (e) {
        var selected = $('#tlds_available OPTION:selected');

        $.each(selected, function (key, item) {
            $('#watchlisted_tlds').append(item);
        });
    });

    $('#notifications_settings').submit(function (e) {
        e.preventDefault();

        $('#watchlisted_tlds option').prop('selected', 'selected');

        this.submit();
    });

    $('#to_the_left').click(function (e) {
        var selected = $('#watchlisted_tlds OPTION:selected');

        $.each(selected, function (key, item) {
            $('#tlds_available').append(item);
        });
    });

    $('.tabbox').css('display', 'none');

    var selectedTab;

    $('.tab').click(function () {
        var elid = $(this).attr('id');
        $('.tab').removeClass('tabselected active');
        $('#' + elid).addClass('tabselected active');
        if (elid != selectedTab) {
            $('.tabbox').hide();
            $('#' + elid + 'box').show();
            selectedTab = elid;
        }
        $('#tab').val(elid.substr(3));
    });

    if (window.location.hash) {
        var selectedTab = window.location.hash;
    } else {
        var selectedTab = '#tab0box';
    }


    $(selectedTab).addClass('tabselected');
    $(selectedTab).css('display', '');
</script>