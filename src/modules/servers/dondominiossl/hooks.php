<?php
$sideBar = new \WHMCS\Module\Server\Dondominiossl\Hooks\SideBar();
add_hook('ClientAreaPrimarySidebar', 1, $sideBar);
