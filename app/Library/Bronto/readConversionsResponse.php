<?php
namespace App\Library\Bronto;
class readConversionsResponse
{

    /**
     * @var conversionObject[] $return
     */
    protected $return = null;

    /**
     * @param conversionObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return conversionObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param conversionObject[] $return
     * @return readConversionsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
