<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:54 PM
 */

namespace App\Repositories;

use App\Models\Proxy;

class ProxyRepo
{
    protected $proxy;

    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

}