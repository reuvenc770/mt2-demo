<?php
namespace App\Library\Bronto;
class readMessageRules
{

    /**
     * @var messageRuleFilter $filter
     */
    protected $filter = null;

    /**
     * @var int $pageNumber
     */
    protected $pageNumber = null;

    /**
     * @param messageRuleFilter $filter
     * @param int $pageNumber
     */
    public function __construct($filter, $pageNumber)
    {
      $this->filter = $filter;
      $this->pageNumber = $pageNumber;
    }

    /**
     * @return messageRuleFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param messageRuleFilter $filter
     * @return readMessageRules
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
     * @return readMessageRules
     */
    public function setPageNumber($pageNumber)
    {
      $this->pageNumber = $pageNumber;
      return $this;
    }

}
