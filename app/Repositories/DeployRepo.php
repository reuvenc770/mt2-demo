<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/29/16
 * Time: 2:37 PM
 */

namespace App\Repositories;


use App\Models\Deploy;

class DeployRepo
{
    protected $deploy;

    public function __construct(Deploy $deploy){
        $this->deploy = $deploy;
    }
}