<?php

class formatSettings
{

    /**
     * @var string $timeZone
     */
    protected $timeZone = null;

    /**
     * @var string $dateFormat
     */
    protected $dateFormat = null;

    /**
     * @var string $locale
     */
    protected $locale = null;

    /**
     * @param string $timeZone
     * @param string $dateFormat
     * @param string $locale
     */
    public function __construct($timeZone, $dateFormat, $locale)
    {
      $this->timeZone = $timeZone;
      $this->dateFormat = $dateFormat;
      $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getTimeZone()
    {
      return $this->timeZone;
    }

    /**
     * @param string $timeZone
     * @return formatSettings
     */
    public function setTimeZone($timeZone)
    {
      $this->timeZone = $timeZone;
      return $this;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
      return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     * @return formatSettings
     */
    public function setDateFormat($dateFormat)
    {
      $this->dateFormat = $dateFormat;
      return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
      return $this->locale;
    }

    /**
     * @param string $locale
     * @return formatSettings
     */
    public function setLocale($locale)
    {
      $this->locale = $locale;
      return $this;
    }

}
