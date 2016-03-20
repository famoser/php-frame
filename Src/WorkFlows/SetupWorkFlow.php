<?php
/**
 * Created by PhpStorm.
 * User: florianmoser
 * Date: 08.03.16
 * Time: 19:57
 */

namespace famoser\phpFrame\WorkFlows;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Core\Tracing\TraceHelper;
use famoser\phpFrame\Core\Tracing\TraceInstance;
use famoser\phpFrame\Helpers\CompressionHelper;
use famoser\phpFrame\Helpers\FileHelper;
use famoser\phpFrame\Services\GenericDatabaseService;
use famoser\phpFrame\Services\RuntimeService;

class SetupWorkFlow extends WorkFlowBase
{
    /* @var TraceInstance */
    private $trace;

    private $cssEntries = array();
    private $jsEntries = array();
    private $paths = array();

    public function execute()
    {
        try {
            $this->trace = TraceHelper::getInstance()->getTraceInstance("SetupWorkFlow");
            if (!GenericDatabaseService::getInstance()->setup())
                return false;
            else
                $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "data service initialized");

            if (!$this->processFontFolders())
                return false;
            else
                $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "fonts processed");

            if (!$this->processLibraryFolders())
                return false;
            else
                $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "libraries processed");

            if (!$this->processCssFolders())
                return false;
            else
                $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "css processed");

            if (!$this->processJsFolders())
                return false;
            else
                $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "js processed");

            if (!$this->processFileFolders())
                return false;
            else
                $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "files processed");

            if (!$this->createCss())
                return false;
            else
                $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "css compressed & copied");

            if (!$this->createJs())
                return false;
            else
                $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "js compressed & copied");

            if (!$this->copyFiles())
                return false;
            else
                $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "files copied");

            return true;
        } catch (\Exception $ex) {
            LogHelper::getInstance()->logException($ex, "setup failed :(");
        }
        return false;
    }

    public function refreshCss()
    {

        if (!$this->processFontFolders())
            return false;
        else
            $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "fonts processed");

        if (!$this->processLibraryFolders())
            return false;
        else
            $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "libraries processed");

        if (!$this->processCssFolders())
            return false;
        else
            $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "css processed");

        if (!$this->createCss())
            return false;
        else
            $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "css compressed & copied");

        return true;
    }

    public function refreshJs()
    {
        if (!$this->processLibraryFolders())
            return false;
        else
            $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "libraries processed");

        if (!$this->processJsFolders())
            return false;
        else
            $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "js processed");

        if (!$this->createJs())
            return false;
        else
            $this->trace->trace(TraceHelper::TRACE_LEVEL_INFO, "js compressed & copied");

        return true;
    }

    private function addCopyFile($source, $destination)
    {
        if (isset($this->paths[$destination]))
            $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "file " . $this->paths[$destination] . " for destination " . $destination . " is overwritten by file " . $source);
        $this->paths[$destination] = $source;
    }

    private function getJsonPath($libraryPath)
    {
        return $libraryPath . DIRECTORY_SEPARATOR . "includes.json";
    }

    private function processLibraryFolders()
    {
        $libFolder = RuntimeService::getInstance()->getFrameworkContentDirectory() . DIRECTORY_SEPARATOR . "libraries";
        if (is_dir($libFolder)) {
            return $this->processLibraryFolder($libFolder);
        } else {
            $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "framework library folder does not exist");
        }
        return false;
    }

    private function processLibraryFolder($path)
    {
        $libraryContent = FileHelper::getInstance()->getALlSubFolders($path);
        $included = array();
        foreach ($libraryContent as $library) {
            $libraryName = substr($library, strripos($library, "/") + 1);
            $jsonPath = $this->getJsonPath($library);
            if (!file_exists($jsonPath)) {
                $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "no config found in library: " . $libraryName);
                continue;
            }

            $config = json_decode(file_get_contents($jsonPath), true);
            if (isset($config["required"])) {
                foreach ($config["required"] as $reqLibrary) {
                    if (!in_array($reqLibrary, $included)) {
                        if (!$this->processLibrary($reqLibrary, $path . DIRECTORY_SEPARATOR . $reqLibrary)) {
                            $this->trace->trace(TraceHelper::TRACE_LEVEL_FAILURE, "could not include library '" . $reqLibrary . "' required by '" . $libraryName . "'");
                            return false;
                        }
                        $included[] = $reqLibrary;
                    }
                }
            }

            if (!in_array($libraryName, $included)) {
                if (!$this->processLibrary($libraryName, $library, $config)) {
                    $this->trace->trace(TraceHelper::TRACE_LEVEL_FAILURE, "could not include library '" . $library . "'");
                    return false;
                }
                $included[] = $libraryName;
            }
        }
        return true;
    }

    private function processLibrary($libName, $path, $json = null)
    {
        if ($json == null) {
            $jsonPath = $this->getJsonPath($path);
            if (!file_exists($jsonPath)) {
                $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "no config found in library " . $libName);
                return false;
            }
            $json = json_decode(file_get_contents($jsonPath), true);
        }

        if (isset($json["css"])) {
            foreach ($json["css"] as $css) {
                $cssPath = $path . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . $css;
                if (!file_exists($cssPath)) {
                    $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "css file " . $css . "not found in library " . $libName);
                    continue;
                }

                $this->cssEntries[] = file_get_contents($cssPath);
            }
        }

        if (isset($json["js"])) {
            foreach ($json["js"] as $js) {
                $jsPath = $path . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . $js;
                if (!file_exists($jsPath)) {
                    $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "js file " . $js . "not found in library " . $libName);
                    continue;
                }

                $this->jsEntries[] = file_get_contents($jsPath);
            }
        }

        if (isset($json["img"])) {
            foreach ($json["img"] as $img) {
                $imgPath = $path . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . $img;
                if (!file_exists($imgPath)) {
                    $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "img " . $img . "not found in library " . $libName);
                    continue;
                }

                $this->addCopyFile($imgPath, "img" . DIRECTORY_SEPARATOR . $img);
            }
        }
        return true;
    }

    private function processCssFolders()
    {
        $libFolder = RuntimeService::getInstance()->getFrameworkContentDirectory() . DIRECTORY_SEPARATOR . "css";
        if (is_dir($libFolder)) {
            return $this->processCssFolder($libFolder);
        } else {
            $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "framework css folder does not exist");
        }
        return false;
    }

    private function processCssFolder($path)
    {
        $files = FileHelper::getInstance()->getALlFilesInFolders($path, "css");
        foreach ($files as $file) {
            $this->cssEntries[] = file_get_contents($file);
        }

        $subFolders = FileHelper::getInstance()->getALlSubFolders($path);
        $mediaFound = false;
        foreach ($subFolders as $subFolder) {
            if (str_ends_with($subFolder, "media")) {
                $mediaFound = true;
            } else
                $this->processCssFolder($path . DIRECTORY_SEPARATOR . $subFolder);
        }
        if ($mediaFound) {
            $this->processCssFolder($path . DIRECTORY_SEPARATOR . "media");
        }
        return true;
    }

    private function processJsFolders()
    {
        $libFolder = RuntimeService::getInstance()->getFrameworkContentDirectory() . DIRECTORY_SEPARATOR . "js";
        if (is_dir($libFolder)) {
            return $this->processJsFolder($libFolder);
        } else {
            $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "framework js folder does not exist");
        }
        return false;
    }

    private function processJsFolder($path)
    {
        $subFolders = FileHelper::getInstance()->getALlSubFolders($path);
        foreach ($subFolders as $subFolder) {
            $this->processJsFolder($subFolder);
        }

        $files = FileHelper::getInstance()->getALlFilesInFolders($path, "js");
        foreach ($files as $file) {
            $this->jsEntries[] = file_get_contents($file);
        }
        return true;
    }

    private function processFileFolders()
    {
        $folders = array(
            "img"
        );
        $successful = true;
        foreach ($folders as $folder) {
            $fullFolder = RuntimeService::getInstance()->getFrameworkContentDirectory() . DIRECTORY_SEPARATOR . $folder;
            if (is_dir($fullFolder)) {
                $successful &= $this->processFileFolder($fullFolder, $folder);
            } else {
                $successful = false;
                $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "framework " . $folder . " folder does not exist at " . $fullFolder);
            }
        }
        return $successful;
    }

    private function processFileFolder($path, $relativePath)
    {
        $subFolders = FileHelper::getInstance()->getALlSubFolders($path);
        foreach ($subFolders as $subFolder) {
            $lastFolder = substr($subFolder, strripos($subFolder, "/") + 1);
            $this->processFileFolder($subFolder, $relativePath . DIRECTORY_SEPARATOR . $lastFolder);
        }

        $files = FileHelper::getInstance()->getALlFilesInFolders($path);
        foreach ($files as $file) {
            $fileName = substr($file, strripos($file, "/") + 1);
            $this->addCopyFile($file, $relativePath . DIRECTORY_SEPARATOR . $fileName);
        }
        return true;
    }

    private function processFontFolders()
    {
        $fontFolder = RuntimeService::getInstance()->getFrameworkContentDirectory() . DIRECTORY_SEPARATOR . "fonts";
        if (is_dir($fontFolder)) {
            return $this->processFontFolder($fontFolder);
        } else {
            $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "framework library folder does not exist");
        }
        return false;
    }

    private function processFontFolder($path)
    {
        $fontFolders = FileHelper::getInstance()->getALlSubFolders($path);
        foreach ($fontFolders as $font) {
            $jsonPath = $this->getJsonPath($font);
            $fontName = substr($font, strripos($font, "/") + 1);
            if (!file_exists($jsonPath)) {
                $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "no config found in font folder: " . $fontName);
                continue;
            }

            if (!$this->processFont($fontName, $font)) {
                $this->trace->trace(TraceHelper::TRACE_LEVEL_FAILURE, "could not include font '" . $fontName . "'");
                return false;
            }
        }
        return true;
    }

    private function processFont($fontName, $path)
    {
        $jsonPath = $this->getJsonPath($path);
        if (!file_exists($jsonPath)) {
            $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "no config found for font " . $fontName);
            return false;
        }
        $json = json_decode(file_get_contents($jsonPath), true);

        if (!isset($json["fonts"])) {
            $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "config file for font " . $fontName . " has no fonts entry");
            return false;
        }

        foreach ($json["fonts"] as $font) {
            if (!isset($font["weight"]))
                $font["weight"] = "normal";
            if (!isset($font["style"]))
                $font["style"] = "normal";

            if (!isset($json["family"])) {
                $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "font family not specified in config file for font " . $fontName);
                return false;
            }

            if (!isset($font["file"])) {
                $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "no font file specified for font " . $fontName);
                return false;
            }

            $css = "
