<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 19.02.2016
 * Time: 12:50
 */
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Models\Database\BaseDatabaseModel;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Views\ViewBase;

?>

<?= PartHelper::getInstance()->getFormStart(); ?>

<?php
if ($this instanceof ViewBase) {
    foreach ($this->getViewModels() as $viewModel) {
        $model = $this->retrieve($viewModel->getSingleListName());
        if ($model instanceof BaseDatabaseModel) { ?>

            <p><?= LocaleService::getInstance()->translate("Edit") ?>
                <b><?= $model->getIdentification() ?></b></p>

            <?= PartHelper::getInstance()->editDatabaseProperties($model, $this); ?>

        <?php }
    }
}
?>

<?= PartHelper::getInstance()->getFormEnd(); ?>
