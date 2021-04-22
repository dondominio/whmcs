<div class="row">

    <div class="col-sm-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <table class="datatable" style="width: 100%;">
                    <tbody>
                        {if ($new_version eq true) or ($conection_erro eq true)}
                        {if $new_version eq true}
                        <tr>
                            <td colspan="2">{$LANG.home_new_version}</td>
                            <td>
                                <a class="btn btn-warning" href="{$links.admin}">{$LANG.home_go_update}</a>
                                <div class="note">
                                    <a target="_blank" href="{$LANG.changelog_link}">{$LANG.home_check_changelog}</a>
                                </div>
                            </td>
                        </tr>
                        {/if}
                        {if $conection_erro eq true}
                        <tr>
                            <td colspan="2">{$LANG.home_api_error}</td>
                            <td>
                                <a type="button" class="btn btn-danger"
                                    href="{$links.settings}">{$LANG.home_check_credentials}</a>
                            </td>
                        </tr>
                        {/if}
                        {/if}
                        <tr>
                            <td style="width: 250px">{$LANG.home_domains_dd}</td>
                            <td><a href="{$links.domains}">{$total_domains}</a></td>
                            <td><a href="{$links.domains}" class="btn btn-primary">{$LANG.home_admin_domains}</a></td>
                        </tr>
                        <tr>
                            <td style="width: 250px">{$LANG.home_tlds_dd}</td>
                            <td><a href="{$links.tlds}">{$total_tlds}</a></td>
                            <td><a href="{$links.tlds}" class="btn btn-primary">{$LANG.home_admin_tlds}</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-body">
                <a href="https://dev.dondominio.com/whmcs/" class="btn btn-dd" target="_blank"><img
                        src="https://www.dondominio.com/images/favicon_appletouch.png" class="absmiddle" width="16"
                        height="16"> {$LANG.home_documentation}</a>
                <a href="https://github.com/dondominio/whmcs/" class="btn btn-transparent" target="_blank"><i
                        class="fab fa-github"></i> GitHub</a>
            </div>
        </div>
    </div>

</div>