<?php

namespace WHMCS\Module\Addon\Dondominio\Helpers;

use Exception;

class API
{
    protected $apiOptions;
    protected $connection;

    public function __construct(array $apiOptions = [])
    {
        $this->apiOptions = $apiOptions;
    }

    /**
     * Returns API Options attribute
     *
     * @return array API Options
     */
    public function getApiOptions()
    {
        return $this->apiOptions;
    }

    /**
     * Returns API Option attribute by key
     *
     * @param string $key Key to search
     *
     * @return mixed API Option
     */
    public function getApiOption($key)
    {
        if (!array_key_exists($key, $this->apiOptions)) {
            return null;
        }

        return $this->apiOptions[$key];
    }

    /**
     * Returns Dondominio API path
     *
     * @return string
     */
    public static function getApiPath()
    {
        return implode(DIRECTORY_SEPARATOR, [ROOTDIR, 'includes', 'dondominio', 'sdk']);
    }

    /**
     * Returns composer.json path from Dondominio API
     *
     * @return string
     */
    public static function getComposerJsonPath()
    {
        return implode(DIRECTORY_SEPARATOR, [static::getApiPath(), 'composer.json']);
    }

    /**
     * Returns autoloader.php from Dondominio API
     *
     * @return string
     */
    public static function getAutoloaderPath()
    {
        return implode(DIRECTORY_SEPARATOR, [static::getApiPath(), 'src', 'autoloader.php']);
    }

    /**
     * Returns if Dondominio API is installed
     *
     * @return bool
     */
    public static function findApiFolder()
    {
        return is_dir(static::getApiPath());
    }

    /**
     * Returns if Dondominio API composer.json exists
     *
     * @return bool
     */
    public static function findComposerJsonFile()
    {
        return file_exists(static::getComposerJsonPath());
    }

    /**
     * Returns if Dondominio API autoloader.php exists
     *
     * @return bool
     */
    public static function findAutoloaderFile()
    {
        return file_exists(static::getAutoloaderPath());
    }

    /**
     * Instantiates Dondominio API and returns instance
     *
     * @return \Dondominio\API\API
     */
    public function getConnection()
    {
        if (is_null($this->connection)) {
            if (!static::findApiFolder()) {
                throw new Exception('API folder not found: (' . static::getApiPath() . ')');
            }

            if (!static::findComposerJsonFile()) {
                throw new Exception('API file not found: (' . static::getComposerJsonPath() . ')');
            }

            if (!static::findAutoloaderFile()) {
                throw new Exception('API autoloader not found: (' . static::getAutoloaderPath() . ')');
            }

            include_once(static::getAutoloaderPath());

            $options = array_merge($this->getApiOptions(), [
                'autoValidate' => false,
                'versionCheck' => true,
                'response' => [
                    'throwExceptions' => false
                ]
            ]);

            $this->connection = new \Dondominio\API\API($options);
        }

        return $this->connection;
    }
}