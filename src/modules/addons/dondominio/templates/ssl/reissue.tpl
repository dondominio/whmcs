<div class="panel panel-default">

    <div class="panel-heading">
        <h3 class="panel-title domain-title">{$certificate.certificateID}</h3>
    </div>

    <form action='' method='post'>
        <div class="panel-body">
            <div class="widget-content-padded">
                <input type="hidden" name="module" value="{$module_name}">
                <input type="hidden" name="__c__" value="{$__c__}">
                <input type="hidden" name="__a__" value="{$actions.reissue}">
                <input type="hidden" name="certificate_id" value="{$certificate.certificateID}">
                <div class="form-group">
                    <label for="common_name">{$LANG.ssl_certificate_common_name}</label>
                    <input type="text" class="form-control" name="common_name" id="common_name"
                        value="{$certificate.commonName}">
                </div>

                <div class="form-group">
                    <label for="organization_name">{$LANG.ssl_organization_name}</label>
                    <input type="text" class="form-control" name="organization_name" id="organization_name"
                        value="{$user.companyname}">
                </div>

                <div class="form-group">
                    <label for="organization_unit_name">{$LANG.ssl_organization_name}</label>
                    <input type="text" class="form-control" name="organization_unit_name" id="organization_unit_name"
                        value="{$user.companyname}">
                </div>

                <div class="form-group">
                    <label for="country_name">{$LANG.ssl_country_name}</label>
                    <input type="text" class="form-control" name="country_name" id="country_name"
                        value="{$user.countrycode}">
                </div>

                <div class="form-group">
                    <label for="state_or_province_name">{$LANG.ssl_state_or_province}</label>
                    <input type="text" class="form-control" name="state_or_province_name" id="state_or_province_name"
                        value="{$user.state}">
                </div>

                <div class="form-group">
                    <label for="location_name">{$LANG.ssl_location_name}</label>
                    <input type="text" class="form-control" name="location_name" id="location_name"
                        value="{$user.city}">
                </div>

                <div class="form-group">
                    <label for="email_address">{$LANG.ssl_email_address}</label>
                    <input type="text" class="form-control" name="email_address" id="email_address"
                        value="{$user.email}">
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
                                    <input type="text" class="form-control" name="alt_name[]" id="alt_name[]"
                                        disabled="disabled">
                                </div>
                                <div class="col-sm-6">
                                    <label data-dd-alt-name-method for="alt_validation[]">Método de validación de
                                        Dominio
                                        Alternativo 1</label>
                                    <select class="form-control" name="alt_validation[]" id="alt_validation[]"
                                        disabled="disabled">
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
            </div>
        </div>
        <div class="panel-footer form-footer">
            <button action='submit' name='submit_button' id='settings_submit'
                class='btn btn-primary'>{$LANG.ssl_reissue}</button>
            <a href='{$links.view_certificateinfo}' class='btn btn-default'>{$LANG.btn_back}</a>
        </div>
    </form>
</div>

<input data-dd-max-alt-domains type="hidden" value="{$certificate.sanMaxDomains}">

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

            if (altNameCount >= maxAlts) {
                $(this).hide();
            }
        });

        $('body').on('click', '[data-dd-close-altdomain]', function (e) {
            e.preventDefault();
            let altNameCount = $('[data-dd-alt-name]').length;
            let altNameDiv = $(this).parents('[data-dd-alt-name]');

            if (altNameCount === 1) {
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