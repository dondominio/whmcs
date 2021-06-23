<?php

/**
 * The DonDominio Manager Addon for WHMCS.
 * Mod: English langfile
 * WHMCS version 5.2.x / 5.3.x
 * @link https://github.com/dondominio/dondominiowhmcsaddon
 * @package DonDominioWHMCSAddon
 * @license CC BY-ND 3.0 <http://creativecommons.org/licenses/by-nd/3.0/>
 */

$_ADDONLANG = array(
    //Buttons
    'btn_edit' => 'Edit',
    'btn_add' => 'Add',
    'btn_delete' => 'Delete',
    'btn_update_selected' => 'Update Selected',
    'btn_delete_selected' => 'Delete Selected',
    'btn_add_selected' => 'Add Selected',
    'btn_save' => 'Save changes',
    'btn_back' => 'Go back',
    'btn_transfer' => 'Transfer to DonDominio/MrDomain',

    //Info
    'info_with_selected' => 'With selected:',
    'info_no_results' => 'No Records Found',
    'errors_title' => 'Error found:',
    'info_too_much_requests' => 'Take into account that this feature may make a big amount of requests to the API, and thus take some time to complete. Depending on your server configuration you may run into timeout limits. To avoid these problems, limit the amount of domains being sent each time using the filters and selecting only the domains that need updates.',
    'succcess_title' => 'Operation was succesfully!',
    'unknown_error' => 'Unknown error.',
    'info_title' => 'The following messages has been ocurred',

    //Links
    'link_more_info' => 'More information',

    //Menu
    'menu_home' => 'Home',
    'menu_status' => 'Admin',
    'menu_tlds_update' => 'TLDs and Price',
    'menu_domains' => 'Manage domains',
    'menu_whois' => 'Whois Proxy',
    'menu_help' => 'Help & Documentation',
    'menu_ssl' => 'SSL Certificates',

    //Filter
    'filter_title' => 'Filter/Search',
    'filter_domain' => 'Domain Name',
    'filter_tld' => 'TLD',
    'filter_status' => 'Status',
    'filter_registrar' => 'Registrar',
    'filter_any' => 'Any',
    'filter_search' => 'Search',
    'filter_pending' => 'Pending',
    'filter_pending_transfer' => 'Pending Transfer',
    'filter_active' => 'Active',
    'filter_expired' => 'Expired',
    'filter_cancelled' => 'Cancelled',
    'filter_fraud' => 'Fraud',

    //Pagination
    'pagination_results_found' => 'Results found',
    'pagination_page' => 'Page',
    'pagination_of' => 'of',
    'pagination_go' => 'Go',
    'pagination_go_to' => 'Go to Page:',
    'pagination_previous' => 'Previous page',
    'pagination_next' => 'Next page',

    //TLDs
    'tld_title' => 'Price update',
    'tld_info' => 'These are the TLDs currently installed on your WHMCS. You can update their price information and switch them to autoregister using the DonDominio API, if you have the DonDominio Registrar Addon installed.',
    'tld_new_title' => 'Available TLDs',
    'tld_new_info' => 'These are the TLDs available to configure in your WHMCS installation. When you add them to WHMCS, they will be created with the current price information. This list is updated each time the Cron runs.',
    'tld_create_title' => 'Add new TLDs',
    'tld_tld' => 'TLD',
    'tld_registrar' => 'Registrar',
    'tld_no_selected' => 'No TLD selected',
    'tld_created_success_info' => 'The following TLDs have been added:',
    'tld_created_no_tlds' => 'No new TLDs have been added; all TLDs in sync',
    'tld_prices_success' => 'Domain prices updated correctly for the following extensions:',
    'tld_update_success' => 'The following TLDs have been updated:',
    'btn_prices_selected' => 'Update prices',
    'btn_registrar_selected' => 'Switch Registrar to DonDominio',
    'btn_reorder_selected' => 'Reorder the TLDs list',
    'btn_create_selected' => 'Add to WHMCS',
    'tld_register' => 'Register',
    'tld_transfer' => 'Transfer',
    'tld_renew' => 'Renew',
    'tld_not_available' => 'Not Available',
    'tld_regenerate' => 'Rebuild TLD cache',
    'sync_tlds' => 'Update TLDs',
    'sync_alert' => 'Warning: Enabling this option will cause prices in WHMCS to change.',
    'sync_message' => 'The synchronization of the TLDs will affect the prices of the current available TLDs.',
    'sync_success' => 'TLDs properly synced',
    'sync_wait' => 'This may take a few minutes.',

    //Domains
    'domains_title' => 'Manage',
    'domains_info' => 'These are the domains currently registered on your WHMCS installation. You can update their information using the DonDominio API and configure them to use the DonDominio Registrar addon, if installed. You can also update their contact information using a DonDominio Contact ID.',
    'domains_domain' => 'Domain',
    'domains_status' => 'Status',
    'domains_registrar' => 'Registrar',
    'domains_set_owner' => 'Update Owner Contact',
    'domains_set_admin' => 'Update Admin Contact',
    'domains_set_tech' => 'Update Tech Contact',
    'domains_set_billing' => 'Update Billing Contact',
    'domains_set_dondominio' => 'Switch registrar to DonDominio',
    'domains_contact_id' => 'DonDominio Contact ID',
    'domains_operation_complete' => 'Operation completed',
    'domains_no_domains_selected' => 'No domains selected',
    'domains_registrar_success' => 'Registrar switched successfully for the following domains',
    'domains_price_update_success' => 'The price for the following domains has been updated successfully',
    'domains_price_no_changes' => 'No changes were made to domains',
    'domains_contacts_error' => 'There was an error when updating the contacts',
    'domains_contacts_success' => 'The following domains have been updated correctly',
    'domains_error_dondominio_id' => 'You need to specify a DonDominio Contact ID to continue',
    'domains_requests' => 'Requests',
    'domains_success' => 'Success',
    'domains_errors' => 'Errors',
    'domains_sync' => 'Update information from DonDominio',
    'domains_price' => 'Update renewal price',
    'domains_sync_success' => 'Domains synced successfully',
    'domains_price_errors' => 'The following domains couldn\'t be updated',
    'domains_tld_price_not_found' => 'The price for the TLD could not be found',
    'domains_eur_not_found' => 'The currency Euro could not be found',
    'domains_tld_not_valid' => 'The TLD could not be found',
    'domain_synced_succesfully' => 'synced succesfully!',
    'domain_updated_succesfully' => 'updated successfully',
    'domain_not_found' => 'domain not found',
    'domain_name' => 'Name',
    'domain_status' => 'Status',
    'domain_tld' => 'TLD',
    'domain_ts_create' => 'Expiration date',
    'domain_ts_expire' => 'Creation date',
    'domain_transfer' => 'Transfer',
    'domain_more_info' => 'More information',

    //Import
    'import_title' => 'Import',
    'import_info' => 'These are the domains that exist in your DonDominio account. If a domain is not on your WHMCS installation, you may import its information and assign it to an existing customer.',
    'import_btn_import' => 'Import to WHMCS and assign to selected customer',
    'import_imported' => 'Imported',
    'import_not_imported' => 'Not Imported',
    'import_success' => 'The following domains have been imported:',
    'import_completed_not_imported' => 'The following domains were already in the database:',
    'import_error' => 'The following couldn\'t be imported because of an error:',

    //Transfer
    'transfer_title' => 'Transfer',
    'transfer_info' => 'Use this option to transfer domains to DonDominio/MrDomain from other registrars',
    'transfer_domain' => 'Domain name',
    'transfer_authcode' => 'Authcode/EPP',
    'transfer_authcode_required' => 'This domain extension requires an Authcode/EPP to transfer domains',
    'transfer_generic_error' => 'There was an error while starting the transfer',
    'transfer_domain_not_found' => 'The request domain could not be found on WHMCS',
    'transfer_invalid_domain_name' => 'This domain does not have a valid domain name',
    'transfer_tld_not_found' => 'This domain extension is not supported by DonDominio/MrDomain',
    'transfer_client_not_found' => 'The customer could not be found on WHMCS',
    'transfer_vatnumber_empty' => 'The customer\'s Vat Number has not been found (The Vat Number will be obtained from the custom field "Vat Number", if it is not found, it will be searched in the one selected by the Registrar of MrDomain)',
    'transfer_already_transferred' => 'This domain has been already transferred to DonDominio/MrDomain',
    'transfer_error' => 'There was an error while starting the transfer',
    'transfer_success' => 'Transfer has been initiated correctly',

    //Settings
    'settings_title' => 'Settings',
    'settings_prices_title' => 'Price adjustment',
    'settings_prices_register_add' => 'Registration increase',
    'settings_prices_transfer_add' => 'Transfer increase',
    'settings_prices_renew_add' => 'Renew increase',
    'settings_prices_type_fixed' => 'Fixed',
    'settings_prices_type_percent' => '%',
    'settings_prices_type_disabled' => 'Disabled (fixed price)',
    'settings_prices_update_cron' => 'Update prices on WHMCS when they change',
    'settings_prices_update_cron_info' => '<strong>Warning:</strong> Enabling this option will cause prices on your WHMCS to update automatically. Use with caution.',
    'settings_notifications_title' => 'Automatic notifications',
    'settings_notifications_enable' => 'Enable notifications',
    'settings_notifications_email' => 'Email for notifications',
    'settings_notifications_email_info' => 'Email address where notifications will be sent',
    'settings_notifications_select' => 'Enabled notifications',
    'settings_notifications_new_tld' => 'New TLD available',
    'settings_notifications_prices_updated' => 'Prices have been updated',
    'settings_save_success' => 'Settings saved successfully',
    'settings_api_title' => 'DonDominio API',
    'settings_api_username' => 'API Username',
    'settings_api_username_info' => 'Fill in your API Username for DonDominio',
    'settings_api_password' => 'API Password',
    'settings_api_password_info' => 'Fill in your API Password for DonDominio',
    'settings_api_required' => 'Before using the DonDominio WHMCS Addon you need to enter and save your API account details.',
    'settings_watch_ignore' => 'Watch/Ignorelist',
    'settings_watch_ignore_disable' => 'Do not use the Watch/Ignorelist',
    'settings_watch_ignore_watch' => 'Watch only these TLDs',
    'settings_watch_ignore_ignore' => 'Ignore these TLDs',
    'settings_watch_ignore_available' => 'Available TLDs to select',
    'settings_watch_ignore_active' => 'Chosen TLDs',
    'settings_cache_title' => 'Cache status',
    'settings_cache_last_update' => 'Last update',
    'settings_cache_total' => 'TLDs in cache',
    'settings_cache_rebuild' => 'Rebuild cache',
    'settings_cache_rebuild_info' => 'Check this box and click on "Save changes" to rebuild the TLD cache',
    'settings_whois_title' => 'Whois Proxy',
    'settings_whois_domain' => 'WHMCS Domain',
    'settings_whois_ip' => 'Allowed IP address',
    'settings_whois_ip_info' => 'Enter more than one IP address by separating them with ;',

    'tld_settings_title' => 'Individual TLD settings',
    'tld_settings_description' => 'Adjust the price increase for each TLD individually',
    'tld_settings_no_update' => 'Do not update automatically the prices for this TLD',
    'tld_settings_enabled' => 'Enable these settings',

    /**
     * CONFIG
     */
    'config_settings' => 'Change settings',
    'config_username' => 'API Username',
    'config_password' => 'API Password',
    'config_domain' => 'Access domain',
    'config_domain_info' => 'This is the domain name where your WHMCS frontend is hosted, con http:// o https://.',
    'config_ip' => 'Allowed IPs',
    'config_ip_info' => 'Only requests coming from these IPs will be allowed to access the Whois proxy. Separate IPs with ;.',
    'config_save' => 'Save settings',
    'config_cancel' => 'Cancel',
    'config_save_success' => 'Settings successfully saved',
    'config_save_error' => 'Settings couldn\'t be saved. Maybe you have a permissions problem?',
    'config_switch' => 'Switch to MrDomain/DonDominio',

    /**
     * NEW TLD
     */
    'new_tld' => 'Add a new TLD',
    'new_tld_tld' => 'TLD',
    'new_tld_add' => 'Add TLD',

    /**
     * INFO
     */
    'info_path_whois' => 'Yor Whois servers file is located here',
    'info_path_moreinfo' => 'Documentation',
    'info_whois_domain' => 'Before using the Whois Proxy, configure it.',

    /**
     * IMPORT/EXPORT
     */	
    'servers_export' => 'Export server list',
    'servers_import' => 'Import',
    'import_btn' => 'Import file',
    'servers_delete' => 'Delete MrDomain server list',

    /**
     * MESSAGES
     */
    'error_servers_no_writable' => 'Whois servers file is not writable by the server. Make it writable or edit it directly.',
    'error_whois_domain_empty' => 'Whois domain name is empty',
    'new-tld-error-permissions' => 'Couldn\'t access file, check permissions or edit the file directly',
    'new-tld-ok' => 'TLD updated successfully',
    'new-tld-error' => 'Empty TLD provided',
    'import-ok' => 'Whois Servers file imported correctly',
    'import-error' => 'The provided Whois Servers file is invalid or you don\'t have enough permissions to updated the file',
    'settings-ok' => 'Settings modified successfully',
    'settings-error' => 'Could not save settings',
    'currency_error' => 'A currency with the code EUR was not found',

    // 8.0
    'no_domain_provided' => 'no domain provided.',
    'was_already_imported' => 'was already imported.',
    'tld_already_exists' => 'TLD already exists.',
    'new-tld-not-found' => 'TLD not found.',
    'registrar_not_dondominio' => 'registrar is not mrdomain.',
    'file_not_uploaded' => 'File not uploaded correctly.',
    'tld_updated_succesfully' => 'TLD updated succesfully.',
    'prices_updated' => 'prices updated succesfully.',
    'domains_updated' => 'domains updated successfully.',
    'tld_added_succesfully' => 'TLD added succesfully.',
    'imported_successfully' => 'imported succesfully.',
    'cant_upgrade_dondominio' => 'Dondominio Addon upgrade was unsuccessfully',
    'create_order_error' => 'Error while creating order.',
    'no_customer_selected' => 'No customer selected',
    'created_by_whmcs_dondominio_addon' => 'Created by DonDominio WHMCS Addon',
    'whois_servers_deleted_ok' => 'WHOIS Servers from MrDomain have been deleted successfully',

     /**
     * MODULES DASHBOARD
     */
    'status_title' => 'Status',
    'going_back_in' => 'Going back in ',
    'modules_updated_successfully' => 'Modules updated succesfully.',
    'dondominio_modules_information' => 'Dondominio Modules Information',
    'new_version_available' => 'There is a new version available!',
    'update' => 'Update',
    'more_info' => 'More Info',
    'check_credentials' => 'API credentials',
    'check_api_status' => 'Check Status',
    'success_api_conection' => 'Correct connection with DonDominio API',
    'registrar_config_title' => 'Registrar Configuration',

    /**
     * GENERIC
     */
    'ok' => 'OK',
    'error' => 'ERROR',
    'close' => 'Close',
    'success_action' => 'Action successful',
    'error_action' => 'Setting update failed',
    'config' => 'Configure',
    'active' => 'Activate',
    'update' => 'Update',

    /**
     * UPDATE
     */
    'unable_to_retrieve_last_version' => 'Unable to retrieve latest modules version.',
    'latest_version_decoded_unsuccessfully' => 'Retrieving latest version was unsuccessfully decoded.',
    'unable_to_retrieve_local_version' => 'Unable to retrieve local version.',
    'local_version_is_empty' => 'Unable to retrieve local version. Version is empty.',
    'registrar_folder_not_found' => 'Registrar folder not found.',
    'registrar_file_not_found' => 'Registrar file not found',
    'no_extension_found_for_decompressing' => 'No extension found for decompressing.',
    'unable_to_retrieve_latest_json_info' => 'Unable to retrieve latest info (json).',
    'unable_to_download_latest_version' => 'Unable to download latest version',
    'couldnt_save_download' => 'Couldn\'t save download.',
    'unable_to_open_compressed_file' => 'Unable to open compressed file.',
    'unable_to_decompress' => 'Unable to decompress.',
    'decompress_method_not_found' => 'Decompress method not implemented.',
    'registrar_not_activated' => 'Registrar Module not activated.',
    'version' => 'Version',
    'whmcs_version' => 'WHMCS Version',
    'changelog_link' => 'https://github.com/dondominio/whmcs/blob/main/CHANGELOG-en.md',
    'new_version_changelog' => 'Check the changes of the new version',
    'sdk_status' => 'SDK Status',
    'api_connection_status' => 'API Connection Status',
    'modules_installed' => 'Modules Installed',
    'addon_module' => 'Addon Module',
    'registrar_module' => 'Registrar Module',

    /**
     * DELETED DOMAINS
     */
    'deleted_domains_title' => 'Deleted',
    'deleted_domains_ts' => 'Delete date',
    'deleted_domains_info' => 'Info',

    /**
     * PREMIUM DOMAINS
     */
    'premium_domains' => 'Premium Domains',
    'enable' => 'Enable',
    'disable' => 'Disabled',

    /**
     * WHOIS
     */
    'change_selected_whois' => 'Change selected to DonDominio',
    'whois_config_title' => 'Whois Config',
    'whois_import_title' => 'WHois Import',

    /**
     * DOMAIN HISTORY
     */
    'bradcrumbs_history_title' => 'History',
    'history_title' => 'Domain history',
    'log_date' => 'Date',
    'log_ip' => 'IP',
    'log_user' => 'User',
    'log_title' => 'Title',
    'log_message' => 'Message',

    /**
     * DOMAIN VIEW
     */
    'domain_name_view' => 'Domain name',
    'domain_tld_view' => 'TLD',
    'domain_register_view' => 'Registrar',
    'domain_status_view' => 'Status',
    'domain_expire_view' => 'Expiration date',
    'domain_create_view' => 'Creation date',
    'domain_verification' => 'Holder verification',
    'domain_nameservers' => 'DNS',
    'domain_api_check_view' => 'API Check',
    'domain_actions_view' => 'Actions',
    'domain_sync_view' => 'Sync status',
    'domain_check_view' => 'Check',
    'domain_history_view' => 'History',

    /**
     * BALANCE
     */
    'balance_title' => 'Balance',
    'balance_client_name' => 'Client name',
    'balance_threshold' => 'Warning threshold',
    'balance_currency' => 'Currency',

     /**
     * CONTACTS
     */
    'contacts_title' => 'Contacts',
    'contact_id' => 'ID',
    'contact_type' => 'Type',
    'contact_name' => 'Name',
    'contact_email' => 'Email',
    'contact_country' => 'Country',
    'contact_verification' => 'Verification',
    'contact_daaccepted' => 'Designated Agent ',
    'contact_da_accepted' => 'Accepted',
    'contact_da_no_accepted' => 'No accepted',
    'contact_ver_verified' => 'Verified',
    'contact_ver_inprocess' => 'In process',
    'contact_ver_notapplicable' => 'Not Applicable/Unnecessary',
    'contact_ver_failed' => 'Verification failed',
    'contact_type_organization' => 'Organization',
    'contact_type_individual' => 'Individual',
    'contact_type_phone' => 'Phone',
    'contact_type_fax' => 'Fax',
    'contact_type_address' => 'Address',
    'contact_type_postal_code' => 'Postal Code',
    'contact_type_city' => 'City',
    'contact_type_state' => 'State',
    'contact_type_country' => 'Country',
    'contact_resend' => 'Resend verification email',
    'contact_success_resend' => 'Verification email resend successfully',
    
    /**
     * SSL
     */
    'ssl_products' => 'SSL Products',
    'ssl_label_product_name' => 'Product Name',
    'ssl_label_product_multi_domain' => 'Multidomain',
    'ssl_label_product_wildcard' => 'Wildcard',
    'ssl_label_product_trial' => 'Is Trial',
    'ssl_label_product_imported' => 'Imported',
    'ssl_product_id' => 'ID',
    'ssl_product_name' => 'Name',
    'ssl_product_price_create' => 'Creation Price',
    'ssl_product_price_renew' => 'Renew Price',
    'ssl_sync_message' => 'Sync SSL',
    'ssl_sync_message' => 'Synchronize products with MrDomain\'s API.',
    'ssl_sync_success' => 'SSL products synced successfully.',
    'ssl_sync_wait' => 'This operation may take a few seconds.',
    'ssl_product_group' => 'Product group',
    'ssl_product_name' => 'Product name',
    'ssl_price_increment' => 'Creation price increase',
    'ssl_product_create_succesful' => 'Product created/edited successfully',

    /**
     * CONTENT TITLES
     */
    'content_title_admin' => 'DonDominio / Admin',
    'content_title_tld' => 'DonDominio / TLDs and Price',
    'content_title_domains' => 'DonDominio / Manage domains',
    'content_title_ssl' => 'DonDominio / Manage SSL Certificates',
    'content_title_whois' => 'DonDominio / Proxy Whois',

    /**
     * HOME
     */
    'home_new_version' => 'The new version of the MrDomain Module is now available',
    'home_go_update' => 'Go to Update',
    'home_api_error' => 'An error was logged in the last connection to the API',
    'home_check_credentials' => 'Check API credentials',
    'home_no_problems' => 'No issues detected!',
    'home_documentation' => 'Documentation',
    'home_check_changelog' => 'Check changes',
    'home_domains_dd' => 'Domains with MrDomain/DonDominio',
    'home_tlds_dd' => 'TLDs with MrDomain/DonDominio',
    'home_admin_tlds' => 'Manage TLDs',
    'home_admin_domains' => 'Manage Domains',
);