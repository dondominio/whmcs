<?php

namespace WHMCS\Module\Server\Dondominiossl\Lang;


class Spanish implements \WHMCS\Module\Server\Dondominiossl\Lang\Translations
{

    public function getTranslations(): array
    {
        return [
            'product_data' => 'Datos del Producto',
            'cert_data' => 'Datos del Certificado',
            'cert_type' => 'Tipo de certificado',
            'cert_id' => 'ID del Certificado',
            'cert_status' => 'Estado',
            'cert_max_domains' => 'Número máximo de dominios',
            'cert_creation' => 'Creación',
            'cert_expiration' => 'Expiración',
            'cert_company_validation' => 'Estado validación de empresa',
            'cert_brand_company_validation' => 'Estado validación de la marca de empresa',
            'cert_msg_validation' => 'Mensaje de validación',
            'cert_external_validation' => 'Validación externa',
            'cert_download' => 'Descargar',
            'cert_reissue' => 'Reemitir',
            'cert_validation_data' => 'Control de validación del certificado',
            'cert_domain' => 'Dominio',
            'cert_validation_method' => 'Método de validación',
            'cert_validation_status' => 'Validado',
            'cert_change_method' => 'Cambiar método',
            'cert_new_validation_method' => 'Nuevo método de validación',
            'cert_resend_mail' => 'Reenviar correo de validación',
            'cert_download_need_pass' => 'Para generar este formato es necesaria una contraseña',
            'cert_pass' => 'Contraseña',
            'cert_org_name' => 'Nombre de organización',
            'cert_org_unit' => 'Nombre de unidad',
            'cert_country' => 'Código de 2 caracteres del país',
            'cert_state' => 'Nombre de la provincia o estado de la compañía',
            'cert_location' => 'Nombre de la población de la compañía',
            'cert_mail' => 'Correo',
            'cert_alt_name' => 'Dominio Alternativo',
            'cert_alt_validation_name' => 'Método de validación de Dominio Alternativo',
            'cert_add_alt' => 'Añadir Dominio Alternativo',
            'cert_cancel' => 'Cancelar',
            'cert_alt_names' => 'Nombres Alternativos',
            'cert_resend' => 'Reenviar',
            'cert_change' => 'Cambiar',
            'cert_need' => 'Necesaria',
            'cert_not_need' => 'No Necesaria',
            'cert_validation_mail_send' => 'Correo de validación enviado',
            'cert_validation_create_cname' => 'Crear CNAME',
            'cert_validation_create_link' => 'Crear link',
            'cert_validation_with_content' => 'con el contenido',
            'cert_load_validation' => 'Cargar Estado de Validación',
        ];
    }
}
