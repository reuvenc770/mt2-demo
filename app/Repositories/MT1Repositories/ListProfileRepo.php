<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/2/16
 * Time: 2:23 PM
 */

namespace App\Repositories\MT1Repositories;


use App\Models\MT1Models\ListProfile;

class ListProfileRepo
{
    protected $listProfile;

    public function __construct(ListProfile $listProfile)
    {
        $this->listProfile = $listProfile;
    }

    public function getAllListProfiles()
    {
        return $this->listProfile->all();
    }

    public function getListProfileById($id)
    {
        try {
            return $this->listProfile->find($id);
        } catch (\Exception $e) {
            return false;
        }
    }
}