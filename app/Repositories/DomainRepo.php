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

    public function insertRow($data)
    {
        return $this->domain->updateOrCreate(['domain_name' => $data['domain_name']], $data);
    }

    public function getModel()
    {

        return $this->domain
            ->select('domains.esp_account_id as esp_account_id', 'esps.name as esp_name', 'esp_accounts.account_name as account_name',
                'registrars.name as registrar_name', 'proxies.name as proxy_name',
                'doing_business_as.dba_name as dba_name', DB::raw('COUNT(domains.id) as domain_numbers'))
            ->join('esp_accounts', 'domains.esp_account_id', '=', 'esp_accounts.id')
            ->join('esps', 'esp_accounts.esp_id', '=', 'esps.id')
            ->join('registrars', 'domains.registrar_id', '=', 'registrars.id')
            ->join('proxies', 'domains.proxy_id', '=', 'proxies.id')
            ->join('doing_business_as', 'domains.doing_business_as_id', '=', 'doing_business_as.id')
            ->groupBy('esp_account_id');
    }

    public function getDomainsByTypeAndEsp($type, $espAccountId)
    {
        return $this->domain->select('domains.id as dom_id',
            'domains.domain_name',
            'proxies.name as proxy_name',
            'registrars.name as registrar_name',
            'doing_business_as.dba_name',
            'domains.main_site',
            'domains.created_at',
            'domains.expires_at',
            'domains.active')
            ->where("domains.domain_type", $type)
            ->where("domains.esp_account_id", $espAccountId)
            ->join('registrars', 'domains.registrar_id', '=', 'registrars.id')
            ->join('proxies', 'domains.proxy_id', '=', 'proxies.id')
            ->join('doing_business_as', 'domains.doing_business_as_id', '=', 'doing_business_as.id')
            ->orderBy('domains.active', "DESC")
            ->get();
    }

    public function inactivateDomain($id){
        return $this->domain->where("id",$id)->update([ "active" => 0]);
    }

}