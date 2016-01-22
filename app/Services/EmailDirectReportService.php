<?php
/**
 *
 */

namespace App\Services;

use App\Services\API\EmailDirect;
use App\Repositories\ReportRepo;
use App\Services\Interfaces\IAPIReportService;
use App\Services\Interfaces\IReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;

/**
 *
 */
class EmailDirectReportService extends EmailDirect implements IAPIReportService , IReportService {
    protected $reportRepo;

    public function __construct ( ReportRepo $reportRepo , $apiName , $accountNumber ) {
        parent::__construct( $apiName , $accountNumber );
        $this->reportRepo = $reportRepo;
    }

    public function retrieveReportStats ( $date ) {

    }

    public function insertRawStats ( $data ) {

    }

    public function mapToStandardReport ( $data ) {

    }
}
