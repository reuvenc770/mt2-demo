<?php
namespace App\Library\Bronto;
class readOnlyContactData
{

    /**
     * @var string $geoIPCity
     */
    protected $geoIPCity = null;

    /**
     * @var string $geoIPStateRegion
     */
    protected $geoIPStateRegion = null;

    /**
     * @var string $geoIPZip
     */
    protected $geoIPZip = null;

    /**
     * @var string $geoIPCountry
     */
    protected $geoIPCountry = null;

    /**
     * @var string $geoIPCountryCode
     */
    protected $geoIPCountryCode = null;

    /**
     * @var string $primaryBrowser
     */
    protected $primaryBrowser = null;

    /**
     * @var string $mobileBrowser
     */
    protected $mobileBrowser = null;

    /**
     * @var string $primaryEmailClient
     */
    protected $primaryEmailClient = null;

    /**
     * @var string $mobileEmailClient
     */
    protected $mobileEmailClient = null;

    /**
     * @var string $operatingSystem
     */
    protected $operatingSystem = null;

    /**
     * @var \DateTime $firstOrderDate
     */
    protected $firstOrderDate = null;

    /**
     * @var \DateTime $lastOrderDate
     */
    protected $lastOrderDate = null;

    /**
     * @var float $lastOrderTotal
     */
    protected $lastOrderTotal = null;

    /**
     * @var int $totalOrders
     */
    protected $totalOrders = null;

    /**
     * @var float $totalRevenue
     */
    protected $totalRevenue = null;

    /**
     * @var float $averageOrderValue
     */
    protected $averageOrderValue = null;

    /**
     * @var \DateTime $lastDeliveryDate
     */
    protected $lastDeliveryDate = null;

    /**
     * @var \DateTime $lastOpenDate
     */
    protected $lastOpenDate = null;

    /**
     * @var \DateTime $lastClickDate
     */
    protected $lastClickDate = null;

    /**
     * @param string $geoIPCity
     * @param string $geoIPStateRegion
     * @param string $geoIPZip
     * @param string $geoIPCountry
     * @param string $geoIPCountryCode
     * @param string $primaryBrowser
     * @param string $mobileBrowser
     * @param string $primaryEmailClient
     * @param string $mobileEmailClient
     * @param string $operatingSystem
     * @param \DateTime $firstOrderDate
     * @param \DateTime $lastOrderDate
     * @param float $lastOrderTotal
     * @param int $totalOrders
     * @param float $totalRevenue
     * @param float $averageOrderValue
     * @param \DateTime $lastDeliveryDate
     * @param \DateTime $lastOpenDate
     * @param \DateTime $lastClickDate
     */
    public function __construct($geoIPCity, $geoIPStateRegion, $geoIPZip, $geoIPCountry, $geoIPCountryCode, $primaryBrowser, $mobileBrowser, $primaryEmailClient, $mobileEmailClient, $operatingSystem, \DateTime $firstOrderDate, \DateTime $lastOrderDate, $lastOrderTotal, $totalOrders, $totalRevenue, $averageOrderValue, \DateTime $lastDeliveryDate, \DateTime $lastOpenDate, \DateTime $lastClickDate)
    {
      $this->geoIPCity = $geoIPCity;
      $this->geoIPStateRegion = $geoIPStateRegion;
      $this->geoIPZip = $geoIPZip;
      $this->geoIPCountry = $geoIPCountry;
      $this->geoIPCountryCode = $geoIPCountryCode;
      $this->primaryBrowser = $primaryBrowser;
      $this->mobileBrowser = $mobileBrowser;
      $this->primaryEmailClient = $primaryEmailClient;
      $this->mobileEmailClient = $mobileEmailClient;
      $this->operatingSystem = $operatingSystem;
      $this->firstOrderDate = $firstOrderDate->format(\DateTime::ATOM);
      $this->lastOrderDate = $lastOrderDate->format(\DateTime::ATOM);
      $this->lastOrderTotal = $lastOrderTotal;
      $this->totalOrders = $totalOrders;
      $this->totalRevenue = $totalRevenue;
      $this->averageOrderValue = $averageOrderValue;
      $this->lastDeliveryDate = $lastDeliveryDate->format(\DateTime::ATOM);
      $this->lastOpenDate = $lastOpenDate->format(\DateTime::ATOM);
      $this->lastClickDate = $lastClickDate->format(\DateTime::ATOM);
    }

    /**
     * @return string
     */
    public function getGeoIPCity()
    {
      return $this->geoIPCity;
    }

    /**
     * @param string $geoIPCity
     * @return readOnlyContactData
     */
    public function setGeoIPCity($geoIPCity)
    {
      $this->geoIPCity = $geoIPCity;
      return $this;
    }

    /**
     * @return string
     */
    public function getGeoIPStateRegion()
    {
      return $this->geoIPStateRegion;
    }

    /**
     * @param string $geoIPStateRegion
     * @return readOnlyContactData
     */
    public function setGeoIPStateRegion($geoIPStateRegion)
    {
      $this->geoIPStateRegion = $geoIPStateRegion;
      return $this;
    }

    /**
     * @return string
     */
    public function getGeoIPZip()
    {
      return $this->geoIPZip;
    }

