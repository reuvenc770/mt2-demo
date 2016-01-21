<?php
namespace App\Library\Campaigner;
class ReportTicket
{

    /**
     * @var string $ReportTicketId
     */
    protected $ReportTicketId = null;

    /**
     * @var int $RowCount
     */
    protected $RowCount = null;

    /**
     * @param string $ReportTicketId
     * @param int $RowCount
     */
    public function __construct($ReportTicketId, $RowCount)
    {
      $this->ReportTicketId = $ReportTicketId;
      $this->RowCount = $RowCount;
    }

    /**
     * @return string
     */
    public function getReportTicketId()
    {
      return $this->ReportTicketId;
    }

    /**
     * @param string $ReportTicketId
     * @return ReportTicket
     */
    public function setReportTicketId($ReportTicketId)
    {
      $this->ReportTicketId = $ReportTicketId;
      return $this;
    }

    /**
     * @return int
     */
    public function getRowCount()
    {
      return $this->RowCount;
    }

    /**
     * @param int $RowCount
     * @return ReportTicket
     */
    public function setRowCount($RowCount)
    {
      $this->RowCount = $RowCount;
      return $this;
    }

}
