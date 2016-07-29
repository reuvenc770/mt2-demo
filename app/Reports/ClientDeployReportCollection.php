<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Reports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\AttributionClientDeployReport;

class ClientDeployReportCollection extends Collection {
    protected $dateRonge;

    public function __construct ( $items = [] ) {
        parent::__construct( $items );
    }

    public function load ( $dateRange = null ) {
        $this->setDateRange( $dateRange );

        parent::__construct( AttributionClientDeployReport::whereBetween( 'date' , [ $this->dateRange[ 'start' ] , $this->dateRange[ 'end' ] ] )->get()->toArray() );
    }

    public function setDateRange ( $dateRange = null ) { 
        if ( is_null( $dateRange ) && !isset( $this->dateRange ) ) { 
            $this->dateRange = [ "start" => Carbon::today()->startOfDay()->toDateTimeString() , "end" => Carbon::today()->endOfDay()->ToDateTimeString() ];
        } elseif ( !is_null( $dateRange ) && isset( $this->dateRange ) ) { 
            $this->dateRange = $dateRange;
        }   
    } 
}
