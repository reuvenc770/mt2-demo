<?php
namespace App\Services;

use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;
use App\Facades\DeployActionEntry;
use App\Facades\Suppression;
use App\Library\AWeber\AWeberAPIException;
use App\Repositories\ReportRepo;
use App\Services\AbstractReportService;
use App\Services\API\AWeberApi;
use App\Services\EmailRecordService;
use App\Services\Interfaces\IDataService;
use Illuminate\Support\Facades\Event;
use Log;

/**
 * Class AWeberReportService
 * @package App\Services
 */
class AWeberSubscriberService
{
    protected $api;

    public function __construct(AWeberApi $weberApi)
    {
        $this->api = $weberApi;
    }

    public function pullUnsubsEmailsByLookback($lookback)
    {
            $records = $this->api->getAllUnsubs();
            return $records;
    }

    public function insertUnsubs($data, $espAccountId){
        foreach($data as $record){
            switch($record->unsubscribe_method){

                case "unsubscribe link":
                    Suppression::recordRawHardBounce($espAccountId,$record->email,0, $record->unsubscribed_at);
                    break;
                case "customer cp":
                    Suppression::recordRawComplaint($espAccountId,$record->email,0, $record->unsubscribed_at);
                    break;
                case "undeliverable";
                    Suppression::recordRawHardBounce($espAccountId,$record->email,0, $record->unsubscribed_at);
                    break;
                default:
                    throw new \Exception("I am not even mad, but aweber created a new unsub method");
                    break;
            }

        }

    }
}
