<?php
namespace App\Library\Campaigner;
class UploadMassContactsResult
{

    /**
     * @var string $UploadTicketId
     */
    protected $UploadTicketId = null;

    /**
     * @param string $UploadTicketId
     */
    public function __construct($UploadTicketId)
    {
      $this->UploadTicketId = $UploadTicketId;
    }

    /**
     * @return string
     */
    public function getUploadTicketId()
    {
      return $this->UploadTicketId;
    }

    /**
     * @param string $UploadTicketId
     * @return UploadMassContactsResult
     */
    public function setUploadTicketId($UploadTicketId)
    {
      $this->UploadTicketId = $UploadTicketId;
      return $this;
    }

}
