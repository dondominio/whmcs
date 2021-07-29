<div class="panel panel-default">

    <div class="panel-heading">
        <h3 class="panel-title domain-title">{$product->product_name}</h3>
    </div>

    <form data-dd-editproduct-form action="" method="post">
        <div class="panel-body">
            <div class="widget-content-padded">
                <input type="hidden" name="module" value="{$module_name}">
                <input type="hidden" name="__c__" value="{$__c__}">
                <input type="hidden" name="__a__" value="{$actions.update_product}">
                <input type="hidden" name="tld" value="{$tld_settings.tld}">
                <div class="table-responsive">
                    <table class="form-table" width="100%">
                        <tbody>
                            <tr>
                                <td class="form-label">
                                    {$LANG.ssl_product_group}
                                </td>

                                <td>
                                    <select name="group" id="group">
                                        <option value=""></option>
                                        {html_options options=$groups selected=$product_group}
                                    </select>
                                    <a target="_blank" href="{$links.create_group}">{$LANG.ssl_new_group}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">
                                    {$LANG.ssl_product_name}
                                </td>

                                <td>
                                    <input type="text" name="name" size="20" value="{$product_name}" />
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">
                                    {$LANG.ssl_price_increment}
                                </td>

                                <td>
                                    <input type="text" name="increment" size="20" value="{$price_create_increment}" />

                                    <label><input type="radio" name="increment_type" value="FIX"
                                            {if $increment_type=="FIX" } checked="checked" {/if}>
                                        {$LANG.settings_prices_type_fixed}</label>
                                    <label><input type="radio" name="increment_type" value="PERCENTAGE"
                                            {if $increment_type=="PERCENTAGE" } checked="checked" {/if}>
                                        {$LANG.settings_prices_type_percent}</label>
                                    <label><input type="radio" name="increment_type" value="" {if $increment_type=="" }
                                            checked="checked" {/if}>
                                        {$LANG.settings_prices_type_disabled}</label>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">
                                    {$LANG.ssl_vat_number_custom_field}
                                </td>

                                <td>
                                    <select name="vat_number" id="vat_number">
                                        <option value=""></option>
                                        {html_options options=$client_custom_field selected=$vat_number_id}
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-footer form-footer">
            <span data-submit-message class="float-left" style="display: none;">{$LANG.ssl_sync_wait}</span>
            {if $has_whmcs_product}
                <a target="_blank" href="{$links.whmcs_product_edit}" class="pull-left">{$LANG.ssl_whmcs_edit_product}</a>
            {/if}
            <input type="submit" name="submit_button" id="settings_submit" class="btn btn-primary"
                value="{$LANG.btn_save}" />
            {if $has_whmcs_product}
                <a href="{$links.ssl_products}" class="btn btn-default">{$LANG.btn_back}</a>
            {else}
                <a href="{$links.ssl_availables}" class="btn btn-default">{$LANG.btn_back}</a>
            {/if}
        </div>
    </form>
</div>

{literal}
    <script>
        $(document).ready(function() {
            $('[data-dd-editproduct-form]').submit(function(e) {
                $(this).find('input, select').attr('readonly', true);

                $('[data-submit-message]').show();
            });
        });
    </script>
{/literal}