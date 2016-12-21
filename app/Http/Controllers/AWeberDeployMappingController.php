<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Services\DeployService;
use App\Services\StandardReportService;
use App\Http\Requests\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;

class AWeberDeployMappingController extends Controller
{
        use DispatchesJobs;
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
        $deploy = $this->deployService->getDeploy($request->input('deploy_id'));
        $return = $this->standardReportService->convertStandardReport($request->input('report_id'),$deploy);
        return response()->json(['success' => $return]);
    }
}
