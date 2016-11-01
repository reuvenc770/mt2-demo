<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/3/16
 * Time: 4:08 PM
 */

namespace App\Repositories;


use App\Models\ListProfile;

class ListProfileRepo
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

    public function getProfile($id) {
        return $this->listProfile->where('id', $id)->firstOrFail();
    }

    public function updateTotalCount($id, $count) {
        $this->listProfile->where('id', $id)->update(['total_count' => $count]);
    }

    public function shouldInsertHeader($id) {
        return $this->listProfile->where('id', $id)->firstOrFail()->insert_header === 1;
    }
}