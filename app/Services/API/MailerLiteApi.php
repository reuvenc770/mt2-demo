<?php

namespace App\Services\API;

use \MailerLiteApi\MailerLite;
use App\Facades\EspApiAccount;

/**
 * Class MailerLite
 * @package App\Services\API
 */
class MailerLiteApi extends EspBaseAPI
{
    const ESP_NAME = "MailerLite";
    private $sdk;
    private $date;

    public function __construct($espAccountId)
    {
        parent::__construct(self::ESP_NAME, $espAccountId);
        $creds = EspApiAccount::grabApiUsernameWithPassword($espAccountId);
        $this->sdk = new MailerLite($creds->key_1);
        $this->date = null;
    }

    public function sendApiRequest()
    {
        try {
            // There are filters on date, but these seemingly do not work.
            // These are ok for now because they are only pulling 28 campaigns
            return $this->sdk->campaigns()->get()->toArray();
        }
        catch (\Exception $e) {
            throw new \Exception("MailerLiteApi sendApiRequest() method failed due to " . $e->getMessage());
        }
        
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    
}
