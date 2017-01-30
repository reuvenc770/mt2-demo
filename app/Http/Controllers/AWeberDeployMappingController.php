<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Services\DeployService;
use App\Services\StandardReportService;
use App\Services\AWeberReportService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Laracasts\Flash\Flash;
use Carbon\Carbon;

class AWeberDeployMappingController extends Controller
{
        const ESP_NAME = "AWeber";
        protected $deployService;
        protected $standardReportService;
        protected $reportService;

    public function __construct(DeployService $deployService, StandardReportService $standardService, AWeberReportService $reportService )
    {
        $this->deployService = $deployService;
        $this->standardReportService = $standardService;
        $this->reportService = $reportService;
    }


    public function mapDeploys(){
        $combinedDeploys = array();
        $deploys = $this->deployService->getOrphanDeploysForEsp(self::ESP_NAME);
        foreach($deploys as $key => $deploy){
            $rawRepords = $this->reportService->getBySubject($deploy->subject_line);
            $combinedDeploys[$key] = $deploy->toArray();
            foreach ($rawRepords as $record){
                $currentReport = $record->toArray();
                $currentReport['datetime'] = Carbon::parse( $currentReport['datetime'] )->toDateString();
                $combinedDeploys[$key]['raw_reports'][] = $currentReport;
            }
        }


        return view('pages.tools.aweber.mapdeploys', ["deploys" => $combinedDeploys]);
    }


    public function getOrphanReports(){
       return response()->json($this->standardReportService->getOrphanReportsByEsp());
    }

    public function convertReport(Request $request)
    {
        $deploy = $this->deployService->getDeploy( $request->get('deploy_id') );
        $this->reportService->convertRawToStandard( $request , $deploy );

        Flash::success( 'Deploy was successfully mapped.' );
        return response()->json(['success' => true]);
    }
}
