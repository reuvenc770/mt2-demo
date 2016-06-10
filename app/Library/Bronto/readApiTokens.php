<?php
namespace App\Library\Bronto;
class readApiTokens
{

    /**
     * @var apiTokenFilter $filter
     */
    protected $filter = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @param apiTokenFilter $filter
     * @param int $pageNumber
     */
    public function __construct($filter, $pageNumber)
    {
      $this->filter = $filter;
      $this->pageNumber = $pageNumber;
    }

    /**
     * @return apiTokenFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param apiTokenFilter $filter
     * @return readApiTokens
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
     * @return readApiTokens
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
