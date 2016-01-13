<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:31 PM
 */

namespace App\Services;
use App\Repositories\ReportsRepo;


class BlueHornetService
{
    protected $reportRepo;

    public function __construct(ReportsRepo $reportRepo){
        $this->reportRepo = $reportRepo;
    }

    public function test() {
        $this->reportRepo->testRepo();
        $this->reportRepo->testModel();
    }

}