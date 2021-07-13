<h3>{$LANG.clientareaproductdetails}</h3>

<hr>

<div class="row">
    <div class="col-md-6">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th colspan="2">
                        Datos del Producto
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {$LANG.clientareahostingregdate}
                    </td>
                    <td>
                        {$regdate}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.orderproduct}
                    </td>
                    <td>
                        {$groupname} - {$product}
                    </td>
                </tr>

                {if $domain}
                <tr>
                    <td>
                        {$LANG.orderdomain}
                    </td>
                    <td>
                        {$domain}
                        <a href="http://{$domain}" target="_blank"
                            class="btn btn-default btn-xs">{$LANG.visitwebsite}</a>
                    </td>
                </tr>
                {/if}

                <tr>
                    <td>
                        {$LANG.orderpaymentmethod}
                    </td>
                    <td>
                        {$paymentmethod}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.firstpaymentamount}
                    </td>
                    <td>
                        {$firstpaymentamount}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.recurringamount}
                    </td>
                    <td>
                        {$recurringamount}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.clientareahostingnextduedate}
                    </td>
                    <td>
                        {$nextduedate}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.orderbillingcycle}
                    </td>
                    <td>
                        {$billingcycle}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.clientareastatus}
                    </td>
                    <td>
                        {$status}
                    </td>
                </tr>

                {if $suspendreason}
                <tr>
                    <td>
                        {$LANG.suspendreason}
                    </td>
                    <td>
                        {$suspendreason}
                    </td>
                </tr>
                {/if}
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th colspan="2">
                        Datos Certificado
                    </th>
                </tr>
            </thead>
            <tbody>
                {if $dd_product_name}
                <tr>
                    <td>
                        Tipo de certificado
                    </td>
                    <td>
                        {$dd_product_name}
                    </td>
                </tr>
                {/if} 
                {if $certificate.certificateID}
                <tr>
                    <td>
                        ID del Certificado
                    </td>
                    <td>
                        {$certificate.certificateID}
                    </td>
                </tr>
                {/if}
                {if $certificate.displayStatus}
                <tr>
                    <td>
                        Estado
                    </td>
                    <td>
                        {$certificate.displayStatus}
                    </td>
                </tr>
                {/if}
                {if $certificate.sanMaxDomains}
                <tr>
                    <td>
                        Número máximo de dominios
                    </td>
                    <td>
                        {$certificate.sanMaxDomains}
                    </td>
                </tr>
                {/if}
                {if $certificate.tsCreate}
                <tr>
                    <td>
                        Creacion
                    </td>
                    <td>
                        {$certificate.tsCreate}
                    </td>
                </tr>
                {/if}
                {if $certificate.tsExpir}
                <tr>
                    <td>
                        Expiracion
                    </td>
                    <td>
                        {$certificate.tsExpir}
                    </td>
                </tr>
                {/if}
                {if $can_download}
                <tr>
                    <td class="text-center" colspan="2">
                        <a class="btn btn-primary" href="{$links.download_crt}" download>Descargar Certificado</a>
                    </td>
                </tr>
                {/if}
            </tbody>
        </table>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-sm-6 pull-right">
        <a href="clientarea.php?action=cancel&amp;id={$id}"
            class="btn btn-danger btn-block{if $pendingcancellation}disabled{/if}">
            {if $pendin6cancellation}
            {$LANG.cancellationrequested}
            {else}
            {$LANG.cancel}
            {/if}
        </a>
    </div>
</div>