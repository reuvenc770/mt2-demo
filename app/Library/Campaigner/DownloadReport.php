<?php
namespace App\Library\Campaigner;
class DownloadReport
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var string $reportTicketId
     */
    protected $reportTicketId = null;

    /**
     * @var int $fromRow
     */
    protected $fromRow = null;

    /**
     * @var int $toRow
     */
    protected $toRow = null;

    /**
     * @var string $reportType
     */
    protected $reportType = null;

    /**
     * @param Authentication $authentication
     * @param string $reportTicketId
     * @param int $fromRow
     * @param int $toRow
     * @param string $reportType
     */
    public function __construct($authentication, $reportTicketId, $fromRow, $toRow, $reportType)
    {
      $this->authentication = $authentication;
      $this->reportTicketId = $reportTicketId;
      $this->fromRow = $fromRow;
      $this->toRow = $toRow;
      $this->reportType = $reportType;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
      return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     * @return DownloadReport
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return string
     */
    public function getReportTicketId()
    {
      return $this->reportTicketId;
    }

    /**
     * @param string $reportTicketId
     * @return DownloadReport
     */
    public function setReportTicketId($reportTicketId)
    {
      $this->reportTicketId = $reportTicketId;
      return $this;
    }

    /**
     * @return int
     */
    public function getFromRow()
    {
      return $this->fromRow;
    }

    /**
     * @param int $fromRow
     * @return DownloadReport
     */
    public function setFromRow($fromRow)
    {
      $this->fromRow = $fromRow;
      return $this;
    }

    /**
     * @return int
     */
    public function getToRow()
    {
      return $this->toRow;
    }

    /**
     * @param int $toRow
     * @return DownloadReport
     */
    public function setToRow($toRow)
    {
      $this->toRow = $toRow;
      return $this;
    }

    /**
     * @return string
     */
    public function getReportType()
    {
      return $this->reportType;
    }

    /**
     * @param string $reportType
     * @return DownloadReport
     */
    public function setReportType($reportType)
    {
      $this->reportType = $reportType;
      return $this;
    }

}
