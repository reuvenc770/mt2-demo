<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/19/16
 * Time: 12:35 PM
 */

namespace App\Services;

use App\Services\ServiceTraits\PaginateList;

use App\Repositories\EspApiAccountRepo;
use League\Csv\Reader;
/**
 * Class EspApiAccountService
 * @package App\Services
 */
class EspApiAccountService
{
    use PaginateList;

    /**
     * @var EspApiAccountRepo
     */
    protected $espRepo;

    /**
     * EspApiAccountService constructor.
     * @param EspApiAccountRepo $espRepo
     */
    public function __construct(EspApiAccountRepo $espRepo)
    {
        $this->espRepo = $espRepo;
    }

    public function getModel () { return $this->espRepo->getModel(); }

    public function getType () { return 'espapiaccount'; }

    /**
     * @param int $id The ID of the account to retrieve.
     * @return EspAccount
     */
    public function getAccount ( $id ) {
        return $this->espRepo->getAccount( $id );
    }

    /**
     *
     */
    public function getAccountAndEsp ( $id ) {
        return $this->espRepo->getAccountAndEsp( $id );
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllAccounts () {
        return $this->espRepo->getAllAccounts();
    }

    /**
     *
     */
    public function getAllAccountsByESPName($espName){
        return $this->espRepo->getAccountsByESPName($espName);
    }

    /**
     *
     */
    public function grabApiKeyWithSecret($espAccountId)
    {
        $espDetails = $this->espRepo->getAccount($espAccountId);
        return array(
            "apiKey"        => $espDetails['key_1'],
            "sharedSecret"  => $espDetails['key_2']
        );
    }

    /**
     *
     */
    public function grabApiKey($espAccountId)
    {
        $espDetails = $this->espRepo->getAccount($espAccountId);
        return $espDetails['key_1'];
    }

    /**
     *
     */
    public function grabApiUsernameWithPassword($espAccountId)
    {
        $espDetails = $this->espRepo->getAccount($espAccountId);
        return array(
            "userName"        => $espDetails['key_1'],
            "password"        => $espDetails['key_2']
        );
    }

    /**
     *
     */
    public function grabCsvMapping($espAccountId)
    {
        $espDetails = $this->espRepo->getAccount($espAccountId)->accountMapping;
        return  explode(',',$espDetails->mappings);
    }

    /**
     *
     */
    public function mapCsvToRawStatsArray($espAccountId,$filePath) {
        $returnArray = array();
        $mapping = $this->grabCsvMapping($espAccountId);
        $reader = Reader::createFromPath(storage_path().'/app/'.$filePath);

        $data = $reader->fetchAssoc($mapping);
        foreach ($data as $row) {
            $row['esp_account_id'] = $espAccountId;
            $returnArray[] = $row;
        }
        return $returnArray;
    }

    /**
     *
     */
    public function mapCsvToRawActionsArray($espAccountId, $action, $filePath) {
        $returnArray = array();
        echo $filePath;
        #$mapping = $this->grabCsvDeliverableMapping($espAccountId);
        $reader = Reader::createFromPath($filePath);
        $data = $reader->fetchAssoc();

        foreach ($data as $row) {
            $returnArray[] = $row;
        }

        return $returnArray;
    }

    /**
     * @param array $newAccount The collection of account details to save.
     */
    public function saveAccount ( $accountData ) {
        $this->espRepo->saveAccount( $accountData );
    }

    /**
     * @param int $id The id of the account to update.
     * @param array $accountData The account information to update.
     */
    public function updateAccount ( $id , $accountData ) {
        $this->espRepo->updateAccount( $id , $accountData );
    }

    public function grabApiAccountIdAndKey($espAccountId) {
        $espDetails = $this->espRepo->getAccount($espAccountId);
        return array(
            'account' => $espDetails['key_1'],
            'apiKey' => $espDetails['key_2']
        );
    }

}
