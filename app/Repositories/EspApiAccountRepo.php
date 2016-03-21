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

    /**
     * @param $espAccountId
     * @return EspAccount
     */
    public function getAccountAndEsp($espAccountId){
        $accountObject = $this->espAccount; //cannot use $this-> to invoke static method
        return $accountObject::with( 'esp' )->find($espAccountId);
    }

    public function getEspInfoByAccountName($accountName){
        $accountObject = $this->espAccount; //cannot use $this-> to invoke static method
        return $accountObject::with( 'esp' )->where("account_name", $accountName)->first();
    }

    /**
     * @return mixed
     */
    public function getAllAccounts(){
        $accountObject = $this->espAccount; //cannot use $this-> to invoke static method
        return $accountObject::with( 'esp' )->get();
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
        $this->espAccount->where( 'id' , $id )->update( [
            'account_name' => $accountData[ 'accountName' ] ,
            'key_1' => $accountData[ 'key1' ] ,
            'key_2' => $accountData[ 'key2' ]
        ] );
    }

    public function getAccountsbyEsp($esp){
        $this->espAccount->where('esp_id', $esp)->get();
    }

    
}
