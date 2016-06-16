<?php
namespace App\Library\Bronto;
class readHeaderFootersResponse
{

    /**
     * @var headerFooterObject[] $return
     */
    protected $return = null;

    /**
     * @param headerFooterObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return headerFooterObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param headerFooterObject[] $return
     * @return readHeaderFootersResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