@font-face {
    font-family: '" . $json["family"] . "';
    font-weight: " . $font["weight"] . ";
    font-style: " . $font["style"] . ";";

            $baseFileName = $path . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $font["file"];
            $fontPath = "fonts";
            $sourceSet = false;
            if (file_exists($baseFileName . ".eot")) {
                $css .= "src: url('../" . $fontPath . "/" . $font["file"] . ".eot');"; // IE9 Compat Modes
                $css .= "src: url('../" . $fontPath . "/" . $font["file"] . ".eot?#iefix') format('embedded-opentype')"; // IE6-IE8
                $sourceSet = true;
                $this->addCopyFile($baseFileName . ".eot", $fontPath . DIRECTORY_SEPARATOR . $font["file"] . ".eot");
            }

            if (file_exists($baseFileName . ".woff2")) {
                if (!$sourceSet)
                    $css .= "src: ";
                else
                    $css .= ", ";
                $css .= "url('../" . $fontPath . "/" . $font["file"] . ".woff2') format('woff2')"; //Super Modern Browsers
                $sourceSet = true;
                $this->addCopyFile($baseFileName . ".woff2", $fontPath . DIRECTORY_SEPARATOR . $font["file"] . ".woff2");
            }

            if (file_exists($baseFileName . ".woff")) {
                if (!$sourceSet)
                    $css .= "src: ";
                else
                    $css .= ", ";
                $css .= "url('../" . $fontPath . "/" . $font["file"] . ".woff') format('woff')"; //Pretty Modern Browsers
                $sourceSet = true;
                $this->addCopyFile($baseFileName . ".woff", $fontPath . DIRECTORY_SEPARATOR . $font["file"] . ".woff");
            }

            if (file_exists($baseFileName . ".ttf")) {
                if (!$sourceSet)
                    $css .= "src: ";
                else
                    $css .= ", ";
                $css .= "url('../" . $fontPath . "/" . $font["file"] . ".ttf') format('truetype')"; //Safari, Android, iOS
                $sourceSet = true;
                $this->addCopyFile($baseFileName . ".ttf", $fontPath . DIRECTORY_SEPARATOR . $font["file"] . ".ttf");
            }

            if (file_exists($baseFileName . ".otf")) {
                if (!$sourceSet)
                    $css .= "src: ";
                else
                    $css .= ", ";
                $css .= "url('../" . $fontPath . "/" . $font["file"] . ".otf') format('opentype')"; //Modern Browsers
                $sourceSet = true;
                $this->addCopyFile($baseFileName . ".otf", $fontPath . DIRECTORY_SEPARATOR . $font["file"] . ".otf");
            }

            if (file_exists($baseFileName . ".svg")) {
                if (!$sourceSet)
                    $css .= "src: ";
                else
                    $css .= "";
                $css .= "url('../" . $fontPath . "/" . $font["file"] . ".svg#svgFontName') format('svg')"; //Legacy iOS
                $sourceSet = true;
                $this->addCopyFile($baseFileName . ".svg", $fontPath . DIRECTORY_SEPARATOR . $font["file"] . ".svg");
            }

            if (!$sourceSet) {
                $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "no font file found for font " . $fontName);
                continue;
            } else {
                $css .= ";";
            }

            $css .= "}";
            $this->cssEntries[] = $css;
        }

        if (isset($json["css"])) {
            foreach ($json["css"] as $css) {
                $cssPath = $path . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . $css;
                if (!file_exists($cssPath)) {
                    $this->trace->trace(TraceHelper::TRACE_LEVEL_WARNING, "css file " . $css . "not found found in font folder " . $fontName);
                    continue;
                }

                $this->cssEntries[] = file_get_contents($cssPath);
            }
        }

        return true;
    }

    private function createCss()
    {
        $allCss = "";
        foreach ($this->cssEntries as $cssEntry) {
            $allCss .= " " . $cssEntry;
        }
        $compressedCss = CompressionHelper::getInstance()->compressCss($allCss);
        $cssFolder = RuntimeService::getInstance()->getBaseDirectory() . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR;
        $this->trace->trace(TraceHelper::TRACE_LEVEL_ERROR, $cssFolder);
        if (!is_dir($cssFolder))
            mkdir($cssFolder, 0755, true);
        file_put_contents($cssFolder . DIRECTORY_SEPARATOR . "styles.min.css", $compressedCss);
        file_put_contents($cssFolder . DIRECTORY_SEPARATOR . "styles.css", $allCss);
        return true;
    }

    private function createJs()
    {
        $allJs = "";
        foreach ($this->jsEntries as $jsEntry) {
            $allJs .= " " . $jsEntry;
        }
        $compressedJs = CompressionHelper::getInstance()->compressJavascript($allJs);
        $jsFolder = RuntimeService::getInstance()->getBaseDirectory() . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR;
        if (!is_dir($jsFolder))
            mkdir($jsFolder, 0755, true);
        file_put_contents($jsFolder . DIRECTORY_SEPARATOR . "scripts.min.js", $compressedJs);
        file_put_contents($jsFolder . DIRECTORY_SEPARATOR . "scripts.js", $allJs);
        return true;
    }

    private function copyFiles()
    {
        $successful = true;
        $baseDir = RuntimeService::getInstance()->getBaseDirectory() . DIRECTORY_SEPARATOR;
        foreach ($this->paths as $destination => $source) {
            $folder = substr($destination, 0, strripos($destination, "/"));
            if (!is_dir($baseDir . $folder))
                mkdir($baseDir . $folder, 0755, true);
            if (!file_exists($source)) {
                $this->trace->trace(TraceHelper::TRACE_LEVEL_ERROR, "file cannot be copied because it does not exist. Source: " . $source . " Destination: " . $destination);
                $successful = false;
            } else
                copy($source, $baseDir . $destination);
        }
        return $successful;
    }
}