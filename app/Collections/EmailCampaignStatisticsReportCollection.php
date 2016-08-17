<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Collections;

use App\Collections\AbstractReportCollection;
use App\Models\EmailCampaignStatistic;

class EmailCampaignStatisticsReportCollection extends AbstractReportCollection {
    protected $totalFields = [
        'esp_total_opens' ,
        'esp_total_clicks' ,
        'trk_total_opens' ,
        'trk_total_clicks' ,
        'mt_total_opens' ,
        'mt_total_clicks' ,
        'unsubscribed' ,
        'hard_bounce' ,
    ];

    public function __construct ( $items = [] ) {
        parent::__construct( $items );

        $this->model = new EmailCampaignStatistic();
    }

    protected function processQuery () {
        $order = 'asc';
        if ( $this->query[ 'sort' ][ 'desc' ] ) {
            $order = 'desc';
        }

        return $this->model->orderBy( $this->query[ 'sort' ][ 'field' ] , $order );
    }

    protected function getDateField () { return 'updated_at'; }
}
