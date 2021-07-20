<input data-dd-domain-mail-url type="hidden" value="{$links.domain_mails}">

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

                    if (reloadValidation.length > 0) {
                        reloadValidation[0].click();
                    }

                    if (json.success) {
                        $('[data-success-dd-ssl]').text(json.msg);
                        $('[data-success-dd-ssl]').show();
                        $('[data-dd-back-btn]').show();

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

        $('body').on('click', '[data-dd-modal]', function (e) {
            let domainParent = $(this).parents('[data-dd-domain]');
            let domain = domainParent.data('dd-domain');
            let mail = domainParent.find('[data-dd-domain-check-mail]').data('dd-domain-check-mail');
            let method = mail !== undefined && mail.length > 0 ? mail : $(this).attr('data-dd-change-method');
            let url = $('[data-dd-domain-mail-url]').val();

            if (domain === undefined) {
                domain = $(this).parents('[data-dd-alt-name]').find('[data-dd-alt-name-domain]').val();
            }

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

    });
</script>
{/literal}