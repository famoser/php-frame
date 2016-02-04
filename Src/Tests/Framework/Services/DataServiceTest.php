<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 19.01.2016
 * Time: 11:20
 */

/**
 * @group framework
 */

namespace Famoser\phpSLWrapper\Tests\Framework\Services;


use Famoser\phpSLWrapper\Framework\Services\DataService;
use Famoser\phpSLWrapper\Tests\Models\TestModel;
use PHPUnit_Framework_TestCase;

class DataServiceTest extends PHPUnit_Framework_TestCase
{
    public function testSaveToDatabase()
    {
        include dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "TestHelper.php";
        InitTestFramework();

        $model = new TestModel("content");
        $this->assertTrue($model->getId() == 0);

        $service = DataService::getInstance();
        $this->assertNotNull($service);

        $service->saveToDatabase($model);
        $this->assertTrue($model->getId() > 0);

        $objs = $service->getFromDatabase("Famoser\\phpSLWrapper\\Tests\\Models\\TestModel");
        if (count($objs) > 0) {
            $this->assertTrue($objs[count($objs)]->getText() == "content");
            $this->assertTrue($objs[count($objs)]->getId() > 0);
        }
    }
}
