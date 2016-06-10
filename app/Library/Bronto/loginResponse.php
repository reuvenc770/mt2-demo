<?php
namespace App\Library\Bronto;
class loginResponse
{

    /**
     * @var string $return
     */
    protected $return = null;

    /**
     * @param string $return
     */
    public function __construct($return)
    {
      $this->return = $return;
    }

    /**
     * @return string
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param string $return
     * @return loginResponse
     */
    public function setReturn($return)
    {
      $this->return = $return;
      return $this;
    }

}
