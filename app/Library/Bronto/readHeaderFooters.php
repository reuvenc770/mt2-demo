<?php
namespace App\Library\Bronto;
class readHeaderFooters
{

    /**
     * @var headerFooterFilter $filter
     */
    protected $filter = null;

    /**
     * @var boolean $includeContent
     */
    protected $includeContent = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @param headerFooterFilter $filter
     * @param boolean $includeContent
     * @param int $pageNumber
     */
    public function __construct($filter, $includeContent, $pageNumber)
    {
      $this->filter = $filter;
      $this->includeContent = $includeContent;
      $this->pageNumber = $pageNumber;
    }

    /**
     * @return headerFooterFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param headerFooterFilter $filter
     * @return readHeaderFooters
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
     * @return readHeaderFooters
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
     * @return readHeaderFooters
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
