<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:58 PM
 */

namespace App\Services;


use App\Repositories\ProxyRepo;

class ProxyService
{
    protected $proxyRepo;

    public function __construct(ProxyRepo $proxyRepo)
    {
        $this->proxyRepo = $proxyRepo;
    }

}