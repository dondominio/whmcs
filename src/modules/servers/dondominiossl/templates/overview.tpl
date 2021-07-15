<h3>{$LANG.clientareaproductdetails}</h3>

<hr>

<div data-error-dd-ssl class="alert alert-danger" role="alert" {if not $error_msg}style="display: none;"{/if}>{$error_msg}</div>
<div data-success-dd-ssl class="alert alert-success" role="alert" style="display: none;"></div>

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
                        Datos del Certificado
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
                        Creación
                    </td>
                    <td>
                        {$certificate.tsCreate}
                    </td>
                </tr>
                {/if}
                {if $certificate.tsExpir}
                <tr>
                    <td>
                        Expiración
                    </td>
                    <td>
                        {$certificate.tsExpir}
                    </td>
                </tr>
                {/if}
                {if $certificate.validationData.organizationValidationStatus}
                <tr>
                    <td>
                        Estado validación de empresa
                    </td>
                    <td>
                        {$certificate.validationData.organizationValidationStatus}
                    </td>
                </tr>
                {/if}
                {if $certificate.validationData.brandValidationStatus}
                <tr>
                    <td>
                        Estado validación de la marca de empresa
                    </td>
                    <td>
                        {$certificate.validationData.brandValidationStatus}
                    </td>
                </tr>
                {/if}
                {if $certificate.validationData.message}
                <tr>
                    <td>
                        Mensaje de validación
                    </td>
                    <td>
                        {$certificate.validationData.message}
                    </td>
                </tr>
                {/if}
                <tr>
                    <td>
                        Validación externa
                    </td>
                    <td>
                        {if $certificate.validationData.externalValidation}
                        Necesaria
                        {else}
                        No necesaria
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <form data-dd-download-form action="{$links.download_crt}" method="POST" style="display: inline-block;">
                        <div class="btn-group">
                                <input type="hidden" name="password" value="">
                                <input type="hidden" name="need_pass" value="">
                                <input type="hidden" name="type" value="zip">
                                <a data-dd-download-crt href="#" class="btn btn-primary">Descargar</a>
                                <a href="#" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span data-dd-download-text>ZIP</span>
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </a>
                                <ul class="dropdown-menu">
                                    {foreach from=$download_types key=type item=name}
                                    <li><a data-dd-download-type='{$type}'
                                            data-dd-download-type-need-pass="{$name.need_pass}"
                                            href="#">{$name.name}</a></li>
                                    {/foreach}
                                </ul>
                            </div>
                        </form>
                        {if $is_valid}
                        <a href="{$links.viewreissue}" class="btn btn-primary">Remitir</a>
                        {/if}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<hr />

<h4>Control de validación del dominio</h4>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Dominio</th>
            <th>Método de validación</th>
            <th>Estado de validación</th>
        </tr>
    </thead>
    <tbody>
        {foreach $domains item=domain}
        <tr data-dd-domain="{$domain.domainName}">
            <td>
                {$domain.domainName}
            </td>
            <td>
                {$domain.displayValidationMethod}
                {if $can_change_validation}
                <a data-dd-modal data-dd-change-method="{$domain.method}" data-toggle="modal"
                    data-target="#changemethod" href="#" class="btn btn-xs btn-primary pull-right">Cambiar</a>
                {/if}
            </td>
            <td>
                <div class="row">
                    <div class="col-sm-7 text-right">
                        {if $domain.validated}
                        <i class="fas text-success fa-check"></i>
                        {else}
                        <i class="fas text-danger fa-times"></i>
                        {/if}
                    </div>
                    <div class="col-sm-5">
                        {if not $domain.validated and $in_process and $domain.method eq 'mail'}
                        <a data-dd-modal data-toggle="modal" data-target="#resendmail" href="#"
                            class="btn btn-xs btn-primary pull-right">Reenviar</a>
                        {/if}
                    </div>
                </div>
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>

<div id="changemethod" class="modal" tabindex="-1" role="modal">
    <form data-form-dd-ssl action='{$links.changemethod}' method='post'>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content panel panel-primary">
                    <div class="modal-header panel-heading">
                        <h5 class="modal-title">Cambiar método</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="common_name">Dominio</label>
                            <input data-dd-domain class="form-control" name="common_name" id="common_name" readonly />
                        </div>

                        <div class="form-group">
                            <label for="validation_method">Nuevo método de validación</label>
                            <select data-dd-validation-method class="form-control" name="validation_method"
                                id="validation_method">
                                {html_options options=$validation_methods}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <input type='submit' name='submit_button' id='settings_submit' class='btn btn-primary'
                            value="Cambiar" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="resendmail" class="modal" tabindex="-1" role="modal">
    <form data-form-dd-ssl action='{$links.resendmail}' method='post'>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content panel panel-primary">
                <div class="modal-header panel-heading">
                    <h5 class="modal-title">Reenviar correo de validación</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="common_name">Dominio</label>
                        <input data-dd-domain class="form-control" name="common_name" id="common_name" readonly />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <input type='submit' name='submit_button' id='settings_submit' class='btn btn-primary'
                        value="Reenviar" />
                </div>
            </div>
        </div>
    </form>
</div>

<div id="crtpassword" class="modal" tabindex="-1" role="modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content panel panel-primary">
            <div class="modal-header panel-heading">
                <h5 class="modal-title">Para generar este formato es necesaria una contraseña</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input data-dd-download-pass type="password" class="form-control" name="password" id="password" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <a data-dd-download-crt class='btn btn-primary'>Descargar</a>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-sm-6 pull-right">
        <a href="clientarea.php?action=cancel&amp;id={$id}"
            class="btn btn-danger btn-block{if $pendingcancellation}disabled{/if}">
            {if $pendingcancellation}
            {$LANG.cancellationrequested}
            {else}
            {$LANG.cancel}
            {/if}
        </a>
    </div>
</div>

{include file=$js}

{literal}
<script>
    $(document).ready(function () {
        $('[data-dd-modal]').click(function (e) {
            let domain = $(this).parents('[data-dd-domain]').data('dd-domain');
            let method = $(this).data('dd-change-method');

            $('[data-dd-domain]').val(domain);
            $('[data-dd-validation-method]').val(method);
        });

        $('[data-dd-download-type]').click(function (e) {
            e.preventDefault();
            let text = $(this).text();
            let form = $('[data-dd-download-form]')
            let type = $(this).data('dd-download-type');
            let needPass = $(this).data('dd-download-type-need-pass');

            $('[data-dd-download-text]').text(text);
            form.find('[name="type"]').val(type);
            form.find('[name="need_pass"]').val(needPass);
        });

        $('[data-dd-download-crt]').click(function (e) {
            e.preventDefault();
            let form = $('[data-dd-download-form]');
            let needPass = form.find('[name="need_pass"]').val();
            let password = form.find('[name="password"]').val();

            if (needPass && password.length < 1) {
                $('#crtpassword').modal('show');
                return;
            }

            form.submit();
            $('#crtpassword').modal('hide');
            $('[data-dd-download-pass]').val('');
        });

        $('[data-dd-download-pass]').on('change', function () {
            let pass = $(this).val();
            let form = $('[data-dd-download-form]');

            form.find('[name="password"]').val(pass);
        });

        $('#crtpassword').on('hidden.bs.modal', function () {
            $('[data-dd-download-pass]').val('');
            let form = $('[data-dd-download-form]');

            form.find('[name="password"]').val('');
        });
    });
</script>
{/literal}