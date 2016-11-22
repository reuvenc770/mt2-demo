<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\AmpReport;

class AmpReportRepo {
    const REPORT_URL_TEMPLATE = 'http://report.mtroute.com/v2/show_report.amp?id=%d&norun=1&iat=0';

    protected $reports;

    public function __construct ( AmpReport $reports ) {
        $this->reports = $reports;
    }

    public function getModel () {
        return $this->reports;
    }

    public function getPageData ( $id ) {
        $report = $this->reports->find( $id );

        return [ 'name' => $report->name , 'ampReportId' => $report->amp_report_id ];
    }

    public function saveReport ( $name , $reportId ) {
        $report = new AmpReport();

        $report->name = $name;
        $report->amp_report_id = $reportId;
        $report->save();
    }

    public function updateReport ( $systemId , $name , $reportId ) {
        $report = $this->reports->find( $systemId );
        $report->name = $name;
        $report->amp_report_id = $reportId;
        $report->save();
    }
}
