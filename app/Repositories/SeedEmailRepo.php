<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/26/17
 * Time: 4:30 PM
 */

namespace App\Repositories;


use App\Models\SeedEmail;

class SeedEmailRepo
{
    private $model;

    public function __construct(SeedEmail $seedEmail)
    {
        $this->model = $seedEmail;
    }

    public function addSeed($emailAddress)
    {
        return $this->model->create(["email_address" => $emailAddress]);
    }

    public function removeSeedById($id)
    {
        return $this->model->destroy($id);
    }

    public function truthCheckSeed($emailAddress){
        return $this->model->where(["email_address" => $emailAddress])->count() >= 1;
    }

    public function getAllSeeds(){
        return $this->model->all();
    }
}