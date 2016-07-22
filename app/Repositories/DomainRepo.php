<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/22/16
 * Time: 1:42 PM
 */

namespace App\Repositories;


use App\Models\Domain;
use DB;
class DomainRepo
{
    protected $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    public function insertRow($data){
        return $this->domain->create($data);
    }

    public function getModel () {

       return $this->domain
           ->select('domains.esp_account_id as esp_account_id','esps.name as esp_name','esp_accounts.account_name as account_name',
               'registrars.name as registrar_name', 'proxies.name as proxy_name',
               'doing_business_as.dba_name as dba_name', DB::raw('COUNT(domains.id) as domain_numbers'))
           ->join('esp_accounts', 'domains.esp_account_id', '=', 'esp_accounts.id')
           ->join('esps','esp_accounts.esp_id' ,'=', 'esps.id')
           ->join('registrars', 'domains.registrar_id', '=', 'registrars.id')
           ->join('proxies', 'domains.proxy_id', '=', 'proxies.id')
           ->join('doing_business_as', 'domains.doing_business_as_id', '=', 'doing_business_as.id')
           ->groupBy('esp_account_id');
    }

}