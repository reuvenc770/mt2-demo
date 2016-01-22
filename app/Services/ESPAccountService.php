<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/19/16
 * Time: 12:35 PM
 */

namespace App\Services;


use App\Repositories\ESPAccountRepo;

/**
 * Class ESPAccountService
 * @package App\Services
 */
class ESPAccountService
{
    /**
     * @var ESPAccountRepo
     */
    protected $espRepo;

    /**
     * ESPAccountService constructor.
     * @param ESPAccountRepo $espRepo
     */
    public function __construct(ESPAccountRepo $espRepo)
    {
        $this->espRepo = $espRepo;
    }


    public function getAllAccountsByESPName($espName){
        return $this->espRepo->getAccountsByESPName($espName);
    }

    public function grabApiKeyWithSecret($account_number)
    {
        $espDetails = $this->espRepo->getAPICredsByNumber($account_number);
        return array(
            "apiKey"        => $espDetails['key_1'],
            "sharedSecret"  => $espDetails['key_2']

        );
    }

    public function grabApiKey($account_number)
    {
        $espDetails = $this->espRepo->getAPICredsByNumber($account_number);
        return $espDetails['key_1'];
    }

    public function grabApiUsernameWithPassword($account_number)
    {
        $espDetails = $this->espRepo->getAPICredsByNumber($account_number);
        return array(
            "userName"        => $espDetails['key_1'],
            "password"        => $espDetails['key_2']

        );
    }

}
