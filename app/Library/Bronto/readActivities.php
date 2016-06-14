<?php
namespace App\Library\Bronto;
class readActivities
{

    /**
     * @var activityFilter $filter
     */
    protected $filter = null;

    /**
     * @param activityFilter $filter
     */
    public function __construct($filter)
    {
      $this->filter = $filter;
    }

    /**
     * @return activityFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param activityFilter $filter
     * @return readActivities
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
      return $this;
    }

}
