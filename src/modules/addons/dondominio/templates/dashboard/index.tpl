<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title" style="touch-action: none;">{$LANG.dondominio_modules_information}</h3>
    </div>
    <div class="panel-body">
        <div class="widget-content-padded">
            <table class="datatable" style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 250px">{$LANG.whmcs_version}</td>
                        <td>{$whmcs_version}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width: 250px">{$LANG.version}</td>
                        <td>{$version}</td>
                        <td>
                            {if !is_null($checks.version.success)}
                            {if $checks.version.success eq false}
                            <a class="btn btn-warning" href="{$links.update_modules}">{$LANG.update}</a>
                            {$checks.version.message}
                            <br>
                            <a target="_blank" href="{$LANG.changelog_link}">{$LANG.new_version_changelog}</a>
                            {else}
                            <a data-toggle="tooltip" data-placement="top" title="{$LANG.reinstall_title}" class="btn btn-warning" href="{$links.reinstall}">{$LANG.reinstall}</a>
                            {/if}
                            {else}
                            <span class="text-danger">{$LANG.error}</span>
                            {$checks.version.message}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td>{$LANG.sdk_status}</td>
                        <td>
                            {if $checks.sdk.success eq true}
                            {$LANG.ok}
                            {else}
                            <span class="text-danger">{$LANG.error}</span>
                            {/if}
                        </td>
                        <td>
                            {$checks.sdk.message}
                        </td>
                    </tr>
                    <tr>
                        <td>{$LANG.api_connection_status}</td>
                        <td>
                            {if $checks.api.success eq true}
                            {$LANG.ok}
                            {else}
                            <span class="text-danger">{$LANG.error}</span>
                            {/if}
                        </td>
                        <td>
                            <a class="btn btn-info" href="{$links.check_api_status_link}">{$LANG.check_api_status}</a>
                            {if $checks.api.success eq false}
                            <a type="button" class="btn btn-danger"
                                href="{$links.settings}">{$LANG.check_credentials}</a>
                            {/if}
                        </td>
                    </tr>
                    <tr data-premium-domains="{$premium_domains}">
                        <td>{$LANG.premium_domains}</td>
                        <td>
                            <span data-lang-active="{$LANG.enable}" data-lang-disable="{$LANG.disable}"
                                data-premium="{$premium_domains}"></span>
                        </td>
                        <td>
                            <input id="toggle-premiumdomains" type="checkbox" />
                            <a id="linkConfigurePremiumMarkup" href="configdomains.php?action=premium-levels"
                                class="btn btn-default open-modal"
                                data-btn-submit-id="btnSavePremium">{$LANG.config}</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">{$LANG.modules_installed}</td>
                    </tr>
                    <tr class="align-top">
                        <td>
                            <ul>
                                <li>{$LANG.addon_module}</li>
                            </ul>
                        </td>
                        <td>{$LANG.ok}</td>
                        <td></td>
                    </tr>
                    <tr class="align-top">
                        <td>
                            <ul>
                                <li>{$LANG.registrar_module}</li>
                            </ul>
                        </td>
                        <td>
                            {if $checks.registrar.success eq true}
                            {$LANG.ok}
                            {else}
                            <span class="text-danger">{$LANG.error}</span>
                            {/if}
                        </td>
                        <td>
                            {$checks.registrar.message}
                            {if $checks.registrar.success eq true}
                            {if $checks.registrar.active eq true}
                            <button class="btn btn-default" data-toggle="modal"
                                data-target="#registrar">{$LANG.config}</button>
                            {else}
                            <a data-reload-link class="btn btn-success" href='{$links.active_registrar}'>{$LANG.active}</a>
                            {/if}
                            {else}
                            <a class="btn btn-success" href='{$links.install_registrar}'>{$LANG.install}</a>
                            {/if}
                        </td>
                    </tr>
                    <tr class="align-top">
                        <td>
                            <ul>
                                <li>{$LANG.ssl_provisioning_module}</li>
                            </ul>
                        </td>
                        <td>
                            {if $checks.ssl_provisioning.success eq true}
                            {$LANG.ok}
                            {else}
                            <span class="text-danger">{$LANG.error}</span>
                            {/if}
                        </td>
                        <td>
                            {$checks.ssl_provisioning.message}
                            {if $checks.ssl_provisioning.success eq true}
                            {else}
                            <a class="btn btn-success" href='{$links.install_ssl_provisioning}'>{$LANG.install}</a>
                            {/if}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" id="registrar" tabindex="-1" role="modal">
    <form data-form="registrar" method="post"
        action="{$links.registrar_config}">
        <fieldset>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content panel panel-primary">
                    <div class="modal-header panel-heading">
                        <h5 class="modal-title">{$LANG.registrar_config_title}</h5>
                    </div>
                    <div class="modal-body">
                        <table class="form" width="100%">
                            <tbody>
                                {foreach from=$registrar_config key=key item=val}
                                <tr>
                                    <td class="fieldlabel">{$val.FriendlyName}</td>
                                    <td class="fieldarea">
                                        {if ($val.Type eq 'text') or ($val.Type eq 'password')}
                                        <input type="text" name="{$val.Name}"
                                            class="form-control input-inline {$val.inputClass}" value="{$val.Value}"
                                            placeholder="{$val.Placeholder}" />
                                        {$val.Description}
                                        {/if}
                                        {if $val.Type eq 'yesno'}
                                        <label class="checkbox-inline"><input type="hidden" name="{$val.Name}"
                                                value=""><input type="checkbox" name="{$val.Name}" {if $val.Value}
                                                checked="checked" {/if} />{$val.Description}</label>
                                        {/if}
                                        {if $val.Type eq 'dropdown'}
                                        <select name="{$val.Name}{if $val.Multiple}[]" multiple="true" size="3"
                                            {else}"{/if} class="form-control select-inline">
                                            {html_options options=$val.Options selected=$val.Value}
                                            {/if}
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" name="save" value="{$LANG.btn_save}" class="btn btn-primary">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.close}</button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>

