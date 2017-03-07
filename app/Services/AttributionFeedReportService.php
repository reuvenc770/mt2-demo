<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\AttributionFeedReportRepo;
use Illuminate\Http\Request;

class AttributionFeedReportService {
    protected $repo;

    public function __construct ( AttributionFeedReportRepo $repo ) {
        $this->repo = $repo;
    }

    public function getReportData ( Request $request ) {
        return $this->repo->getReportData( $request );
    }
}
