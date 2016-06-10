<?php
namespace App\Library\Bronto;
class readRecentInboundActivitiesResponse
{

    /**
     * @var recentActivityObject[] $return
     */
    protected $return = null;

    /**
     * @param recentActivityObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return recentActivityObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param recentActivityObject[] $return
     * @return readRecentInboundActivitiesResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
