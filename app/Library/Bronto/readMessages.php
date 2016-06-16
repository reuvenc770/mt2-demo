<?php
namespace App\Library\Bronto;
class readMessages
{

    /**
     * @var messageFilter $filter
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
     * @var int $pageSize
     */
    protected $pageSize = null;

    /**
     * @var boolean $includeStats
     */
    protected $includeStats = null;

    /**
     * @param messageFilter $filter
     * @param boolean $includeContent
     * @param int $pageNumber
     * @param int $pageSize
     * @param boolean $includeStats
     */
    public function __construct($filter, $includeContent, $pageNumber, $pageSize, $includeStats)
    {
      $this->filter = $filter;
      $this->includeContent = $includeContent;
      $this->pageNumber = $pageNumber;
      $this->pageSize = $pageSize;
      $this->includeStats = $includeStats;
    }

    /**
     * @return messageFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param messageFilter $filter
     * @return readMessages
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
     * @return readMessages
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
     * @return readMessages
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
     * @return readMessages
     */
    public function setPageSize($pageSize)
    {
      $this->pageSize = $pageSize;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeStats()
    {
      return $this->includeStats;
    }

    /**
     * @param boolean $includeStats
     * @return readMessages
     */
    public function setIncludeStats($includeStats)
    {
      $this->includeStats = $includeStats;
      return $this;
    }

}
