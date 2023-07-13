# DonDominio - WHMCS Integration Modules

This project has all the developed integrations between MrDomain and WHMCS.

1. Addon Module [(What is WHMCS Addon Module?)](https://developers.whmcs.com/addon-modules/) 
2. Registrar Module [(What is WHMCS Registrar Module?)](https://docs.whmcs.com/Addon_Modules_Management)

## Status
| Version |
|:--------|
| 2.2.16   |

## Requirements
| Name      | Version   |
|:----------|:----------|
| WHMCS     | >= 7.0    |
| PHP       | >= 7.4    |
| php-curl  | enabled   |
| php-json  | enabled   |
| php-zip   | enabled   |

## Installation
This project consists in more than one integration module with WHMCS. Please, refer to the documentation to choose which module
you want to install.
1. **Easy Installation**.
The regular way to install it is, simply **drag and drop the `src/modules` folder and `src/includes` folder in WHMCS root folder and accept and merge all the changes**.
2. **Customized Installation**.
If you want to install one specific module, first copy `src/includes` folder into WHMCS root folder
and then copy `src/modules/xxx/dondominio` folder into `/path/to/whmcs/modules/xxx` where `xxx` is the module type (addons, registrars... etc).
For example, if you want to install only the addon module, the final folder will be `/path/to/whmcs/modules/addons/dondominio`.

Finally, if you chose the easy installation or decided to install the registrar module, you must follow the next instructions:

>**WHMCS 7.x and 8.x**
>
>You must copy the file `/path/to/whmcs/modules/registrars/dondominio/additionalfields.php` into `/path/to/whmcs/resources/domains` folder.
>
>If this file existed previously, you must cancel the operation and you must add the TLD fields manually
>following the instructions here: [WHMCS Additional Domain Fields](https://docs.whmcs.com/Additional_Domain_Fields).
>
>If you are not sure how to proceed, please, contact our support team.
>
>To have the translations of the recorder module, it will be necessary to copy the following files into the folder `/path/to/whmcs/lang/overrides`:
>- `/path/to/whmcs/modules/registrars/dondominio/lang/overrides/spanish.php`
>- `/path/to/whmcs/modules/registrars/dondominio/lang/overrides/english.php`
>
>If these files already exist, the content of DonDominio/MrDomain's translations must be copied to the end of the existing files.

**\*Please, note that all the modules share a common library (DonDominio SDK) that is `src/includes/dondominio/sdk` folder.**

## Documentation
### 1. Addon Module
---

This addon for WHMCS will enable you to manage your domains in your MrDomain account
directly within WHMCS's administration panel.

Import your domains from MrDomain, perform mass changes to them, add and configure TLDs,
automatically update prices and domain status, and more.

**Features**
| Name              | Description                                                       |
|:------------------|:------------------------------------------------------------------|
| Manage extensions | Import extensions from MrDomain to manage extensions prices       |
| Manage domains    | Import domains to WHMCS from MrDomain                             |
| Transfer domains  | Transfer domains from other registrars to MrDomain                |
| Update contact    | Update domain contact details                                     |
| Whois             | Use MrDomain as WHOIS tool                                        |
| Watchlist         | Enable automatic notifications for extensions changing prices     |
| Sync              | Synchronization with MrDomain every day                           |
| Manage SSL        | Manage the SSL certificates related to your MrDomain API account  |

For more information, documentation, support, and guides, visit [dev.mrdomain.com/whmcs/docs/addon/](https://dev.mrdomain.com/whmcs/docs/addon/)

### 2. Registrar Module
---

This is the WHMCS Registrar Module from MrDomain. Once installed on any WHMCS 7.x or 8.x system, it will allow to register, renew, and transfer domains using the MrDomain
API.

The module also provides support for managing domain contacts.

**Features**
| Name                      | Description                                           |
|:--------------------------|:------------------------------------------------------|
| Import domains Tool       | Import all MrDomain domains from CLI                  |
| Register domain           | Register domain directly into MrDomain                |
| Renew domain              | Renew domain directly into MrDomain                   |
| Transfer domain           | Transfer domain from other registrar into MrDomain    |
| Get contact details       | Retrieve domain contact details                       |
| Update contact details    | Update domain contact details                         |
| Get registrar lock        | Retrieve domain registrar lock                        |
| Update registrar lock     | Update domain registrar lock                          |
| Update whois privacy      | Update domain whois privacy                           |
| Update ID protection      | Update domain ID protection                           |
| Get EPP Code (Auth code)  | Retrieve domain EPP Code (Auth code)                  |
| Get Nameservers           | Retrieve domain nameservers                           |
| Update Nameservers        | Update domain nameservers                             |
| Create Glue Record        | Create new glue record                                |
| Update Glue Record        | Update existing glue record                           |
| Remove Glue Record        | Remove existing glue record                           |
| Sync                      | Synchronize domain status                             |
| Transfer Sync             | Synchronize domain transfer status                    |
| Check Availability        | Check if Domain is available                          |
| Get Domain Suggestions    | Get domain suggestions for user recommendations       |

For documentation, more information, support, and guides, visit: [dev.mrdomain.com/whmcs/docs/registrar/](dev.mrdomain.com/whmcs/docs/registrar/)

### 3. Provisioning Module
---

This module offers the possibility to sell SSL Certificates using the MrDomain API and its management in the client and administration part

**Features**
| Name                      | Description                                                           |
|:--------------------------|:----------------------------------------------------------------------|
| Product creation          | Create WHMCS products from DonDominio/MrDomain's SSL Certificates     |
| SSL certificate creation  | Create an SSL certificate when purchasing a product                   |
| Reissue certificate       | Reissue valid certificates                                            |
| Renew certificate         | Automatically renew certificates                                      |
| Change validation method  | Change the method of validating alternative names for a certificate   |
| Resend validation email   | Resend the validation mail of the alternative names of a certificate  |

For documentation, more information, support, and guides, visit: [dev.mrdomain.com/whmcs/docs/ssl/](dev.mrdomain.com/whmcs/docs/ssl/)

## Changelog

[Visit Changelog](CHANGELOG-en.md)
