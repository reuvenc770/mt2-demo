<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/29/16
 * Time: 2:38 PM
 */

namespace App\Services;


use App\Repositories\DeployRepo;
use App\Repositories\MT1Repositories\EspAdvertiserJoinRepo;

class DeployService
{
    protected $deployRepo;
    protected $espAdvertiser;

    public function __construct(DeployRepo $deployRepo, EspAdvertiserJoinRepo $repo)
    {
        $this->deployRepo = $deployRepo;
        $this->espAdvertiser = $repo;
    }

    public function getCakeAffiliates(){
        return $this->espAdvertiser->getCakeAffiliates();
    }

}