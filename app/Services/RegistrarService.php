<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 2:01 PM
 */

namespace App\Services;


use App\Repositories\RegistrarRepo;

class RegistrarService
{

    protected $registrar;

    public function __construct(RegistrarRepo $registrarRepo)
    {
        $this->registrar = $registrarRepo;
    }
}