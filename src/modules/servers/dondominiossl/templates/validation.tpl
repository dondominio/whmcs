<div data-dd-validation-view>
    <h4>{$DD_LANG.cert_validation_data}</h4>

    <table class="table table-condensed">
        {if $certificate.validationData.organizationValidationStatus}
            <tr>
                <td>
                    {$DD_LANG.cert_company_validation}
                </td>
                <td>
                    {$certificate.validationData.organizationValidationStatus}
                </td>
            </tr>
        {/if}
        {if $certificate.validationData.brandValidationStatus}
            <tr>
                <td>
                    {$DD_LANG.cert_brand_company_validation}
                </td>
                <td>
                    {$certificate.validationData.brandValidationStatus}
                </td>
            </tr>
        {/if}
        {if $certificate.validationData.message}
            <tr>
                <td>
                    {$DD_LANG.cert_msg_validation}
                </td>
                <td>
                    {$certificate.validationData.message}
                </td>
            </tr>
        {/if}
        {if isset($certificate.validationData.externalValidation)}
            <tr>
                <td>
                    {$DD_LANG.cert_external_validation}
                </td>
                <td>
                    {if $certificate.validationData.externalValidation}
                        {$DD_LANG.cert_need}
                    {else}
                        {$DD_LANG.cert_not_need}
                    {/if}
                </td>
            </tr>
        {/if}
    </table>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>{$DD_LANG.cert_domain}</th>
                <th>{$DD_LANG.cert_validation_method}</th>
                <th>{$DD_LANG.cert_validation_status}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $domains item=domain}
                <tr data-dd-domain="{$domain.domainName}" data-dd-domain-mails='{$domain.validationMails}'>
                    <td>
                        {$domain.domainName}
                    </td>
                    <td style="max-width: 620px">
                        <div style="margin-bottom: 1em">
                            {$domain.displayValidationMethod}
                            <div class="btn-group pull-right">
                                {if $can_change_validation and not $domain.validated}
                                    <a data-dd-modal data-dd-change-method="{$domain.method}" data-toggle="modal"
                                        data-target="#changemethod" href="#"
                                        class="btn btn-xs btn-primary">{$DD_LANG.cert_change}</a>
                                {/if}
                                {if not $domain.validated and $in_process and $domain.method eq 'mail'}
                                    <a data-dd-modal data-toggle="modal" data-target="#resendmail" href="#"
                                        class="btn btn-xs btn-primary">{$DD_LANG.cert_resend}</a>
                                {/if}
                                {if not $domain.validated and $in_process and ( $domain.method eq 'https' or $domain.method eq 'http' )}
                                    <a href="{$domain.download_http}" class="btn btn-xs btn-primary" download>
                                        <i class="fa fa-download"></i> {$DD_LANG.cert_download}
                                    </a>
                                {/if}
                            </div>
                        </div>
                        {if not $domain.validated}
                            <div>
                                {if $domain.method eq mail}
                                    {$DD_LANG.cert_validation_mail_send}: <span
                                        data-dd-domain-check-mail="{$domain.checkvalue}">{$domain.checkvalue}</span>
                                {elseif $domain.method eq dns}
                                    {$DD_LANG.cert_validation_create_cname}:
                                    <pre>{$domain.checkvalue}</pre>
                                {elseif $domain.method eq http or $domain.method eq https}
                                    {$DD_LANG.cert_validation_create_link} <a target="_blank"
                                        href="{$domain.checkvalue.link}">{$domain.checkvalue.link}</a>
                                    {$DD_LANG.cert_validation_with_content}:
                                    <pre>{$domain.checkvalue.contents}</pre>
                                {/if}
                            </div>
                        {/if}
                    </td>
                    <td class="text-center">
                        {if $domain.validated}
                            <i class="fas text-success fa-check"></i>
                        {else}
                            <i class="fas text-danger fa-times"></i>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>

    <div id="changemethod" class="modal" tabindex="-1" role="modal">
        <form data-form-dd-ssl action='{$links.changemethod}' method='post'>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content panel panel-primary">
                        <div class="modal-header panel-heading">
                            <h5 class="modal-title">{$DD_LANG.cert_change_method}</h5>
                        </div>
                        <div class="modal-body">
                            <div data-dd-mails-loading class="text-center" style="display: none;">
                                <i class="fas fa-lg fa-circle-notch fa-spin"></i>
                            </div>

                            <div data-dd-mails-error class="alert alert-danger" role="alert" style="display: none;">
                            </div>

                            <div data-dd-mails class="form-group">
                                <label for="common_name">{$DD_LANG.cert_domain}</label>
                                <input data-dd-domain class="form-control" name="common_name" readonly />
                            </div>

                            <div data-dd-mails class="form-group">
                                <label for="validation_method">{$DD_LANG.cert_new_validation_method}</label>
                                <select data-dd-validation-method class="form-control" name="validation_method">
                                    {html_options options=$validation_methods}
                                    <optgroup data-dd-mail-validation-method label="{$DD_LANG.cert_mail}"></optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.close}</button>
                            <input type='submit' name='submit_button' class='btn btn-primary' value="Cambiar" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="resendmail" class="modal" tabindex="-1" role="modal">
        <form data-form-dd-ssl action='{$links.resendmail}' method='post'>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content panel panel-primary">
                    <div class="modal-header panel-heading">
                        <h5 class="modal-title">{$DD_LANG.cert_resend_mail}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="common_name">{$DD_LANG.cert_domain}</label>
                            <input data-dd-domain class="form-control" name="common_name" id="common_name" readonly />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.close}</button>
                        <input type='submit' name='submit_button' id='settings_submit' class='btn btn-primary'
                            value="Reenviar" />
                    </div>
                </div>
            </div>
        </form>
    </div>

    {include file=$js}
</div>