<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/19/16
 * Time: 12:35 PM
 */

namespace App\Services;


use App\Repositories\EspAccountRepo;
use League\Csv\Reader;
/**
 * Class ESPAccountService
 * @package App\Services
 */
class ESPAccountService
{
    /**
     * @var EspAccountRepo
     */
    protected $espRepo;

    /**
     * ESPAccountService constructor.
     * @param EspAccountRepo $espRepo
     */
    public function __construct(EspAccountRepo $espRepo)
    {
        $this->espRepo = $espRepo;
    }


    public function getAllAccountsByESPName($espName){
        return $this->espRepo->getAccountsByESPName($espName);
    }

    public function grabApiKeyWithSecret($account_number)
    {
        $espDetails = $this->espRepo->getAccountByNumber($account_number);
        return array(
            "apiKey"        => $espDetails['key_1'],
            "sharedSecret"  => $espDetails['key_2']

        );
    }

    public function grabApiKey($account_number)
    {
        $espDetails = $this->espRepo->getAccountByNumber($account_number);
        return $espDetails['key_1'];
    }

    public function grabApiUsernameWithPassword($account_number)
    {
        $espDetails = $this->espRepo->getAccountByNumber($account_number);
        return array(
            "userName"        => $espDetails['key_1'],
            "password"        => $espDetails['key_2']

        );
    }

    public function grabCsvMapping($account_number)
    {
        $espDetails = $this->espRepo->getAccountByNumber($account_number)->accountMapping;
        return  explode(',',$espDetails->mappings);
    }

    public function mapCsvToRawStatsArray($accountNumber,$filePath){
        $returnArray = array();
        $mapping = $this->grabCsvMapping($accountNumber);
        $reader = Reader::createFromPath(storage_path().'/app/'.$filePath);

        $data = $reader->fetchAssoc($mapping);
        foreach ($data as $row) {
            $row['account_name'] = $accountNumber;
            $returnArray[] = $row;
        }
        return $returnArray;
    }

}
