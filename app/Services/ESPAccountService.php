<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/19/16
 * Time: 12:35 PM
 */

namespace App\Services;


use App\Repositories\ESPAccountRepo;

/**
 * Class ESPAccountService
 * @package App\Services
 */
class ESPAccountService
{
    /**
     * @var ESPAccountRepo
     */
    protected $espRepo;

    /**
     * ESPAccountService constructor.
     * @param ESPAccountRepo $espRepo
     */
    public function __construct(ESPAccountRepo $espRepo)
    {
        $this->espRepo = $espRepo;
    }


    public function getAllAccountsByESPName($espName){
        return $this->espRepo->getAccountsByESPName($espName);
    }

}