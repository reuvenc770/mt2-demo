<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/12/16
 * Time: 2:50 PM
 */

namespace App\Services;


use App\Services\API\BlueHornetApi;
use Carbon\Carbon;
use App\Facades\Suppression;

class BlueHornetSubscriberService
{
    CONST UNSUB_REQUEST = "legacy.retrieve_unsub";
    CONST HARDBOUNCE_REQUEST = "transactional.bouncedList";
    protected $api;

    protected $request = null;
    protected $methodData = null;
    protected $endDate;

    public function __construct(BlueHornetApi $api)
    {
        $this->api = $api;
        $this->endDate = Carbon::now()->endOfDay()->toDateString();
    }

    public function pullBounceEmailsByLookback($lookback)
    {
        throw new \Exception("DEAD MANS LAND NOT ENABLED");
        /**
         * $start = Carbon::now()->startOfDay()->subDay($lookback)->toDateString();
         * $this->request = self::HARDBOUNCE_REQUEST;
         * $this->methodData = ["start_date"=> $start, "end_date" => $this->endDate ];
         * $emails = $this->_handleRequest();
         * print_r($emails);
         * **/

    }

    public function pullUnsubsEmailsByLookback($lookback)
    {
        $start = Carbon::now()->startOfDay()->subDay($lookback)->toDateString();
        $this->request = self::UNSUB_REQUEST;
        $this->methodData = ["date_deleted1" => $start, "date_deleted2" => $this->endDate];
        $return = $this->_handleRequest();
        return $return->item->responseData->manifest->deleted_contact_data;

    }

    private function _handleRequest()
    {
        try {
            $this->api->buildRequest($this->request, $this->methodData);
            $response = $this->api->sendApiRequest();
            $xmlBody = simplexml_load_string($response->getBody()->__toString());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if ($xmlBody->item->responseCode != 201) {
            throw new \Exception($xmlBody->asXML());
        }
        return $xmlBody;
    }


    public function insertUnsubs($data, $espAccountId)
    {
        foreach ($data as $entry) {
            $campaign_id = isset($entry->message_id) ? $entry->message_id : 0;
            if ($campaign_id == 0) {// System Opt Out
                continue;
            }
            if ($entry->method_unsubscribed == "Spam Complaint") {
                Suppression::recordRawComplaint(
                    $espAccountId,
                    $entry->email,
                    $campaign_id,
                    $entry->date_deleted
                );
                continue;
            }
            Suppression::recordRawUnsub($espAccountId, $entry->email, $campaign_id, $entry->date_deleted);
        }

    }
}
