<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Collections\Attribution;

use Carbon\Carbon;
use App\Collections\AbstractReportCollection;
use App\Models\AttributionClientDeployReport;

class ClientDeployReportCollection extends AbstractReportCollection {
    public function __construct ( $items = [] ) {
        parent::__construct( $items );

        $this->model = new AttributionClientDeployReport();
    }
}
