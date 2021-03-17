# Changelog
Todos los cambios importantes de este proyecto serán documentados en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto se adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Pendiente por lanzar]

## [2.1.2] - 2020-03-17
### Fixed
- Ahora las tablas configuradas con row_format compact funcionan correctamente
- Las operaciones relacionadas con Vat Number funcionan correctamente

### Changed
- Estados de dominio más específicos
- Más información en la pestaña de estado del Addon

## [2.1.1] - 2020-02-26
### Arreglado
- Consistencia en base de datos con campos no nulables.

## [2.1.0] - 2020-02-23
### Cambiado
- Nuevo sistema de actualizaciones. Ahora es más robusto, incluyendo copias de seguridad, comprobación de permisos, rollbacks, etc.

## [2.0.5] - 2020-02-22
### Arreglado
- Sincronización de transferencias de dominios arreglada. [Más Info](https://developers.whmcs.com/domain-registrars/domain-syncing/)

## [2.0.4] - 2020-02-22
### Arreglado
- El actualizador de módulos ahora hace una comprobación más exhaustiva de permisos antes de actualizar.

## [2.0.3] - 2020-02-19
### Arreglado
- Arreglado typo al parsear los datos de contacto. Ahora importar y transferir dominios funciona como se espera.

## [2.0.2] - 2020-02-19
### Arreglado
- Añadida prevención contra tablas con colaciones no recomendadas. [Ver WHMCS Database Collations](https://docs.whmcs.com/Database_Collations)

## [2.0.1] - 2020-02-11
Esta versión está basada en los cambios propuestos de [documentación de actualización a WHMCS8](https://developers.whmcs.com/advanced/upgrade-to-whmcs-8/) y arregla los problemas relacionados con la actualización de Laravel (métodos `plug` y `get`).

### Arreglado
- Módulo Registrador: Selector para VAT Number en Configuración ahora funciona correctamente.
- Módulo Addon: Filtro de TLDS en Gestión de Dominios ahora funciona correctamente.

## [2.0.0] - 2020-01-31
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