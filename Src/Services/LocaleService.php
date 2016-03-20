<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.02.2016
 * Time: 11:48
 */

namespace famoser\phpFrame\Services;


use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Helpers\FileHelper;
use famoser\phpFrame\Models\Locale\Language;
use famoser\phpFrame\Models\Locale\ResourceWrapper;

class LocaleService extends ServiceBase
{
    private $languages;
    private $activeLang;
    private $activeLangShort;

    public function __construct()
    {
        parent::__construct();

        //parse language resources
        if ($this->getConfig("DefaultLanguage") == null) {
            LogHelper::getInstance()->logWarning("Default language not configured, switching to first available language");
            $this->activeLangShort = $this->getConfig("LanguageResources")[0]["Language"];
        } else
            $this->activeLangShort = $this->getConfig("DefaultLanguage");

        foreach ($this->getConfig("LanguageResources") as $languageResource) {
            $this->languages[$languageResource["Language"]] = new Language($languageResource["Language"], $languageResource, RuntimeService::getInstance()->getLocaleDirectory());
        }

        if (isset($this->languages[$this->activeLangShort]))
            $this->activeLang = $this->languages[$this->activeLangShort];
        else {
            $this->activeLang = array_values($this->languages)[0];
            LogHelper::getInstance()->logError("Default language not found");
        }

        setlocale(LC_ALL, $this->activeLangShort . ".utf8");
    }

    /**
     * @return Language
     */
    public function getActiveLang()
    {
        return $this->activeLang;
    }

    public function getFormats()
    {
        return $this->getActiveLang()->getFormats();
    }

    /**
     * @return ResourceWrapper
     */
    public function getResources()
    {
        return $this->getActiveLang()->getResources();
    }

    /**
     * @param $content
     * @return ResourceWrapper
     */
    public function translate($content)
    {
        return $this->getActiveLang()->getResources()->getKey($content);
    }
}