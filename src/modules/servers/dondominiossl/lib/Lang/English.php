<?php

namespace WHMCS\Module\Server\Dondominiossl\Lang;


class English implements \WHMCS\Module\Server\Dondominiossl\Lang\Translations
{

    public function getTranslations(): array
    {
        return [
            'product_data' => 'Product dates',
            'cert_data' => 'Certificate Data',
            'cert_type' => 'Certificate type',
            'cert_id' => 'Certificate ID',
            'cert_status' => 'Condition',
            'cert_max_domains' => 'Maximum number of domains',
            'cert_creation' => 'Creation',
            'cert_expiration' => 'Expiration',
            'cert_company_validation' => 'Company validation status',
            'cert_brand_company_validation' => 'Company brand validation status',
            'cert_msg_validation' => 'Validation message',
            'cert_external_validation' => 'External validation',
            'cert_download' => 'Download',
            'cert_reissue' => 'Reissue',
            'cert_dcv' => 'Domain validation control',
            'cert_domain' => 'Domain',
            'cert_validation_method' => 'Validation method',
            'cert_validation_status' => 'Validation status',
            'cert_change_method' => 'Change method',
            'cert_new_validation_method' => 'New validation method',
            'cert_resend_mail' => 'Resend validation email',
            'cert_download_need_pass' => 'To generate this format a password is required',
            'cert_pass' => 'Password',
            'cert_org_name' => 'Organization name',
            'cert_org_unit' => 'Unit name',
            'cert_country' => '2-character country code',
            'cert_state' => 'Name of the province or state of the company',
            'cert_location' => 'Company town name',
            'cert_mail' => 'Mail',
            'cert_alt_name' => 'Alternative Domain',
            'cert_alt_validation_name' => 'Alternative Domain validation method',
            'cert_add_alt' => 'Add Alternate Domain',
            'cert_cancel' => 'Cancel',
        ];
    }
}
