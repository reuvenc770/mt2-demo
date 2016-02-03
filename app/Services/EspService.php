<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/19/16
 * Time: 12:35 PM
 */

namespace App\Services;


use App\Repositories\EspAccountRepo;
use League\Csv\Reader;
/**
 * Class ESPAccountService
 * @package App\Services
 */
class ESPAccountService
{
    /**
     * @var EspRepo
     */
    protected $espRepo;

    /**
     * ESPAccountService constructor.
     * @param EspAccountRepo $espRepo
     */
    public function __construct(EspRepo $espRepo)
    {
        $this->espRepo = $espRepo;
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllEsps () {
        return $this->espRepo->getAllEsps();
    }
}
