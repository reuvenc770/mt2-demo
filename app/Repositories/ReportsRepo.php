<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:35 PM
 */

namespace App\Repositories;
use App\Models\Report;
use App\Models\Interfaces\IReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ReportsRepo implements  IReport
{
    /**
     * @var Report
     */
    protected $report;

    public function __construct(IReport $report){
        $this->report = $report;
    }

    public function testRepo(){
        echo "test from repo";
    }

    public function testModel(){
        echo $this->report->testModel();
    }

    public function insertRawStats($data)
    {

        $this->report->create($data);
        print_r(DB::getQueryLog());
    }


}