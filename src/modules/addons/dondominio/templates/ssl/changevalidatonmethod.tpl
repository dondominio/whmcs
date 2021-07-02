<div class="panel panel-default">

    <div class="panel-heading">
        <h3 class="panel-title domain-title">{$certificate.certificateID}</h3>
    </div>

    <form action='' method='post'>
        <div class="panel-body">
            <div class="widget-content-padded">
                <input type="hidden" name="module" value="{$module_name}">
                <input type="hidden" name="__c__" value="{$__c__}">
                <input type="hidden" name="__a__" value="{$actions.change_validaton_method}">
                <input type="hidden" name="certificate_id" value="{$certificate.certificateID}">

                <div class="form-group">
                    <label for="common_name">{$LANG.ssl_certificate_common_name}</label>
                    <select class="form-control" id="common_name">
                        {html_options options=$domains}
                    </select>
                </div>

                <div class="form-group">
                    <label for="validation_method">{$LANG.ssl_new_validation_name}</label>
                    <select class="form-control" id="validation_method">
                        {html_options options=$validation_methods}
                    </select>
                </div>

            </div>
        </div>
        <div class="panel-footer form-footer">
            <button action='submit' name='submit_button' id='settings_submit' class='btn btn-primary'>{$LANG.ssl_change_method}</button>
            <a href='{$links.tlds_index}' class='btn btn-default'>{$LANG.btn_back}</a>
        </div>
    </form>
</div>