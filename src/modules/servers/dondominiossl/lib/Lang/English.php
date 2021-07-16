<?php

namespace WHMCS\Module\Server\Dondominiossl\Lang;


class English extends \WHMCS\Module\Server\Dondominiossl\Lang\Base
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
            'cert_validation_data' => 'Certificate validation control',
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
            'cert_alt_names' => 'Alternatime Names',
            'cert_resend' => 'Resend',
            'cert_change' => 'Change',
            'cert_need' => 'Needed',
            'cert_not_need' => 'Not needed',
            'cert_validation_mail_send' => 'Validation email sent',
            'cert_validation_create_cname' => 'Create CNAME',
            'cert_validation_create_link' => 'Create link',
            'cert_validation_with_content' => 'with content',
            'cert_load_validation' => 'Load Validation Status',
            'cert_resend_mail_success' => 'Mail resend successfully',
            'cert_change_method_success' => 'Method changed successfully',
            'cert_reissue_success' => 'Certificate submitted successfully',
            'cert_download_fail' => 'Error getting file',
            'cert_validation_mail' => 'Email validation',
            'cert_validation_dns' => 'Validation using dns',
            'cert_validation_http' => 'Validation using http protocol',
            'cert_validation_https' => 'Validation using https protocol',
            'cert_status_process' => 'In progress',
            'cert_status_valid' => 'Valid',
            'cert_status_expired' => 'Expired',
            'cert_status_renew' => 'Renovation in process',
            'cert_status_reissue' => 'In process of reissue',
            'cert_status_cancel' => 'Cancelled',
            'cert_can_not_reissue' => 'This certificate cannot be reissued',
            'back' => 'Go Back',
        ];
    }
}
