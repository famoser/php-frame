<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 24.05.2015
 * Time: 10:15
 */
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Models\Database\LoginDatabaseModel;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Views\ViewBase;

if ($this instanceof ViewBase) {
    $model = $this->tryRetrieve("model");
    if ($model instanceof LoginDatabaseModel) { ?>

        <?= PartHelper::getInstance()->getFormStart(); ?>

        <p><?= LocaleService::getInstance()->translate("Geben Sie Ihre E-Mail an, mit der Sie hier registriert sind.
    Sofern diese E-Mail im System gefunden wird, bekommen Sie eine Email zugesendet mit Informationen, wie Sie das Passwort zurücksetzten können") ?></p>

        <?= PartHelper::getInstance()->getInput($model, "Username", "Email", "email"); ?><br/>

        <?= PartHelper::getInstance()->getSubmit("send mail") ?>

        <?= PartHelper::getInstance()->getFormEnd(false); ?>

        <?php
    }
}
?>
