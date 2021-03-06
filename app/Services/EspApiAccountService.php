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
use App\Exceptions\EspAccountDoesNotExistException;
/**
 * Class EspApiAccountService
 * @package App\Services
 */
class EspApiAccountService
{
    use PaginateList;
    const CUSTOM_ID_MIN = 100000;
    const CUSTOM_ID_MAX = 4294967295;

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

    public function getAllActiveAccounts () {
        return $this->espRepo->getAllActiveAccounts();
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
    public function grabCsvMapping($espName)
    {
        $espDetails = $this->espRepo->getAccountESPMapping($espName);
        return  $espDetails->mappings;
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

    public function grabAccessTokenAndSecret($espAccountId){
        $espDetails = $this->espRepo->getAccount($espAccountId);
        return array(
            'accessToken' => $espDetails['key_1'],
            'accessSecret' => $espDetails['key_2']
        );
    }

    public function grabAccessToken($espAccountId){
        $espDetails = $this->espRepo->getAccount($espAccountId);
        return $espDetails['key_1'];
    }


    public function getEspAccountDetailsByName($name){
        return $this->espRepo->getEspInfoByAccountName($name);
    }

    public function getTemplatesByEspId($id){
        return $this->espRepo->getTemplatesByEspId($id);
    }

    public function getEspAccountIdFromCampaignName($name)
    {
        $espAccountName = explode('_', $name)[1];
        return $this->getEspAccountIdFromName($espAccountName);
    }

    public function getEspAccountIdFromName($name)
    {
        try {
            $espAccountId = $this->espRepo->getIdFromName($name);
            return $espAccountId->id;
        } catch (\Exception $e) {
            throw new EspAccountDoesNotExistException();
        }
    }

    public function getEspAccountName($id){
        return $this->espRepo->getAccount( $id )->account_name;
    }

    public function getKeysWithOAuth($espAccountId){
        $espDetails = $this->espRepo->getAccountWithOAuth($espAccountId);
        return array(
            'accessToken' => $espDetails['key_1'],
            'accessSecret' => $espDetails['key_2'],
            'consumerToken' => $espDetails->OAuthTokens['access_token'],
            'consumerSecret' => $espDetails->OAuthTokens['access_secret'],
        );
    }

    public function getAccountsBySearchName($search){
        return $this->espRepo->backFuzzySearch($search);
    }

    public function generateCustomId() {
        $existingCustomIds = $this->espRepo->getAllAccounts()->pluck('custom_id')->toArray();

        do {
            $newUniqueCustomId = mt_rand( self::CUSTOM_ID_MIN , self::CUSTOM_ID_MAX );
        } while (
            in_array($newUniqueCustomId, $existingCustomIds)
        );

        return $newUniqueCustomId;
    }

    public function getCustomIdHistoryByEsp( $espAccountId ){
        return $this->espRepo->getCustomIdHistoryByEsp( $espAccountId );
    }

    public function statsEnabledForAccount ( $accountId ) {
        return $this->espRepo->statsEnabledForAccount( $accountId );
    }

    public function suppressionEnabledForAccount ( $accountId ) {
        return $this->espRepo->suppressionEnabledForAccount( $accountId );
    }

    public function toggleStats ( $accountId , $currentStatus ) {
        return $this->espRepo->toggleStats( $accountId , $currentStatus );
    }

    public function toggleSuppression ( $accountId , $currentStatus ) {
        return $this->espRepo->toggleSuppression( $accountId , $currentStatus );
    }

    public function activate ( $accountId ) {
        return $this->espRepo->activate( $accountId );
    }

    public function deactivate ( $accountId ) {
        return $this->espRepo->deactivate( $accountId );
    }
}
