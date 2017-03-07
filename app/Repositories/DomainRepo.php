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
use App\Facades\EspApiAccount;
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
            ->leftjoin('proxies', 'domains.proxy_id', '=', 'proxies.id')
            ->join('doing_business_as', 'domains.doing_business_as_id', '=', 'doing_business_as.id')
            ->groupBy('esp_account_id');
    }

    public function getDomainsByTypeAndEsp($type, $espAccountId)
    {
        return $this->domain->select('domains.id as dom_id',
            'domains.domain_name',
            'proxies.name as proxy_name',
            'registrars.name as registrar_name',
            'registrars.username as registrar_username',
            'doing_business_as.dba_name',
            'domains.main_site',
            'domains.created_at',
            'domains.expires_at',
            'domains.status',
            'domains.live_a_record',
            DB::raw('domains.expires_at < NOW() + INTERVAL 7 DAY as is_expired') )
            ->where("domains.domain_type", $type)
            ->where("domains.esp_account_id", $espAccountId)
            ->join('registrars', 'domains.registrar_id', '=', 'registrars.id')
            ->leftjoin('proxies', 'domains.proxy_id', '=', 'proxies.id')
            ->join('doing_business_as', 'domains.doing_business_as_id', '=', 'doing_business_as.id')
            ->orderBy('domains.status', "DESC")
            ->orderBy('domains.expires_at',"DESC")
            ->get();
    }

    public function getActiveDomainsByTypeAndEsp($type, $espAccountId)
    {
        return $this->domain
            ->where("status",1)
            ->where("live_a_record",1)
            ->where("domain_type", $type)
            ->where("esp_account_id", $espAccountId)
            ->where('expires_at' , '>' , DB::raw('NOW() - INTERVAL 1 DAY') )
            ->get();
    }

    public function toggleRow($id, $direction){
        return $this->domain->find($id)->update(["status" => $direction]);
    }

    public function getDomainsByExpiration($date){
        return $this->domain->select(
            'domains.domain_name',
            'registrars.name as registrar_name',
            'domains.expires_at')
            ->where("domains.expires_at", $date)
            ->where("domains.status", 1)
            ->join('registrars', 'domains.registrar_id', '=', 'registrars.id')
            ->get();
    }

    public function updateRow($domain){
        $id = $domain['id'];
        unset($domain['id']);
        return $this->domain->find($id)->update($domain);
    }

    public function getRow($id){
        return $this->domain->find($id);
    }

    public function getDomainsBySearch($searchData){
            $query = $this->domain->select('domains.id as dom_id',
                'esps.name as esp_account',
                'esp_accounts.account_name as esp_account_name',
                'domains.domain_name',
                'proxies.name as proxy_name',
                'registrars.name as registrar_name',
                'registrars.username as registrar_username',
                'doing_business_as.dba_name',
                'domains.main_site',
                'domains.created_at',
                'domains.expires_at',
                'domains.status',
                'domains.live_a_record' ,
                'domains.domain_type as type',
                DB::raw('domains.expires_at < NOW() + INTERVAL 7 DAY as is_expired') )
                ->join('registrars', 'domains.registrar_id', '=', 'registrars.id')
                ->join('doing_business_as', 'domains.doing_business_as_id', '=', 'doing_business_as.id')
                ->leftjoin('proxies', 'domains.proxy_id', '=', 'proxies.id')
                ->join('esp_accounts', 'domains.esp_account_id', '=', 'esp_accounts.id')
                ->join('esps','esp_accounts.esp_id', '=', 'esps.id' );
        return $this->mapQuery($searchData, $query)
                ->orderBy('domains.status', "DESC")
                ->orderBy('domains.domain_type',"DESC")
                ->orderBy('domains.expires_at',"DESC")
                ->get();

    }

    public function getDomainsByDomainType( $domainType ){
        return $this->domain->where('domains.domain_type', $domainType)->pluck('domain_name')->toArray();
    }

    public function getDomainIdByTypeAndName($type, $name){
        return $this->domain->where('domain_type', $type)->where('domain_name', $name)->first();
    }

    private function mapQuery($searchData, $query){

        if (isset($searchData['esp'])) {
            $espAccounts = collect(EspApiAccount::getAllAccountsByESPName($searchData['esp']));
            $espAccountIds = $espAccounts->pluck('id');
            $query->whereIn('domains.esp_account_id', $espAccountIds);
        }

        if (isset($searchData['esp_account_id'])) {
            $query->where('domains.esp_account_id', (int)$searchData['esp_account_id']);
        }

        if (isset($searchData['proxy_id'])) {
            $query->where('domains.proxy_id', (int)$searchData['proxy_id']);
        }

        if (isset($searchData['registrar_id'])) {
            $query->where('domains.registrar_id', (int)$searchData['registrar_id']);
        }

        if (isset($searchData['doing_business_as_id'])) {
            $query->where('domains.doing_business_as_id', (int)$searchData['doing_business_as_id']);
        }

        if (isset($searchData['domain'])) {
            $query->where('domains.domain_name','LIKE', $searchData['domain'] . '%');
        }

        if (isset($searchData['domain_type'])) {
            $query->where('domains.domain_type', (int)$searchData['domain_type']);
        }


        return $query;
    }




}
