<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 19.02.2016
 * Time: 14:37
 */

use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Helpers\RequestHelper;
use famoser\phpFrame\Models\Database\BaseDatabaseModel;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Services\RuntimeService;
use famoser\phpFrame\Views\ViewBase;

if ($this instanceof ViewBase) { ?>

    <div class="col-md-3 right-content">
        <?php
        foreach ($this->getViewModels() as $viewModel) { ?>

            <div class="col-md-12 content">
                <a href="<?= PartHelper::getInstance()->getControllerLink($viewModel->getControllerLink() . "/add") ?>"
                   class="tilebutton dialogbutton" data-dialog-title="add new
            <?= $viewModel->getFriendlyName() ?>"
                   data-dialog-size="wide" data-dialog-type="primary">add new <?= $viewModel->getFriendlyName() ?>
                </a>
            </div>

        <?php } ?>

        <div class="col-md-12 content">
            <?php
            foreach ($this->getViewModels() as $viewModel) { ?>
                <input type="text" class="searchinput" placeholder="search <?= $viewModel->getMultipleListName() ?>..."
                       data-table-id="<?= $viewModel->getMultipleListName() ?>">
            <?php } ?>
        </div>
    </div>

    <div class="col-md-9 no-padding">
        <?php
        foreach ($this->getViewModels() as $viewModel) { ?>
            <div class="content">
            <h1><?= $viewModel->getMultiplyFriendlyName() ?></h1>
        <table class="table table-hover sortable" id="<?= $viewModel->getMultipleListName() ?>">
            <thead>
            <tr>
                <th data-sort="string">
                    <a>name</a>
                </th>
                <th class="buttons buttons3"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $models = $this->retrieve($viewModel->getMultipleListName());
            if (is_array($models)) {
                foreach ($models as $model) {
                    if ($model instanceof BaseDatabaseModel) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $model->GetIdentification(); ?>
                            </td>
                            <td>
                                <?= PartHelper::getInstance()->getDialogButtons(
                                    PartHelper::getInstance()->getControllerLink($viewModel->getControllerLink()),
                                    $model->getId(),
                                    array(PartHelper::DIALOG_READ, PartHelper::DIALOG_UPDATE, PartHelper::DIALOG_DELETE)) ?>
                            </td>
                        </tr>
                    <?php }
                } ?>
                </tbody>
                </table>
                </div>
            <?php }
        } ?>
    </div>
<?php } ?>