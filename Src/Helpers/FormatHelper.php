<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 16.08.2015
 * Time: 22:11
 */

namespace famoser\phpFrame\Helpers;


use DateTime;
use famoser\phpFrame\Core\Logging\LogHelper;
use famoser\phpFrame\Models\Locale\Language;
use famoser\phpFrame\Services\LocaleService;
use famoser\phpFrame\Services\SettingsService;
use famoser\phpFrame\Models\Locale\ResourceWrapper;

class FormatHelper extends HelperBase
{
    private $formats;
    private $resources;

    public function __construct()
    {
        $this->formats = LocaleService::getInstance()->getFormats();
        $this->resources = LocaleService::getInstance()->getResources();
    }

    public function textOrPlaceholder($input)
    {
        if ($input == "")
            return "-";
        return $input;
    }

    public function dateTimeFromString($str)
    {
        return date($this->formats["DateTime"]["Display"], strtotime($str));
    }

    public function dateTime($input)
    {
        return $this->dateTimeInput($input);
    }

    public function dateTimeInput($input)
    {
        $res = "";
        $time = $this->parseDateTimeObject($input);
        if ($time !== false) {
            return $time->format($this->formats["DateTime"]["Display"]);
        }
        return $res;
    }

    public function dateTimeDatabase($input)
    {
        $time = $this->parseDateTimeObject($input);
        if ($time !== false) {
            return $time->format($this->formats["DateTime"]["Database"]);
        }
        return null;
    }

    public function dateTimeShort($input)
    {
        $res = "";
        $time = $this->parseDateTimeObject($input);
        if ($time !== false) {
            $res .= $time->format($this->formats["DateTime"]["Display"]);
        }
        return $res;
    }

    public function dateTimeText($input, $input2 = null)
    {
        $res = "";
        $time = $this->parseDateTimeObject($input);
        if ($time !== false) {
            $date = $this->dateText($input);
            $res = $date . ", " . $this->dateTimeShort($input);
            if ($input2 != null) {
                $date2 = $this->dateText($input2);
                if ($date2 != $date) {
                    $date2text = $this->dateTimeText($input2);
                    if ($date2text != "")
                        $res .= " - " . $date2text;
                } else
                    $res .= " - " . $this->dateTimeShort($input);
            }
        }
        return $res;
    }

    public function dateFromString($str)
    {
        return date($this->formats["Date"]["Display"], strtotime($str));
    }

    public function date($input)
    {
        return $this->dateInput($input);
    }

    public function dateInput($input)
    {
        $res = "";
        $time = $this->parseDateTimeObject($input);
        if ($time !== false) {
            return $time->format($this->formats["Date"]["Display"]);
        }
        return $res;
    }

    public function dateDatabase($input)
    {
        $time = $this->parseDateTimeObject($input);
        if ($time !== false) {
            return $time->format($this->formats["Date"]["Database"]);
        }
        return null;
    }

    public function dateText($input)
    {
        $time1 = $this->parseDateTimeObject($input);
        if ($time1 !== false) {
            $res = $time1->format("l") . ", " . $time1->format("d") . " " . $time1->format("F") . " " . $time1->format("Y");
            return $res;
        }
        return "-";
    }

    public function timeFromString($str)
    {
        return date($this->formats["Time"]["Display"], strtotime($str));
    }

    public function timestamp()
    {
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));

        return $d->format("H:i:s.u");
    }

    public function time($input)
    {
        return $this->timeInput($input);
    }

    public function timeInput($input)
    {
        $res = "";
        $time = $this->parseDateTimeObject($input);
        if ($time !== false) {
            return $time->format($this->formats["Time"]["Display"]);
        }
        return $res;
    }

    public function timeDatabase($input)
    {
        $time = $this->parseDateTimeObject($input);
        if ($time !== false) {
            return $time->format($this->formats["Time"]["Database"]);
        }
        return null;
    }

    public function timeSpanMinutesShort($input1, $input2)
    {
        $time1 = $this->parseDateTimeObject($input1);
        $time2 = $this->parseDateTimeObject($input2);

        if ($time1 == false || $time2 == false)
            return 0;

        return abs($time1->getTimestamp() - $time2->getTimestamp()) / 60;
    }

    public function timeSpanMinutesText($input1, $input2)
    {
        $time1 = $this->parseDateTimeObject($input1);
        $time2 = $this->parseDateTimeObject($input2);

        if ($time1 == false || $time2 == false)
            return "";

        return (abs($time1->getTimestamp() - $time2->getTimestamp()) / 60) . " " . $this->resources->getKey("Minutes");
    }

    public function timeSpanHoursMinutesShort($timeSpan)
    {
        $std = $timeSpan / 60;
        $min = $timeSpan % 60;
        return number_format($std, 0) . ":" . $min;
    }

    public function timeSpanHoursMinutesText($timeSpan)
    {
        $std = $timeSpan / 60;
        $min = $timeSpan % 60;
        return number_format($std, 0) . " " . $this->resources->getKey("Hours") . ", " . $min . " " . $this->resources->getKey("Minutes");
    }

    private function parseDateTimeObject($input)
    {
        $time = DateTime::createFromFormat($this->formats["DateTime"]["Database"], $input);
        if ($time == false)
            $time = DateTime::createFromFormat($this->formats["Date"]["Database"], $input);
        if ($time == false)
            $time = DateTime::createFromFormat($this->formats["Time"]["Database"], $input);
        return $time;
    }

    public function money($money, $isZeroValid = true)
    {
        if ($money == 0)
            if ($isZeroValid)
                return "- " . $this->resources->getKey("Currency");
            else
                return "-";
        return number_format($money, 2) . " " . $this->resources->getKey("Currency");
    }

    public function percentage($value, $maxValue = 100)
    {
        $percentage = $value / $maxValue * 100;
        return number_format($percentage, 0);
    }
}