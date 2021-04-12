<div class="panel-heading">
    <h3 class="panel-title" style="touch-action: none;">{$LANG.dondominio_modules_information}</h3>
</div>
<div class="panel-body">
    <div class="widget-content-padded">

        {if $do_check eq true}
        {if $checks.api.success eq true}
        <div class="alert alert-success">
            {$LANG.success_api_conection}
        </div>
        {else}
        <div class="alert alert-danger">
            {$checks.api.message}.
            <a href="{$links.settings}"> {$LANG.check_credentials}</a>
        </div>
        {/if}
        {/if}


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
                        <a type="button" class="btn btn-danger" href="{$links.settings}">{$LANG.check_credentials}</a>
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
                            class="btn btn-default open-modal" data-modal-title="Configure Premium Domain Levels"
                            data-btn-submit-id="btnSavePremium" data-btn-submit-label="Save">Configure</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">{$LANG.modules_installed}</td>
                </tr>
                <tr>
                    <td>
                        <ul>
                            <li>{$LANG.addon_module}</li>
                        </ul>
                    </td>
                    <td>{$LANG.ok}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <ul>
                            <li>{$LANG.registrar_module}</li>
                        </ul>
                    </td>
                    <td>
                        {if $checks.registrar eq true}
                        {$LANG.ok}
                        {else}
                        <span class="text-danger">{$LANG.error}</span>
                        {/if}
                    </td>
                    <td>
                        {$checks.registrar.message}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<br>
<input id="get-more-api-info" type="button" class="btn btn-info" value="{$LANG.more_info}"
    data-url="{$links.more_api_info}">
<a class="btn btn-warning" href="/admin/systemphpinfo.php" target="_blank">PHP Info <i
        class="fad fa-external-link-alt"></i></a>
<div id="more-api-infobox" class='infobox hide'>
    <img id="more-api-infobox-loading" src="../assets/img/loadingsml.gif" style="width: 25px;">
    <div id="more-api-infobox-details"></div>
</div>

<input data-success value="{$LANG.success_action}" hidden />
<input data-error value="{$LANG.error_action}" hidden />

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

        $('#loading').show()

        $.ajax({
            url: '?module=ispapidomaincheck&action=savepremiumdomains',
            type: 'POST',
            data: {
                premiumDomains: val
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
        }).on('switchChange.bootstrapSwitch', saveSettingPremiumDomains)
    });

</script>
{/literal}