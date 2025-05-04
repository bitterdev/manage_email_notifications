<?php

namespace Bitter\ManageEmailNotifications\Routing;

use Bitter\ManageEmailNotifications\API\V1\Middleware\FractalNegotiatorMiddleware;
use Bitter\ManageEmailNotifications\API\V1\Configurator;
use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\ManageEmailNotifications\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/manage_email_notifications')
            ->routes('dialogs/support.php', 'manage_email_notifications');
    }
}