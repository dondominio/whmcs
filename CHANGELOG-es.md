# Changelog
Todos los cambios importantes de este proyecto serán documentados en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto se adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [Pendiente por lanzar]

## [2.2.6] - 2022-05-10
### fixed
- Consulta para obtener información extra del dominio.
- Consulta para obtener campos personalizados por email.

## [2.2.5] - 2022-01-28
### fixed
- Consulta para obtener precios de TLDs.

## [2.2.4] - 2022-01-25
### Changed
- Eliminar los campos adicionales para los dominios `.law` y `.abogado` al ya no ser necesarios.

### fixed
- Bug en sistema de actualización.

## [2.2.3] - 2021-11-02
### fixed
- OwnerContactType para clientes no Españoles.

## [2.2.2] - 2021-09-07
### Changed
- Actualizar additional fields para módulo registrador.
- Añadir traducciones para additional fields de `.es`.
- Utilizar la ruta temporal de actualización de WHMCS para actualizar los módulos.
  
## [2.2.1] - 2021-09-06
### Fixed
- Fix detectar Tipo de Contacto según su Número de Identificación.

## [2.2.0] - 2021-07-29
### Añadido
- Nuevo Módulo de Aprovisionamiento para la creación de productos dentro de WHMCS a partir de productos SSL de DonDominio.
- Nuevo apartado para la gestión de los Certificados y Productos SSL.
- Listado de certificados SSL relacionados con tu usuario API.
- Listado de productos disponibles de la API de DonDominio.
- Listado de productos importados de DonDominio a WHMCS.
- Sincronización diaria de los productos de WHMCS con los productos de DonDominio.
- Vista con la información detallada de un Certificado SSL.
- Formulario para la creación de productos de WHMCS a partir de un Producto SSL de DonDominio.
- Vista de la información de un certificado desde la parte de cliente.
- Cambio de método validación de nombres alternativos del certificado desde la parte de cliente y administración.
- Reenvío de email de validación de nombre alternativo desde la parte de cliente y administración.
- Formulario para re-emitir un certificado desde la parte de cliente y administración.
- Descarga de certificado en distintos formatos en la parte de cliente.
- Popup en dashboard para avisar de que la instalación del módulo no está terminada.

## [2.1.7] - 2021-07-07
### Changed
- Adaptar sistema de actualizaciones para poder añadir módulos de DonDominio.

## [2.1.6] - 2021-07-06
### Fixed
- Fix URLs de apartado de administración.
- Fix cambio de estado de Dominios Prémium.

## [2.1.5] - 2021-06-1
### Changed
- No mostrar errores si falla la petición para comprobar nuevas actualizaciones del Registrador/Addon.
  
### Fixed
- Corregir error al actualizar precios de dominio si el código de moneda 'EUR' está duplicado.

## [2.1.4] - 2021-04-22
### Changed
- Mostrar el saldo disponible del usuario API.
- Tabla con los contactos del usuario API.
- Mostrar más información en la consulta de un dominio.
- Poder activar/desactivar los dominios prémium desde el Addon.
- Vista para los contactos del usuario API.
- Poder reenviar el correo de verificación a un contacto.
- Mejora en la navegación dentro del Addon.
- Nueva pantalla de inicio.
- Poder activar y configurar el Registrador dentro del Addon.
- Widget en dashboard de WHMCS para actualizar el Registrador/Addon y para acceder al Addon.
- Popup en el dashboard de WHMCS que avisa de una nueva versión.
- Poder sincronizar los TLDs disponibles y sus presión dentro de WHMCS desde el Addon.
- Filtros para la lista de Actualizar tarifas.
  
### Fixed
- Permanecer filtros al paginar en listas dentro del apartado de gestión de Dominios.

## [2.1.3] - 2021-04-06
### Changed
- El registrador admite dominios premium.
- Optimización en la consulta de nuevas versiones del módulo.
- Consulta interactiva de la conexión con la API.
- Mejora en la navegación dentro del Addon.
- Listado de dominios borrados.
- Filtros para la importación de dominios.
- Historial de los dominios importados.
- Vista para los dominios de DonDominio.
- Poder transferir dominios a DonDominio directamente desde la Gestión de dominios.
- Si no se encuentra el campo personalizado "Vat Number" para la transferencia de dominios dentro del Addon se usara el seleccionado por el Registrador

