<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 04.09.2015
 * Time: 10:50
 */
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Helpers\RequestHelper;
use famoser\phpFrame\Views\ViewBase;


if ($this instanceof ViewBase) { ?>


<?php if (RequestHelper::getInstance()->isAjaxRequest()) { ?>
<div class="row no-gutters content clearfix">
    <?php
    } else {
    $this->includeFile(PartHelper::getInstance()->getPart(PartHelper::PART_HEADER_CONTENT)); ?>
    <div class="row content">
        <?php
        }
        $this->includeFile(PartHelper::getInstance()->getPart(PartHelper::PART_MESSAGES));
        $this->includeFile(PartHelper::getInstance()->getPart(PartHelper::PART_LOADING_PLACEHOLDER)); ?>
        <?php } ?>

