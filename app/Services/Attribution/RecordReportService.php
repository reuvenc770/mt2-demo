<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Repositories\AttributionRecordReportRepo;

class RecordReportService {
    protected $repo;

    public function __construct ( AttributionRecordReportRepo $repo ) {
        $this->repo = $repo;
    }

    public function getByDateRange ( array $dateRange ) {
        return $this->repo->getByDateRange( $dateRange );
    }
}
