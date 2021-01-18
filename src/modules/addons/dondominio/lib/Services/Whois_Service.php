<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\Services\Contracts\WhoisService_Interface;
use Exception;

class Whois_Service extends AbstractService implements WhoisService_Interface
{
    protected $apiServiceForWhois = null;

    /**
     * Gets API Service for Whois (different user agent)
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
    public function getWhoisServerFilePath()
    {
        if (!defined('ROOTDIR')) {
            throw new Exception('ROOTDIR is not defined.');
        }

        $whmcsVersion = $this->getApp()->getService('whmcs')->getConfiguration('version');

        if (version_compare($whmcsVersion, '7.0.0', '<')) {
            return implode(DIRECTORY_SEPARATOR, [ROOTDIR, 'includes', 'whoisservers.php']);
        }
            
        return implode(DIRECTORY_SEPARATOR, [ROOTDIR, 'resources', 'domains', 'dist.whois.json']);
    }

    /**
     * Get Whois Servers Backup Path
     *
     * @return string
     */
    public function getWhoisServerBackupPath()
    {
        return implode(DIRECTORY_SEPARATOR, [$this->getApp()->getDir(), 'whois_backups', 'dist.whois.json']);
    }

    /**
     * Get Whois Server File contents as an array
     *
     * @return array
     */
    public function getWhoisServerFile()
    {
        $whmcsVersion = $this->getApp()->getService('whmcs')->getConfiguration('version');

        if (version_compare($whmcsVersion, '7.0.0', '<')) {
            return @file($this->getWhoisServerFilePath());
        }

        $whois_file = file_get_contents($this->getWhoisServerFilePath());
        
        $whois_servers = json_decode($whois_file, true);
        
        $new_whois_servers = [];
        
        foreach ($whois_servers as $entry) {
            $extensions = explode(',', $entry['extensions']);            
            foreach ($extensions as $tld) {
                $new_whois_servers[] = [
                    'extensions' => $tld,
                    'uri' => $entry['uri'],
                    'available' => $entry['available']
                ];
            }
        }

        return $new_whois_servers;
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
        //Do not overwrite the backup if it already exists
        if (!file_exists($this->getWhoisServerBackupPath())) {
            $backupFile = $this->getWhoisServerBackupPath() . '_' . date('Y_m_d_H_i_s') . '.backup';
            return copy($this->getWhoisServerFilePath(), $backupFile);
        }

        return false;
    }

    public function getWhoisItems()
    {
        $whoisDomain = $this->getApp()->getService('settings')->getSetting('whois_domain');
        $json = $this->getWhoisServerFile();

        foreach ($json as &$entry) {
            $found = $this->getApp()->getService('pricing')->findPricingByTld($entry['extensions']);

            $entry['style'] = '';

            if (is_null($found)) {
                $entry['style'] = '#FDF4E8';
            }

            if (strpos($entry['uri'], 'whoisproxy.php')) {
                $entry['style'] = '#EBFEE2';
            }

            $entry['can_switch'] = is_writable($this->getWhoisServerFilePath()) && strlen($whoisDomain) > 0 && !is_null($found);
        }

        return $json;
    }

    /**
     * Setup a TLD to use DD API for Whois
     *
     * @param string $tld The TLD to configure
     *
     * @return bool
     */
    public function setup($new_tld)
    {
        $tld = $this->getApp()->getService('api')->getAccountZones([
            'tld' => substr($new_tld, 1)
        ]);

        $queryInfo = $tld->get('queryInfo');

        if ($queryInfo['total'] < 1) {
            throw new Exception('new-tld-not-found');
        }

        # Backing up the original server file

        $this->doWhoisBackup();

        # Build the URL for the proxy

        $url = $_SERVER['REQUEST_URI'];
        $admin_section = strpos($url, '/admin');
        $route = substr($url, 0, $admin_section);

        $domain = $this->getApp()->getService('settings')->getSetting('whois_domain');

        if (substr($domain, 0, 4) != 'http') {
            $domain = 'http://' . $domain;
        }

        # Loading the file

        $file = $this->getWhoisServerFile();

        $whmcsVersion = $this->getApp()->getService('whmcs')->getConfiguration('version');

        if (version_compare($whmcsVersion, '7.0.0', '>=')) {
            $this->setup7($new_tld, $domain, $route, $file);
        } else {
            $this->setupLegacy($new_tld, $domain, $route, $file);
        }
    }

    public function setup7($new_tld, $domain, $route, $file)
    {
        // Replacing the TLD

        $found = false;

        foreach ($file as $id => $entry) {
            if ($entry['extensions'] == $new_tld) {
                $found = true;

                $file[$id]['uri'] = $domain . $route . '/modules/addons/dondominio/whoisproxy.php?domain=';
                $file[$id]['available'] = 'DDAvailable';
                break;
            }
        }

        // Adding the TLD
        if (!$found) {
            $file[] = [
                'extensions' => $new_tld,
                'uri' => $domain . $route . '/modules/addons/dondominio/whoisproxy.php?domain=',
                'available' => 'DDAvailable'
            ];
        }

        # Saving the new file

        $result = @file_put_contents($this->getWhoisServerFilePath(), json_encode($file));

        if (!$result) {
            throw new Exception('new-tld-error-permissions');
        }
    }

    public function setupLegacy($new_tld, $domain, $route, $file)
    {
        $found = false;

        //Looking for the TLD in the file
        foreach ($file as $whois_id => $whois_entry) {
            [$tld, $server, $match] = explode('|', $whois_entry);
            
            //TLD found; modify its settings
            if($tld == $new_tld) {
                $file[$whois_id] = $tld . '|' . $domain . $route . '/modules/addons/dondominio/whois/whoisproxy.php?domain=|HTTPREQUEST-DDAVAILABLE' . "\r\n";
                $found = true;
                break;
            }
        }

        //TLD not found in current file; add it to the bottom
        if (!$found){
            $file[] = $new_tld . '|' . $domain . $route . '/modules/addons/dondominio/whois/whoisproxy.php?domain=|HTTPREQUEST-DDAVAILABLE' . "\r\n";
        }

        //Save the resulting file
        $result = @file_put_contents($this->getWhoisServerFilePath(), implode( "", $file));

        if (!$result) {
            throw new Exception('new-tld-error-permissions');
        }
    }

    public function importWhois(array $file)
    {
        $this->doWhoisBackup();

        $file_contents = @file_get_contents($file['tmp_name']);

        if (!($json = json_decode($file_contents))) {
            throw new Exception('import-error');
        }

        move_uploaded_file($file['tmp_name'], $this->getWhoisServerFilePath());
    }

    public function doWhois($domain)
    {
        // We cannot use api service provided by App because we want to add custom userAgent
        // to the api service (consequently to api client)

        $whois = $this->getApiServiceForWhois()->checkDomain($domain);

        return $whois->get("domains")[0];
    }
}