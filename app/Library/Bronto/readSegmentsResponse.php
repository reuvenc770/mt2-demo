<?php
namespace App\Library\Bronto;
class readSegmentsResponse
{

    /**
     * @var segmentObject[] $return
     */
    protected $return = null;

    /**
     * @param segmentObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return segmentObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param segmentObject[] $return
     * @return readSegmentsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
