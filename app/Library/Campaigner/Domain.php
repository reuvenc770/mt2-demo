<?php
namespace App\Library\Campaigner;
class Domain
{

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var DeliveryResult $DeliveryResults
     */
    protected $DeliveryResults = null;

    /**
     * @var ActivityResult $ActivityResults
     */
    protected $ActivityResults = null;

    /**
     * @param string $Name
     * @param DeliveryResult $DeliveryResults
     * @param ActivityResult $ActivityResults
     */
    public function __construct($Name, $DeliveryResults, $ActivityResults)
    {
      $this->Name = $Name;
      $this->DeliveryResults = $DeliveryResults;
      $this->ActivityResults = $ActivityResults;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->Name;
    }

    /**
     * @param string $Name
     * @return Domain
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return DeliveryResult
     */
    public function getDeliveryResults()
    {
      return $this->DeliveryResults;
    }

    /**
     * @param DeliveryResult $DeliveryResults
     * @return Domain
     */
    public function setDeliveryResults($DeliveryResults)
    {
      $this->DeliveryResults = $DeliveryResults;
      return $this;
    }

    /**
     * @return ActivityResult
     */
    public function getActivityResults()
    {
      return $this->ActivityResults;
    }

    /**
     * @param ActivityResult $ActivityResults
     * @return Domain
     */
    public function setActivityResults($ActivityResults)
    {
      $this->ActivityResults = $ActivityResults;
      return $this;
    }

}
