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
//TODO ADD CACHING ONCE ESP SECTION IS DONE

/**
 * Class ESPAccountRepo
 * @package App\Repositories
 */
class EspAccountRepo
{

    /**
     * @var EspAccount
     */
    protected $espAccount;

    /**
     * ESPAccountRepo constructor.
     * @param EspAccount $espAccount
     */
    public function __construct(EspAccount $espAccount)
    {
        $this->espAccount = $espAccount;
    }

    /**
     * Used Query Builder because inverse selection is difficult in elequent
     * @param $espName
     * @return mixed
     */
    public function getAccountsByESPName($espName){

        return DB::table('esp_accounts')
            ->join('esps', 'esps.id', '=', 'esp_accounts.esp_id')
            ->select('*')
            ->where('esps.name',$espName)
            ->get();


    }

    public function getAccountByNumber($accountNumber){

        return $this->espAccount
            ->where('account_number',$accountNumber)
            ->first();
    }

}