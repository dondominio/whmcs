<?php

namespace WHMCS\Module\Addon\Dondominio\Helpers;

class Response
{
    const HTTP_VERSION = 'HTTP/1.1';

    const ERROR_BAD_REQUEST		= 400;
    const ERROR_UNAUTHORIZED	= 401;
    const ERROR_FORBIDDEN		= 403;
    const ERROR_NOT_FOUND		= 404;
    const OK                    = 200;

    const AUTH_DIGEST	= 0;
    const AUTH_BASIC	= 1;

    const CONTENT_HTML 	= 0;
    const CONTENT_TEXT 	= 1;
    const CONTENT_JSON 	= 2;
    const CONTENT_PDF 	= 3;
    const CONTENT_CSV 	= 4;
    const CONTENT_SCRIPT= 5;
    const CONTENT_TAR	= 6;
    const CONTENT_JPEG	= 7;
    const CONTENT_XML	= 8;
    const CONTENT_BINARY= 9;

    protected static $instance;

    protected $http_headers;
    protected $cookies;

    public $force_success = false;
    public $success = [];

    public $force_errors = false;
    public $errors = [];

    public $force_info = false;
    public $info = [];

    public function __construct()
    {
        ob_start();
        $this->http_headers = [];
        $this->cookies = $_COOKIE;
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Se añade una cabecera a la respuesta
     * @param string $header
     * @param bool $replace
     * @param int $code
     */
    public function addHeader($header, $replace=true, $code=null)
    {
        $this->http_headers[] = [
            'value'		=>  (string) $header,
            'replace'	=> $replace,
            'code'		=> $code,
        ];
        return $this;
    }

    /**
     * Añade una cabecera de error http a la respuesta
     * @param int $error
     */
    public function setHttpError($error)
    {
        switch ($error) {
            case static::ERROR_BAD_REQUEST:
                $this->addHeader(static::HTTP_VERSION . ' 400 Bad Request');
                break;

            case static::ERROR_UNAUTHORIZED:
                $this->addHeader(static::HTTP_VERSION . ' 401 Unauthorized');
                break;

            case static::ERROR_FORBIDDEN:
                $this->addHeader(static::HTTP_VERSION . ' 403 Forbidden');
                break;

            case static::ERROR_NOT_FOUND:
                $this->addHeader(static::HTTP_VERSION . ' 404 Not Found');
                break;

        }
        return $this;
    }

    // SUCCESS

    public function getSuccess()
    {
        return $this->success;
    }

    public function addSuccess($key = null, $value = null)
    {
        if (is_null($value)) {
            $this->success[] = $key;
        } else {
            $this->success[$key] = $value;
        }
    }

    public function setForceSuccess($bool)
    {
        $this->force_success = $bool;
    }

    // ERRORS

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($key = null, $value = null)
    {
        if (is_null($value)) {
            $this->errors[] = $key;
        } else {
            $this->errors[$key] = $value;
        }
    }

    public function setForceErrors($bool)
    {
        $this->force_errors = $bool;
    }

    // INFO

    public function getInfo()
    {
        return $this->info;
    }

    public function addInfo($key = null, $value = null)
    {
        if (is_null($value)) {
            $this->info[] = $key;
        } else {
            $this->info[$key] = $value;
        }
    }

    public function setForceInfo($bool)
    {
        $this->force_info = $bool;
    }

    /**
     * Realiza una redireccion y sale del script
     * @param string $uri
     */
    public function redirect($uri, $code = null)
    {
        switch ($code) {
            case 301:
                header(static::HTTP_VERSION . " 301 Moved Permanently");
                break;

            case 302:
                header(static::HTTP_VERSION . " 302 Found");
                break;

            case 303:
                header(static::HTTP_VERSION . " 303 See Other");
                break;

            case 305:
                header(static::HTTP_VERSION . " 305 Use Proxy");
                break;

            case 307:
                header(static::HTTP_VERSION . " 307 Temporary Redirect");
                break;

        }

        header("Location: $uri");
        exit;
    }

    /**
     * Envia las cabeceras de la respuesta
     */
    public function sendHeaders()
    {
        if (headers_sent()) {
            return false;
        }

        foreach ($this->cookies as $name => $cookie) {
            if (is_string($cookie)) {
                continue;
            }

            setcookie(
                $name,
                $cookie['value'],
                $cookie['expire'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['http_only']
            );
        }

        foreach ($this->http_headers as $header) {
            if (is_null($header['code'])) {
                header(
                    $header['value'],
                    $header['replace']
                );
            } else {
                header(
                    $header['value'],
                    $header['replace'],
                    $header['code']
                );
            }
        }

        return true;
    }

    /**
     * Envia el cuerpo de la respuesta
     * @param string $body
     * @param bool $die
     */
    public function send($body, $die=false)
    {
        $this->sendHeaders();
        echo (string) $body;
        if ($die) {
            $this->flush();
            exit;
        }
    }

    /**
     * Fuerza flush del buffer pendiente de escribir
     * en stdout
     */
    public function flush()
    {
        ob_flush();
        ob_end_clean();
    }

    /**
     * Añade cabeceras para evitar que el cliente cachee la
     * respuesta
     */
    public function noCache()
    {
        $this->addHeader("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
        $this->addHeader("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        $this->addHeader("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
        $this->addHeader("Cache-Control: post-check=0, pre-check=0", false);
        $this->addHeader("Pragma: no-cache");                          // HTTP/1.0
        return $this;
    }

    /**
     * Añade una cabecera indicando el tipo de contenido que se esta retornando
     * @param mixed
     */
    public function setContentType($content_type)
    {
        $contentTypes = static::getContentTypes();
        $ct = array_key_exists($content_type, $contentTypes) ? $contentTypes[$content_type] : $content_type; 
        $this->addHeader('Content-Type: ' . $ct);
        return $this;
    }

    /**
     * Retorna un array con todos los tipos de contenido conocidos
     * por la clase
     * @return string[]
     */
    public static function getContentTypes()
    {
        return [
            static::CONTENT_HTML 	=> 'text/html; charset=UTF-8',
            static::CONTENT_TEXT 	=> 'text/plain; charset=UTF-8',
            static::CONTENT_CSV 	=> 'text/csv; charset=UTF-8',
            static::CONTENT_XML	    => 'text/xml; charset=UTF-8',
            static::CONTENT_JSON 	=> 'application/json; charset=UTF-8',
            static::CONTENT_SCRIPT 	=> 'application/javascript; charset=UTF-8',
            static::CONTENT_PDF 	=> 'application/pdf',
            static::CONTENT_TAR		=> 'application/x-tar',
            static::CONTENT_JPEG	=> 'image/jpeg',
            static::CONTENT_BINARY  => 'application/octet-stream',
        ];
    }
}