<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Services\API\PublicatorsApi;
use Carbon\Carbon;
use App\Facades\Suppression;

class PublicatorsSubscriberService {
    protected $api;

    public function __construct (PublicatorsApi $api) {
        $this->api = $api;
    }

    public function pullUnsubsEmailsByLookback ( $lookback ) {
        return $this->api->getUnsubReport( $lookback );
    }

    public function insertUnsubs ( $data , $espAccountId ) {
        foreach ( $data as $record ) {
            Suppression::recordRawUnsub(
                $espAccountId ,
                $record->Email ,
                $record->CampaignID ,
                $record->TimeStamp
            );
        }
    }

    public function uploadEmails($emails, $listId) {
        $result = $this->api->uploadEmails($emails, $listId);
    }

    public function exportUnsubs($emails) {
        $result = $this->api->setToUnsubscribed($emails);
    }
} 
