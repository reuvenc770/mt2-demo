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
    protected $reportType;
    protected $collection;
    protected $records;
    protected $totals;
    protected $currentRequest;

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

    public function getRecords ( Request $request ) {
        $this->currentRequest = $request;

        $this->buildCollection();

        return response()->json( $this->getTableData() );
    }

    public function export ( Request $request ) {
        $this->currentRequest = $request;

        $this->buildCollection();

        $csv = $this->collection->getCsv();
        $fileName = $this->reportType . '.' . Carbon::now()->format( 'Y.m.d.G.i.s' ) . '.csv';

        $headers = [
            "Content-Type" => "text/csv" ,
            "Content-Disposition" => "attachment; filename=\"{$fileName}\"" ,
            "Content-Length" => strlen( $csv )
        ];

        return Response::make( $csv , 200 , $headers );
    }

    protected function buildCollection () {
        $this->reportType = $this->currentRequest->input( 'type' );

        $className = "\App\Collections\\" . $this->reportType . "ReportCollection";

        $this->collection = \App::make( $className ); 

        $this->collection->config( $this->getQueryFromRequest( $this->currentRequest ) );
    }

    protected function getQueryFromRequest () {
        $filters = json_decode( $this->currentRequest->input( 'filters' ) );

        $descSort = ( preg_match( '/^\-/' , $this->currentRequest->input( 'order' ) ) === 1 );

        $query = [
            "date" => [ 
                "start" => Carbon::parse( $filters->date->start )->toDateString() ,
                "end" => Carbon::parse( $filters->date->end )->toDateString()
            ] ,
            "sort" => [
                "field" =>  ( $descSort ? substr( $this->currentRequest->input( 'order' ) , 1 ) : $this->currentRequest->input( 'order' ) ) ,
                "desc" => $descSort
            ] ,
            "limit" => $this->currentRequest->input( 'limit' ) ,
            "page" => $this->currentRequest->input( 'page' )
        ];

        return $query;
    }

    protected function getTableData () {
        $this->buildPaginatedRecords();

        $responseContainer = [
            "totalRecords" => $this->collection->recordCount() ,
            "totals" => $this->totals ,
            "records" => $this->records->all()
        ];

        return $responseContainer;
    }

    protected function buildPaginatedRecords () {
        $page = $this->currentRequest->input( 'page' );
        $chunkSize = $this->currentRequest->input( 'limit' );

        $this->collection->load();

        $data = $this->collection->getRecordsAndTotals( [ 'page' => $page , 'chunkSize' => $chunkSize ] );
        
        $this->records = $data[ 'records' ];
        $this->totals = $data[ 'totals' ];
    }
}