## [2.1.2] - 2021-03-17
### Fixed
- Ahora las tablas configuradas con row_format compact funcionan correctamente.
- Las operaciones relacionadas con Vat Number funcionan correctamente.

### Changed
- Estados de dominio más específicos.
- Más información en la pestaña de estado del Addon.

## [2.1.1] - 2021-02-26
### Arreglado
- Consistencia en base de datos con campos no nulables.

## [2.1.0] - 2021-02-23
### Cambiado
- Nuevo sistema de actualizaciones. Ahora es más robusto, incluyendo copias de seguridad, comprobación de permisos, rollbacks, etc.

## [2.0.5] - 2021-02-22
### Arreglado
- Sincronización de transferencias de dominios arreglada. [Más Info](https://developers.whmcs.com/domain-registrars/domain-syncing/)

## [2.0.4] - 2021-02-22
### Arreglado
- El actualizador de módulos ahora hace una comprobación más exhaustiva de permisos antes de actualizar.

## [2.0.3] - 2021-02-19
### Arreglado
- Arreglado typo al parsear los datos de contacto. Ahora importar y transferir dominios funciona como se espera.

## [2.0.2] - 2021-02-19
### Arreglado
- Añadida prevención contra tablas con colaciones no recomendadas. [Ver WHMCS Database Collations](https://docs.whmcs.com/Database_Collations)

## [2.0.1] - 2021-02-11
Esta versión está basada en los cambios propuestos de [documentación de actualización a WHMCS8](https://developers.whmcs.com/advanced/upgrade-to-whmcs-8/) y arregla los problemas relacionados con la actualización de Laravel (métodos `plug` y `get`).

### Arreglado
- Módulo Registrador: Selector para VAT Number en Configuración ahora funciona correctamente.
- Módulo Addon: Filtro de TLDS en Gestión de Dominios ahora funciona correctamente.

## [2.0.0] - 2021-01-31
Este es un lanzamiento de cambio de versión mayor del proyecto Integración de Módulos Dondominio - WHMCS. Hemos analizado y verificado toda la funcionalidad desde cero para crear un producto mejor, más rápido y más mantenible. Hemos desarrollado todas las funcionalidades para que sean 100% compatible con WHMCS 7 y 8, así como lo hemos actualizado con las nuevas buenas prácticas que introduce la guía del desarrollador de WHMCS 7.

Desde el equipo de Dondominio, estamos muy orgullosos de anunciar esta nueva versión de la Integración de Módulos entre Dondominio - WHMCS 2.0.

### Añadido
- Nueva estructura de proyecto (desde cero) con namespaces.
- Más fácil de instalar y configurar.
- Módulo Addon: Nueva pantalla de dashboard.
- Módulo Addon: Comprobación de última versión y descarga de última versión.
- Módulo Addon: Ahora comprueba el usuario/contraseña de API en el módulo registrador.
- Módulo Addon: Ahora actualiza el usuario/contraseña de API en el módulo registrador.
- Módulo Registrador: ahora comprueba el usuario/contraseña de API en el módulo addon.
- Desarrollador: añadidas herramientas para el desarrollador (despliegue y testing).

### Arreglado
- Módulo Addon: error de collation.
- Módulo Addon: carga lenta en la pestaña Proxy Whois.
- Módulo Addon, Módulo Registrador: eliminadas todas las referencias a funciones `mysql_`, que están obsoletas.
- Módulo Addon, Módulo Registrador: eliminadas todas las referencias de funciones `select_query`, `update_query`, `insert_query`, `full_query`, que están obsoletas.

### Cambiado
- SDK: Unificado en un solo lugar (carpeta `/includes`).
- SDK: Actualizado a versión 2.0.0.
- Módulo Addon: Sistema WHOIS actualizado a las buenas prácticas de WHMCS 7.0. [Compruébelo aquí](https://docs.whmcs.com/WHOIS_Servers)
- Módulo Registrador: Sistema de dominios sugeridos cambiado de módulo addon a módulo registrador, en concordancia con las buenas prácticas WHMCS 7.0. [Compruébelo aquí](https://docs.whmcs.com/Domain_Suggestions)
- Campos adicionales: La manera de añadir los campos adicionales se ha cambiado para que sea mucho más sencilla y más estándar. [Compruébelo aquí](https://docs.whmcs.com/Additional_Domain_Fields)
