{if $need_config eq true}
<div class='infobox'>
    <span class='title'>{$LANG.error_whois_domain_empty}</span>
</div>
<p>{$LANG.info_whois_domain}</p>
{/if}

<div id='tab_content'>
    <form method='post' action="">
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$settings_controller}">
    <input type="hidden" name="__a__" value="{$save_whois_action}">
    <input type="hidden" name="redirect" value="whois">
    <table class='form' width='100%' border='0' cellspacing='2' cellpadding='3'>
        <tbody>
            <tr>
                <td class='fieldlabel' width='200'>
                    {$LANG.settings_whois_domain}
                </td>

                <td class='fieldarea'>
                    <input type='text' name='domain' value='{$whois_domain}' size='35' placeholder={$whois_domain_placeholder}><br />{$lang.config_domain_info}
                </td>
            </tr>

            <tr>
                <td class='fieldlabel' width='200'>
                    {$LANG.settings_whois_ip}
                </td>

                <td class='fieldarea'>
                    <input type='text' name='ip' value='{$whois_ip}' size='35' placeholder={$whois_ip_placeholder}><br />{$lang.config_ip_info}
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