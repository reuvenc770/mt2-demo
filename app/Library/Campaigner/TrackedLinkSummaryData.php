<?php
namespace App\Library\Campaigner;
class TrackedLinkSummaryData
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var int $TotalClicks
     */
    protected $TotalClicks = null;

    /**
     * @var int $UniqueClicks
     */
    protected $UniqueClicks = null;

    /**
     * @var string $Url
     */
    protected $Url = null;

    /**
     * @var string $Format
     */
    protected $Format = null;

    /**
     * @param int $Id
     * @param string $Name
     * @param int $TotalClicks
     * @param int $UniqueClicks
     * @param string $Url
     * @param string $Format
     */
    public function __construct($Id, $Name, $TotalClicks, $UniqueClicks, $Url, $Format)
    {
      $this->Id = $Id;
      $this->Name = $Name;
      $this->TotalClicks = $TotalClicks;
      $this->UniqueClicks = $UniqueClicks;
      $this->Url = $Url;
      $this->Format = $Format;
    }

    /**
     * @return int
     */
    public function getId()
    {
      return $this->Id;
    }

    /**
     * @param int $Id
     * @return TrackedLinkSummaryData
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->Name;
    }

    /**
     * @param string $Name
     * @return TrackedLinkSummaryData
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return int
     */
    public function getTotalClicks()
    {
      return $this->TotalClicks;
    }

    /**
     * @param int $TotalClicks
     * @return TrackedLinkSummaryData
     */
    public function setTotalClicks($TotalClicks)
    {
      $this->TotalClicks = $TotalClicks;
      return $this;
    }

    /**
     * @return int
     */
    public function getUniqueClicks()
    {
      return $this->UniqueClicks;
    }

    /**
     * @param int $UniqueClicks
     * @return TrackedLinkSummaryData
     */
    public function setUniqueClicks($UniqueClicks)
    {
      $this->UniqueClicks = $UniqueClicks;
      return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
      return $this->Url;
    }

    /**
     * @param string $Url
     * @return TrackedLinkSummaryData
     */
    public function setUrl($Url)
    {
      $this->Url = $Url;
      return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
      return $this->Format;
    }

    /**
     * @param string $Format
     * @return TrackedLinkSummaryData
     */
    public function setFormat($Format)
    {
      $this->Format = $Format;
      return $this;
    }

}
