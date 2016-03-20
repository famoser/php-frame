<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 01.07.2015
 * Time: 19:21
 */
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Views\ViewBase;

if ($this instanceof ViewBase) { ?>

    <?php
    $this->includeFile(PartHelper::getInstance()->getPart(PartHelper::PART_HEAD));
    ?>

    <body>
<div class="mobile-container">
    <div id="loading-bar"></div>
    <header class="no-menu">
        <div class="container">
            <div class="clearfix">
                <div class="col-md-6">
                    <a href="/">
                        <img class="brand" width="111" height="33" alt="Admin Logo" src="/img/Logo.png">
                    </a>
                </div>
                <div class="col-md-6">
                    <h2 class="application"><?= $this->getApplicationTitle() ?></h2>
                </div>
            </div>
        </div>
    </header>

    <div class="center-content-wrapper">
    <div class="container">
    <div class="center-content">
    <?php
    /*include $_SERVER["DOCUMENT_ROOT"] . "/src/Framework/Templates/_parts/messages.php" */
    $this->includeFile(PartHelper::getInstance()->getPart(PartHelper::PART_MESSAGES));
    ?>
    <div class="content">


<?php }
?>