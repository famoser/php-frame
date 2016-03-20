<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 19.02.2016
 * Time: 13:54
 */

use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Models\Database\BaseDatabaseModel;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Views\ViewBase;

if ($this instanceof ViewBase) {
    foreach ($this->getViewModels() as $viewModel) {
        $model = $this->retrieve($viewModel->getSingleListName());
        if ($model instanceof BaseDatabaseModel) {
            ?>

            <p><?= LocaleService::getInstance()->translate("View") ?>
                <b><?= $model->getIdentification() ?></b></p>

            <?= PartHelper::getInstance()->readDatabaseProperties($model, $this); ?>

        <?php }
    }
} ?>