<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 04.09.2015
 * Time: 10:51
 */
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Helpers\RequestHelper;
use famoser\phpFrame\Views\ViewBase;

if ($this instanceof ViewBase) { ?>

    </div>
    <?php if (!RequestHelper::getInstance()->isAjaxRequest())
        echo $this->includeFile(PartHelper::getInstance()->getPart(PartHelper::PART_FOOTER_CONTENT));
    ?>

<?php } ?>
