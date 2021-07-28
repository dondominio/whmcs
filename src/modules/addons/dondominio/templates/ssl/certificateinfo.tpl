<div class="panel panel-default">

    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8">
                <h3 class="panel-title domain-title">{$certificate.commonName}</h3>
            </div>
            <div class="col-xs-4">
                {if $service || $in_process || $in_reissue || $is_valid || $certificate.renewable}
                <div class="dropdown pull-right">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        {$LANG.domain_actions_view}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        {if $service}
                        <li><a target="_blank" href="{$links.whmcs_order}{$service->id}">{$LANG.ssl_whmcs_order}</a></li>
                        {/if}

                        {if $is_valid}
                        <li><a href="{$links.view_reissue}">{$LANG.ssl_reissue}</a></li>
                        {/if}
                        {if $in_process || $in_reissue}
                        <li><a href="{$links.view_resend_validation_mail}">{$LANG.ssl_resend_validation_mail}</a></li>
                        <li><a href="{$links.view_change_validation_method}">{$LANG.ssl_change_validation_method}</a></li>
                        {/if}
                        {if $certificate.renewable}
                        <li><a href="{$links.view_renew}">{$LANG.ssl_renew}</a></li>
                        {/if}
                    </ul>
                </div>
                {/if}
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="widget-content-padded">
            <table class="datatable domain-table" style="width: 100%;">
                <tbody>
                    <tr>
                        <td>{$LANG.ssl_certificate_id}</td>
                        <td>{$certificate.certificateID}</td>
                    </tr>
                    {if $whmcs_product}
                    <tr>
                        <td>{$LANG.ssl_whmcs_product_name}</td>
                        <td>{$whmcs_product->name}</td>
                    </tr>
                    {/if}
                    {if $product}
                    <tr>
                        <td>{$LANG.ssl_dd_product_name}</td>
                        <td>{$product->product_name}</td>
                    </tr>
                    {/if}
                    {if $certificate.displayStatus}
                    <tr>
                        <td>{$LANG.ssl_certificate_status}</td>
                        <td>{$certificate.displayStatus}</td>
                    </tr>
                    {/if}
                    {if $certificate.tsCreate}
                    <tr>
                        <td>{$LANG.ssl_certificate_ts_ini}</td>
                        <td>{$certificate.tsCreate}</td>
                    </tr>
                    {/if}
                    {if $certificate.tsExpir}
                    <tr>
                        <td>{$LANG.ssl_certificate_ts_end}</td>
                        <td>{$certificate.tsExpir}</td>
                    </tr>
                    {/if}
                    {if $certificate.alternativeNames}
                    <tr>
                        <td>{$LANG.ssl_alternative_names}</td>
                        <td>
                            {foreach $certificate.alternativeNames item=name}
                                <div>{$name}</div>
                            {/foreach}
                        </td>
                    </tr>
                    {/if}
                    {if $certificate.sslCert}
                    <tr>
                        <td>CSR Data</td>
                        <td><pre>{$certificate.sslCert}</pre></td>
                    </tr>
                    {/if}
                    {if $certificate.sslKey}
                    <tr>
                        <td>CSR Key</td>
                        <td><pre>{$certificate.sslKey}</pre></td>
                    </tr>
                    {/if}
                </tbody>
            </table>
        </div>
    </div>
</div>