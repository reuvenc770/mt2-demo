<?php
namespace App\Library\Bronto;
class readActivitiesResponse
{

    /**
     * @var activityObject[] $return
     */
    protected $return = null;

    /**
     * @param activityObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return activityObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param activityObject[] $return
     * @return readActivitiesResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
