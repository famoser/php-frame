<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 17.01.2016
 * Time: 21:39
 */


use Famoser\phpSLWrapper\CRM\Models\Commons\UserModel;
use Famoser\phpSLWrapper\Framework\Core\Logging\Logger;
use function Famoser\phpSLWrapper\Framework\Helpers\ValidationHelper\assert_file_exist;
use function Famoser\phpSLWrapper\Framework\Hook\bye_framework;
use function Famoser\phpSLWrapper\Framework\Hook\hi_framework;
use Famoser\phpSLWrapper\Framework\Models\DataService\EntityInfo;
use Famoser\phpSLWrapper\Framework\Services\DataService;
use Famoser\phpSLWrapper\Framework\Services\SettingsService;

//prepare framework
require __DIR__ . DIRECTORY_SEPARATOR . "Src" . DIRECTORY_SEPARATOR . "Framework" . DIRECTORY_SEPARATOR . "Hook.php";
hi_framework();
//do stuff

$info = new EntityInfo(new UserModel("Florian"));
$service = DataService::getInstance();
Logger::getInstance()->logDebug("info: ", $info->getProperties());

//say bye
bye_framework();
echo Logger::getInstance()->getLogsAsHtml();
