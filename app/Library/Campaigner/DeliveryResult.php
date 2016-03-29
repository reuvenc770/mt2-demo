<?php
namespace App\Library\Campaigner;
class DeliveryResult
{

    /**
     * @var int $Sent
     */
    protected $Sent = null;

    /**
     * @var int $Delivered
     */
    protected $Delivered = null;

    /**
     * @var int $HardBounces
     */
    protected $HardBounces = null;

    /**
     * @var int $SoftBounces
     */
    protected $SoftBounces = null;

    /**
     * @var int $SpamBounces
     */
    protected $SpamBounces = null;

    /**
     * @param int $Sent
     * @param int $Delivered
     * @param int $HardBounces
     * @param int $SoftBounces
     * @param int $SpamBounces
     */
    public function __construct($Sent, $Delivered, $HardBounces, $SoftBounces, $SpamBounces)
    {
      $this->Sent = $Sent;
      $this->Delivered = $Delivered;
      $this->HardBounces = $HardBounces;
      $this->SoftBounces = $SoftBounces;
      $this->SpamBounces = $SpamBounces;
    }

    /**
     * @return int
     */
    public function getSent()
    {
      return $this->Sent;
    }

    /**
     * @param int $Sent
     * @return DeliveryResult
     */
    public function setSent($Sent)
    {
      $this->Sent = $Sent;
      return $this;
    }

    /**
     * @return int
     */
    public function getDelivered()
    {
      return $this->Delivered;
    }

    /**
     * @param int $Delivered
     * @return DeliveryResult
     */
    public function setDelivered($Delivered)
    {
      $this->Delivered = $Delivered;
      return $this;
    }

    /**
     * @return int
     */
    public function getHardBounces()
    {
      return $this->HardBounces;
    }

    /**
     * @param int $HardBounces
     * @return DeliveryResult
     */
    public function setHardBounces($HardBounces)
    {
      $this->HardBounces = $HardBounces;
      return $this;
    }

    /**
     * @return int
     */
    public function getSoftBounces()
    {
      return $this->SoftBounces;
    }

    /**
     * @param int $SoftBounces
     * @return DeliveryResult
     */
    public function setSoftBounces($SoftBounces)
    {
      $this->SoftBounces = $SoftBounces;
      return $this;
    }

    /**
     * @return int
     */
    public function getSpamBounces()
    {
      return $this->SpamBounces;
    }

    /**
     * @param int $SpamBounces
     * @return DeliveryResult
     */
    public function setSpamBounces($SpamBounces)
    {
      $this->SpamBounces = $SpamBounces;
      return $this;
    }

}
