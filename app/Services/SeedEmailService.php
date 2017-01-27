<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/26/17
 * Time: 4:30 PM
 */

namespace App\Services;


use App\Repositories\SeedEmailRepo;

class SeedEmailService
{
    private $seedEmailRepo;

    public function __construct(SeedEmailRepo $repo)
    {
        $this->seedEmailRepo = $repo;
    }


    public function addSeed($emailEmail){
        try{
            return $this->seedEmailRepo->addSeed($emailEmail);
        } catch (\Exception $e){
            return false;
        }
    }

    public function deleteSeed($id){
        try{
            return $this->seedEmailRepo->removeSeedById($id);
        } catch (\Exception $e){
            return false;
        }
    }


    public function getAllSeeds(){
        return $this->seedEmailRepo->getAllSeeds();
    }

    public function checkForSeed($emailAddress){
        return $this->seedEmailRepo->truthCheckSeed($emailAddress);
    }
}