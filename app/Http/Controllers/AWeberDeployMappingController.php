<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Services\DeployService;
use App\Services\StandardReportService;
use App\Services\AWeberReportService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Laracasts\Flash\Flash;
use App\Factories\ReportFactory;
use Carbon\Carbon;

class AWeberDeployMappingController extends Controller
{
        const ESP_NAME = "AWeber";
        protected $deployService;
        protected $standardReportService;

    public function __construct(DeployService $deployService, StandardReportService $reportService)
    {
        $this->deployService = $deployService;
        $this->standardReportService = $reportService;
        $this->rawRepo = ReportFactory::createEspRawRepo(self::ESP_NAME);
    }


    public function mapDeploys(){
        $deploys = $this->deployService->getOrphanDeploysForEsp(self::ESP_NAME);

        $rawReportCollection = $this->rawRepo->getByEspAccountDateSubject(
            array_unique( $deploys->pluck('esp_account_id')->toArray() ),
            array_unique( $deploys->pluck('send_date')->toArray() ),
            array_unique( $deploys->pluck('subject_line')->toArray() )
        );

        $rawReports = [];

        foreach ($rawReportCollection as $record){
            $currentReport = $record->toArray();
            $currentReport['datetime'] = Carbon::parse( $currentReport['datetime'] )->toDateString();
            $rawReports[] = $currentReport;
        }

        return view('pages.tools.aweber.mapdeploys', ["deploys" => $deploys , "rawReports" => $rawReports ]);
    }


    public function getOrphanReports(){
       return response()->json($this->standardReportService->getOrphanReportsByEsp());
    }

    public function convertReport(Request $request)
    {
        $deploy = $this->deployService->getDeploy( $request->get('deploy_id') );
        $campaignName = $deploy->deploy_name;
        $internalId = $request->get( 'internal_id' );

        $rawRecord = $this->rawRepo->getRowByExternalId( $internalId );
        $rawRecord['campaign_name'] = $campaignName;
        $rawRecord->save();
        $rawRecord['deploy_id'] = $deploy->id;

        $standardRecord = AWeberReportService::convertToStandardReport( $rawRecord );
        $this->standardReportService->insertStandardStats( $standardRecord );

        Flash::success( 'Deploy was successfully mapped.' );
        return response()->json(['success' => true]);
    }
}
