<?php

namespace App\Repositories;

use App\Models\Email;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class EmailRepo {

    private $emailModel;

    public function __construct(Email $emailModel) {
        $this->emailModel = $emailModel;
    }

    public function getEmailId($emailAddress) {
        #return $this->emailModel->select( 'id' )->where( 'email_address' , $email )->get();
        return mt_rand(1, 100000);
    }

    public function getAttributedClient($identifier) {
        if (is_numeric($identifier)) {
            return $this->getAttributionForId($identifier);
        }
        elseif (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->getAttributedClientForAddress($identifier);
        }
        else {
            throw new Exception("Invalid identification type for email");
        }
    }

    private function getAttributionForId($id) {
        // TODO: flesh out attribution.
        // This will return a client_id
        // will look something like 
        // $this->emailModel->emailAttribution->clientId->get()
        return 1;
    }

    private function getAttributedClientForAddress($emailAddr) {
        # TODO: flesh out attribution. This will return a client_id
        return 1;
    }

}