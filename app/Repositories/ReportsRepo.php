<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:35 PM
 */

namespace App\Repositories;
use App\Models\Report;

class ReportsRepo
{
    /**
     * @var Report
     */
    protected $report;

    public function __construct(Report $report){
        $this->report = $report;
    }

    public function testRepo(){
        echo "test from repo";
    }

    public function testModel(){
        echo $this->report->testModel();
    }


}