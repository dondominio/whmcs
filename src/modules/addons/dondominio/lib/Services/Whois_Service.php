<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\Services\Contracts\WhoisService_Interface;
use Exception;

class Whois_Service extends AbstractService implements WhoisService_Interface
{
    protected $defaultWhoisServers = null;
    protected $customWhoisServers = null;

    protected $apiServiceForWhois = null;
    protected $isWhmcsVersionLt7 = null;

    public function isWHMCSVersionLte7()
    {
        if (is_null($this->isWhmcsVersionLt7)) {
            $whmcsVersion = $this->getApp()->getService('whmcs')->getConfiguration('version');
            $this->isWhmcsVersionLt7 = version_compare($whmcsVersion, '7.0.0', '<');
        }

        return $this->isWhmcsVersionLt7;
    }

    /**
     * Gets API Service for Whois (different user agent)
     *
     * @return API_Service
     */
    public function getApiServiceForWhois()
    {
        if (is_null($this->apiServiceForWhois)) {
            $appApiHelper = $this->getApp()->getService('api')->getApi();

            $this->apiServiceForWhois = new API_Service([
                'apiuser' => $appApiHelper->getApiOption('apiuser'),
                'apipasswd' => $appApiHelper->getApiOption('apipasswd'),
                'userAgent' => ['WhoisProxyAddonForWHMCS' => '1.0']
            ]);
        }

        return $this->apiServiceForWhois;
    }

    /**
     * Get Whois Servers File Path
     *
     * @return string
     */
    public function getDefaultWhoisServerFilePath()
    {
        if (!defined('ROOTDIR')) {
            throw new Exception('ROOTDIR is not defined.');
        }

        if ($this->isWhmcsVersionLte7()) {
            return implode(DIRECTORY_SEPARATOR, [ROOTDIR, 'includes', 'whoisservers.php']);
        }

        return implode(DIRECTORY_SEPARATOR, [ROOTDIR, 'resources', 'domains', 'dist.whois.json']);
    }

    /**
     * Get Default Whois Servers as array
     *
     * @return array
     */
    public function getDefaultWhoisServers()
    {
        if (is_null($this->defaultWhoisServers)) {
            $this->defaultWhoisServers = $this->parseWhoisServers($this->getDefaultWhoisServerFilePath());
        }

        return $this->defaultWhoisServers;
    }

    /**
     * Get Custom Whois Servers File Path
     *
     * @return string
     */
    public function getCustomWhoisServerFilePath()
    {
        if ($this->isWhmcsVersionLte7()) {
            return $this->getDefaultWhoisServerFilePath();
        }

        return implode(DIRECTORY_SEPARATOR, [ROOTDIR, 'resources', 'domains', 'whois.json']);
    }

    /**
     * Get Custom Whois Servers as array
     *
     * @param bool $reload Reloads the array
     *
     * @return array
     */
    public function getCustomWhoisServers($reload = false)
    {
        if (is_null($this->customWhoisServers) || $reload) {
            if (!file_exists($this->getCustomWhoisServerFilePath())) {
                touch($this->getCustomWhoisServerFilePath());
            }

            $this->customWhoisServers = $this->parseWhoisServers($this->getCustomWhoisServerFilePath());
        }

        return $this->customWhoisServers;
    }


    /**
     * Adds Custom Whois Server to internal array
     *
     * @param string $tld Extension
     * @param string $domain Host domain
     * @param string $route Configured WHOIS Server
     *
     * @return void
     */
    protected function addCustomWhoisServer($tld, $url, $available)
    {
        $this->customWhoisServers[$tld] = [
            'extensions' => $tld,
            'uri' => $url,
            'available' => $available,
            'found_in_db' => null,
            'whois_by_dd' => null
        ];
    }

    /**
     * Removes an entry from custom whois servers by key
     *
     * @param string $key Key to search
     *
     * @return void
     */
    protected function removeCustomWhoisServerByKey($key)
    {
        unset($this->customWhoisServers[$key]);
    }

    /**
     * Get Whois Servers Backup Path
     *
     * @return string
     */
    public function getWhoisServersBackupPath()
    {
        if ($this->isWhmcsVersionLte7()) {
            return implode(DIRECTORY_SEPARATOR, [$this->getApp()->getDir(), 'whois_backups', 'whoisservers.php']);
        }

        return implode(DIRECTORY_SEPARATOR, [$this->getApp()->getDir(), 'whois_backups', 'whois.json']);
    }

