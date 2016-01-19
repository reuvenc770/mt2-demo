<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/19/16
 * Time: 11:46 AM
 */

namespace App\Models;
use App\Models\Interfaces\IESPAccount;


class BlueHornetAccount extends EspAccount implements IESPAccount
{
    protected $table = 'esp_accounts';

    public function __construct()
    {
        parent::__construct();
    }

    public function getApiKey(){
        return $this->attributes['key_1'];
    }

    public function getSecretKey(){
        return $this->attributes['key_2'];
    }
}