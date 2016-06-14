<?php
namespace App\Library\Bronto;
class readDeliveryGroups
{

    /**
     * @var deliveryGroupFilter $filter
     */
    protected $filter = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @var boolean $includeStats
     */
    protected $includeStats = null;

    /**
     * @param deliveryGroupFilter $filter
     * @param int $pageNumber
     * @param boolean $includeStats
     */
    public function __construct($filter, $pageNumber, $includeStats)
    {
      $this->filter = $filter;
      $this->pageNumber = $pageNumber;
      $this->includeStats = $includeStats;
    }

    /**
     * @return deliveryGroupFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param deliveryGroupFilter $filter
     * @return readDeliveryGroups
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
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
     * @return readDeliveryGroups
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeStats()
    {
      return $this->includeStats;
    }

    /**
     * @param boolean $includeStats
     * @return readDeliveryGroups
     */
    public function setIncludeStats($includeStats)
    {
      $this->includeStats = $includeStats;
      return $this;
    }

}
