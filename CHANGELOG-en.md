# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.1] - 2020-02-26
### Fixed
- Database consistency with non-nullable fields.

## [2.1.0] - 2020-02-23
### Changed
- New Updater system. Update system now includes backups, robust permissions checking, rollback, etc

## [2.0.5] - 2020-02-22
### Fixed
- Transfer sync fixed. [More Info](https://developers.whmcs.com/domain-registrars/domain-syncing/)

## [2.0.4] - 2020-02-22
### Fixed
- The module updater now does a more extensive permission check before updating.

## [2.0.3] - 2020-02-19
### Fixed
- Fixed typo in parsing owner contact data. Now import & transfer domains works as expected.

## [2.0.2] - 2020-02-19
### Fixed
- Added prevention against tables with non-recommended collations. [See WHMCS Database Collations](https://docs.whmcs.com/Database_Collations)

## [2.0.1] - 2020-02-11
This release is based on the proposed changes in [WHMCS 8 Upgrade Docs](https://developers.whmcs.com/advanced/upgrade-to-whmcs-8/) and fixes the Laravel upgrade issues (`plug`and `get` methods).

### Fixed
- Registrar Module: Selector for VAT Number in Configuration now works correctly.
- Addon Module: TLD Filter in Domains Management now works correctly.

## [2.0.0] - 2020-01-31
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