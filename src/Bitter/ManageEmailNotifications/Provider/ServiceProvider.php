<?php

namespace Bitter\ManageEmailNotifications\Provider;

use Bitter\ManageEmailNotifications\Routing\RouteList;
use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Mail\Service as CoreMailService;
use Bitter\ManageEmailNotifications\Mail\Service as MailService;
use Concrete\Core\Routing\RouterInterface;

class ServiceProvider extends Provider
{
    protected RouterInterface $router;

    public function __construct(
        Application     $app,
        RouterInterface $router
    )
    {
        parent::__construct($app);

        $this->router = $router;
    }

    public function register()
    {
        $this->registerRoutes();
        $this->initializeMailService();
    }

    private function registerRoutes()
    {
        $this->router->loadRouteList(new RouteList());
    }

    private function initializeMailService()
    {
        foreach (['helper/mail', 'mail', CoreMailService::class] as $abstract) {
            $this->app->extend($abstract, function () {
                /** @noinspection PhpUnhandledExceptionInspection */
                return $this->app->make(MailService::class);
            });
        }
    }
}
