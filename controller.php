<?php

namespace Concrete\Package\ManageEmailNotifications;

use Bitter\ManageEmailNotifications\Provider\ServiceProvider;
use Concrete\Core\Package\Package;
use Concrete\Core\Entity\Package as PackageEntity;

class Controller extends Package
{
    protected string $pkgHandle = 'manage_email_notifications';
    protected string $pkgVersion = '0.0.2';
    protected $appVersionRequired = '9.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/ManageEmailNotifications' => 'Bitter\ManageEmailNotifications',
    ];

    public function getPackageDescription(): string
    {
        return t('Add an account page to manage email notifications.');
    }

    public function getPackageName(): string
    {
        return t('Manage Email Notifications');
    }

    public function on_start()
    {
        /** @var ServiceProvider $serviceProvider */
        /** @noinspection PhpUnhandledExceptionInspection */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    public function install(): PackageEntity
    {
        $pkg = parent::install();
        $this->installContentFile("data.xml");
        return $pkg;
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile("data.xml");
    }
}
