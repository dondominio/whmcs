<h3>Cambiar método de validación</h3>

<hr>

<div data-error="changevalidationmethod" class="alert alert-danger" role="alert" style="display: none;"></div>
<div data-success="changevalidationmethod" class="alert alert-success" role="alert" style="display: none;"></div>

<form data-form="changevalidationmethod" action='{$links.changemethod}' method='post'>
    <div class="widget-content-padded">
        <div class="form-group">
            <label for="common_name">Dominio</label>
            <select class="form-control" name="common_name" id="common_name">
                {html_options options=$domains}
            </select>
        </div>

        <div class="form-group">
            <label for="validation_method">Nuevo método de validación</label>
            <select class="form-control" name="validation_method" id="validation_method">
                {html_options options=$validation_methods}
            </select>
        </div>

    </div>
    <input type='submit' name='submit_button' id='settings_submit' class='btn btn-primary' value="Cambiar" />
</form>

{literal}
<script>
    $(document).ready(function () {
        $('[data-form="changevalidationmethod"]').submit(function (e) {
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
                    form.find('input, select').attr('disabled', true);
                    $('[data-error="changevalidationmethod"]').hide();
                },
                success: function (json) {

                    if (json.success){
                        $('[data-success="changevalidationmethod"]').text('Certificate domain method changed');
                        $('[data-success="changevalidationmethod"]').show();
                        form.hide();
                        return;
                    }

                    form.find('input, select').attr('disabled', false);
                    $('[data-error="changevalidationmethod"]').text(json.msg);
                    $('[data-error="changevalidationmethod"]').show();
                }
            });

        });
    });
</script>
{/literal}