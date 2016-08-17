<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Collections;

use App\Collections\AbstractReportCollection;
use App\Models\StandardReport;

class DeployReportCollection extends AbstractReportCollection {
    protected $totalFields = [
        'm_sent' ,
        'e_sent' ,
        'delivered' ,
        'bounced' ,
        'optouts' ,
        'm_opens' ,
        'e_opens' ,
        't_opens' ,
        'm_opens_unique' ,
        'e_opens_unique' ,
        't_opens_unique' ,
        'm_clicks' ,
        'e_clicks' ,
        't_clicks' ,
        'm_clicks_unique' ,
        'e_clicks_unique' ,
        't_clicks_unique' ,
        'conversions' ,
        'cost' ,
        'revenue' ,
    ];

    public function __construct ( $items = [] ) {
        parent::__construct( $items );

        $this->model = new StandardReport();
    }

    protected function getDateField () { return 'datetime'; }
}
