<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/2/16
 * Time: 12:33 PM
 */

namespace App\Services\MT1Services;


use App\Repositories\MT1Repositories\UniqueProfileRepo;

class UniqueProfileService
{
    protected $profileRepo;

    public function __construct(UniqueProfileRepo $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    public function getAllProfiles(){
        return $this->profileRepo->getProfilesNameAndId();
    }
}