<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Http\Requests;
use App\Http\Requests\SaveAmpReportRequest;
use Laracasts\Flash\Flash;
use Carbon\Carbon;

use App\Services\AmpReportService;

use Log;

class ReportController extends Controller
{
    protected $service;

    public function __construct ( AmpReportService $service ) {
        $this->service = $service;
    }

    public function view () {
        return response()->view( 'bootstrap.pages.report.report-index' );
    }

    public function iframeReport ( $id ) {
        return response()->view( 'bootstrap.pages.report.amp-report' , $this->service->getPageData( $id ) );
    }

    public function store ( SaveAmpReportRequest $request ) {
        Flash::success( 'Report was successfully created.' );

        $this->service->saveReport( $request->input( 'name' ) , $request->input( 'reportId' ) );
    }

    public function update ( SaveAmpReportRequest $request ) {
        Flash::success( 'Report was successfully updated.' );

        $this->service->updateReport( $request->input( 'systemId' ) , $request->input( 'name' ) , $request->input( 'reportId' ) );
    }
}
