<h3>{$DD_LANG.cert_title} {$certificate.commonName}</h3>

<hr>

<div data-error-dd-ssl class="alert alert-danger" role="alert" {if not $error_msg}style="display: none;" {/if}>
    {$error_msg}</div>
<div data-success-dd-ssl class="alert alert-success" role="alert" style="display: none;"></div>

<div class="row">
    <div class="col-md-6">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th colspan="2">
                        {$DD_LANG.product_data}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {$LANG.clientareahostingregdate}
                    </td>
                    <td>
                        {$regdate}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.orderproduct}
                    </td>
                    <td>
                        {$groupname} - {$product}
                    </td>
                </tr>

                {if $domain}
                <tr>
                    <td>
                        {$LANG.orderdomain}
                    </td>
                    <td>
                        {$domain}
                    </td>
                </tr>
                {/if}

                <tr>
                    <td>
                        {$LANG.orderpaymentmethod}
                    </td>
                    <td>
                        {$paymentmethod}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.firstpaymentamount}
                    </td>
                    <td>
                        {$firstpaymentamount}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.recurringamount}
                    </td>
                    <td>
                        {$recurringamount}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.clientareahostingnextduedate}
                    </td>
                    <td>
                        {$nextduedate}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.orderbillingcycle}
                    </td>
                    <td>
                        {$billingcycle}
                    </td>
                </tr>

                <tr>
                    <td>
                        {$LANG.clientareastatus}
                    </td>
                    <td>
                        {$status}
                    </td>
                </tr>

                {if $suspendreason}
                <tr>
                    <td>
                        {$LANG.suspendreason}
                    </td>
                    <td>
                        {$suspendreason}
                    </td>
                </tr>
                {/if}
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        {if not $product_is_pending}
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th colspan="2">
                        {$DD_LANG.cert_data}
                    </th>
                </tr>
            </thead>
            <tbody>
                {if $certificate.commonName}
                <tr>
                    <td>
                        Common Name
                    </td>
                    <td>
                        {$certificate.commonName}
                    </td>
                </tr>
                {/if}
                {if $dd_product_name}
                <tr>
                    <td>
                        {$DD_LANG.cert_type}
                    </td>
                    <td>
                        {$dd_product_name}
                    </td>
                </tr>
                {/if}
                {if $certificate.displayStatus}
                <tr>
                    <td>
                        {$DD_LANG.cert_status}
                    </td>
                    <td>
                        {$certificate.displayStatus}
                    </td>
                </tr>
                {/if}
                {if $certificate.numDomains}
                <tr>
                    <td>
                        {$DD_LANG.cert_max_domains}
                    </td>
                    <td>
                        {$certificate.numDomains}
                    </td>
                </tr>
                {/if}
                {if $certificate.alternativeNames|count gt 0}
                <tr>
                    <td>
                        {$DD_LANG.cert_alt_names}
                    </td>
                    <td>
                        {foreach from=$certificate.alternativeNames item=alt_name}
                        {if $alt_name neq $certificate.commonName}
                        <span style="display: block;">{$alt_name}</span>
                        {/if}
                        {/foreach}
                    </td>
                </tr>
                {/if}
                {if $certificate.tsCreate}
                <tr>
                    <td>
                        {$DD_LANG.cert_creation}
                    </td>
                    <td>
                        {$certificate.tsCreate}
                    </td>
                </tr>
                {/if}
                {if $certificate.tsExpir}
                <tr>
                    <td>
                        {$DD_LANG.cert_expiration}
                    </td>
                    <td>
                        {$certificate.tsExpir}
                    </td>
                </tr>
                {/if}
                {if $is_valid}
                <tr>
                    <td colspan="2">
                        <form data-dd-download-form action="{$links.download_crt}" method="POST"
                            style="display: inline-block;">
                            <input type="hidden" name="password" value="">
                            <input type="hidden" name="need_pass" value="">
                            <input type="hidden" name="type" value="zip">
                            <div class="btn-group">
                                <a data-dd-download-crt href="#" class="btn btn-primary"><i class="fa fa-download"></i>
                                    {$DD_LANG.cert_download}</a>
                                <a href="#" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span data-dd-download-text>ZIP</span>
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </a>
                                <ul class="dropdown-menu">
                                    {foreach from=$download_types key=type item=name}
                                    <li><a data-dd-download-type='{$type}'
                                            data-dd-download-type-need-pass="{$name.need_pass}" href="#"><i
                                                class="fa fa-download"></i> {$name.name}</a></li>
                                    {/foreach}
                                </ul>
                            </div>
                        </form>
                        <a href="{$links.viewreissue}" class="btn btn-primary">{$DD_LANG.cert_reissue}</a>
                    </td>
                </tr>
                {/if}
            </tbody>
        </table>
        {/if}
    </div>
