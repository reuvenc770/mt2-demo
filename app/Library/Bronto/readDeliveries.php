<?php
namespace App\Library\Bronto;
class readDeliveries
{

    /**
     * @var deliveryFilter $filter
     */
    protected $filter = null;

    /**
     * @var boolean $includeRecipients
     */
    protected $includeRecipients = null;

    /**
     * @var boolean $includeContent
     */
    protected $includeContent = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @var boolean $includeOrderIds
     */
    protected $includeOrderIds = null;

    /**
     * @param deliveryFilter $filter
     * @param boolean $includeRecipients
     * @param boolean $includeContent
     * @param int $pageNumber
     * @param boolean $includeOrderIds
     */
    public function __construct($filter, $includeRecipients, $includeContent, $pageNumber, $includeOrderIds)
    {
      $this->filter = $filter;
      $this->includeRecipients = $includeRecipients;
      $this->includeContent = $includeContent;
      $this->pageNumber = $pageNumber;
      $this->includeOrderIds = $includeOrderIds;
    }

    /**
     * @return deliveryFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param deliveryFilter $filter
     * @return readDeliveries
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeRecipients()
    {
      return $this->includeRecipients;
    }

    /**
     * @param boolean $includeRecipients
     * @return readDeliveries
     */
    public function setIncludeRecipients($includeRecipients)
    {
      $this->includeRecipients = $includeRecipients;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeContent()
    {
      return $this->includeContent;
    }

    /**
     * @param boolean $includeContent
     * @return readDeliveries
     */
    public function setIncludeContent($includeContent)
    {
      $this->includeContent = $includeContent;
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
     * @return readDeliveries
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeOrderIds()
    {
      return $this->includeOrderIds;
    }

    /**
     * @param boolean $includeOrderIds
     * @return readDeliveries
     */
    public function setIncludeOrderIds($includeOrderIds)
    {
      $this->includeOrderIds = $includeOrderIds;
      return $this;
    }

}
