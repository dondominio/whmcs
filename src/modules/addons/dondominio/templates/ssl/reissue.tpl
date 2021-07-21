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

                    {if $alt_names|count gt 0}

                    {foreach from=$alt_names key=key item=name}
                    <div data-dd-alt-name class="panel panel-default" style="padding: 10px;">
                        <div class="text-right">
                            <a data-dd-close-altdomain href='#'><i class="fa fa-times"></i></a>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="alt_name[]">{$LANG.ssl_alt_name} <span data-dd-alt-name-count>{$key +
                                            1}</span></label>
                                    <input data-dd-alt-name-domain="{$key + 1}" type="text" class="form-control"
                                        name="alt_name[]" id="alt_name[]" value="{$name}">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label data-dd-alt-name-method-title="{$key + 1}"
                                        data-dd-default-val="{$LANG.ssl_validation_dns}"
                                        for="change_alt_validation[]">{$LANG.ssl_validation_dns}</label>
                                    <input data-dd-alt-name-change="{$key + 1}" data-dd-modal
                                        data-dd-change-method="dns" data-toggle="modal" data-target="#changemethod"
                                        type="button" id='change_alt_validation[]' name='change_alt_validation[]'
                                        class="form-control btn btn-primary" value="Cambiar" />
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
                                    <label for="alt_name[]">{$LANG.ssl_alt_name} <span
                                            data-dd-alt-name-count>1</span></label>
                                    <input data-dd-alt-name-domain="1" type="text" class="form-control"
                                        name="alt_name[]" id="alt_name[]" disabled="disabled">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label data-dd-alt-name-method-title="1"
                                        data-dd-default-val="{$LANG.ssl_validation_dns}"
                                        for="change_alt_validation[]">{$LANG.ssl_validation_dns}</label>
                                    <input data-dd-alt-name-change="1" data-dd-modal data-dd-change-method="dns"
                                        data-toggle="modal" data-target="#changemethod" type="button"
                                        id='change_alt_validation[]' name='change_alt_validation[]'
                                        class="form-control btn btn-primary" value="Cambiar" />
                                </div>

                                <input data-dd-alt-name-method="1" type="hidden" class="form-control"
                                    name="alt_validation[]" id="alt_validation[]" disabled="disabled">
                            </div>
                        </div>
                    </div>
                    {/if}
                </div>

                <div class="form-group text-center">
                    <a data-dd-add-altdomain href="#" class="btn btn-default"><i class="fa fa-plus"></i>
                        {$LANG.ssl_add_alt}</a>
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
<input data-dd-domain-mail-url type="hidden" value="{$links.domain_mails}">

<div id="changemethod" class="modal" tabindex="-1" role="modal">
    <form data-dd-change-method-form action='{$links.changemethod}' method='post'>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content panel panel-primary">
                    <div class="modal-header panel-heading">
                        <h5 class="modal-title">{$LANG.ssl_change_method}</h5>
                    </div>
                    <div class="modal-body">
                        <div data-dd-mails-loading class="text-center" style="display: none;">
                            <i class="fas fa-lg fa-circle-notch fa-spin"></i>
                        </div>

                        <div data-dd-mails-error class="alert alert-danger" role="alert" style="display: none;">
                            {$LANG.ssl_certificate_common_name}</div>

                        <div data-dd-mails class="form-group">
                            <label for="common_name">{$LANG.domains_domain}</label>
                            <input data-dd-domain class="form-control" name="common_name" readonly />
                        </div>

                        <div data-dd-mails class="form-group">
                            <label for="validation_method">{$LANG.ssl_new_validation_method}</label>
                            <select data-dd-validation-method class="form-control" name="validation_method">
                                {html_options options=$validation_methods}
                                <optgroup data-dd-mail-validation-method label="{$LANG.ssl_mail}"></optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.close}</button>
                        <input data-dd-mails type='submit' name='submit_button' class='btn btn-primary'
                            value="Cambiar" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

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

        $('body').on('click', '[data-dd-modal]', function (e) {
            let domainParent = $(this).parents('[data-dd-domain]');
            let mail = domainParent.find('[data-dd-domain-check-mail]').data('dd-domain-check-mail');
            let method = $(this).attr('data-dd-change-method');
            let url = $('[data-dd-domain-mail-url]').val();
            let domain = $(this).parents('[data-dd-alt-name]').find('[data-dd-alt-name-domain]').val();

            $('[data-dd-domain]').val(domain);
            $('[data-dd-mail-validation-method]').empty();

            $('[data-dd-mails-loading]').show();
            $('[data-dd-mails-error]').hide();
            $('[data-dd-mails]').hide();

            $.ajax({
                method: 'post',
                data: { common_name: domain },
                url: url,
                type: 'json',
                success: function (json) {
                    $('[data-dd-mails-loading]').hide();

                    if (json.success) {
                        $('[data-dd-mails]').show();

                        json.mails.forEach(function (element) {
                            let option = $('<option></option>');
                            option.val(element);
                            option.text(element);

                            option.appendTo('[data-dd-mail-validation-method]');
                        });

                        $('[data-dd-validation-method]').val(method);
                        return;
                    }

                    $('[data-dd-mails-error]').show();
                }
            });

        });

        function resetAltNamesKeys() {
            $('[data-dd-alt-name]').each(function (key, element) {
                let altNameKey = ++key;
                let altName = $(element);

                altName.find('[data-dd-alt-name-count]').text(altNameKey);
                altName.find('[data-dd-alt-name-domain]').attr('data-dd-alt-name-domain', altNameKey);
                altName.find('[data-dd-alt-name-method]').attr('data-dd-alt-name-method', altNameKey);
                altName.find('[data-dd-alt-name-change]').attr('data-dd-alt-name-change', altNameKey);
                altName.find('[data-dd-alt-name-method-title]').attr('data-dd-alt-name-method-title', altNameKey);
            })
        }

        function checkMaxAltNames() {
            let maxAlts = $('[data-dd-max-alt-domains]').val();
            let count = ++$('[data-dd-alt-name]').length;

            if (count > maxAlts) {
                $('[data-dd-add-altdomain]').hide();
            }
        }
    });
</script>
{/literal}