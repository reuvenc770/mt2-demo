<?php
namespace App\Library\Bronto;
class readMessageFoldersResponse
{

    /**
     * @var messageFolderObject[] $return
     */
    protected $return = null;

    /**
     * @param messageFolderObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return messageFolderObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param messageFolderObject[] $return
     * @return readMessageFoldersResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
