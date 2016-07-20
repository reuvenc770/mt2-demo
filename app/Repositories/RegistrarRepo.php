<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:55 PM
 */

namespace App\Repositories;


use App\Models\Registrar;

class RegistrarRepo
{
    protected $registrars;

    public function __construct(Registrar $registrar )
    {
        $this->registrars = $registrar;
    }

}