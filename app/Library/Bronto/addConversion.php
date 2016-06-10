<?php
namespace App\Library\Bronto;
class addConversion
{

    /**
     * @var conversionObject[] $conversions
     */
    protected $conversions = null;

    /**
     * @param conversionObject[] $conversions
     */
    public function __construct(array $conversions)
    {
      $this->conversions = $conversions;
    }

    /**
     * @return conversionObject[]
     */
    public function getConversions()
    {
      return $this->conversions;
    }

    /**
     * @param conversionObject[] $conversions
     * @return addConversion
     */
    public function setConversions(array $conversions)
    {
      $this->conversions = $conversions;
      return $this;
    }

}
