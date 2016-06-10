<?php
namespace App\Library\Bronto;
class readSMSKeywords
{

    /**
     * @var smsKeywordFilter $filter
     */
    protected $filter = null;

    /**
     * @var boolean $includeDeleted
     */
    protected $includeDeleted = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @param smsKeywordFilter $filter
     * @param boolean $includeDeleted
     * @param int $pageNumber
     */
    public function __construct($filter, $includeDeleted, $pageNumber)
    {
      $this->filter = $filter;
      $this->includeDeleted = $includeDeleted;
      $this->pageNumber = $pageNumber;
    }

    /**
     * @return smsKeywordFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param smsKeywordFilter $filter
     * @return readSMSKeywords
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeDeleted()
    {
      return $this->includeDeleted;
    }

    /**
     * @param boolean $includeDeleted
     * @return readSMSKeywords
     */
    public function setIncludeDeleted($includeDeleted)
    {
      $this->includeDeleted = $includeDeleted;
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
     * @return readSMSKeywords
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
