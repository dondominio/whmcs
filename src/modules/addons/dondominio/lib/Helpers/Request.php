<?php

namespace WHMCS\Module\Addon\Dondominio\Helpers;

class Request
{
    const REMOVE_HTML_TAGS = 3;
    const ESCAPE_HTML = 1;
    const REMOVE_HTML = 2;
    const NOESCAPE = 0;

    protected static $instance;

    protected static $cookies;
    protected static $request;
    protected static $post;
    protected static $get;
    protected static $files;

    public function __construct()
    {
        static::$cookies = $_COOKIE;
        static::$request = $_REQUEST;
        static::$post = $_POST;
        static::$get = $_GET;
        static::$files = $_FILES;
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function getCookies()
    {
        return static::$cookies;
    }

    public function getCookie($cookie)
    {
        return array_key_exists($cookie, static::$cookies) ? static::$cookies[$cookie] : null;
    }

    public function getPost()
    {
        return static::$post;
    }

    public function getGet()
    {
        return static::$get;
    }

    public function getFiles()
    {
        return static::$files;
    }

    public function getParam( $name, $default = null, $escape_mode = null, $source = null )
    {
        return static::getRequestValue( $name, $default, $escape_mode, $source );
    }

    public function getArrayParam( $name, $default = null, $escape_mode = null, $source = null )
    {
        return static::getRequestArrayValue( $name, $default, $escape_mode, $source );
    }

   /**
     * Filtra un valor obtenido de forma externa ( POST, GET ... )
     * @param string $name Nombre del valor
     * @param mixed $default Valor por defecto
     * @return string
     */
    public static function getRequestValue( $name, $default = null, $escape_mode = null, $source = null )
    {
        if ( is_null( $escape_mode ) ) {
            $escape_mode = static::REMOVE_HTML;
        }

        if ( is_null( $source ) ) {
            $source = static::$request;
        }

        if ( !array_key_exists( $name, $source ) ) {
            return $default;
        }

        return static::escape( $source[$name], $escape_mode );
    }

    /**
     * Retorna un array obtenido de forma externa ( POST, GET ... )
     * @param string $nombre Nombre del valor
     * @param mixed $default Valor por defecto
     * @return array
     */
    public static function getRequestArrayValue( $name, $default = null, $escape_mode = null, $source = null )
    {
        if ( is_null( $escape_mode ) ) {
            $escape_mode = static::REMOVE_HTML;
        }

        if ( is_null( $source ) ) {
            $source = static::$request;
        }

        if ( !array_key_exists( $name, $source ) ) {
            return $default;
        }

        if ( !is_array( $source[$name] ) ) {
            return $default;
        }

        return static::escapeArray( $source[$name], $escape_mode );
    }

    /**
     * Escapa un array obtenido del usuario mediante POST o GET
     * @param array $array
     * @param int $escape_mode
     * @return array
     */
    protected static function escapeArray( $array, $escape_mode )
    {
        $ret = array();
        foreach ( $array as $key => $param ) {
            if ( !is_numeric( $key ) ) {
                $keyName = trim( static::escape( $key, $escape_mode ) );
            } else {
                $keyName = $key;
            }

            if ( is_array( $param ) ) {
                $ret[$keyName] = static::escapeArray( $param, $escape_mode );
            } else {
                $ret[$keyName] = static::escape( $param, $escape_mode );
            }
        }

        return $ret;
    }

    /**
     * Escapa un valor obtenido del usuario mediante POST o GET
     * @param mixed $valor
     * @param int $escape_mode
     * @return string
     */
    public static function escape( $valor, $escape_mode = null )
    {
        if ( is_null( $escape_mode ) ) {
            $escape_mode = static::REMOVE_HTML;
        }

        if ( function_exists( 'get_magic_quotes_gpc' ) && @get_magic_quotes_gpc() ) {
            $valor = stripslashes( $valor );
        }

        if ( is_string( $valor ) ) {
            switch ( $escape_mode ) {
                case static::ESCAPE_HTML:
                    return trim( (string) htmlentities( str_replace( '&nbsp;', ' ', $valor ), ENT_COMPAT, 'UTF-8' ) );
                    break;

                case static::REMOVE_HTML:
                    return trim( (string) strip_tags( html_entity_decode( str_replace( '&nbsp;', ' ', $valor ), ENT_NOQUOTES, 'UTF-8' ) ) );
                    break;

                case static::REMOVE_HTML_TAGS:
                    return trim( (string) strip_tags( $valor ) );
                    break;

                case static::NOESCAPE:
                default:
                    return $valor;
            }
        }
        return '';
    }
}