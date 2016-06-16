<?php
namespace App\Library\Bronto;
class readAccounts
{

    /**
     * @var accountFilter $filter
     */
    protected $filter = null;

    /**
     * @var boolean $includeInfo
     */
    protected $includeInfo = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @param accountFilter $filter
     * @param boolean $includeInfo
     * @param int $pageNumber
     */
    public function __construct($filter, $includeInfo, $pageNumber)
    {
      $this->filter = $filter;
      $this->includeInfo = $includeInfo;
      $this->pageNumber = $pageNumber;
    }

    /**
     * @return accountFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param accountFilter $filter
     * @return readAccounts
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeInfo()
    {
      return $this->includeInfo;
    }

    /**
     * @param boolean $includeInfo
     * @return readAccounts
     */
    public function setIncludeInfo($includeInfo)
    {
      $this->includeInfo = $includeInfo;
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
     * @return readAccounts
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
