<h3>Reissue</h3>

<hr>

<div data-error="reissue" class="alert alert-danger" role="alert" style="display: none;"></div>
<div data-success="reissue" class="alert alert-success" role="alert" style="display: none;"></div>

<form data-form="reissue" action='{$links.action_reissue}' method='post'>
    <div class="form-group">
        <label for="common_name">Common Name</label>
        <input type="text" class="form-control" name="common_name" id="common_name" value="{$domain}">
    </div>

    <div class="form-group">
        <label for="organization_name">Nombre de organización</label>
        <input type="text" class="form-control" name="organization_name" id="organization_name"
            value="{$user.companyname}">
    </div>

    <div class="form-group">
        <label for="organization_unit_name">Nombre de organización</label>
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

    <div class="form-group text-right">
        <input type='submit' name='submit_button' id='settings_submit' class='btn btn-primary' value="Reissue" />
    </div>
</form>

{literal}
<script>
    $(document).ready(function () {
        $('[data-form="reissue"]').submit(function (e) {
            e.preventDefault();

            let form = $(this);
            let url = form.attr('action');
            let data = form.serialize();

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                    form.find('input').attr('disabled', true);
                    $('[data-error="reissue"]').hide();
                },
                success: function (json) {

                    if (json.success){
                        $('[data-success="reissue"]').text('Certificate Reissued');
                        $('[data-success="reissue"]').show();
                        form.hide();
                        return;
                    }

                    form.find('input').attr('disabled', false);
                    $('[data-error="reissue"]').text(json.msg);
                    $('[data-error="reissue"]').show();
                }
            });

        });
    });
</script>
{/literal}