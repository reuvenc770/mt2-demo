<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Services\DeployService;
use App\Services\StandardReportService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use  Laracasts\Flash\Flash;
class AWeberDeployMappingController extends Controller
{
        protected $deployService;
        protected $standardReportService;

    public function __construct(DeployService $deployService, StandardReportService $reportService)
    {
        $this->deployService = $deployService;
        $this->standardReportService = $reportService;
    }


    public function mapDeploys(){
        $deploys = $this->deployService->getOrphanDeploysForEsp("AWeber");
        return view('bootstrap.pages.tools.aweber.mapdeploys', ["deploys" => $deploys]);
    }


    public function getOrphanReports(){
       return response()->json($this->standardReportService->getOrphanReportsByEsp());
    }

    public function convertReport(Request $request){
        $deploy = $this->deployService->getDeploy($request->get('deploy_id'));
        $return = $this->standardReportService->convertStandardReport($request->get('report_id'),$deploy);
        Flash::success( 'Deploy was successfully mapped.' );
        return response()->json(['success' => $return]);
    }
}
