<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 04.09.2015
 * Time: 01:58
 */
use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Core\Tracing\TraceHelper;
use famoser\phpFrame\Helpers\PartHelper;

$logs = LogHelper::getInstance()->getLogs();
$traces = TraceHelper::getInstance()->getFullTrace();
if ($logs != null) {
    foreach ($logs as $log) { ?>
        <div class="col-md-12 content message <?= PartHelper::getInstance()->getLogClass($log); ?>">
            <div class="col-md-11">
                <?= PartHelper::getInstance()->getLogText($log); ?>
            </div>
            <div class="col-md-1">
                <button class="close removebutton" data-remove-parent="2">×</button>
            </div>
        </div>
        <?php
    }
}

if (count($traces) > 0) {
    foreach ($traces as $source => $entries) {
        ?>
        <div class="col-md-12 content trace info-message">
            <div class="col-md-11">
                <p><b><?= $source ?></b></p>
                <p>
                    <? foreach ($entries as $entry) { ?>
                        <?= $entry ?><br/>
                    <?php } ?>
                </p>
            </div>
            <div class="col-md-1">
                <button class="close removebutton" data-remove-parent="2">×</button>
            </div>
        </div>
        <?
    }
}