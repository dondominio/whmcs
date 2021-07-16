<h3>{$DD_LANG.cert_reissue}</h3>

<hr>

<div data-error-dd-ssl class="alert alert-danger" role="alert" {if not $error_msg}style="display: none;" {/if}>
    {$error_msg}</div>
<div data-success-dd-ssl class="alert alert-success" role="alert" style="display: none;"></div>

{if $can_reissue}

<form data-form-dd-ssl action='{$links.action_reissue}' method='post'>
    <div class="form-group">
        <label for="common_name">{$DD_LANG.cert_domain}</label>
        <input type="text" class="form-control" name="common_name" id="common_name" value="{$certificate.commonName}" readonly>
    </div>

    <div class="form-group">
        <label for="validation_method">{$DD_LANG.cert_validation_method}</label>
        <select class="form-control" name="validation_method" id="validation_method">
            {html_options options=$validation_methods selected=dns}
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
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="alt_name[]">{$DD_LANG.cert_alt_name} <span data-dd-alt-name-domain>1</span></label>
                        <input type="text" class="form-control" name="alt_name[]" id="alt_name[]" disabled="disabled">
                    </div>
                    <div class="col-sm-6">
                        <label for="alt_validation[]">{$DD_LANG.cert_alt_validation_name} <span data-dd-alt-name-method>1</span></label>
                        <select class="form-control" name="alt_validation[]" id="alt_validation[]" disabled="disabled">
                            {html_options options=$validation_methods selected=dns}
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group text-center">
        <a data-dd-add-altdomain href="#" class="btn btn-default"><i class="fa fa-plus"></i>{$DD_LANG.cert_add_alt}</a>
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
            clone.find('[data-dd-alt-name-domain]').text(altNameCount)
            clone.find('[data-dd-alt-name-method]').text(altNameCount)
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