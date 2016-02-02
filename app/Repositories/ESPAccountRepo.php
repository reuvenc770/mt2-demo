<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/19/16
 * Time: 12:30 PM
 */

namespace App\Repositories;


use App\Models\EspAccount;
use App\Models\Esp;
use Illuminate\Support\Facades\DB;
//TODO ADD CACHING ONCE ESP SECTION IS DONE

/**
 * Class ESPAccountRepo
 * @package App\Repositories
 */
class EspAccountRepo
{

    /**
     * @var Esp
     */
    protected $esp;

    /**
     * @var EspAccount
     */
    protected $espAccount;

    /**
     * ESPAccountRepo constructor.
     * @param EspAccount $espAccount
     */
    public function __construct(Esp $esp , EspAccount $espAccount)
    {
        $this->esp = $esp;
        $this->espAccount = $espAccount;
    }

    /**
     * @param $espId
     * @return Esp
     */
    public function getEsp ( $espId ) {
        return $this->esp->find( $espId );
    }

    public function getAllEsps () {
        return $this->esp->all();
    }

    /**
     * @param $espAccountId
     * @return EspAccount
     */
    public function getAccount($espAccountId){
        return $this->espAccount->find($espAccountId);
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getAllAccounts(){
        return DB::table('esp_accounts')
            ->join('esps', 'esps.id', '=', 'esp_accounts.esp_id')
            ->select( 'esps.name as esp' , 'esp_accounts.*' )
            ->get();
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
     *
     */
    public function saveAccount ( $newAccount ) {
        $this->espAccount->account_name = $newAccount[ 'accountName' ];
        $this->espAccount->key_1 = $newAccount[ 'key1' ];
        $this->espAccount->key_2 = $newAccount[ 'key2' ];
        $this->espAccount->esp_id = $newAccount[ 'espId' ];
        $this->espAccount->save();
    }

    /**
     *
     */
    public function updateAccount ( $id , $accountData ) {
        $this->espAccount->where( 'id' , $id )->update( [
            'account_name' => $accountData[ 'accountName' ] ,
            'key_1' => $accountData[ 'key1' ] ,
            'key_2' => $accountData[ 'key2' ]
        ] );
    }
}
