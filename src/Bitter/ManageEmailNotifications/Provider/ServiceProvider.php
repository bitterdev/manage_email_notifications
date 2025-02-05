<?php

namespace Bitter\ManageEmailNotifications\Provider;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Mail\Service as CoreMailService;
use Bitter\ManageEmailNotifications\Mail\Service as MailService;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->initializeMailService();
    }

    private function initializeMailService()
    {
        foreach (['helper/mail', 'mail', CoreMailService::class] as $abstract) {
            $this->app->extend($abstract, function () {
                return $this->app->make(MailService::class);
            });
        }
    }
}
