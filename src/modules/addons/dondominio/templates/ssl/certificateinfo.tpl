<div class="panel panel-default">

    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8">
                <h3 class="panel-title domain-title">{$certificate.commonName}</h3>
            </div>
            <div class="col-xs-4">
                {if $service}
                <a target="_blank" href="{$links.whmcs_order}{$service->id}" class="btn btn-primary pull-right">Pedido en WHMCS</a>
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
                    {if $certificate.tsCreate}
                    <tr>
                        <td>{$LANG.ssl_certificate_ts_ini}</td>
                        <td>{$certificate.tsCreate}</td>
                    </tr>
                    {/if}
                    {if $certificate.tsExpire}
                    <tr>
                        <td>Fecha de expiracion</td>
                        <td>{$certificate.tsExpire}</td>
                    </tr>
                    {/if}
                    {if $certificate.alternativeNames}
                    <tr>
                        <td>{$LANG.ssl_certificate_ts_end}</td>
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