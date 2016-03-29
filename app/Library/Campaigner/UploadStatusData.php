<?php
namespace App\Library\Campaigner;
class UploadStatusData
{

    /**
     * @var string $UploadTicketId
     */
    protected $UploadTicketId = null;

    /**
     * @var UploadMassContactsStatusCode $StatusCode
     */
    protected $StatusCode = null;

    /**
     * @var UploadMassContactsResultCode $ResultCode
     */
    protected $ResultCode = null;

    /**
     * @var \DateTime $StartTime
     */
    protected $StartTime = null;

    /**
     * @var \DateTime $EndTime
     */
    protected $EndTime = null;

    /**
     * @var int $TotalContacts
     */
    protected $TotalContacts = null;

    /**
     * @var int $ProcessedContacts
     */
    protected $ProcessedContacts = null;

    /**
     * @var boolean $WorkflowEnabled
     */
    protected $WorkflowEnabled = null;

    /**
     * @param string $UploadTicketId
     * @param UploadMassContactsStatusCode $StatusCode
     */
    public function __construct($UploadTicketId, $StatusCode)
    {
      $this->UploadTicketId = $UploadTicketId;
      $this->StatusCode = $StatusCode;
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
     * @return UploadStatusData
     */
    public function setUploadTicketId($UploadTicketId)
    {
      $this->UploadTicketId = $UploadTicketId;
      return $this;
    }

    /**
     * @return UploadMassContactsStatusCode
     */
    public function getStatusCode()
    {
      return $this->StatusCode;
    }

    /**
     * @param UploadMassContactsStatusCode $StatusCode
     * @return UploadStatusData
     */
    public function setStatusCode($StatusCode)
    {
      $this->StatusCode = $StatusCode;
      return $this;
    }

    /**
     * @return UploadMassContactsResultCode
     */
    public function getResultCode()
    {
      return $this->ResultCode;
    }

    /**
     * @param UploadMassContactsResultCode $ResultCode
     * @return UploadStatusData
     */
    public function setResultCode($ResultCode)
    {
      $this->ResultCode = $ResultCode;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
      if ($this->StartTime == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->StartTime);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $StartTime
     * @return UploadStatusData
     */
    public function setStartTime(\DateTime $StartTime)
    {
      $this->StartTime = $StartTime->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
      if ($this->EndTime == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->EndTime);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $EndTime
     * @return UploadStatusData
     */
    public function setEndTime(\DateTime $EndTime)
    {
      $this->EndTime = $EndTime->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return int
     */
    public function getTotalContacts()
    {
      return $this->TotalContacts;
    }

    /**
     * @param int $TotalContacts
     * @return UploadStatusData
     */
    public function setTotalContacts($TotalContacts)
    {
      $this->TotalContacts = $TotalContacts;
      return $this;
    }

    /**
     * @return int
     */
    public function getProcessedContacts()
    {
      return $this->ProcessedContacts;
    }

    /**
     * @param int $ProcessedContacts
     * @return UploadStatusData
     */
    public function setProcessedContacts($ProcessedContacts)
    {
      $this->ProcessedContacts = $ProcessedContacts;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getWorkflowEnabled()
    {
      return $this->WorkflowEnabled;
    }

    /**
     * @param boolean $WorkflowEnabled
     * @return UploadStatusData
     */
    public function setWorkflowEnabled($WorkflowEnabled)
    {
      $this->WorkflowEnabled = $WorkflowEnabled;
      return $this;
    }

}
