{literal}
<script>
    $(document).ready(function () {
        $('[data-form-dd-ssl]').submit(function (e) {
            e.preventDefault();

            let form = $(this);
            let modal = form.parents('.modal');
            let url = form.attr('action');
            let data = form.serialize();
            let reloadValidation = $('[data-dd-load-validation]');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                    form.find('input, select').attr('disabled', true);
                    $('[data-error-dd-ssl]').hide();
                },
                success: function (json) {
                    modal.modal('hide');
                    form.find('input, select').attr('disabled', false);

                    if (reloadValidation.length > 0){
                        reloadValidation[0].click();
                    }

                    if (json.success) {
                        $('[data-success-dd-ssl]').text(json.msg);
                        $('[data-success-dd-ssl]').show();

                        if (modal.length < 1) {
                            form.hide();
                        }

                        return;
                    }

                    $('[data-error-dd-ssl]').text(json.msg);
                    $('[data-error-dd-ssl]').show();
                }
            });

        });
    });
</script>
{/literal}