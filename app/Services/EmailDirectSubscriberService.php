<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 5/9/16
 * Time: 2:58 PM
 */

namespace App\Services;


use App\Services\API\EmailDirectApi;
use Carbon\Carbon;
use App\Facades\Suppression;
class EmailDirectSubscriberService
{
    protected $api;

    public function __construct(EmailDirectApi $api)
    {
        $this->api = $api;
    }

    public function pullUnsubsEmailsByLookback($lookback){
        $since = Carbon::today()->subDay($lookback)->toDateString();
        return $this->api->getUnsubReport($since);
    }

    public function insertUnsubs($data, $espAccountId){
        foreach ($data as $entry){
            Suppression::recordRawUnsub($espAccountId,$entry['EmailAddress'],0, Carbon::today()->toDateString());
        }
    }
}