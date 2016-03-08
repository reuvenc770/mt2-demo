<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/2/16
 * Time: 12:30 PM
 */

namespace App\Repositories\MT1Repositories;


use App\Models\MT1Models\UniqueProfile;

class UniqueProfileRepo
{
    protected $profile;
    public function __construct(UniqueProfile $profile)
    {
        $this->profile = $profile;
    }

    public function getProfilesNameAndId(){
        return $this->profile->select('profile_id as id', "profile_name as name")->where('status', '=', 'A')->get();
    }
}