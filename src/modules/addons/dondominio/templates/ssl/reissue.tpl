<div class="panel panel-default">

    <div class="panel-heading">
        <h3 class="panel-title domain-title">{$certificate.certificateID}</h3>
    </div>

    <form action='' method='post'>
        <div class="panel-body">
            <div class="widget-content-padded">
                <input type="hidden" name="module" value="{$module_name}">
                <input type="hidden" name="__c__" value="{$__c__}">
                <input type="hidden" name="__a__" value="{$actions.reissue}">
                <input type="hidden" name="certificate_id" value="{$certificate.certificateID}">
                <div class="form-group">
                    <label for="common_name">{$LANG.ssl_certificate_common_name}</label>
                    <input type="text" class="form-control" id="common_name" value="{$certificate.commonName}">
                </div>

                <div class="form-group">
                    <label for="organization_name">{$LANG.ssl_organization_name}</label>
                    <input type="text" class="form-control" id="organization_name" value="{$user.companyname}">
                </div>

                <div class="form-group">
                    <label for="organization_unit_name">{$LANG.ssl_organization_name}</label>
                    <input type="text" class="form-control" id="organization_unit_name" value="{$user.companyname}">
                </div>

                <div class="form-group">
                    <label for="country_name">{$LANG.ssl_country_name}</label>
                    <input type="text" class="form-control" id="country_name" value="{$user.countrycode}">
                </div>

                <div class="form-group">
                    <label for="state_or_province_name">{$LANG.ssl_state_or_province}</label>
                    <input type="text" class="form-control" id="state_or_province_name" value="{$user.state}">
                </div>

                <div class="form-group">
                    <label for="location_name">{$LANG.ssl_location_name}</label>
                    <input type="text" class="form-control" id="location_name" value="{$user.city}">
                </div>

                <div class="form-group">
                    <label for="email_address">{$LANG.ssl_email_address}</label>
                    <input type="text" class="form-control" id="email_address" value="{$user.email}">
                </div>

            </div>
        </div>
        <div class="panel-footer form-footer">
            <button action='submit' name='submit_button' id='settings_submit' class='btn btn-primary'>{$LANG.ssl_reissue}</button>
            <a href='{$links.tlds_index}' class='btn btn-default'>{$LANG.btn_back}</a>
        </div>
    </form>
</div>