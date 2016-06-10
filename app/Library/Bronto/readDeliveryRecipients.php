<?php
namespace App\Library\Bronto;
class readDeliveryRecipients
{

    /**
     * @var deliveryRecipientFilter $filter
     */
    protected $filter = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @param deliveryRecipientFilter $filter
     * @param int $pageNumber
     */
    public function __construct($filter, $pageNumber)
    {
      $this->filter = $filter;
      $this->pageNumber = $pageNumber;
    }

    /**
     * @return deliveryRecipientFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param deliveryRecipientFilter $filter
     * @return readDeliveryRecipients
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
     * @return readDeliveryRecipients
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
