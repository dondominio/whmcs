{if $addon_outdated eq true}
<a href='https://github.com/dondominio/whmcs-addon'>
    <div style='background-color: #F3F3C8; padding: 10px; border: 2px black solid; font-weight: 600;'>
        <h1>New Addon version available</h1>
        
        <p>A new version of the DonDominio Addon for WHMCS has been released. Regularly updating the plugin is recommended to get all the features
        and avoid future incompatibilities with the DonDominio API.</p>
        
        Click here to download <strong>version {$version.version} released on {date('d/m/Y', strtotime($version['releaseDate']))}</strong></a>
    </div>
</a>
{else}
    Dondominio Addon Version: {$version}
{/if}

{if $plugin_outdated eq true}
<a href='https://github.com/dondominio/whmcs-plugin'>
    <div style='background-color: #F3F3C8; padding: 10px; border: 2px black solid; font-weight: 600;'>
        <h1>DonDominio Registrar Plugin for WHMCS updated</h1>
        
        <p>A new version of the DonDominio Registrar Plugin for WHMCS has been released. Regularly updating the plugin is recommended to get all the features
        and avoid future incompatibilities with the DonDominio API.</p>
        
        <p>Click here to download <strong>version {$version.version} released on {date('d/m/Y', strtotime($version['releaseDate']))}</strong></a></p>
    </div>
</a>
{/if}
<br>
<br>
<p>API Info:</p>
<div class='infobox'>
    {$api_info}
</div>