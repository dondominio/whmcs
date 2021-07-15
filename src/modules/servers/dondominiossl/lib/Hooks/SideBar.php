<?php

namespace WHMCS\Module\Server\Dondominiossl\Hooks;

class SideBar
{
    public function __invoke(\WHMCS\View\Menu\Item  $primarySidebar): void
    {
        $servicePanel = $primarySidebar->getChild('Service Details Overview');

        if (is_null($servicePanel)) {
            return;
        }

        $serviceID = $this->getServiceID();

        if (empty($serviceID)) {
            return;
        }

        $childs = $this->getChilds($serviceID);

        $servicePanel->removeChild('Information');

        foreach ($childs as $child) {
            $servicePanel->addChild($child['name'], $child['extra']);
        }

        $servicePanel->getChild($this->getSelectedChild($childs))->setClass('active');
    }

    protected function getServiceID(): int
    {
        $service = \WHMCS\Application\Support\Facades\Menu::context('service');

        if (!is_object($service)) {
            return 0;
        }

        $product = $service->product;
        $serviceID = $service->id;

        if (!is_object($product)) {
            return 0;
        }

        $module = $product->module;

        if ($module !== 'dondominiossl') {
            return 0;
        }

        return (int) $serviceID;
    }

    protected function getChilds(int $serviceID): array
    {
        return [
            'index' => [
                'name' => 'Information',
                'extra' => [
                    'uri' => sprintf('clientarea.php?action=productdetails&id=%d', $serviceID),
                    'icon'  => 'fa-list-alt',
                    'order' => 1,
                ]
            ],
        ];
    }

    protected function getSelectedChild(array $childs): string
    {
        $customAction = 'index';

        if (isset($_REQUEST['custom_action'])) {
            $customAction = $_REQUEST['custom_action'];
        }

        if (isset($childs[$customAction]['name'])) {
            return $childs[$customAction]['name'];
        }

        return $childs['index']['name'];
    }
}