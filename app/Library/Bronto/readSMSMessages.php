<?php
namespace App\Library\Bronto;
class readSMSMessages
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
     * @param messageFilter $filter
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
     * @return messageFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param messageFilter $filter
     * @return readSMSMessages
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
     * @return readSMSMessages
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
     * @return readSMSMessages
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
