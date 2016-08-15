<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Collections\Attribution;

use Carbon\Carbon;
use App\Collections\AbstractReportCollection;
use App\Models\AttributionListOwnerReport;

class ListOwnerReportCollection extends AbstractReportCollection  {
    public function __construct ( $items = [] ) {
        parent::__construct( $items );

        $this->model = new AttributionListOwnerReport();
    }
}
