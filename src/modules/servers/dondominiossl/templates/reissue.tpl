<h3>{$DD_LANG.cert_reissue}</h3>

<hr>

<div data-error-dd-ssl class="alert alert-danger" role="alert" {if not $error_msg}style="display: none;" {/if}>
    {$error_msg}</div>
<div data-success-dd-ssl class="alert alert-success" role="alert" style="display: none;"></div>

{if $can_reissue}

<form data-form-dd-ssl action='{$links.action_reissue}' method='post'>
    <div class="form-group">
        <label for="common_name">{$DD_LANG.cert_domain}</label>
        <input data-dd-alt-name-domain="0" type="text" class="form-control" name="common_name" id="common_name"
            value="{$certificate.commonName}" readonly>
    </div>

    <div class="form-group">
        <label for="validation_method">{$DD_LANG.cert_validation_method}</label>
        <select class="form-control" name="validation_method" id="validation_method">
            {html_options options=$validation_methods selected=dns}
            <optgroup label="{$DD_LANG.cert_mail}">
                {html_options options=$validation_mails}
            </optgroup>
        </select>
    </div>

    <div class="form-group">
        <label for="organization_name">{$DD_LANG.cert_org_name}</label>
        <input type="text" class="form-control" name="organization_name" id="organization_name"
            value="{$user.companyname}">
    </div>

    <div class="form-group">
        <label for="organization_unit_name">{$DD_LANG.cert_org_unit}</label>
        <input type="text" class="form-control" name="organization_unit_name" id="organization_unit_name"
            value="{$user.companyname}">
    </div>

    <div class="form-group">
        <label for="country_name">{$DD_LANG.cert_country}</label>
        <input type="text" class="form-control" name="country_name" id="country_name" value="{$user.countrycode}">
    </div>

    <div class="form-group">
        <label for="state_or_province_name">{$DD_LANG.cert_state}</label>
        <input type="text" class="form-control" name="state_or_province_name" id="state_or_province_name"
            value="{$user.state}">
    </div>

    <div class="form-group">
        <label for="location_name">{$DD_LANG.cert_location}</label>
        <input type="text" class="form-control" name="location_name" id="location_name" value="{$user.city}">
    </div>

    <div class="form-group">
        <label for="email_address">{$DD_LANG.cert_mail}</label>
        <input type="text" class="form-control" name="email_address" id="email_address" value="{$user.email}">
    </div>

    {if $certificate.sanMaxDomains gt 0}
    <div data-dd-alt-name-container>

        {if $alt_names|count gt 0}

        {foreach from=$alt_names key=key item=name}
        <div data-dd-alt-name class="panel panel-default" style="padding: 10px;">
            <div class="text-right">
                <a data-dd-close-altdomain href='#'><i class="fa fa-times"></i></a>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="form-group">
                        <label for="alt_name[]">{$DD_LANG.cert_alt_name} <span data-dd-alt-name-count>{$key +
                                1}</span></label>
                        <input data-dd-alt-name-domain="{$key + 1}" type="text" class="form-control" name="alt_name[]"
                            id="alt_name[]" value="{$name}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label data-dd-alt-name-method-title="{$key + 1}" data-dd-default-val="{$default_method_title}"
                            for="change_alt_validation[]">{$default_method_title}</label>
                        <input data-dd-alt-name-change="{$key + 1}" data-dd-modal data-dd-change-method="dns"
                            data-toggle="modal" data-target="#changemethod" type="button" id='change_alt_validation[]'
                            name='change_alt_validation[]' class="form-control btn btn-primary" value="Cambiar" />
                    </div>

                    <input data-dd-alt-name-method="{$key + 1}" type="hidden" class="form-control"
                        name="alt_validation[]" id="alt_validation[]" value="dns">
                </div>
            </div>
        </div>
        {/foreach}

        {else}
        <div data-dd-alt-name class="panel panel-default" style="display: none; padding: 10px;">
            <div class="text-right">
                <a data-dd-close-altdomain href='#'><i class="fa fa-times"></i></a>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="form-group">
                        <label for="alt_name[]">{$DD_LANG.cert_alt_name} <span data-dd-alt-name-count>1</span></label>
                        <input data-dd-alt-name-domain="1" type="text" class="form-control" name="alt_name[]"
                            id="alt_name[]" disabled="disabled">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label data-dd-alt-name-method-title="1" data-dd-default-val="{$default_method_title}"
                            for="change_alt_validation[]">{$default_method_title}</label>
                        <input data-dd-alt-name-change="1" data-dd-modal data-dd-change-method="dns" data-toggle="modal"
                            data-target="#changemethod" type="button" id='change_alt_validation[]'
                            name='change_alt_validation[]' class="form-control btn btn-primary" value="Cambiar" />
                    </div>

                    <input data-dd-alt-name-method="1" type="hidden" class="form-control" name="alt_validation[]"
                        id="alt_validation[]" disabled="disabled">
                </div>
            </div>
        </div>
        {/if}
    </div>

    <div class="form-group text-center">
        <a data-dd-add-altdomain href="#" class="btn btn-default"><i class="fa fa-plus"></i> {$DD_LANG.cert_add_alt}</a>
    </div>

    {/if}

    <div class="form-group text-right">
        <a href="{$links.index}" class='btn btn-default'>{$DD_LANG.cert_cancel}</a>
        <input type='submit' name='submit_button' id='settings_submit' class='btn btn-primary' value="Remitir" />
    </div>
</form>

{/if}

<div data-dd-back-btn class="form-group text-right" {if $can_reissue} style="display: none;" {/if}>
    <a href="{$links.index}" class='btn btn-default'>{$DD_LANG.back}</a>
</div>

