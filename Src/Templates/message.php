<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 24.05.2015
 * Time: 10:15
 */
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Views\MessageView;

if ($this instanceof MessageView) {
    ?>
    <?php $this->includeFile(PartHelper::getInstance()->getPart(PartHelper::PART_HEADER_CENTER)); ?>
    <div class="clearfix">
        <?php $this->includeFile(PartHelper::getInstance()->getPart(PartHelper::PART_MESSAGES)); ?>
        <?php if ($this->showLink()) { ?>
            <a href="/"><?= LocaleService::getInstance()->translate("back to frontpage") ?></a>
        <?php } ?>
    </div>
    <?php $this->includeFile(PartHelper::getInstance()->getPart(PartHelper::PART_FOOTER_CENTER)); ?>
<?php } ?>