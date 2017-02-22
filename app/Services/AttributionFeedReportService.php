<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\AttributionFeedReportRepo;
use App\Http\Requests;

class AttributionFeedReportService {
    protected $repo;

    public function __construct ( AttributionFeedReportRepo $repo ) {
        $this->repo = $repo;
    }

    public function getReportData ( Request $request ) {
        if () {

        }
    }
}
