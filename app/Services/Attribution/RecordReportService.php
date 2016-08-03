<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Repositories\Attribution\RecordReportRepo;

class RecordReportService {
    protected $repo;

    public function __construct ( RecordReportRepo $repo ) {
        $this->repo = $repo;
    }

    public function getByDateRange ( array $dateRange ) {
        return $this->repo->getByDateRange( $dateRange );
    }
}