    /**
     * Saves a TLD to use DD API for Whois
     *
     * @param string $tld The TLD to configure
     *
     * @throws Exception If TLD not found in API or WHOIS Servers File saving went bad
     *
     * @return bool
     */
    public function setup($tld)
    {
        // API CALL

        $response = $this->getApp()->getService('api')->getAccountZones([
            'tld' => substr($tld, 1)
        ]);

        $queryInfo = $response->get('queryInfo');

        if ($queryInfo['total'] < 1) {
            throw new Exception('new-tld-not-found');
        }

        // BACKUP WHOIS SERVERS FILE

        $this->doWhoisBackup();

        // BUILD NEW WHOIS SERVER

        $url = $_SERVER['REQUEST_URI'];
        $admin_section = strpos($url, '/admin');
        $route = substr($url, 0, $admin_section);

        $domain = $this->getApp()->getService('settings')->getSetting('whois_domain');

        if (substr($domain, 0, 4) != 'http') {
            $protocol = array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : $_SERVER['REQUEST_SCHEME'];
            $domain = !empty($protocol) ? $protocol . '://' . $domain : $domain;
        }

        // CALCULATE WHOIS PROXY

        $this->getCustomWhoisServers();

        // ADD NEW ITEM

        $this->addCustomWhoisServer(
            $tld,
            $domain . $route . '/modules/addons/dondominio/whoisproxy.php?domain=',
            $this->isWhmcsVersionLte7() ? 'HTTPREQUEST-DDAVAILABLE' : 'DDAvailable'
        );

        // SAVE NEW WHOIS SERVER INTO FILE

       $result = $this->saveWhoisServersIntoFile();

        if (!$result) {
            throw new Exception('new-tld-error-permissions');
        }

        // RELOAD INTERNAL ARRAY

        $this->getCustomWhoisServers(true);
    }

    /**
     * Delete all WHOIS Servers in Dondominio
     */
    public function deleteWhoisServersByDondominio()
    {
        $this->doWhoisBackup();

        foreach ($this->getCustomWhoisServers() as $key => $whoisServer) {
            if ($whoisServer['whois_by_dd']) {
                $this->removeCustomWhoisServerByKey($key);
            }
        }

        return $this->saveWhoisServersIntoFile();
    }

    /**
     * Saves Custom Whois Servers into file
     *
     * @return bool
     */
    protected function saveWhoisServersIntoFile()
    {
        if ($this->isWhmcsVersionLte7()) {
            return $this->setupLegacy();
        }

        return $this->setup7();
    }

    /**
     * Saves TLD in WHOIS Servers File
     *
     * @return bool
     */
    protected function setup7()
    {
        $new_file = [];

        foreach ($this->getCustomWhoisServers() as $key => $value) {
            $new_file[$key] = [
                'extensions' => $value['extensions'],
                'uri' => $value['uri'],
                'available' => $value['available']
            ];
        }

        return @file_put_contents($this->getCustomWhoisServerFilePath(), json_encode(array_values($new_file)));
    }

    /**
     * Saves TLD in WHOIS Servers File (FOR LEGACY WHMCS VERSION <7)
     *
     * @return bool
     */
    protected function setupLegacy()
    {
        $new_file = [];

        foreach ($this->getCustomWhoisServers() as $key => $value) {
            $new_file[$key] = implode("|", [$value['extensions'], $value['uri'], $value['available']]);
        }

        array_walk($new_file, function(&$line) {
            $line = $line . "\r\n";
        });

        //Save the resulting file
        return @file_put_contents($this->getCustomWhoisServerFilePath(), $new_file);
    }

    /**
     * Make a backup of the original whois servers file
     * Creates a backup on the local directory of the original whois servers file for restoring it
     * later, if needed.
     *
     * @return bool
     */
    public function doWhoisBackup()
    {
        $backupFile = $this->getWhoisServersBackupPath() . '.' . date('Y_m_d_H_i_s') . '.backup';

        return copy($this->getCustomWhoisServerFilePath(), $backupFile);
    }

