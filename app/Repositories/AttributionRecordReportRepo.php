<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionRecordReport;

class AttributionRecordReportRepo {
    protected $report;

    public function __construct ( AttributionRecordReport $report ) {
        $this->report = $report;
    }

    public function getByDateRange ( array $dateRange ) {
        return $this->report->whereBetween( 'date' , [ $dateRange[ 'start' ] , $dateRange[ 'end' ] ] )->get();
    }
}
