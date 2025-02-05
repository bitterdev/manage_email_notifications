<?php

namespace Bitter\ManageEmailNotifications\Mail;

use Concrete\Core\Mail\Service as CoreMailService;
use Exception;

class Service extends CoreMailService
{

    public function sendMail($resetData = true)
    {
        $signature = null;
        /** @var $siteService \Concrete\Core\Site\Service */
        $siteService = $this->app->make(\Concrete\Core\Site\Service::class);
        $site = $siteService->getSite();
        $config = $site->getConfigRepository();
        $enabledNotifications = (array)$config->get("manage_email_notifications.enabled_notifications", []);
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @var \Bitter\ManageEmailNotifications\EmailNotifications\Service $emailNotificationService */
        $emailNotificationService = $this->app->make(\Bitter\ManageEmailNotifications\EmailNotifications\Service::class);
        $allowedMailTemplates = [];

        if (count($enabledNotifications) === 0) {
            $allowedMailTemplates = $emailNotificationService->getMailTemplates();
        } else {
            foreach ($enabledNotifications as $mailTemplate => $isEnabled) {
                if ($isEnabled) {
                    $allowedMailTemplates[] = $mailTemplate;
                }
            }
        }

        // Add support for Email Signature add-on
        if (class_exists(\Bitter\EmailSignature\Settings::class)) {
            /** @var $settings \Bitter\EmailSignature\Settings */
            $settings = $this->app->make(\Bitter\EmailSignature\Settings::class);
            $signature = $settings->getSignature(null, $site);
        }

        // Save original body
        $body = $this->body;
        $bodyHTML = $this->bodyHTML;

        // Append signature to body
        $this->body = $this->body . strip_tags($signature);

        if (strlen($this->bodyHTML) > 0) {
            $this->bodyHTML = $this->bodyHTML . $signature;
        }

        if (strlen($this->template) > 0 && !in_array($this->template, $allowedMailTemplates)) {
            return true;
        }

        // Send the email
        $retVal = parent::sendMail($resetData);

        if (!empty($resetData)) {
            // Restore original body
            $this->body = $body;
            $this->bodyHTML = $bodyHTML;
        }

        return $retVal;
    }

}