        /**
     * Get all Whois Servers to display in HTML
     *
     * @return array
     */
    public function getWhoisItems()
    {
        $whoisDomain = $this->getApp()->getService('settings')->getSetting('whois_domain');

        if ($this->isWHMCSVersionLte7()) {
            $whoisServers = $this->getCustomWhoisServers();
        } else {
            $whoisServers = array_merge($this->getDefaultWhoisServers(), $this->getCustomWhoisServers());
        }

        usort($whoisServers, function($a, $b) {
            // Sort by "whois by dondominio"
            $aWhoisByDD = $a['whois_by_dd'];
            $bWhoisByDD = $b['whois_by_dd'];

            if ($aWhoisByDD != $bWhoisByDD) {
                return $aWhoisByDD ? -1 : 1;
            }

            // Sort by find in TLD found in DB
            $aFoundInDB = $a['found_in_db'];
            $bFoundInDB = $b['found_in_db'];

            if ($aFoundInDB != $bFoundInDB) {
                return $aFoundInDB ? -1 : 1;
            }

            // Sort extension alphabetically
            return strcmp($a['extensions'], $b['extensions']);
        });

        $fileIsWritable = is_writable($this->getCustomWhoisServerFilePath());
        $domainIsNotEmpty = strlen($whoisDomain) > 0;

        // For element, set style
        // If not found in whmcs -> background: red
        // If found in dondominio -> background: red

        foreach ($whoisServers as &$entry) {
            $entry['style'] = '';

            if (!$entry['found_in_db']) {
                $entry['style'] = '#FDF4E8';
            }

            if ($entry['whois_by_dd']) {
                $entry['style'] = '#EBFEE2';
            }

            $entry['can_switch'] = $fileIsWritable && $domainIsNotEmpty && $entry['found_in_db'];
        }

        return $whoisServers;
    }

    /**
     * Import new WHOIS Server File
     *
     * @param array $file $_FILE
     *
     * @return bool
     */
    public function importWhois(array $file)
    {
        $this->doWhoisBackup();

        $file_contents = @file_get_contents($file['tmp_name']);

        if ($this->isWHMCSVersionLte7()) {
            // Should we add some checking?
        } else {
            if (!($json = json_decode($file_contents))) {
                throw new Exception('import-error');
            }
        }

        return move_uploaded_file($file['tmp_name'], $this->getCustomWhoisServerFilePath());
    }

    /**
     * Parses WHOIS Servers file depend upon WHMCS Version
     *
     * @param string $path Path where is the file to parse
     *
     * @return array
     */
    public function parseWhoisServers($path)
    {
        if ($this->isWHMCSVersionLte7()) {
            return $this->parseWhoisServersFile($path);
        }

        return $this->parseWhoisServersJson($path);
    }

    /**
     * Parses WHPOS Servers File with the following structure:
     * .com|whois.crsnic.net|No match for
     * .org|whois.publicinterestregistry.net|NOT FOUND
     * ...
     *
     * @param string $path Path where is the file to parse
     *
     * @return array
     */
    public function parseWhoisServersFile($path)
    {
        $pricings = $this->getApp()->getService('pricing')->findAllTldsAsArray();

        $whois_servers = @file($path);

        $new_whois_servers = [];

        foreach ($whois_servers as $entry) {
            list($tld, $uri, $available) = explode('|', $entry);

            $found = in_array($tld, $pricings) || in_array("." . $tld, $pricings);

            $new_whois_servers[$tld] = [
                'extensions' => $tld,
                'uri' => $uri,
                'available' => trim($available),
                'found_in_db' => $found,
                'whois_by_dd' => strpos($uri, 'whoisproxy.php') !== false
            ];
        }

        return $new_whois_servers;
    }

    /**
     * Transforms json file into array
     *
     * @param string $path Path of file
     *
     * @return array
     */
    public function parseWhoisServersJson($path)
    {
        $pricings = $this->getApp()->getService('pricing')->findAllTldsAsArray();

        $whois_file = file_get_contents($path);
        $whois_servers = json_decode($whois_file, true);

        $new_whois_servers = [];

        foreach ($whois_servers as $entry) {
            $extensions = explode(',', $entry['extensions']);
            foreach ($extensions as $tld) {
                $found = in_array($entry['extensions'], $pricings) || in_array("." . $entry['extension'], $pricings);

                $new_whois_servers[$tld] = [
                    'extensions' => $tld,
                    'uri' => $entry['uri'],
                    'available' => $entry['available'],
                    'found_in_db' => $found,
                    'whois_by_dd' => strpos($entry['uri'], 'whoisproxy.php') !== false
                ];
            }
        }

        return $new_whois_servers;
    }

    /**
     * Makes whois call to API
     *
     * @param string $domain Domain to whois
     *
     * @return array
     */
    public function doWhois($domain)
    {
        // We cannot use api service provided by App because we want to add custom userAgent
        // to the api service (consequently to api client)

        $whois = $this->getApiServiceForWhois()->checkDomain($domain);

        return $whois->get("domains")[0];
    }
}