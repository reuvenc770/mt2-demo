<?php
namespace App\Library\Campaigner;
class ArrayOfInt
{

    /**
     * @var int[] $int
     */
    protected $int = null;

    /**
     * @param int[] $int
     */
    public function __construct(array $int)
    {
      $this->int = $int;
    }

    /**
     * @return int[]
     */
    public function getInt()
    {
      return $this->int;
    }

    /**
     * @param int[] $int
     * @return ArrayOfInt
     */
    public function setInt(array $int)
    {
      $this->int = $int;
      return $this;
    }

}
