<?php
namespace App\Library\Bronto;
class readRecentOutboundActivities
{

    /**
     * @var recentOutboundActivitySearchRequest $filter
     */
    protected $filter = null;

    /**
     * @param recentOutboundActivitySearchRequest $filter
     */
    public function __construct($filter)
    {
      $this->filter = $filter;
    }

    /**
     * @return recentOutboundActivitySearchRequest
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param recentOutboundActivitySearchRequest $filter
     * @return readRecentOutboundActivities
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
      return $this;
    }

}
