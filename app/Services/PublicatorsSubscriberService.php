<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Services\API\PublicatorsApi;
use Carbon\Carbon;
use App\Facades\Suppression;
use App\Repositories\EspApiAccountRepo;

class PublicatorsSubscriberService {
    protected $api;
    protected $emails = [];

    public function __construct ( PublicatorsApi $api, EspApiAccountRepo $accountRepo) {
        $this->api = $api;
        $this->accountRepo = $accountRepo;
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
                '' ,
                $record->TimeStamp
            );

            $this->emails[] = $record->Email;
        }

        $this->exportUnsubs();
    }

    protected function exportUnsubs() {
        $accounts = $this->accountRepo->getAccountsByESPName('Publicators');
        $segmentedEmails = array_chunk($this->emails, 1500);

        foreach ($accounts as $account) {
            if ($account->id != $this->api->getEspAccountId()) {
                $exportApi = new PublicatorsApi($account->id);

                foreach ($segmentedEmails as $segment) {
                    $exportApi->setToUnsubscribed($segment);
                }
            }
        }

    }
} 
