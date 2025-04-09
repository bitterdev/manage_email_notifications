<?php

namespace Bitter\ManageEmailNotifications\Mail;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Mail\Service as CoreMailService;
use Concrete\Core\User\User;
use Exception;

class Service extends CoreMailService
{

    public function sendMail($resetData = true)
    {
        $signature = null;
        /** @var $siteService \Concrete\Core\Site\Service */
        $siteService = $this->app->make(\Concrete\Core\Site\Service::class);
        $site = $siteService->getSite();
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $u = new User();

        if ($this->template !== "" && (int)$db->fetchOne("SELECT COUNT(*) FROM DisabledNotifications WHERE uID = ? AND mailTemplate = ?", [$u->getUserID(), $this->template]) > 0) {
            // Ignore Mail
            return true;
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