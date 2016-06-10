<?php
namespace App\Library\Bronto;
class updateSMSDeliveriesResponse
{

    /**
     * @var writeResult $return
     */
    protected $return = null;

    /**
     * @param writeResult $return
     */
    public function __construct($return)
    {
      $this->return = $return;
    }

    /**
     * @return writeResult
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param writeResult $return
     * @return updateSMSDeliveriesResponse
     */
    public function setReturn($return)
    {
      $this->return = $return;
      return $this;
    }

}
