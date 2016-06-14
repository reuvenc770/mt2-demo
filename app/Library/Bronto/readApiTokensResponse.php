<?php
namespace App\Library\Bronto;
class readApiTokensResponse
{

    /**
     * @var apiTokenObject[] $return
     */
    protected $return = null;

    /**
     * @param apiTokenObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return apiTokenObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param apiTokenObject[] $return
     * @return readApiTokensResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