<input data-dd-max-alt-domains type="hidden" value="{$certificate.sanMaxDomains}">
<input data-dd-domain-mail-url type="hidden" value="{$links.domain_mails}">

<div id="changemethod" class="modal" tabindex="-1" role="modal">
    <form data-dd-change-method-form action='{$links.changemethod}' method='post'>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content panel panel-primary">
                    <div class="modal-header panel-heading">
                        <h5 class="modal-title">{$DD_LANG.cert_change_method}</h5>
                    </div>
                    <div class="modal-body">
                        <div data-dd-mails-loading class="text-center" style="display: none;">
                            <i class="fas fa-lg fa-circle-notch fa-spin"></i>
                        </div>

                        <div data-dd-mails-error class="alert alert-danger" role="alert" style="display: none;">{$DD_LANG.invalid_common_name}</div>
                        
                        <div data-dd-mails class="form-group">
                            <label for="common_name">{$DD_LANG.cert_domain}</label>
                            <input data-dd-domain class="form-control" name="common_name" readonly />
                        </div>

                        <div data-dd-mails class="form-group">
                            <label for="validation_method">{$DD_LANG.cert_new_validation_method}</label>
                            <select data-dd-validation-method class="form-control" name="validation_method">
                                {html_options options=$validation_methods}
                                <optgroup data-dd-mail-validation-method label="{$DD_LANG.cert_mail}"></optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.close}</button>
                        <input data-dd-mails type='submit' name='submit_button' class='btn btn-primary' value="Cambiar" />
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
        checkMaxAltNames();

        $('[data-dd-add-altdomain]').click(function (e) {
            e.preventDefault();
            let altNameContainer = $('[data-dd-alt-name-container]');
            let altNameDivs = $('[data-dd-alt-name]');
            let altNameDiv = altNameDivs.first();
            let altNameCount = altNameDivs.length + 1;
            let clone = altNameDiv.clone();
            let defaultValTitle = $('[data-dd-default-val]').data('dd-default-val');

            if (altNameDiv.is(":hidden")) {
                altNameDiv.show();
                altNameDiv.find('input, select').attr('disabled', false);
                altNameDiv.find('[name="alt_name[]"]').val('');
                altNameDiv.find('[name="alt_validation[]"]').val('dns');
                altNameDiv.find('[data-dd-alt-name-method-title]').text(defaultValTitle)
                altNameDiv.find('[data-dd-close-altdomain]').show();
                return;
            }

            clone.find('[data-dd-alt-name-count]').text(altNameCount);
            clone.find('[data-dd-alt-name-domain]').attr('data-dd-alt-name-domain', altNameCount);
            clone.find('[data-dd-alt-name-method]').attr('data-dd-alt-name-method', altNameCount);
            clone.find('[data-dd-alt-name-change]').attr('data-dd-alt-name-change', altNameCount);
            clone.find('[data-dd-alt-name-method-title]').attr('data-dd-alt-name-method-title', altNameCount);
            clone.find('[name="alt_name[]"]').val('');
            clone.find('[name="alt_validation[]"]').val('dns');
            clone.find('[data-dd-alt-name-method-title]').text(defaultValTitle)

            altNameContainer.append(clone);
            
            checkMaxAltNames();
        });

        $('body').on('click', '[data-dd-close-altdomain]', function (e) {
            e.preventDefault();
            let altNameCount = $('[data-dd-alt-name]').length;
            let altNameDiv = $(this).parents('[data-dd-alt-name]');

            if (altNameCount === 1) {
                altNameDiv.hide()
                altNameDiv.find('input, select').attr('disabled', true);
                resetAltNamesKeys();
                return;
            }

            altNameDiv.remove();
            $('[data-dd-alt-name]').last().find('[data-dd-close-altdomain]').show();

            $('[data-dd-add-altdomain]').show();

            resetAltNamesKeys();
        });

        $('form[data-dd-change-method-form]').submit(function (e) {
            e.preventDefault();
            let validationMethod = $(this).find('[name="validation_method"]').val();
            let validationMethodText = $(this).find('[name="validation_method"] option:selected').text();
            let num = $(this).attr('data-dd-change-method-form');

            $('[data-dd-alt-name-method="' + num + '"]').val(validationMethod);
            $('[data-dd-alt-name-method-title="' + num + '"]').text(validationMethodText);
            $('[data-dd-alt-name-change="' + num + '"]').attr('data-dd-change-method', validationMethod);

            $(this).parents('.modal').modal('hide');
        });

        $('body').on('click', '[data-dd-alt-name-change]', function (e) {
            let num = $(this).data('dd-alt-name-change');
            let selectedMthod = $('[data-dd-alt-name-method="' + num + '"]').val();

            $('[data-dd-change-method-form]').attr('data-dd-change-method-form', num);
            $('form[data-dd-change-method-form]').find('[name="validation_method"]')
        });

        function resetAltNamesKeys() {

            $('[data-dd-alt-name]').each(function (key, element) {
                let altNameKey = key + 1;
                let altName = $(element);

                altName.find('[data-dd-alt-name-count]').text(altNameKey);
                altName.find('[data-dd-alt-name-domain]').attr('data-dd-alt-name-domain', altNameKey);
                altName.find('[data-dd-alt-name-method]').attr('data-dd-alt-name-method', altNameKey);
                altName.find('[data-dd-alt-name-change]').attr('data-dd-alt-name-change', altNameKey);
                altName.find('[data-dd-alt-name-method-title]').attr('data-dd-alt-name-method-title', altNameKey);
            });

        }

        function checkMaxAltNames() {
            let maxAlts = $('[data-dd-max-alt-domains]').val();
            let count = $('[data-dd-alt-name]').length + 1;

            if (count > maxAlts){
                $('[data-dd-add-altdomain]').hide();
            }
        }
    });
</script>
{/literal}