<?php
namespace App\Library\Bronto;
class readRecentInboundActivities
{

    /**
     * @var recentInboundActivitySearchRequest $filter
     */
    protected $filter = null;

    /**
     * @param  $filter
     */
    public function __construct($filter)
    {
      $this->filter = $filter;
    }

    /**
     * @return recentInboundActivitySearchRequest
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param recentInboundActivitySearchRequest $filter
     * @return readRecentInboundActivities
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
      return $this;
    }

}
