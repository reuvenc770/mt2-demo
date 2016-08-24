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

    public function recordCount () {
        return $this->model->count();
    }

    public function getRecordsAndTotals ( $options = [] ) {
        $records = $this;

        return [
            'records' => &$records ,
            'totals' => $this->sumTotals( $records , $this->totalFields )
        ];
    }

    protected function processQuery () {
        $order = 'asc';
        if ( $this->query[ 'sort' ][ 'desc' ] ) {
            $order = 'desc';
        }

        $lastPage = $this->query[ 'page' ] - 1;

        $recordsToSkip = ( $lastPage > 0 ? $this->query[ 'limit' ] * ( $lastPage ) : 0 );

        return $this->model->skip( $recordsToSkip )->take( $this->query[ 'limit' ] )->orderBy( $this->query[ 'sort' ][ 'field' ] , $order );
    }

    protected function getDateField () { return 'updated_at'; }
}
