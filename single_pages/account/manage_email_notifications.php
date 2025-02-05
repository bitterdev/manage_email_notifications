<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var array $enabledNotifications */
/** @var array $mailTemplates */
/** @var array $nameMapping */

$app = Application::getFacadeApplication();
/** @var Form $form */
/** @noinspection PhpUnhandledExceptionInspection */
$form = $app->make(Form::class);
/** @var Token $token */
/** @noinspection PhpUnhandledExceptionInspection */
$token = $app->make(Token::class);
?>

<form action="#" method="post">
    <?php echo $token->output("update_settings"); ?>

    <fieldset>
        <legend>
            <?php echo t("Email Notifications"); ?>
        </legend>

        <p>
            <?php echo t("Please choose which email notifications you would like to enable or disable."); ?>
        </p>

        <div class="form-group">
            <?php foreach ($mailTemplates as $templateName) { ?>
                <div class="form-check">
                    <?php echo $form->checkbox("enabledNotifications[" . $templateName . "]", true, $enabledNotifications[$templateName] ?? true, ["id" => "enabledNotifications_" . $templateName]); ?>
                    <?php echo $form->label("enabledNotifications_" . $templateName, $nameMapping[$templateName] ?? ucwords(str_replace('_', ' ', $templateName))); ?>
                </div>
            <?php } ?>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $form->submit('save', t('Save'), ['class' => 'btn btn-primary float-end']); ?>
        </div>
    </div>
</form>