<h3>Validación del Certificado</h3>

<hr />

<div data-error-dd-ssl class="alert alert-danger" role="alert" style="display: none;"></div>
<div data-success-dd-ssl class="alert alert-success" role="alert" style="display: none;"></div>

<div class="row">
    <div class="col-sm-4">
        Dominio
    </div>
    <div class="col-sm-8">
        {$certificate.commonName}
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        Estado
    </div>
    <div class="col-sm-8">
        {$certificate.displayStatus}
    </div>
</div>

{if $certificate.validationData.message}
<div class="row">
    <div class="col-sm-4">
        Mensaje de validación
    </div>
    <div class="col-sm-8">
        {$certificate.validationData.message}
    </div>
</div>
{/if}

<div class="row">
    <div class="col-sm-4">
        Validación externa
    </div>
    <div class="col-sm-8">
        {if $certificate.validationData.externalValidation}
        Necesaria
        {else}
        No necesaria
        {/if}
    </div>
</div>

{if $certificate.validationData.organizationValidationStatus}
<div class="row">
    <div class="col-sm-4">
        Estado validación de empresa 
    </div>
    <div class="col-sm-8">
        {$certificate.validationData.organizationValidationStatus}
    </div>
</div>
{/if}

{if $certificate.validationData.brandValidationStatus}
<div class="row">
    <div class="col-sm-4">
        Estado validación de la marca de empresa
    </div>
    <div class="col-sm-8">
        {$certificate.validationData.brandValidationStatus}
    </div>
</div>
{/if}

{if $is_valid}
<div class="text-right">
    <a href="{$links.viewreissue}" class="btn btn-primary">Remitir</a>
</div>
{/if}

<hr />

<h4>Control de validación del dominio</h4>

<div class="row">
    {foreach $domains item=domain}
    <div class="col-sm-6" data-dd-domain="{$domain.domainName}" style="min-height: 180px;">
        <div class="panel panel-default">
            <div class="panel-body">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <td>Dominio</td>
                            <td colspan="2">{$domain.domainName}</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Método de validación</td>
                            <td {if not $can_change_validation} colspan="2" {/if}>{$domain.displayValidationMethod}</td>
                            <td>
                                {if $can_change_validation}
                                <a data-dd-modal data-dd-change-method="{$domain.method}" data-toggle="modal"
                                    data-target="#changemethod" href="#" class="btn btn-xs btn-primary">Cambiar</a></td>
                                {/if}
                        </tr>
                        <tr>
                            <td>Estado de validación</td>
                            <td class="text-center">
                                {if $domain.validated}
                                <i class="fas text-success fa-check"></i>
                                {else}
                                <i class="fas text-danger fa-times"></i>
                                {/if}
                            </td>
                            <td>
                                {if not $domain.validated and $in_process}
                                <a data-dd-modal data-toggle="modal" data-target="#resendmail" href="#"
                                    class="btn btn-xs btn-primary">Reenviar Correo</a></td>
                                {/if}
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {/foreach}
</div>

<div id="changemethod" class="modal" tabindex="-1" role="modal">
    <form data-form-dd-ssl action='{$links.changemethod}' method='post'>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content panel panel-primary">
                    <div class="modal-header panel-heading">
                        <h5 class="modal-title">Cambiar metodo</h5>
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
        </div>
    </form>
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
    });
</script>
{/literal}