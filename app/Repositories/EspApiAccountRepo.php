<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/19/16
 * Time: 12:30 PM
 */

namespace App\Repositories;

use App\Models\EspAccount;
use App\Models\EspAccountCustomIdHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use App\Repositories\Traits\ToggleBooleanColumn;

//TODO ADD CACHING ONCE ESP SECTION IS DONE

/**
 * Class EspApiAccountRepo
 * @package App\Repositories
 */
class EspApiAccountRepo
{
    use ToggleBooleanColumn;

    const DEACTIVATION_PERIOD_DAYS = 30;

    /**
     * @var EspAccount
     */
    protected $espAccount;

    /**
     * EspApiAccountRepo constructor.
     * @param EspAccount $espAccount
     */
    public function __construct( EspAccount $espAccount , EspAccountCustomIdHistory $espCustomIdHistory )
    {
        $this->espAccount = $espAccount;
        $this->espCustomIdHistory = $espCustomIdHistory;
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
        #Changed this to check enable_stats. It is only used in ReportFactory. The Report doesn't seem to be active but adding here just in case.
        return $this->espAccount->where("account_name", $accountName)->where( 'enable_stats', '=' , 1 )->with('esp')->first();
    }

    /**
     * @return mixed
     */
    public function getAllAccounts(){
        return $this->espAccount->with( 'esp' )->orderBy('account_name')->get();
    }

    public function getAllActiveAccounts(){
        return $this->espAccount->where([ [ "enable_suppression" , '=' , 1 ] , [ 'enable_stats' , '=' , 1 ]] )->with( 'esp' )->orderBy('account_name')->get();
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
            ->get();
    }

    public function suppressionEnabledForAccount ( $accountId ) {
        return (bool)$this->espAccount->where( [ [ 'id' , '=' , $accountId ] , [ 'enable_suppression' , '=' , '1' ] ] )->count();
    } 

    public function statsEnabledForAccount ( $accountId ) {
        return (bool)$this->espAccount->where( [ [ 'id' , '=' , $accountId ] , [ 'enable_stats' , '=' , '1' ] ] )->count();
    } 

    public function toggleStats ( $accountId , $currentStatus ) {
        return $this->toggleBooleanColumn(
            $this->espAccount ,
            $accountId ,
            'enable_stats' ,
            $currentStatus
        );
    }

    public function toggleSuppression ( $accountId , $currentStatus ) {
        $this->toggleBooleanColumn(
            $this->espAccount ,
            $accountId ,
            'enable_suppression' ,
            $currentStatus
        );

        if ( $currentStatus == 1 ) {
            $this->removeDeactivationDate( $accountId );
        }

        return true;
    }

    public function setDeactivationDateFromToday ( $accountId ) {
        $account = $this->espAccount->find( $accountId );

        $date = Carbon::now()->addDays( self::DEACTIVATION_PERIOD_DAYS )->toDateString();

        $account->deactivation_date = $date;

        $account->save(); 
    }

    public function removeDeactivationDate ( $accountId ) {
        $account = $this->espAccount->find( $accountId );

        $account->deactivation_date = null;

        $account->save(); 
    }

    public function activate ( $accountId ) {
        $this->toggleStats( $accountId , false );
        $this->toggleSuppression( $accountId , false );
        $this->removeDeactivationDate( $accountId );

        return true;
    }

    public function deactivate ( $accountId ) {
        $this->toggleStats( $accountId , true );
        $this->setDeactivationDateFromToday( $accountId );

        return true;
    }

    /**
     * @param array $newAccount The collection of account details to save.
     */
    public function saveAccount ( $newAccount ) {
        $this->espAccount->account_name = $newAccount[ 'accountName' ];
        $this->espAccount->key_1 = $newAccount[ 'key1' ];
        $this->espAccount->key_2 = $newAccount[ 'key2' ];
        $this->espAccount->esp_id = $newAccount[ 'espId' ];
        $this->espAccount->custom_id = $newAccount[ 'customId' ];
        $this->espAccount->save();
    }

    /**
     * @param int $id The id of the account to update.
     * @param array $accountData The account information to update.
     */
    public function updateAccount ( $id , $accountData ) {
        $account = $this->espAccount->find( $id );
        $account->account_name = $accountData[ 'accountName' ];
        $account->custom_id = $accountData[ 'customId' ];
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

    public function getAccountsbyEspWithSuppression($esp){
        return $this->espAccount->where('esp_id', $esp)->where(function ($query) {
            $query->where('enable_suppression',1);
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

    public function getAccountWithOAuth($id) {
        return $this->espAccount->with('OAuthTokens')->find($id);
    }

    public function backFuzzySearch($search){
        return $this->espAccount->where("account_name",'like',"{$search}%");
    }

    public function getCustomIdHistoryByEsp( $espAccountId ){
        $history = $this->espCustomIdHistory->where( 'esp_account_id' , $espAccountId )->orderBy('created_at','desc')->get();
        return $history->pluck('created_at','custom_id');
    }
}
