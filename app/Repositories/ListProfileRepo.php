<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/3/16
 * Time: 4:08 PM
 */

namespace App\Repositories;


use App\Models\ListProfile;

class ListProfileRepo extends AbstractDataSyncRepo
{
    private $listProfile;

    public function __construct(ListProfile $listProfile) {
        $this->listProfile = $listProfile;
    }

    public function updateOrCreate($data) {
        $this->listProfile->updateOrCreate(['profile_id' => $data['profile_id']], $data);
    }

    public function returnActiveProfiles(){
       return $this->listProfile->where("status", "A")->select('id','profile_name')->get();
    }

    public function bulkInsert()
    {
        //Interface Adherence and maybe update later
    }

}