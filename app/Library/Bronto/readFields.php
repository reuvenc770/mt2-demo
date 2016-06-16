<?php
namespace App\Library\Bronto;
class readFields
{

    /**
     * @var fieldsFilter $filter
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
     * @param fieldsFilter $filter
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
     * @return fieldsFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param fieldsFilter $filter
     * @return readFields
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
     * @return readFields
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
     * @return readFields
     */
    public function setPageSize($pageSize)
    {
      $this->pageSize = $pageSize;
      return $this;
    }

}
