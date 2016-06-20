<?php
namespace App\Library\Bronto;
class readSMSDeliveries
{

    /**
     * @var smsDeliveryFilter $filter
     */
    protected $filter = null;

    /**
     * @var boolean $includeContent
     */
    protected $includeContent = null;

    /**
     * @var boolean $includeRecipients
     */
    protected $includeRecipients = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @param smsDeliveryFilter $filter
     * @param boolean $includeContent
     * @param boolean $includeRecipients
     * @param int $pageNumber
     */
    public function __construct($filter, $includeContent, $includeRecipients, $pageNumber)
    {
      $this->filter = $filter;
      $this->includeContent = $includeContent;
      $this->includeRecipients = $includeRecipients;
      $this->pageNumber = $pageNumber;
    }

    /**
     * @return smsDeliveryFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param smsDeliveryFilter $filter
     * @return readSMSDeliveries
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
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
     * @return readSMSDeliveries
     */
    public function setIncludeContent($includeContent)
    {
      $this->includeContent = $includeContent;
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
     * @return readSMSDeliveries
     */
    public function setIncludeRecipients($includeRecipients)
    {
      $this->includeRecipients = $includeRecipients;
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
     * @return readSMSDeliveries
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
