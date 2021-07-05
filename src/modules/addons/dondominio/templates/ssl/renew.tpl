<div class="panel panel-default">

    <div class="panel-heading">
        <h3 class="panel-title domain-title">{$certificate.certificateID}</h3>
    </div>

    <form action='' method='post'>
        <div class="panel-body">
            <div class="widget-content-padded">
                <input type="hidden" name="module" value="{$module_name}">
                <input type="hidden" name="__c__" value="{$__c__}">
                <input type="hidden" name="__a__" value="{$actions.renew}">
                <input type="hidden" name="certificate_id" value="{$certificate.certificateID}">
                <div class="form-group">
                    <label for="common_name">{$LANG.ssl_certificate_common_name}</label>
                    <input type="text" class="form-control" name="common_name" id="common_name" value="{$certificate.commonName}">
                </div>

                <div class="form-group">
                    <label for="organization_name">{$LANG.ssl_organization_name}</label>
                    <input type="text" class="form-control" name="organization_name" id="organization_name" value="{$user.companyname}">
                </div>

                <div class="form-group">
                    <label for="organization_unit_name">{$LANG.ssl_organization_name}</label>
                    <input type="text" class="form-control" name="organization_unit_name" id="organization_unit_name" value="{$user.companyname}">
                </div>

                <div class="form-group">
                    <label for="country_name">{$LANG.ssl_country_name}</label>
                    <input type="text" class="form-control" name="country_name" id="country_name" value="{$user.countrycode}">
                </div>

                <div class="form-group">
                    <label for="state_or_province_name">{$LANG.ssl_state_or_province}</label>
                    <input type="text" class="form-control" name="state_or_province_name" id="state_or_province_name" value="{$user.state}">
                </div>

                <div class="form-group">
                    <label for="location_name">{$LANG.ssl_location_name}</label>
                    <input type="text" class="form-control" name="location_name" id="location_name" value="{$user.city}">
                </div>

                <div class="form-group">
                    <label for="email_address">{$LANG.ssl_email_address}</label>
                    <input type="text" class="form-control" name="email_address" id="email_address" value="{$user.email}">
                </div>

                <hr>

                <h4>{$LANG.ssl_contact_data}</h4>

                <div class="btn-group mb-10">
                    <button data-contact-info="id" class="btn btn-default active">
                        {$LANG.ssl_contact_id}
                    </button>
                    <button data-contact-info="data" class="btn btn-default">
                        {$LANG.ssl_contact_data}
                    </button>
                </div>

                <div data-contact-inputs="id">

                    <div class="form-group">
                        <label for="contact_id">{$LANG.ssl_contact_id}</label>
                        <input type="text" class="form-control" name="contact_id" id="contact_id" >
                    </div>

                </div>

                <div data-contact-inputs="data" style="display: none;">

                    <div class="form-group">
                        <label for="contact_type">{$LANG.ssl_contact_type}</label>
                        <select data-contact-type type="text" class="form-control" name="contact_type" id="contact_type" disabled="true">
                            {html_options options=$contact_types}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contact_first_name">{$LANG.ssl_contact_first_name}</label>
                        <input type="text" class="form-control" name="contact_first_name" id="contact_first_name" value="{$user.firstname}" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_last_name">{$LANG.ssl_contact_last_name}</label>
                        <input type="text" class="form-control" name="contact_last_name" id="contact_last_name" value="{$user.lastname}" disabled="true">
                    </div>

                    <div data-org-inputs class="form-group" style="display: none;">
                        <label for="contact_org_name">{$LANG.ssl_contact_org_name}</label>
                        <input type="text" class="form-control" name="contact_org_name" id="contact_org_name" value="{$user.companyname}" disabled="true">
                    </div>

                    <div data-org-inputs class="form-group" style="display: none;">
                        <label for="contact_org_type">{$LANG.ssl_contact_org_type}</label>
                        <input type="text" class="form-control" name="contact_org_type" id="contact_org_type" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_iden_num">{$LANG.ssl_contact_iden_num}</label>
                        <input type="text" class="form-control" name="contact_iden_num" id="contact_iden_num" value="{$vat_number}" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_email">{$LANG.ssl_contact_email}</label>
                        <input type="text" class="form-control" name="contact_email" id="contact_email" value="{$user.email}" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_phone">{$LANG.ssl_contact_phone}</label>
                        <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="{$user.phonenumberformatted}" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_fax">{$LANG.ssl_contact_fax}</label>
                        <input type="text" class="form-control" name="contact_fax" id="contact_fax" value="{$user.phonenumberformatted}" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_address">{$LANG.ssl_contact_address}</label>
                        <input type="text" class="form-control" name="contact_address" id="contact_address" value="{$address}" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_post_code">{$LANG.ssl_contact_post_code}</label>
                        <input type="text" class="form-control" name="contact_post_code" id="contact_post_code" value="{$user.postcode}" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_city">{$LANG.ssl_contact_city}</label>
                        <input type="text" class="form-control" name="contact_city" id="contact_city" value="{$user.city}" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_state">{$LANG.ssl_contact_state}</label>
                        <input type="text" class="form-control" name="contact_state" id="contact_state" value="{$user.state}" disabled="true">
                    </div>

                    <div class="form-group">
                        <label for="contact_country">{$LANG.ssl_contact_country}</label>
                        <input type="text" class="form-control" name="contact_country" id="contact_country" value="{$user.countrycode}" disabled="true">
                    </div>

                </div>

            </div>
        </div>
        <div class="panel-footer form-footer">
            <button action='submit' name='submit_button' id='settings_submit'
                class='btn btn-primary'>{$LANG.ssl_renew}</button>
            <a href='{$links.view_certificateinfo}' class='btn btn-default'>{$LANG.btn_back}</a>
        </div>
    </form>
</div>

{literal}
<script>
    $(document).ready(function () {

        $('[data-contact-info]').click(function (e) {
            e.preventDefault();
            let key = $(this).data('contact-info');

            $('[data-contact-info]').removeClass('active');
            $(this).addClass('active');

            $('[data-contact-inputs]').hide();
            $('[data-contact-inputs]').find('input, select').prop('disabled', true);
            $('[data-contact-inputs="' + key + '"]').slideDown();
            $('[data-contact-inputs="' + key + '"]').find('input, select').prop('disabled', false);
        })

        $('[data-contact-type]').click(function (e) {
            e.preventDefault();
            let val = $(this).val();

            if (val === 'organization'){
                $('[data-org-inputs]').show();
                return;
            }

            $('[data-org-inputs]').hide();
        })

    });
</script>
{/literal}