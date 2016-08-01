<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/29/16
 * Time: 2:38 PM
 */

namespace App\Services;


use App\Repositories\DeployRepo;

class DeployService
{
    protected $deployRepo;

    public function __construct(DeployRepo $deployRepo)
    {
        $this->deployRepo = $deployRepo;
    }

}