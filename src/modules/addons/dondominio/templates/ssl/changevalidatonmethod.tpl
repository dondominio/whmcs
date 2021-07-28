<div class="panel panel-default">

    <div class="panel-heading">
        <h3 class="panel-title domain-title">{$certificate.certificateID}</h3>
    </div>

    {if $domains|count gt 0}
        <form action='' method='post'>
            <div class="panel-body">
                <div class="widget-content-padded">
                    <input type="hidden" name="module" value="{$module_name}">
                    <input type="hidden" name="__c__" value="{$__c__}">
                    <input type="hidden" name="__a__" value="{$actions.change_validaton_method}">
                    <input type="hidden" name="certificate_id" value="{$certificate.certificateID}">

                    <div class="form-group">
                        <label for="common_name">{$LANG.ssl_certificate_common_name}</label>
                        <select data-dd-change-domain class="form-control" name="common_name" id="common_name">
                            {html_options options=$domains}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="validation_method">{$LANG.ssl_new_validation_name}</label>
                        <select class="form-control" name="validation_method" id="validation_method">
                            {html_options options=$validation_methods selected=dns}
                            <optgroup data-dd-validation-mails label="{$LANG.ssl_mail}">
                                {html_options options=$validation_mails}
                            </optgroup>
                        </select>
                    </div>

                </div>
            </div>
            <div class="panel-footer form-footer">
                <button action='submit' name='submit_button' id='settings_submit'
                    class='btn btn-primary'>{$LANG.ssl_change_method}</button>
                <a href='{$links.view_certificateinfo}' class='btn btn-default'>{$LANG.btn_back}</a>
            </div>
        </form>
    {else}
        <div class="panel-body">
            <p>{$LANG.ssl_no_change_method_domain_text}</p>
        </div>
        <div class="panel-footer form-footer">
            <a href='{$links.view_certificateinfo}' class='btn btn-default'>{$LANG.btn_back}</a>
        </div>
    {/if}
</div>

<input data-dd-validatio-mails-url type="hidden" value="{$links.domain_mails}" />

{literal}
    <script>
        $(document).ready(function() {

            $('[data-dd-change-domain]').on('change', function() {
                let domain = $(this).val();
                let url = $('[data-dd-validatio-mails-url]').val();

                $('[data-dd-validation-mails]').empty();

                $.ajax({
                    method: 'post',
                    url: url,
                    data: { common_name: domain },
                    dataType: 'json',
                    success: function(json) {
                        if (!json.success) {
                            return;
                        }

                        json.mails.forEach(function(element) {
                            let option = $('<option></option>');
                            option.val(element);
                            option.text(element);

                            option.appendTo('[data-dd-validation-mails]');
                        });
                    }
                });

            })

        });
    </script>
{/literal}