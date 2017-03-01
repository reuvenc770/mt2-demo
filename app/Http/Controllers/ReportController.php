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

use Log;

class ReportController extends Controller
{
    protected $reportType;
    protected $collection;
    protected $records;
    protected $totals;
    protected $currentRequest;

    public function __construct () {}

    public function viewAmpReports () {
        return response()->view( 'pages.report.amp-reports' );
    }

    public function users () {
        return response()->view( 'pages.report.amp-users' );
    }
}
