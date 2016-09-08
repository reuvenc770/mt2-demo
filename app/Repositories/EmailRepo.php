<?php

namespace App\Repositories;

use App\Models\Email;
#use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use DB;

/**
 *
 */
class EmailRepo {

    private $emailModel;

    public function __construct(Email $emailModel) {
        $this->emailModel = $emailModel;
    }

    public function getEmailId($emailAddress) {
        return $this->emailModel->select( 'id' )->where( 'email_address' , $emailAddress )->get();
    }

    public function getAttributedFeed($identifier) {
        if (is_numeric($identifier)) {
            return $this->getAttributionForId($identifier);
        }
        elseif (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->getAttributedFeedForAddress($identifier);
        }
        else {
            throw new \Exception("Invalid identification type for email");
        }
    }

    public function insertCopy($emailData) {
        #$this->emailModel->updateOrCreate($emailData);
        DB::statement(
            "INSERT INTO emails (id, email_address, email_domain_id)
            VALUES(:id, :addr, :domain_id)
            ON DUPLICATE KEY UPDATE
            id = id, email_address=email_address, email_domain_id=email_domain_id ",
            array(
                ':id' => $emailData['id'],
                ':addr' => $emailData['email_address'],
                ':domain_id' => $emailData['email_domain_id']
            )
        );
    }

    private function getAttributionForId($id) {
        // TODO: flesh out attribution.
        // This will return a feed_id
        // will look something like 
        // $this->emailModel->emailAttribution->feedId->get()
        return 1;
    }

    private function getAttributedFeedForAddress($emailAddr) {
        # TODO: flesh out attribution. This will return a feed_id
        return 1;
    }

}