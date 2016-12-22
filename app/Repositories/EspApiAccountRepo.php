<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/19/16
 * Time: 12:30 PM
 */

namespace App\Repositories;

use App\Models\EspAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

//TODO ADD CACHING ONCE ESP SECTION IS DONE

/**
 * Class EspApiAccountRepo
 * @package App\Repositories
 */
class EspApiAccountRepo
{
    /**
     * @var EspAccount
     */
    protected $espAccount;

    /**
     * EspApiAccountRepo constructor.
     * @param EspAccount $espAccount
     */
    public function __construct( EspAccount $espAccount)
    {
        $this->espAccount = $espAccount;
    }

    public function getModel () { return $this->espAccount; }

    /**
     * @param $espAccountId
     * @return EspAccount
     */
    public function getAccount($espAccountId){
        return $this->espAccount->find($espAccountId);
    }

    public function getIdFromName($espAccountName){
        return $this->espAccount->select("id")->where("account_name",$espAccountName)->first();
    }

    /**
     * @param $espAccountId
     * @return EspAccount
     */
    public function getAccountAndEsp($espAccountId){
        $accountObject = $this->espAccount; //cannot use $this-> to invoke static method
        return $accountObject::with( 'esp' )->find($espAccountId);
    }

    public function getEspInfoByAccountName($accountName){
        return $this->espAccount->where("account_name", $accountName)->whereIn('status', [1,2])->with('esp')->first();
    }

    /**
     * @return mixed
     */
    public function getAllAccounts(){
        return $this->espAccount->with( 'esp' )->orderBy('account_name')->get();
    }

    public function getAllActiveAccounts(){
        return $this->espAccount->where("status",1)->orWhere('status',2)->with( 'esp' )->orderBy('account_name')->get();
    }

    /**
     * Used Query Builder because inverse selection is difficult in elequent
     * @param $espName
     * @return mixed
     */
    public function getAccountsByESPName($espName){
        return DB::table('esp_accounts')
            ->join('esps', 'esps.id', '=', 'esp_accounts.esp_id')
            ->select('esp_accounts.*')
            ->addSelect('esps.name')
            ->where('esps.name',$espName)
            ->whereIn('status', [1, 2])
            ->get();
    }


    /**
     * @param array $newAccount The collection of account details to save.
     */
    public function saveAccount ( $newAccount ) {
        $this->espAccount->account_name = $newAccount[ 'accountName' ];
        $this->espAccount->key_1 = $newAccount[ 'key1' ];
        $this->espAccount->key_2 = $newAccount[ 'key2' ];
        $this->espAccount->esp_id = $newAccount[ 'espId' ];
        $this->espAccount->save();
    }

    /**
     * @param int $id The id of the account to update.
     * @param array $accountData The account information to update.
     */
    public function updateAccount ( $id , $accountData ) {
        $account = $this->espAccount->find( $id );
        $account->account_name = $accountData[ 'accountName' ];
        $account->key_1 = $accountData[ 'key1' ];
        $account->key_2 = $accountData[ 'key2' ];
        $account->save();
    }

    public function getAccountESPMapping($espName){
        return DB::table('esp_campaign_mappings')
            ->join('esps', 'esps.id', '=', 'esp_campaign_mappings.esp_id')
            ->select('esp_campaign_mappings.mappings')
            ->where('esps.name',$espName)
            ->first();
    }

    public function getAccountsbyEsp($esp){
        return $this->espAccount->where('esp_id', $esp)->where(function ($query) {
            $query->where('status',1)
                ->orWhere('status',2);
        })->get();
    }

    public function getPublicatorsSuppressionListId($accountId) {
        return $this->espAccount
             ->select('psl.suppression_list_id')
             ->join('publicators_suppression_lists as psl', 'esp_accounts.account_name', '=', 'psl.account_name')
             ->where('esp_accounts.id', $accountId)
             ->first();
    }


    public function getTemplatesByEspId($id){
        $data = $this->espAccount->with('mailingTemplate')->find($id);
       return $data->mailingTemplate;

    }

    public function getImageLinkFormat($id) {
        return $this->espAccount->find($id)->imageLinkFormat;
    }

    public function toggleRow($id, $direction){
        return $this->espAccount->find($id)->update(['status'=> $direction]);
    }

    public function getAccountWithOAuth($id) {
     return $this->espAccount->with('OAuthTokens')->find($id);
    }
}
