<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/2/16
 * Time: 2:26 PM
 */

namespace App\Services\MT1Services;


use App\Repositories\MT1Repositories\ListProfileRepo;

class ListProfileService
{
    protected $listProfileRepo;

    public function __construct(ListProfileRepo $profileRepo)
    {
        $this->listProfileRepo = $profileRepo;
    }

    public function getAll(){
        return $this->listProfileRepo->getAllListProfiles();
    }

    public function getById($id){
        $profile = $this->listProfileRepo->getListProfileById($id);
        if($profile){
            return $profile;
        } else {
            return ["error" => "List Profile not Found" ];
        }
    }

}