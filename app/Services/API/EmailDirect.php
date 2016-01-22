<?php
/**
 *
 */

namespace App\Services\API;

use App\Facades\EspAccount;
use App\Facades\Guzzle;

/**
 *
 */
class EmailDirect extends BaseAPI {
    CONST API_URL = 'https://rest.emaildirect.com/v1';

    private $apiKey;

    public function __construct ( $name , $accountNumber ) {
        parent::__construct( $name , $accountNumber );

        $creds = EspAccount::grabApiKeyWithSecret( $accountNumber );

        $this->apiKey = $creds[ 'apiKey' ];
    }
}
