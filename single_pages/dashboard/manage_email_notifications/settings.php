<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

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
            <?php echo t("Name Mapping"); ?>
        </legend>

        <?php foreach ($mailTemplates as $templateName) { ?>
            <div class="form-group">
                <?php echo $form->label("nameMapping_" . $templateName, t(ucwords(str_replace('_', ' ', $templateName)))); ?>
                <?php echo $form->text("nameMapping[" . $templateName . "]", $nameMapping[$templateName] ?? ucwords(str_replace('_', ' ', $templateName)), ["id" => "nameMapping_" . $templateName, "length" => 255]); ?>
            </div>
        <?php } ?>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $form->submit('save', t('Save'), ['class' => 'btn btn-primary float-end']); ?>
        </div>
    </div>
</form>
