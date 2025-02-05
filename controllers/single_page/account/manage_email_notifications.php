<?php

namespace Concrete\Package\ManageEmailNotifications\Controller\SinglePage\Account;

use Bitter\ManageEmailNotifications\EmailNotifications\Service;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\AccountPageController;
use Concrete\Core\User\User;

class ManageEmailNotifications extends AccountPageController
{
    protected Service $emailNotificationService;

    public function on_start()
    {
        parent::on_start();
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->emailNotificationService = $this->app->make(Service::class);
    }

    protected function setDefaults()
    {
        $this->set("mailTemplates", $this->emailNotificationService->getMailTemplates());
    }

    public function view()
    {
        $this->setDefaults();

        /** @var Site $site */
        /** @noinspection PhpUnhandledExceptionInspection */
        $site = $this->app->make('site')->getSite();
        $config = $site->getConfigRepository();
        /** @var Connection $db */
        /** @noinspection PhpUnhandledExceptionInspection */
        $db = $this->app->make(Connection::class);
        $u = new User();

        if ($this->getRequest()->getMethod() === "POST") {
            /** @var Validation $formValidator */
            /** @noinspection PhpUnhandledExceptionInspection */
            $formValidator = $this->app->make(Validation::class);
            $formValidator->setData($this->request->request->all());
            $formValidator->addRequiredToken("update_settings");

            if ($formValidator->test()) {
                $enabledNotifications = [];

                foreach ((array)$this->request->request->get("enabledNotifications", []) as $mailTemplate => $isChecked) {
                    $enabledNotifications[] = $mailTemplate;
                }

                $db->executeQuery("DELETE FROM DisabledNotifications WHERE uID = ?", [$u->getUserID()]);

                foreach($this->emailNotificationService->getMailTemplates() as $mailTemplate) {
                    if (!in_array($mailTemplate, $enabledNotifications)) {
                        $db->insert("DisabledNotifications", [
                            "uID" => $u->getUserID(),
                            "mailTemplate" => $mailTemplate
                        ]);
                    }
                }

                if (!$this->error->has()) {
                    $this->set("success", t("The settings has been successfully updated."));
                }
            } else {
                /** @var ErrorList $errorList */
                $errorList = $formValidator->getError();

                foreach ($errorList->getList() as $error) {
                    $this->error->add($error);
                }
            }
        }

        $disabledNotifications = [];

        foreach ($db->fetchAll("SELECT mailTemplate FROM DisabledNotifications WHERE uID = ?", [$u->getUserID()]) as $row) {
            $disabledNotifications[] = $row["mailTemplate"];
        }

        $this->set("nameMapping", $config->get("manage_email_notifications.name_mapping"));
        $this->set("disabledNotifications", $disabledNotifications);
    }
}