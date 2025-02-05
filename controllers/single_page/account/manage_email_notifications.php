<?php

namespace Concrete\Package\ManageEmailNotifications\Controller\SinglePage\Account;

use Bitter\ManageEmailNotifications\EmailNotifications\Service;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\AccountPageController;

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

        if ($this->getRequest()->getMethod() === "POST") {
            /** @var Validation $formValidator */
            /** @noinspection PhpUnhandledExceptionInspection */
            $formValidator = $this->app->make(Validation::class);
            $formValidator->setData($this->request->request->all());
            $formValidator->addRequiredToken("update_settings");

            if ($formValidator->test()) {
                $enabledNotifications = (array)$this->request->request->get("enabledNotifications", []);

                foreach($this->emailNotificationService->getMailTemplates() as $templateName) {
                    if (!isset($enabledNotifications[$templateName])) {
                        $enabledNotifications[$templateName] = false;
                    }
                }

                $config->save("manage_email_notifications.enabled_notifications", $enabledNotifications);

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

        $this->set("nameMapping", $config->get("manage_email_notifications.name_mapping"));
        $this->set("enabledNotifications", $config->get("manage_email_notifications.enabled_notifications"));
    }
}