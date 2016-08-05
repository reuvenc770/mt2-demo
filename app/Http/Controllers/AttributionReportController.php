<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;

use Log;

class AttributionReportController extends Controller
{
    protected $reportType;
    protected $collection;
    protected $records;
    protected $currentRequest;

    protected $defaultTotalsList = [
        "delivered" ,
        "opened" ,
        "clicked" ,
        "converted" ,
        "bounced" ,
        "unsubbed" ,
        "revenue"
    ];

    protected $totalsMap = [];

    public function __construct () {
        $this->totalsMap = [ 
            "Record" => $this->defaultTotalsList ,
            "Deploy" => $this->defaultTotalsList ,
            "Client" => $this->defaultTotalsList + [ "cost" ]
        ];
    }

    public function view () {
        return response()->view( 'pages.attribution.attribution-report' );
    }

    public function getRecords ( Request $request ) {
        $this->currentRequest = $request;

        $this->buildCollection();

        return response()->json( $this->getTableData() );
    }

    protected function buildCollection () {
        $this->reportType = $this->currentRequest->input( 'type' );

        $className = "\App\Collections\Attribution\\" . $this->reportType . "ReportCollection";

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
            ]
        ];

        return $query;
    }

    protected function getTableData () {
        $this->buildPaginatedRecords();

        $responseContainer = [
            "totalRecords" => $this->collection->recordCount() ,
            "totals" => [] ,
            "records" => $this->records->all()
        ];

        $this->sumTotals( $responseContainer );

        return $responseContainer;
    }

    protected function buildPaginatedRecords () {
        $page = $this->currentRequest->input( 'page' );
        $chunkSize = $this->currentRequest->input( 'limit' );

        $this->collection->load();

        $this->records = $this->collection->forPage( $page , $chunkSize );
    }

    protected function sumTotals ( &$responseContainer ) {
        foreach ( $this->totalsMap[ $this->reportType ] as $field ) {
            $responseContainer[ 'totals' ][ $field ] = $this->records->pluck( $field )->sum();
        }
    }
}
