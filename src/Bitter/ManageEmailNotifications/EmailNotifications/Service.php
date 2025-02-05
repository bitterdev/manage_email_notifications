<?php

namespace Bitter\ManageEmailNotifications\EmailNotifications;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Package\PackageService;

class Service implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    public function isMailTemplateWhitelisted(string $templateName): bool
    {
        /** @var Site $site */
        /** @noinspection PhpUnhandledExceptionInspection */
        $site = $this->app->make('site')->getSite();
        $config = $site->getConfigRepository();
        $enabledNotifications = $config->get("manage_email_notifications.enabled_notifications");

        if (isset($enabledNotifications[$templateName]) && $enabledNotifications[$templateName] === false) {
            return false;
        } else {
            return true;
        }
    }

    public function getMailTemplates(): array
    {
        $mailTemplates = [];
        $mailTemplateDirectories = [];

        $mailTemplateDirectories[] = DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_MAIL_TEMPLATES;

        /** @var PackageService $packageService */
        /** @noinspection PhpUnhandledExceptionInspection */
        $packageService = $this->app->make(PackageService::class);

        foreach ($packageService->getAvailablePackages(false) as $pkg) {
            $mailTemplateDirectories[] = $pkg->getPackagePath() . DIRECTORY_SEPARATOR . DIRNAME_MAIL_TEMPLATES;
        }

        foreach ($mailTemplateDirectories as $mailTemplateDirectory) {
            if (is_dir($mailTemplateDirectory)) {
                foreach (scandir($mailTemplateDirectory) as $file) {
                    $absFile = $mailTemplateDirectory . DIRECTORY_SEPARATOR . $file;
                    if ($file !== "." &&
                        $file !== ".." &&
                        is_file($absFile) &&
                        strtolower(pathinfo($file, PATHINFO_EXTENSION)) === "php") {
                        $mailTemplate = pathinfo($file, PATHINFO_FILENAME);
                        $mailTemplates[$mailTemplate] = $mailTemplate;
                    }
                }
            }
        }

        return array_keys($mailTemplates);
    }
}