    /**
     * @param string $geoIPZip
     * @return readOnlyContactData
     */
    public function setGeoIPZip($geoIPZip)
    {
      $this->geoIPZip = $geoIPZip;
      return $this;
    }

    /**
     * @return string
     */
    public function getGeoIPCountry()
    {
      return $this->geoIPCountry;
    }

    /**
     * @param string $geoIPCountry
     * @return readOnlyContactData
     */
    public function setGeoIPCountry($geoIPCountry)
    {
      $this->geoIPCountry = $geoIPCountry;
      return $this;
    }

    /**
     * @return string
     */
    public function getGeoIPCountryCode()
    {
      return $this->geoIPCountryCode;
    }

    /**
     * @param string $geoIPCountryCode
     * @return readOnlyContactData
     */
    public function setGeoIPCountryCode($geoIPCountryCode)
    {
      $this->geoIPCountryCode = $geoIPCountryCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryBrowser()
    {
      return $this->primaryBrowser;
    }

    /**
     * @param string $primaryBrowser
     * @return readOnlyContactData
     */
    public function setPrimaryBrowser($primaryBrowser)
    {
      $this->primaryBrowser = $primaryBrowser;
      return $this;
    }

    /**
     * @return string
     */
    public function getMobileBrowser()
    {
      return $this->mobileBrowser;
    }

    /**
     * @param string $mobileBrowser
     * @return readOnlyContactData
     */
    public function setMobileBrowser($mobileBrowser)
    {
      $this->mobileBrowser = $mobileBrowser;
      return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryEmailClient()
    {
      return $this->primaryEmailClient;
    }

    /**
     * @param string $primaryEmailClient
     * @return readOnlyContactData
     */
    public function setPrimaryEmailClient($primaryEmailClient)
    {
      $this->primaryEmailClient = $primaryEmailClient;
      return $this;
    }

    /**
     * @return string
     */
    public function getMobileEmailClient()
    {
      return $this->mobileEmailClient;
    }

    /**
     * @param string $mobileEmailClient
     * @return readOnlyContactData
     */
    public function setMobileEmailClient($mobileEmailClient)
    {
      $this->mobileEmailClient = $mobileEmailClient;
      return $this;
    }

    /**
     * @return string
     */
    public function getOperatingSystem()
    {
      return $this->operatingSystem;
    }

    /**
     * @param string $operatingSystem
     * @return readOnlyContactData
     */
    public function setOperatingSystem($operatingSystem)
    {
      $this->operatingSystem = $operatingSystem;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFirstOrderDate()
    {
      if ($this->firstOrderDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->firstOrderDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $firstOrderDate
     * @return readOnlyContactData
     */
    public function setFirstOrderDate(\DateTime $firstOrderDate)
    {
      $this->firstOrderDate = $firstOrderDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastOrderDate()
    {
      if ($this->lastOrderDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->lastOrderDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $lastOrderDate
     * @return readOnlyContactData
     */
    public function setLastOrderDate(\DateTime $lastOrderDate)
    {
      $this->lastOrderDate = $lastOrderDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return float
     */
    public function getLastOrderTotal()
    {
      return $this->lastOrderTotal;
    }

    /**
     * @param float $lastOrderTotal
     * @return readOnlyContactData
     */
    public function setLastOrderTotal($lastOrderTotal)
    {
      $this->lastOrderTotal = $lastOrderTotal;
      return $this;
    }

    /**
     * @return int
     */
    public function getTotalOrders()
    {
      return $this->totalOrders;
    }

    /**
     * @param int $totalOrders
     * @return readOnlyContactData
     */
    public function setTotalOrders($totalOrders)
    {
      $this->totalOrders = $totalOrders;
      return $this;
    }

    /**
     * @return float
     */
    public function getTotalRevenue()
    {
      return $this->totalRevenue;
    }

    /**
     * @param float $totalRevenue
     * @return readOnlyContactData
     */
    public function setTotalRevenue($totalRevenue)
    {
      $this->totalRevenue = $totalRevenue;
      return $this;
    }

    /**
     * @return float
     */
    public function getAverageOrderValue()
    {
      return $this->averageOrderValue;
    }

    /**
     * @param float $averageOrderValue
     * @return readOnlyContactData
     */
    public function setAverageOrderValue($averageOrderValue)
    {
      $this->averageOrderValue = $averageOrderValue;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastDeliveryDate()
    {
      if ($this->lastDeliveryDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->lastDeliveryDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $lastDeliveryDate
     * @return readOnlyContactData
     */
    public function setLastDeliveryDate(\DateTime $lastDeliveryDate)
    {
      $this->lastDeliveryDate = $lastDeliveryDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastOpenDate()
    {
      if ($this->lastOpenDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->lastOpenDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $lastOpenDate
     * @return readOnlyContactData
     */
    public function setLastOpenDate(\DateTime $lastOpenDate)
    {
      $this->lastOpenDate = $lastOpenDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastClickDate()
    {
      if ($this->lastClickDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->lastClickDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $lastClickDate
     * @return readOnlyContactData
     */
    public function setLastClickDate(\DateTime $lastClickDate)
    {
      $this->lastClickDate = $lastClickDate->format(\DateTime::ATOM);
      return $this;
    }

}
