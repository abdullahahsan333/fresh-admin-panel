<?php
namespace App\Debugbar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class ApiRequestsCollector extends DataCollector implements Renderable
{
    public function getName()
    {
        return 'apis_request';
    }

    public function collect()
    {
        $requests = function_exists('getExternalApiRequests') ? getExternalApiRequests() : [];
        return [
            'requests' => $requests,
        ];
    }

    public function getWidgets()
    {
        return [
            'APIs request' => [
                'icon' => 'arrow-right',
                'widget' => 'PhpDebugBar.Widgets.Table',
                'map' => $this->getName() . '.requests',
                'default' => [],
            ],
        ];
    }
}
