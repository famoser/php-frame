<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 08.03.2016
 * Time: 22:05
 */

namespace famoser\phpFrame\Helpers;


use famoser\phpFrame\Base\AutoLoader;
use famoser\phpFrame\Core\Tracing\TraceHelper;
use famoser\phpFrame\Services\RuntimeService;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

class CompressionHelper extends HelperBase
{
    public function __construct()
    {
        AutoLoader::getInstance()->addNameSpace("MatthiasMullie\\Minify\\", RuntimeService::getInstance()->getFrameworkLibraryDirectory() . DIRECTORY_SEPARATOR . "Minify" . DIRECTORY_SEPARATOR . "src");
    }

    public function compressCss($content)
    {
        $minifier = new CSS();
        $minifier->add($content);
        return $minifier->minify();
    }

    // JavaScript Minimizer
    public function compressJavascript($input)
    {
        $minifier = new JS();
        $minifier->add($input);
        return $minifier->minify();
    }

}