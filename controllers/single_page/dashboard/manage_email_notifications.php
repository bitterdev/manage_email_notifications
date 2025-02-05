<?php

namespace Concrete\Package\ManageEmailNotifications\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class ManageEmailNotifications extends DashboardPageController
{
    public function view()
    {
        return $this->buildRedirectToFirstAccessibleChildPage();
    }
}