<br>
<input id="get-more-api-info" type="button" class="btn btn-info" value="{$LANG.more_info}"
    data-url="{$links.more_api_info}">
<a class="btn btn-warning" href="systemphpinfo.php" target="_blank">PHP Info <i
        class="fad fa-external-link-alt"></i></a>
<div id="more-api-infobox" class='infobox hide'>
    <img id="more-api-infobox-loading" src="../assets/img/loadingsml.gif" style="width: 25px;">
    <div id="more-api-infobox-details"></div>
</div>

<input data-success value="{$LANG.success_action}" hidden />
<input data-error value="{$LANG.error_action}" hidden />
<input data-toggle-premium-domains type="hidden" value="{$links.toogle_premium_domains}">

{literal}
<script>
    document.getElementById('get-more-api-info').addEventListener('click', async function (e) {
        document.getElementById('more-api-infobox').classList.remove('hide');
        document.getElementById('more-api-infobox-loading').classList.remove('hide');
        document.getElementById('more-api-infobox-details').innerHTML = '';

        const url = document.getElementById('get-more-api-info').dataset.url;
        const response = await fetch(url);
        const html = await response.text();

        document.getElementById('more-api-infobox-loading').classList.add('hide');
        document.getElementById('more-api-infobox-details').innerHTML = html;
    });

    function setDomainPremiumText() {
        let premium = $('[data-premium]');
        let isPremium = premium.attr('data-premium');
        let textData = isPremium == 1 ? 'lang-active' : 'lang-disable'

        premium.text(premium.data(textData))
    }

    function saveSettingPremiumDomains(event, state) {
        const val = state ? 1 : 0
        const url = $('[data-toggle-premium-domains]').val()

        $('#loading').show()

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                status: val
            },
            dataType: 'json'
        }).done(function (d) {
            succesPremiumDomainChange(val, d.msg);
        }).fail(function (d) {
            errorPremiumDomainChange(d.msg);
        })
    }

    function succesPremiumDomainChange(state, message) {
        $('[data-premium]').attr('data-premium', state);
        let title = $('[data-success]').val();

        $.growl.notice({
            title: title,
            message: message
        })

        setDomainPremiumText();
    }

    function errorPremiumDomainChange(message) {
        let title = $('[data-error]').val();

        $.growl.error({
            title: title,
            message: message
        })
    }

    $(document).ready(function () {
        setDomainPremiumText();
        let premiumDomainState = $('[data-premium-domains]').data('premium-domains');

        $('#toggle-premiumdomains').off().bootstrapSwitch({
            state: premiumDomainState,
            size: 'small',
            onColor: 'success',
            offColor: 'default'
        }).on('switchChange.bootstrapSwitch', saveSettingPremiumDomains);

        $('[data-form="registrar"]').on('submit', function (event) {
            event.preventDefault();

            let fieldset = $(this).find('fieldset');
            let url = $(this).attr('action');
            let data = $(this).serialize();
            let errorTitle = $('[data-error]').val();
            let successTitle = $('[data-success]').val();

            fieldset.attr("disabled", "disabled");

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
            }).done(function () {
                $.growl.notice({
                    title: successTitle,
                    message: ''
                })
            }).fail(function () {
                $.growl.error({
                    title: errorTitle,
                    message: ''
                })
            }).always(function () {
                fieldset.removeAttr("disabled");
            })

        });

        $('[data-reload-link]').on('click', function(event) {
            event.preventDefault();
            $(this).attr('disabled', true);
            let url = $(this).attr('href');

            $.ajax({
                type: 'POST',
                url: url,
            }).done(function () {
                location.reload();
            })

        });

    });

</script>
{/literal}