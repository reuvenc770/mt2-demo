<?php
namespace App\Library\Bronto;
class readWebforms
{

    /**
     * @var webformFilter $filter
     */
    protected $filter = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @param webformFilter $filter
     * @param int $pageNumber
     */
    public function __construct($filter, $pageNumber)
    {
      $this->filter = $filter;
      $this->pageNumber = $pageNumber;
    }

    /**
     * @return webformFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param webformFilter $filter
     * @return readWebforms
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
     * @return readWebforms
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
