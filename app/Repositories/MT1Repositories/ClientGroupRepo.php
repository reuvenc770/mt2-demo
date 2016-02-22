<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/22/16
 * Time: 1:02 PM
 */

namespace App\Repositories\MT1Repositories;


use App\Models\MT1Models\ClientGroup;
use Exception;
use Log;
use DB;
class ClientGroupRepo
{
    protected $clientGroup;

    public function __construct(ClientGroup $clientGroup)
    {
        $this->clientGroup = $clientGroup;
    }

    public function getAllClientGroups(){
        return $this->clientGroup->select('client_group_id as id' , 'group_name as name' )
            ->orderBy("name")->get();
    }

    public function getAllClientsForGroup($id){
        try{
            DB::enableQueryLog();
            return DB::connection('mt1mail')->table('ClientGroupClients')
                ->join('user', 'user.user_id', '=', 'ClientGroupClients.client_id')
                ->select('user.username as name', 'user.status')
                ->where('ClientGroupClients.client_group_id',$id )
                ->get();
            
        } catch (\Exception $e){
            Log::error("ClientGroup error:: ".$e->getMessage());
        }
    }
}
