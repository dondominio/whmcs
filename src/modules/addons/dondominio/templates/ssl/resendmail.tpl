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
                <input type="hidden" name="__a__" value="{$actions.resend_mail}">
                <input type="hidden" name="certificate_id" value="{$certificate.certificateID}">

                <div class="form-group">
                    <label for="common_name">{$LANG.ssl_certificate_common_name}</label>
                    <select class="form-control" name="common_name" id="common_name">
                        {html_options options=$domains}
                    </select>
                </div>

            </div>
        </div>
        <div class="panel-footer form-footer">
            <button action='submit' name='submit_button' id='settings_submit' class='btn btn-primary'>{$LANG.ssl_resend}</button>
            <a href='{$links.view_certificateinfo}' class='btn btn-default'>{$LANG.btn_back}</a>
        </div>
    </form>
    {else}
    <div class="panel-body">
        <div class="widget-content-padded text-center">
            <p>{$LANG.ssl_no_mail_validation}</p>
            <a href='{$links.view_change_validation_method}' class="btn btn-primary">{$LANG.ssl_change_validation_method}</a>
        </div>
    </div>
    <div class="panel-footer form-footer">
        <a href='{$links.view_certificateinfo}' class='btn btn-default'>{$LANG.btn_back}</a>
    </div>
    {/if}


</div>