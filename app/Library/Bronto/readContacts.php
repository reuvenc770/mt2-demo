<?php
namespace App\Library\Bronto;
class readContacts
{

    /**
     * @var contactFilter $filter
     */
    protected $filter = null;

    /**
     * @var boolean $includeLists
     */
    protected $includeLists = null;

    /**
     * @var string[] $fields
     */
    protected $fields = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @var boolean $includeSMSKeywords
     */
    protected $includeSMSKeywords = null;

    /**
     * @var boolean $includeGeoIPData
     */
    protected $includeGeoIPData = null;

    /**
     * @var boolean $includeTechnologyData
     */
    protected $includeTechnologyData = null;

    /**
     * @var boolean $includeRFMData
     */
    protected $includeRFMData = null;

    /**
     * @var boolean $includeEngagementData
     */
    protected $includeEngagementData = null;

    /**
     * @var boolean $includeSegments
     */
    protected $includeSegments = null;

    /**
     * @param contactFilter $filter
     * @param boolean $includeLists
     * @param string[] $fields
     * @param int $pageNumber
     * @param boolean $includeSMSKeywords
     * @param boolean $includeGeoIPData
     * @param boolean $includeTechnologyData
     * @param boolean $includeRFMData
     * @param boolean $includeEngagementData
     * @param boolean $includeSegments
     */
    public function __construct($filter, $includeLists, array $fields, $pageNumber, $includeSMSKeywords, $includeGeoIPData, $includeTechnologyData, $includeRFMData, $includeEngagementData, $includeSegments)
    {
      $this->filter = $filter;
      $this->includeLists = $includeLists;
      $this->fields = $fields;
      $this->pageNumber = $pageNumber;
      $this->includeSMSKeywords = $includeSMSKeywords;
      $this->includeGeoIPData = $includeGeoIPData;
      $this->includeTechnologyData = $includeTechnologyData;
      $this->includeRFMData = $includeRFMData;
      $this->includeEngagementData = $includeEngagementData;
      $this->includeSegments = $includeSegments;
    }

    /**
     * @return contactFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param contactFilter $filter
     * @return readContacts
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeLists()
    {
      return $this->includeLists;
    }

    /**
     * @param boolean $includeLists
     * @return readContacts
     */
    public function setIncludeLists($includeLists)
    {
      $this->includeLists = $includeLists;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getFields()
    {
      return $this->fields;
    }

    /**
     * @param string[] $fields
     * @return readContacts
     */
    public function setFields(array $fields)
    {
      $this->fields = $fields;
      return $this;
    }

    /**
     * @return int
     */
    public function getPageNumber()
    {
      return $this->pageNumber;
    }

    /**
     * @param int $pageNumber
     * @return readContacts
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeSMSKeywords()
    {
      return $this->includeSMSKeywords;
    }

    /**
     * @param boolean $includeSMSKeywords
     * @return readContacts
     */
    public function setIncludeSMSKeywords($includeSMSKeywords)
    {
      $this->includeSMSKeywords = $includeSMSKeywords;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeGeoIPData()
    {
      return $this->includeGeoIPData;
    }

    /**
     * @param boolean $includeGeoIPData
     * @return readContacts
     */
    public function setIncludeGeoIPData($includeGeoIPData)
    {
      $this->includeGeoIPData = $includeGeoIPData;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeTechnologyData()
    {
      return $this->includeTechnologyData;
    }

    /**
     * @param boolean $includeTechnologyData
     * @return readContacts
     */
    public function setIncludeTechnologyData($includeTechnologyData)
    {
      $this->includeTechnologyData = $includeTechnologyData;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeRFMData()
    {
      return $this->includeRFMData;
    }

    /**
     * @param boolean $includeRFMData
     * @return readContacts
     */
    public function setIncludeRFMData($includeRFMData)
    {
      $this->includeRFMData = $includeRFMData;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeEngagementData()
    {
      return $this->includeEngagementData;
    }

    /**
     * @param boolean $includeEngagementData
     * @return readContacts
     */
    public function setIncludeEngagementData($includeEngagementData)
    {
      $this->includeEngagementData = $includeEngagementData;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeSegments()
    {
      return $this->includeSegments;
    }

    /**
     * @param boolean $includeSegments
     * @return readContacts
     */
    public function setIncludeSegments($includeSegments)
    {
      $this->includeSegments = $includeSegments;
      return $this;
    }

}
