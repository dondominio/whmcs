# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.6] - 2021-07-06
### Fixed
- Fix administration section URLs.
- Fix status change of Premium Domains.

## [2.1.5] - 2021-06-1
### Changed
- Do not show errors if the request to check for new Registrar/Addon updates fails.
  
### Fixed
- Fix error when updating domain prices if the currency code 'EUR' is duplicated.

## [2.1.4] - 2021-04-22
### Changed
- Show available balance of API user.
- Table with API user contacts.
- Show more information when querying a domain.
- To be able to activate / deactivate the premium domains from the Addon.
- View for API user contacts.
- Be able to forward the verification email to a contact.
- Improved navigation within the Addon..
- New Home page.
- To be able to activate and configure the Registrar within the Addon.
- Widget in WHMCS dashboard for update Addon/Registrar and access to Addon.
- Popup in WHMCS dashboard for update Addon/Registrar.
- To be able to synchronize the available TLDs and their pressure within WHMCS from the Addon.
- Filters for the list of Price update.
  
### Fixed
- Stay filters in Admin Domain lists pagination.

## [2.1.3] - 2021-04-06
### Changed
- The registrar supports premium domains.
- Optimization in the query of new versions of the module.
- Interactive query of the connection with the API.
- Improved navigation within the Addon.
- List of deleted domains.
- Filters for importing domains.
- History of imported domains.
- View for MrDomain/DonDominio domains.
- To be able to transfer domains to MrDomain/DonDominio directly from the Domain Management.
- If the custom field "Vat Number" is not found for the transfer of domains within the Addon, the one selected by the Registrar will be used.

## [2.1.2] - 2021-03-17
### Fixed
- Now tables configured with row_format compact works as expected.
- Operations with domains that involve Vat Number, now works correctly.

### Changed
- More specific domain states.
- More information in Addon status tab.

## [2.1.1] - 2021-02-26
### Fixed
- Database consistency with non-nullable fields.

## [2.1.0] - 2021-02-23
### Changed
- New Updater system. Update system now includes backups, robust permissions checking, rollback, etc

## [2.0.5] - 2021-02-22
### Fixed
- Transfer sync fixed. [More Info](https://developers.whmcs.com/domain-registrars/domain-syncing/)

## [2.0.4] - 2021-02-22
### Fixed
- The module updater now does a more extensive permission check before updating.

## [2.0.3] - 2021-02-19
### Fixed
- Fixed typo in parsing owner contact data. Now import & transfer domains works as expected.

## [2.0.2] - 2021-02-19
### Fixed
- Added prevention against tables with non-recommended collations. [See WHMCS Database Collations](https://docs.whmcs.com/Database_Collations)

## [2.0.1] - 2021-02-11
This release is based on the proposed changes in [WHMCS 8 Upgrade Docs](https://developers.whmcs.com/advanced/upgrade-to-whmcs-8/) and fixes the Laravel upgrade issues (`plug`and `get` methods).

### Fixed
- Registrar Module: Selector for VAT Number in Configuration now works correctly.
- Addon Module: TLD Filter in Domains Management now works correctly.

## [2.0.0] - 2021-01-31
This is a major release of the Dondominio - WHMCS Modules Integration. We have analyzed and verified all the functionality from scratch to make a better, 
faster, and more maintainable product. We have developed all the functionalities in order to make it 100% compatible with WHMCS 7 and 8 as well as we updated it with the new good practices that WHMCS 7 developer guide introduces.

From Dondominio Team, we are very proud to announce Dondominio - WHMCS Modules Integration 2.0.

### Added
- New project structure (from scratch) with namespaces.
- Easier to install and configure.
- Addon Module: New dashboard.
- Addon Module: Update Check & Download Latest Updates.
- Addon Module: Now checks API user/password in Registrar Module.
- Addon Module: Now updates API user/password in Registrar Module.
- Registrar Module: now tries to find Addon Module API username/password.
- Dev: added developer tools (deploy and tests).

### Fixed
- Addon Module: Collation bug.
- Addon Module: Slow loading of Whois Proxy tab.
- Addon Module, Registrar Module: Removed all the deprecated `mysql_` functions.
- Addon Module, Registrar Module: Removed all the deprecated `select_query`, `update_query`, `insert_query`, `full_query` functions.

### Changed
- SDK: Unified in one place (`/includes` folder).
- SDK: Updated to 2.0.0.
- Addon Module: WHOIS System updated to WHMCS 7.0 good practices. [Check it](https://docs.whmcs.com/WHOIS_Servers)
- Registrar Module: Suggestion Domains changed from Addon Module to Registrar Module in order to meet good practices. [Check it](https://docs.whmcs.com/Domain_Suggestions)
- Additional Fields: the way it is installed has been changed to do it in a simpler and more standard way. [Check it](https://docs.whmcs.com/Additional_Domain_Fields)