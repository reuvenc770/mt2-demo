<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:41 PM
 */

namespace App\Http\Controllers;
use App\Services\BlueHornetService;
use App\Jobs\RetreiveBlueHornetReports;
class TestStuff extends Controller{

    protected $blueHornetService;

    public function __construct(BlueHornetService $blueHornetService){

        $this->blueHornetService = $blueHornetService;

    }

    public function index(){

        echo "Im in the TestStuff Controller\n\n";
        //$this->blueHornetService->retrieveReportStats('2015-01-05');
        $this->dispatch(new RetreiveBlueHornetReports(null,'2015-01-05'));
    }
}