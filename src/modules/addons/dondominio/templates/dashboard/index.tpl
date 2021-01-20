<div class="home-widgets-container">
    <div class="dashboard-panel-item dashboard-panel-item-columns-2" style="position: absolute; left: 0%; top: 0px;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title" style="touch-action: none;">{$LANG.dondominio_modules_information}</h3>
            </div>
            <div class="panel-body">
                <div class="widget-content-padded">
                    <table class="datatable">
                        <tbody>
                        <tr>
                            <td style="width: 250px">{$LANG.version}</td>
                            <td>{$version}</td>
                            <td>
                                {if !is_null($checks.version.success)}
                                    {if $checks.version.success eq false}
                                        <a class="btn btn-warning" href="{$links.update_modules}">{$LANG.update}</a>
                                        {$checks.version.message}
                                    {/if}
                                {else}
                                    <input type="button" class=" btn btn-danger" value="{$LANG.error}" style="cursor: default;">
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
                                    <input type="button" class=" btn btn-danger" value="{$LANG.error}" style="cursor: default;">
                                {/if}
                            </td>
                            <td>
                                {if $checks.sdk.success eq false}
                                    {$checks.sdk.message}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td>{$LANG.api_connection_status}</td>
                            <td>
                                {if $checks.api.success eq true}
                                    {$LANG.ok}
                                {else}
                                    <input type="button" class=" btn btn-danger" value="{$LANG.error}" style="cursor: default;">
                                {/if}
                            </td>
                            <td>
                                {if $checks.api.success eq true}
                                    {$checks.api.message}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">{$LANG.modules_installed}</td>
                        </tr>
                        <tr>
                            <td><ul><li>{$LANG.addon_module}</li></ul></td>
                            <td>{$LANG.ok}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><ul><li>{$LANG.registrar_module}</li></ul></td>
                            <td>
                                {if $checks.registrar eq true}
                                    {$LANG.ok}
                                {else}
                                    <input type="button" class=" btn btn-danger" value="{$LANG.error}" style="cursor: default;">
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
        </div>
    </div>
</div>
<br>
<input id="get-more-api-info" type="button" class="btn btn-info" value="{$LANG.more_info}" data-url="{$links.more_api_info}">
<div id="more-api-infobox" class='infobox hide'>
    <img id="more-api-infobox-loading" src="../assets/img/loadingsml.gif" style="width: 25px;">
    <div id="more-api-infobox-details"></div>
</div>

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
</script>