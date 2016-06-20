<?php
namespace App\Library\Bronto;
class readContentTagsResponse
{

    /**
     * @var contentTagObject[] $return
     */
    protected $return = null;

    /**
     * @param contentTagObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return contentTagObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param contentTagObject[] $return
     * @return readContentTagsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
