<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 15:21
 */

namespace famoser\phpFrame\Services;


use famoser\phpFrame\Models\Services\ControllerModel;

class RuntimeService extends ServiceBase
{
    private $totalParams;
    private $routeParams;
    private $controllerParams;

    private $frameworkDirectory;
    private $frameworkAssetsDirectory;
    private $templatesDirectory;
    private $cacheDirectory;
    private $baseDirectory;

    private $request;

    public function __construct()
    {
        parent::__construct();
        $documentRoot = $this->getConfig("DocumentRootConnect");
        if (str_ends_with($documentRoot, "/"))
            $documentRoot = substr($documentRoot, 0, -1);
        $this->templatesDirectory = $_SERVER["DOCUMENT_ROOT"] . $documentRoot . DIRECTORY_SEPARATOR . $this->getConfig("TemplatesDirectory");
        $this->frameworkAssetsDirectory = $_SERVER["DOCUMENT_ROOT"] . $documentRoot . DIRECTORY_SEPARATOR . $this->getConfig("FrameworkAssetsDirectory");
        $this->cacheDirectory = $_SERVER["DOCUMENT_ROOT"] . $documentRoot . DIRECTORY_SEPARATOR . $this->getConfig("CacheDirectory");
        $this->baseDirectory = $_SERVER["DOCUMENT_ROOT"] . $documentRoot;
    }

    /**
     * @param string $uri
     * @param ControllerModel $controller
     */
    public function setParams($uri, ControllerModel $controller)
    {
        $this->routeParams = remove_empty_entries(explode("/", $controller->getUrl()));

        $controllerParams = remove_empty_entries(explode("/", substr($uri, strlen($controller->getUrl()))));

        if (count($controllerParams) > 0) {
            $paramNumber = count($controllerParams) - 1;
            $lastParam = $controllerParams[$paramNumber];
            if (($index = strpos($lastParam, "?_=")) !== false)
                $controllerParams[$paramNumber] = substr($lastParam, 0, $index);
        }
        $this->controllerParams = $controllerParams;
        $this->totalParams = array_merge($this->routeParams, $this->controllerParams);
    }

    /**
     * @return array $params
     */
    public function getTotalParams()
    {
        return $this->totalParams;
    }


    /**
     * @return array $params
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @return string $params
     */
    public function getRouteUrl()
    {
        return implode("/", $this->getRouteParams());
    }

    /**
     * @return string $params
     */
    public function getRouteUrlWithoutController()
    {
        $routeUrl = $this->getRouteUrl();
        return substr($routeUrl, 0, strripos($routeUrl, "/"));
    }


    /**
     * @return string $params
     */
    public function getTotalUrl()
    {
        return implode("/", $this->getTotalParams());
    }

    /**
     * @return mixed
     */
    public function getFrameworkDirectory()
    {
        return $this->frameworkDirectory;
    }

    /**
     * @param string $frameworkDirectory
     */
    public function setFrameworkDirectory($frameworkDirectory)
    {
        $this->frameworkDirectory = $frameworkDirectory;
    }

    /**
     * @return array
     */
    public function getControllerParams()
    {
        return $this->controllerParams;
    }

    /**
     * @return mixed
     */
    public function getTemplatesDirectory()
    {
        return $this->templatesDirectory;
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param array $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getCacheDirectory()
    {
        return $this->cacheDirectory;
    }

    /**
     * @return string
     */
    public function getFrameworkAssetsDirectory()
    {
        return $this->frameworkAssetsDirectory;
    }

    /**
     * @return string
     */
    public function getLocaleDirectory()
    {
        return $this->getFrameworkAssetsDirectory() . DIRECTORY_SEPARATOR . "Locale";
    }

    /**
     * @return string
     */
    public function getFrameworkContentDirectory()
    {
        return $this->getFrameworkDirectory() . DIRECTORY_SEPARATOR . "Content";
    }

    /**
     * @return string
     */
    public function getFrameworkLibraryDirectory()
    {
        return $this->getFrameworkDirectory() . DIRECTORY_SEPARATOR . "Libraries";
    }

    /**
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }
}