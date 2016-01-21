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
use PHPUnit_Framework_TestCase;

class DataServiceTest extends PHPUnit_Framework_TestCase
{
    public function testSaveToDatabase()
    {
        include dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "TestHelper.php";
        InitTestFramework();

        $service = DataService::getInstance();
        $this->assertNotNull($service);
        $this->assertTrue(true);
    }
}
