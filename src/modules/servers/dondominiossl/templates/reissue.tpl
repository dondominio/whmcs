<h3>Reissue</h3>

<hr>

<div data-error-dd-ssl class="alert alert-danger" role="alert" style="display: none;"></div>
<div data-success-dd-ssl class="alert alert-success" role="alert" style="display: none;"></div>

<form data-form-dd-ssl action='{$links.action_reissue}' method='post'>
    <div class="form-group">
        <label for="common_name">Dominio</label>
        <input type="text" class="form-control" name="common_name" id="common_name" value="{$domain}" readonly>
    </div>

    <div class="form-group">
        <label for="validation_method">Método de validación</label>
        <select class="form-control" name="validation_method" id="validation_method">
            {html_options options=$validation_methods selected=dns}
        </select>
    </div>

    <div class="form-group">
        <label for="organization_name">Nombre de organización</label>
        <input type="text" class="form-control" name="organization_name" id="organization_name"
            value="{$user.companyname}">
    </div>

    <div class="form-group">
        <label for="organization_unit_name">Nombre de unidad</label>
        <input type="text" class="form-control" name="organization_unit_name" id="organization_unit_name"
            value="{$user.companyname}">
    </div>

    <div class="form-group">
        <label for="country_name">Código de 2 caracteres del país</label>
        <input type="text" class="form-control" name="country_name" id="country_name" value="{$user.countrycode}">
    </div>

    <div class="form-group">
        <label for="state_or_province_name">Nombre de la provincia o estado de la compañía</label>
        <input type="text" class="form-control" name="state_or_province_name" id="state_or_province_name"
            value="{$user.state}">
    </div>

    <div class="form-group">
        <label for="location_name">Nombre de la población de la compañía</label>
        <input type="text" class="form-control" name="location_name" id="location_name" value="{$user.city}">
    </div>

    <div class="form-group">
        <label for="email_address">Nombre de la población de la compañía</label>
        <input type="text" class="form-control" name="email_address" id="email_address" value="{$user.email}">
    </div>

    {if $certificate.sanMaxDomains gt 0}

    <div data-dd-alt-name-container>
        <div data-dd-alt-name class="panel panel-default" style="display: none; padding: 10px;">
            <div class="text-right">
                <a data-dd-close-altdomain href='#'><i class="fa fa-times"></i></a>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label data-dd-alt-name-domain for="alt_name[]">Dominio Alternativo 1</label>
                        <input type="text" class="form-control" name="alt_name[]" id="alt_name[]" disabled="disabled">
                    </div>
                    <div class="col-sm-6">
                        <label data-dd-alt-name-method for="alt_validation[]">Método de validación de Dominio
                            Alternativo 1</label>
                        <select class="form-control" name="alt_validation[]" id="alt_validation[]" disabled="disabled">
                            {html_options options=$validation_methods selected=dns}
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group text-center">
        <a data-dd-add-altdomain href="#" class="btn btn-default"><i class="fa fa-plus"></i> Añadir Dominio
            Alternativo</a>
    </div>

    {/if}

    <div class="form-group text-right">
        <input type='submit' name='submit_button' id='settings_submit' class='btn btn-primary' value="Reissue" />
    </div>
</form>

<input data-dd-max-alt-domains type="hidden" value="{$certificate.sanMaxDomains}">

{include file=$js}

{literal}
<script>
    $(document).ready(function () {
        $('[data-dd-add-altdomain]').click(function (e) {
            e.preventDefault();
            let maxAlts = $('[data-dd-max-alt-domains]').val()
            let altNameContainer = $('[data-dd-alt-name-container]');
            let altNameDivs = $('[data-dd-alt-name]');
            let altNameDiv = altNameDivs.first();
            let altNameCount = altNameDivs.length + 1;
            let clone = altNameDiv.clone();

            if (altNameDiv.is(":hidden")) {
                altNameDiv.show();
                altNameDiv.find('input, select').attr('disabled', false);
                altNameDiv.find('[name="alt_name[]"]').val('');
                altNameDiv.find('[name="alt_validation[]"]').val('dns');
                altNameDiv.find('[data-dd-close-altdomain]').show();
                return;
            }

            altNameDivs.find('[data-dd-close-altdomain]').hide()
            clone.find('[data-dd-alt-name-domain]').text('Dominio Alternativo ' + altNameCount)
            clone.find('[data-dd-alt-name-method]').text('Método de validación de Dominio Alternativo ' + altNameCount)
            clone.find('[name="alt_name[]"]').val('');
            clone.find('[name="alt_validation[]"]').val('dns');
            clone.find('[data-dd-close-altdomain]').show();

            altNameContainer.append(clone);

            if (altNameCount >= maxAlts){
                $(this).hide();
            }
        });

        $('body').on('click', '[data-dd-close-altdomain]', function (e) {
            e.preventDefault();
            let altNameCount = $('[data-dd-alt-name]').length;
            let altNameDiv = $(this).parents('[data-dd-alt-name]');
            console.log('xd');

            if (altNameCount === 1){
                altNameDiv.hide()
                altNameDiv.find('input, select').attr('disabled', true);
                return;
            }

            altNameDiv.remove();
            $('[data-dd-alt-name]').last().find('[data-dd-close-altdomain]').show();
        
            $('[data-dd-add-altdomain]').show();
        });
    });
</script>
{/literal}