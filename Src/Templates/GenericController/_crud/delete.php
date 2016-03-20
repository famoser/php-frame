<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 25.05.2015
 * Time: 10:08
 */
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Interfaces\Models\IDatabaseModel;
use famoser\phpFrame\Interfaces\Models\IModel;
use famoser\phpFrame\Models\Database\BaseDatabaseModel;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Views\ViewBase;

?>

<?= PartHelper::getInstance()->getFormStart(); ?>

<?php
if ($this instanceof ViewBase) { ?>
    <p><?= LocaleService::getInstance()->translate("Are you sure you want to delete this") ?>?
    <?php
    foreach ($this->getViewModels() as $viewModel) {
        $model = $this->retrieve($viewModel->getSingleListName());
        if ($model instanceof BaseDatabaseModel) { ?>

            <p><?= $model->getIdentification() ?></p>

        <?php }
    }
} ?>


<?= PartHelper::getInstance()->getFormStart(); ?>
