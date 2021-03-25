{include file='../nav.tpl'}

<h2>{$LANG.tld_settings_title}: {$tld_settings.tld}</h2>
	
<p>{$LANG.tld_settings_description}</p>

<form action='' method='post'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.save_settings}">
    <input type="hidden" name="tld" value="{$tld_settings.tld}">
    <table class='form' width='100%' border='0' cellpadding='3' cellspacing='0'>
        <tbody>
            <tr>
                <td class='fieldlabel'>
                    &nbsp;
                </td>
                
                <td class='fieldarea'>
                    <label><input type='checkbox' name='no_update' {if $tld_settings.ignore} checked="checked" {/if}> {$LANG.tld_settings_no_update}</label>
                </td>
            </tr>
            
            <tr>
                <td class='fieldlabel'>
                    &nbsp;
                </td>
                
                <td class='fieldarea'>
                    <label><input type='checkbox' name='status' {if $tld_settings.enabled} checked="checked" {/if}> {$LANG.tld_settings_enabled}</label>
                </td>
            </tr>
            
            <tr>
                <td class='fieldlabel'>
                    {$LANG.settings_prices_register_add}
                </td>
                
                <td class='fieldarea'>
                    <input type='text' name='registration' size='20' value='{$tld_settings.register_increase}' />
                    
                    <label><input type='radio' name='registration_type' value='fixed' {if $tld_settings.register_increase_type == 'fixed'} checked="checked" {/if}> {$LANG.settings_prices_type_fixed}</label>
                    <label><input type='radio' name='registration_type' value='percent' {if $tld_settings.register_increase_type == 'percent'} checked="checked" {/if}> {$LANG.settings_prices_type_percent}</label>
                    <label><input type='radio' name='registration_type' value='no_increase' {if $tld_settings.register_increase_type == 'no_increase'} checked="checked" {/if}> {$LANG.settings_prices_type_disabled}</label>
                </td>
            </tr>
            
            <tr>
                <td class='fieldlabel'>
                    {$LANG.settings_prices_transfer_add}
                </td>
                
                <td class='fieldarea'>
                    <input type='text' name='transfer' size='20' value='{$tld_settings.transfer_increase}' />
                    
                    <label><input type='radio' name='transfer_type' value='fixed' {if $tld_settings.transfer_increase_type == 'fixed'} checked="checked" {/if}> {$LANG.settings_prices_type_fixed}</label>
                    <label><input type='radio' name='transfer_type' value='percent' {if $tld_settings.transfer_increase_type == 'percent'} checked="checked" {/if}> {$LANG.settings_prices_type_percent}</label>
                    <label><input type='radio' name='transfer_type' value='no_increase' {if $tld_settings.transfer_increase_type == 'no_increase'} checked="checked" {/if}> {$LANG.settings_prices_type_disabled}</label>
                </td>
            </tr>
            
            <tr>
                <td class='fieldlabel'>
                    {$LANG.settings_prices_renew_add}
                </td>
                
                <td class='fieldarea'>
                    <input type='text' name='renewal' size='20' value='{$tld_settings.renew_increase}' />
                    
                    <label><input type='radio' name='renewal_type' value='fixed' {if $tld_settings.renew_increase_type == 'fixed'} checked="checked" {/if}> {$LANG.settings_prices_type_fixed}</label>
                    <label><input type='radio' name='renewal_type' value='percent' {if $tld_settings.renew_increase_type == 'percent'} checked="checked" {/if}> {$LANG.settings_prices_type_percent}</label>
                    <label><input type='radio' name='renewal_type' value='no_increase' {if $tld_settings.renew_increase_type == 'no_increase'} checked="checked" {/if}> {$LANG.settings_prices_type_disabled}</label>
                </td>
            </tr>
        </tbody>
    </table>
    
    <p align='center'>
        <button action='submit' name='submit_button' id='settings_submit' class='btn'>{$LANG.btn_save}</button>
        <a href='{$links.tlds_index}' class='btn'>{$LANG.btn_back}</a>
    </p>
</form>