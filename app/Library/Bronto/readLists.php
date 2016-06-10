<?php
namespace App\Library\Bronto;
class readLists
{

    /**
     * @var mailListFilter $filter
     */
    protected $filter = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @var int $pageSize
     */
    protected $pageSize = null;

    /**
     * @param mailListFilter $filter
     * @param int $pageNumber
     * @param int $pageSize
     */
    public function __construct($filter, $pageNumber, $pageSize)
    {
      $this->filter = $filter;
      $this->pageNumber = $pageNumber;
      $this->pageSize = $pageSize;
    }

    /**
     * @return mailListFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param mailListFilter $filter
     * @return readLists
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
     * @return readLists
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
      return $this->pageSize;
    }

    /**
     * @param int $pageSize
     * @return readLists
     */
    public function setPageSize($pageSize)
    {
      $this->pageSize = $pageSize;
      return $this;
    }

}