</div>

<hr />

{if not $is_valid and not $product_is_pending}
<div class="text-center">
    <a data-dd-load-validation href="{$links.validation}" class="btn btn-success">
        <i data-dd-loading-validations class="fas fa-lg fa-circle-notch fa-spin" style="display: none;"></i>
        <span data-dd-loading-text>{$DD_LANG.cert_load_validation}</span>
    </a>
</div>

<div data-dd-validation-view></div>

<hr>
{/if}

<div class="row">
    <div class="col-sm-6 pull-right">
        <a href="clientarea.php?action=cancel&amp;id={$id}"
            class="btn btn-danger btn-block{if $pendingcancellation}disabled{/if}">
            {if $pendingcancellation}
            {$LANG.cancellationrequested}
            {else}
            {$LANG.cancel}
            {/if}
        </a>
    </div>
</div>

<div id="crtpassword" class="modal" tabindex="-1" role="modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content panel panel-primary">
            <div class="modal-header panel-heading">
                <h5 class="modal-title">{$DD_LANG.cert_download_need_pass}</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="password">{$DD_LANG.cert_pass}</label>
                    <input data-dd-download-pass type="password" class="form-control" name="password" id="password" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.close}</button>
                <a data-dd-download-crt class='btn btn-primary'>{$DD_LANG.cert_download}</a>
            </div>
        </div>
    </div>
</div>

{literal}
<script>
    $(document).ready(function () {

        $('[data-dd-load-validation]').click(function (e) {
            e.preventDefault();
            let url = $(this).attr('href');

            $('[data-dd-loading-validations]').show();
            $('[data-dd-loading-text]').hide();
            $('[data-dd-validation-view]').css('opacity', '0.5');
            $('[data-dd-validation-view]').css('pointer-events', 'none');

            $.ajax({
                method: 'post',
                url: url,
                dataType: 'html',
                success: function (response) {
                    let validationDiv = $(response).find('[data-dd-validation-view]');

                    $('[data-dd-validation-view]').replaceWith(validationDiv);

                    $('[data-dd-loading-validations]').hide();
                    $('[data-dd-loading-text]').show();
                }
            });
        })

        $('[data-dd-download-type]').click(function (e) {
            e.preventDefault();
            let text = $(this).text();
            let form = $('[data-dd-download-form]')
            let type = $(this).data('dd-download-type');
            let needPass = $(this).data('dd-download-type-need-pass');

            $('[data-dd-download-text]').text(text);
            form.find('[name="type"]').val(type);
            form.find('[name="need_pass"]').val(needPass);

            downloadCRT()
        });

        $('[data-dd-download-crt]').click(function (e) {
            e.preventDefault();
            downloadCRT()
        });

        $('[data-dd-download-pass]').on('change', function () {
            let pass = $(this).val();
            let form = $('[data-dd-download-form]');

            form.find('[name="password"]').val(pass);
        });

        $('#crtpassword').on('hidden.bs.modal', function () {
            $('[data-dd-download-pass]').val('');
            let form = $('[data-dd-download-form]');

            form.find('[name="password"]').val('');
        });

        function downloadCRT() {
            let form = $('[data-dd-download-form]');
            let needPass = form.find('[name="need_pass"]').val();
            let password = form.find('[name="password"]').val();

            if (needPass && password.length < 1) {
                $('#crtpassword').modal('show');
                return;
            }

            form.submit();
            $('#crtpassword').modal('hide');
            $('[data-dd-download-pass]').val('');
            $('[data-error-dd-ssl]').hide();
        }
    });
</script>
{/literal}