<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 24.05.2015
 * Time: 10:15
 */
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Views\ViewBase;


if ($this instanceof ViewBase) { ?>

<?php echo PartHelper::getInstance()->getPart(PartHelper::PART_HEAD); ?>
<body>
<div class="mobile-container">
    <div id="loading-bar"></div>

    <a class="arrow-top"></a>

    <header>
        <div class="container">
            <div class="clearfix">
                <div class="col-md-3">
                    <a href="/">
                        <img class="brand" width="111" height="33" alt="Admin Logo" src="/img/Logo.png">
                    </a>
                    <ul class="tiles menu-toggle">
                        <li>
                            <a href="#">
                                <span class="flaticon-menu55" aria-hidden="true"></span>Menu
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h2 class="application"><?= $this->getApplicationTitle(); ?></h2>
                </div>
                <div class="col-md-3">
                    <div class="support">
                        <p><a href="mailto:me@famoser.ch">Support kontaktieren</a></p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div id="topbar" class="clearfix">
        <div class="container">
            <?php echo PartHelper::getInstance()->getPart(PartHelper::PART_MENU); ?>
        </div>
    </div>

    <div id="tab-content-slider">
        <div class="container">
            <div id="tab-content" class="clearfix">
                <?php echo PartHelper::getInstance()->getPart(PartHelper::PART_MESSAGES); ?>

                <?php } ?>
