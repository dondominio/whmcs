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
        <select data-dd-alt-name-method="0" class="form-control" name="validation_method" id="validation_method">
            {html_options options=$validation_methods selected=dns}
        </select>
    </div>

    <div class="form-group">
        <div class="text-center" data-dd-loading-mails style="display: none;">
            <i class="fas fa-lg fa-circle-notch fa-spin"></i>
        </div>
        <select data-dd-alt-name-mails="0" class="form-control" name="validation_mail" id="validation_mail"
            style="display: none;">
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
        <div data-dd-alt-name class="panel panel-default" style="display: none; padding: 10px;">
            <div class="text-right">
                <a data-dd-close-altdomain href='#'><i class="fa fa-times"></i></a>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="alt_name[]">{$DD_LANG.cert_alt_name} <span data-dd-alt-name-count>1</span></label>
                        <input data-dd-alt-name-domain="1" type="text" class="form-control" name="alt_name[]"
                            id="alt_name[]" disabled="disabled">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="alt_validation[]">{$DD_LANG.cert_alt_validation_name} <span
                                data-dd-alt-name-count>1</span></label>
                        <select data-dd-alt-name-method="1" class="form-control" name="alt_validation[]"
                            id="alt_validation[]" disabled="disabled">
                            {html_options options=$validation_methods selected=dns}
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="text-center" data-dd-loading-mails style="display: none;">
                            <i class="fas fa-lg fa-circle-notch fa-spin"></i>
                        </div>
                        <select data-dd-alt-name-mails="1" class="form-control" name="alt_validation_mail[]"
                            id="alt_validation_mail[]" disabled="disabled" style="display: none;">
                        </select>
                    </div>
                </div>
            </div>
        </div>
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
                altNameDiv.find('[data-dd-alt-name-domain]').attr('disabled', false);
                altNameDiv.find('[name="alt_name[]"]').val('');
                altNameDiv.find('[name="alt_validation[]"]').val('dns');
                altNameDiv.find('[data-dd-close-altdomain]').show();
                return;
            }

            altNameDivs.find('[data-dd-close-altdomain]').hide()
            clone.find('[data-dd-alt-name-count]').text(altNameCount);
            clone.find('[data-dd-alt-name-domain]').attr('data-dd-alt-name-domain', altNameCount);
            clone.find('[data-dd-alt-name-method]').attr('data-dd-alt-name-method', altNameCount);
            clone.find('[data-dd-alt-name-mails]').attr('data-dd-alt-name-mails', altNameCount);
            clone.find('[data-dd-alt-name-mails]').attr('disabled', true);
            clone.find('[data-dd-alt-name-method]').attr('disabled', true);
            clone.find('[name="alt_name[]"]').val('');
            clone.find('[name="alt_validation[]"]').val('dns');
            clone.find('[data-dd-close-altdomain]').show();
            clone.find('[data-dd-alt-name-mails]').hide();
            clone.find('[data-dd-alt-name-mails]').attr('disabled', true)

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

        $('body').on('change', '[data-dd-alt-name-method]', function (e) {
            let num = $(this).data('dd-alt-name-method');
            let method = $(this).val();
            let domain = $('[data-dd-alt-name-domain="' + num + '"]').val();
            let url = $('[data-dd-domain-mail-url]').val();
            let mailSelect = $('[data-dd-alt-name-mails="' + num + '"]');
            let container = mailSelect.parents('[data-dd-alt-name]');
            let loading = mailSelect.siblings('[data-dd-loading-mails]');
            let form = $('[data-form-dd-ssl]');
            let addBtn = $('[data-dd-add-altdomain]');

            if (method !== 'mail') {
                mailSelect.hide();
                mailSelect.attr('disabled', true)
                return;
            }

            loading.show();
            container.css('opacity', '0.5');
            addBtn.css('opacity', '0.5');
            addBtn.css('pointer-events', 'none');
            form.find('input, select').attr('disabled', true);

            $.ajax({
                method: 'post',
                data: { common_name: domain },
                url: url,
                type: 'json',
                success: function (json) {
                    mailSelect.empty();

                    if (json.mails) {
                        $.each(json.mails, function (key, val) {
                            let option = $('<option></option>');
                            option.val(val);
                            option.text(val);

                            mailSelect.append(option);
                        });
                    }

                    mailSelect.attr('disabled', false);
                    mailSelect.show();
                    loading.hide();
                    container.css('opacity', '1');
                    form.find('input, select').not('[data-dd-alt-name-mails]:hidden').attr('disabled', false);
                    addBtn.css('pointer-events', 'auto');
                    addBtn.css('opacity', '1');
                    
                    $('[data-dd-alt-name-domain]').each(function (key, element) {
                        let elementNum = $(element).data('dd-alt-name-domain');
                        let methodSelect = $('[data-dd-alt-name-method="' + elementNum + '"]');
                        methodSelect.attr('disabled', !isValidDomain($(element).val()));
                    });
                }
            });

        });

        $('body').on('input', '[data-dd-alt-name-domain]', function (e) {
            let domain = $(this).val();
            let num = $(this).data('dd-alt-name-domain');
            let methodSelect = $('[data-dd-alt-name-method="' + num + '"]');

            methodSelect.attr('disabled', !isValidDomain(domain));

            methodSelect.val('dns');
            methodSelect.trigger('change');
        });

        function isValidDomain(domain) {
            var re = new RegExp(/^((?:(?:(?:\w[\.\-\+]?)*)\w)+)((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$/);
            return domain.match(re);
        }
    });
</script>
{/literal}