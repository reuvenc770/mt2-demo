<?php
namespace App\Library\Bronto;
class readLogins
{

    /**
     * @var loginFilter $filter
     */
    protected $filter = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @param loginFilter $filter
     * @param int $pageNumber
     */
    public function __construct($filter, $pageNumber)
    {
      $this->filter = $filter;
      $this->pageNumber = $pageNumber;
    }

    /**
     * @return loginFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param loginFilter $filter
     * @return readLogins
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
     * @return readLogins
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
