<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/8/16
 * Time: 11:31 AM
 */

namespace App\Services;


use App\Repositories\ListProfileRepo;

class ListProfileService
{
    protected $profileRepo;

    public function __construct(ListProfileRepo $repo)
    {
        $this->profileRepo = $repo;
    }


    public function getActiveListProflies(){
        return $this->profileRepo->returnActiveProfiles();
    }

}