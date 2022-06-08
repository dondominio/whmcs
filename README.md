# Módulos de Integración WHMCS - DonDominio

Este proyecto contiene todos los módulos desarrollados entre DonDominio y WHMCS.

1. Módulo Addon [(¿Qué es un Módulo Addon? **(EN)**)](https://developers.whmcs.com/addon-modules/) 
2. Módulo de Registrador [(¿Qué es un Módulo de Registrador? **(EN)**)](https://docs.whmcs.com/Addon_Modules_Management)

**If you desire to read this document in English, please, visit [README-en](README-en.md)**

## Estado
| Version |
|:--------|
| 2.2.7   |

## Requerimientos
| Name      | Version       |
|:----------|:--------------|
| WHMCS     | >= 7.0        |
| PHP       | >= 7.4        |
| php-curl  | habilitado    |
| php-json  | habilitado    |
| php-zip   | habilitado    |

## Instalación
Este proyecto tiene más de un módulo de integración con WHMCS. Por favor, dirígase a la documentación para escoger qué módulos desea instalar.

1. **Instalación sencilla**.
La forma sencilla de instalación es, simplemente **arrastrar las carpetas `src/modules` y `src/includes` dentro de la carpeta raíz de WHMCS y aceptar todos los cambios.**
2. **Instalación personalizada**.
Si solo desea instalar un módulo en concreto, primero copie la carpeta `src/includes` en la carpeta raíz de WHMCS
y después copie la carpeta `src/modules/xxx/dondominio` en `/path/to/whmcs/modules/xxx` donde `xxx` es el tipo de módulo (addons, registrars... etc).
Por ejemplo, si solo desea instalar el módulo addon, la carpeta final sería `/path/to/whmcs/modules/addons/dondominio`.

Finalmente, si ha escogido la instalación sencilla o ha decidido instalar el módulo de registrador, debe realizar las siguientes instrucciones:

>**WHMCS 7.x y 8.x**
>
>Es necesario copiar el archivo `/path/to/whmcs/modules/registrars/dondominio/additionalfields.php` dentro de la carpeta `/path/to/whmcs/resources/domains`.
>
>Si este archivo ya existe, deberá cancelar la operación y añadir los campos manualmente siguiendo las instrucciones de
>[WHMCS Additional Domain Fields](https://docs.whmcs.com/Additional_Domain_Fields).
>
>Si no está seguro de como hacerlo, por favor, consulte con nuestro equipo de soporte.
>
>Así mismo, es posible que si instala módulos de otros registradores, estos campos dejen de funcionar.
>Por favor, asegúrese de que si instala otros módulos, éstos no interfieren con este archivo.
>
>Para disponer de las traducciones del módulo registrador será necesario copiar los siguientes archivos dentro de la carpeta `/path/to/whmcs/lang/overrides`:
>- `/path/to/whmcs/modules/registrars/dondominio/lang/overrides/spanish.php`
>- `/path/to/whmcs/modules/registrars/dondominio/lang/overrides/english.php`
>
>Si ya existen estos archivos se deberá copiar el contenido de las traducciones de DonDominio al final de los archivos ya existentes.

**\*Por favor, tenga en cuenta que todos los módulos comparten una biblioteca en común (DonDominio SDK) que está en la carpeta `src/includes/dondominio/sdk`.**

## Documentación
### 1. Módulo Addon
---

Este addon para WHMCS habilita el poder gestionar los dominios de tu cuenta de DonDominio directamente en el panel de WHMCS.

Importe sus dominios directamente desde DonDominio, realice cambios masivos, añada y configure TLDs, actualice automáticamente los precios y estados de dominios, y más.

**Características**
| Característica                | Descripción                                                                   |
|:------------------------------|:------------------------------------------------------------------------------|
| Gestionar extensiones         | Importar extensiones desde DonDominio para gestionar precios                  |
| Gestionar dominios            | Importar dominios a WHMCS desde DonDominio                                    |
| Transferir dominios           | Transferir dominios desde otros registradores a DonDominio                    |
| Cambiar contacto              | Actualizar contacto de dominios                                               |
| Whois                         | Usar DonDominio como herramienta WHOIS                                        |
| Lista de TLDs en seguimiento  | Habilitar notificaciones automáticas para cambios de precios en extensiones   |
| Sincronización                | Sincronización cada día con DonDominio                                        |
| Gestionar Certificados SSL    | Gestionar los certificados SSL relacionados con tu cuenta API de DonDominio   |

Para más información, documentación, soporte, y guías, visite [dev.dondominio.com/whmcs/docs/addon/](https://dev.dondominio.com/whmcs/docs/addon/)

### 2. Módulo de Registrador
---

Este es el Módulo de Registrador de DonDominio para WHMCS. Una vez instalado en cualquier WHMCS 7.x o 8.x,
permitirá registrar, renovar y transferir dominios, aparte de muchás utilidades más, usando la API de DonDominio.

**Características**
| Característica                        | Descripción                                                       |
|:--------------------------------------|:------------------------------------------------------------------|
| Herramienta para importar dominios    | Importar dominios desde DonDomio mediante herramienta CLI         |
| Registrar dominios                    | Registrar dominios directamente en DonDominio                     |
| Renovar dominios                      | Renovar dominios directamente en DonDominio                       |
| Transferir dominios                   | Transferir dominios desde otros registradores a DonDominio        |
| Obtener detalles de contacto          | Obtener detalles de contacto de dominio                           |
| Cambiar detalles de contacto          | Actualizar contacto de dominio                                    |
| Obtener bloqueo de dominio            | Obtener bloqueo de dominio                                        |
| Cambiar bloqueo de dominio            | Actualizar bloqueo de dominio                                     |
| Cambiar privacidad Whois de dominio   | Actualizar privacidad Whois de dominio                            |
| Cambiar protección ID                 | Actualizar protección ID de dominio                               |
| Obtener EPP Code (Auth code)          | Obtener EPP Code (Auth code) de dominio                           |
| Obtener nameservers                   | Obtener nameservers de dominio                                    |
| Cambiar nameservers                   | Actualizar nameservers de dominio                                 |
| Crear Glue Record                     | Crear nuevo Glue Record                                           |
| Cambiar Glue Record                   | Actualizar Glue Record existente                                  |
| Eliminar Glue Record                  | Eliminar Glue Record existente                                    |
| Sincronización de dominios            | Sincronizacón de estado de dominios                               |
| Sincronización de transferencias      | Sincronización de estado de transferencia de dominios             |
| Comprobar disponibilidad              | Comprobar si un dominio esta disponible                           |
| Obtener sugerencias de dominios       | Obtener sugerencias de dominios para recomendaciones de usuario   |

Para más información, documentación, soporte, y guías, visite [dev.dondominio.com/whmcs/docs/registrar/](https://dev.dondominio.com/whmcs/docs/registrar/)

### 3. Módulo de Aprovisionamiento
---

Este módulo ofrece la posibilidad vender Certificados SSL usando la API de DonDominio y su gestión en la parte de cliente y administración.

**Características**
| Característica                        | Descripción                                                                   |
|:--------------------------------------|:------------------------------------------------------------------------------|
| Creación de productos                 | Crear productos de WHMCS a partir de Certificados SSL de DonDominio           |
| Creación de certificados SSL          | Crear un certificado SSL al comprar un producto                               |
| Re-emitir certificado                 | Re-emitir certificados válidos                                                |
| Renovar certificado                   | Renovar certificados automáticamente                                          |
| Cambiar método de validación          | Cambiar el método de validación de los nombres alternativos de un certificado |
| Reenviar correo de validación         | Reenviar el correo de validación de los nombres alternativos de un certificado|

Para más información, documentación, soporte, y guías, visite [dev.dondominio.com/whmcs/docs/ssl/](https://dev.dondominio.com/whmcs/docs/ssl/)

## Changelog

[Ver Historial de cambios](CHANGELOG-es.md)
