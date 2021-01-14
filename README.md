# DonDominio - WHMCS Integration Modules

This project has all the developed integrations between DonDominio and WHMCS.

1. Addon Module [(What is WHMCS Addon Module?)](https://developers.whmcs.com/addon-modules/) 
2. Registrar Module [(What is WHMCS Registrar Module?)](https://docs.whmcs.com/Addon_Modules_Management)

## Status
| Version |
|:-----|
| 2.0.0-beta |

**\*THIS PROJECT IS STILL UNDER DEVELOPMENT, IF YOU CONSIDER TO USE THIS PLEASE REFER TO [WHMCS ADDON](https://github.com/dondominio/whmcs-addon)
AND [WHMCS PLUGIN](https://github.com/dondominio/whmcs-plugin)**

## Requirements
| Name | Version |
|:----|:-------|
| WHMCS | >= 5.0 |
| PHP | >= 5.6 |
| php-curl | enabled |
| php-json | enabled |


## Installation
This project consists in more than one integration module with WHMCS. Please, refer to the index to choose which module
you want to install.<br>
1. **Easy Installation**.
The regular way to install it is, simply **drag and drop the `src` folder in WHMCS root folder**.<br>
2. **Customized Installation**.
If you want to install one specific module, first copy `src/includes` into `includes` folder
and then copy `src/modules/xxx/dondominio/` folder into `modules/xxx/` where `xxx` is the module type.

Finally, if you decided to install registrar module, you must edit the `/includes/additionaldomainfields.php` file 
included on your WHMCS 6 installation, or the `/resources/domains/dist.additionalfields.php` file included in your WHMCS 7 installation.

At the end of this file you need to add this line:
```
include(ROOTDIR . "/modules/registrars/dondominio/additionaldomainfields.php");
```

**\*Please, note that all the modules share a common library (DonDominio API) that is `src/includes/dondominio-sdk/` folder.**

## Documentation
### 1. Addon Module
---

This addon for WHMCS will enable you to manage your domains in your DonDominio/MrDomain account
directly within WHMCS's administration panel.

Import your domains from DonDominio/MrDomain, perform mass changes to them, add and configure TLDs,
automatically update prices and domain status, and more.

**Features**
| Name | Description |
|:-------|:-----------|
| Manage extensions | Import extensions from DonDomain to manage extensions prices |
| Manage domains | Import domains to WHMCS from DonDominio |
| Transfer domains | Transfer domains from other registrars to DonDominio |
| Update contact | Update domain contact details |
| Whois | Use DonDominio as WHOIS tool |
| Watchlist | Enable automatic notifications for extensions changing prices |
| Sync | Synchronization with DonDominio every day |

For more information, documentation, support, and guides, visit:

**(EN)** [dev.mrdomain.com/whmcs/docs/addon/](https://dev.mrdomain.com/whmcs/docs/addon/).<br>
**(ES)** [dev.dondominio.com/whmcs/docs/addon/](https://dev.dondominio.com/whmcs/docs/addon/).

### 2. Registrar Module
---

This is the WHMCS Registrar Plugin from DonDominio/MrDomain. Once installed on any WHMCS 5.x,
6.x, or 7.x system, it will allow to register, renew, and transfer domains using the DonDominio/MrDomain
API.

The plugin also provides support for managing domain contacts.

**Features**
| Name | Description |
|:-------|:-----------|
| Import domains Tool | Import all DonDominio domains from CLI |
| Register domain | Register domain directly into DonDominio |
| Renew domain | Renew domain directly into DonDominio |
| Transfer domain | Transfer domain from other registrar into DonDominio |
| Get contact details | Retrieve domain contact details |
| Update contact details | Update domain contact details |
| Get registrar lock | Retrieve domain registrar lock |
| Update registrar lock | Update domain registrar lock |
| Update whois privacy | Update domain whois privacy |
| Update ID protection | Update domain ID protection |
| Get EPP Code (Auth code) | Retrieve domain EPP Code (Auth code) |
| Get Nameservers | Retrieve domain nameservers |
| Update Nameservers | Update domain nameservers |
| Create Glue Record | Create new glue record |
| Update Glue Record | Update existing glue record |
| Remove Glue Record | Remove existing glue record |
| Sync | Synchronize domain status |
| Transfer Sync | Synchronize domain transfer status |
| Check Availability | Check if Domain is available |
| Get Domain Suggestions | Get domain suggestions for user recommendations |

For documentation, more information, support, and guides, visit:

**(EN)** [dev.mrdomain.com/api/docs/sdk-php/](https://dev.mrdomain.com/api/docs/sdk-php/).<br>
**(ES)** [dev.dondominio.com/whmcs/docs/plugin](https://dev.dondominio.com/whmcs/docs/plugin/).
