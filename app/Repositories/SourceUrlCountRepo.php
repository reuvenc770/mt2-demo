<?php

namespace App\Repositories;

use App\Models\SourceUrlCount;
use Illuminate\Database\Query\Builder;
use DB;

class SourceUrlCountRepo {

    private $model;

    public function __construct(SourceUrlCount $model) {
        $this->model = $model;
    }

    public function getRecordCountForSource ( $search ) {
        $db = config('database.connections.mysql.database');
        $reportDb = config( 'database.connections.reporting_data.database' );

        $builder = $this->model
                            ->join( "$db.feeds" , "$db.feeds.id" , '=' , "$reportDb.source_url_counts.feed_id" )
                            ->join( "$db.clients" , "$db.clients.id" , '=' , "$db.feeds.client_id" )
                            ->select(
                                "$db.clients.name as clientName" ,
                                "$db.feeds.name as feedName" ,
                                "$reportDb.source_url_counts.source_url as sourceUrl" ,
                                "$reportDb.source_url_counts.count"
                            )
                            ->where( "$reportDb.source_url_counts.source_url" , 'LIKE' , "%{$search[ 'source_url' ]}%" )
                            ->whereBetween( "$reportDb.source_url_counts.subscribe_date" , [ $search[ 'startDate' ] , $search[ 'endDate' ] ] );

        if ( !empty( $search[ 'feedIds' ] ) ) {
            $builder = $builder->whereIn( "$reportDb.source_url_counts.feed_id" , $search[ 'feedIds' ] );
        }

        if ( !empty( $search[ 'clientIds' ] ) ) {
            $builder = $builder->whereIn( "$db.feeds.client_id" , $search[ 'clientIds' ] );
        }

        if ( !empty( $search[ 'verticalIds' ] ) ) {
            $builder = $builder->whereIn( "$db.feeds.vertical_id" , $search[ 'verticalIds' ] );
        }

        $builder = $builder->groupBy( [ "$db.feeds.client_id" , "$reportDb.source_url_counts.feed_id" , "$reportDb.source_url_counts.source_url" ] );

        return $builder->get();
    }

    public function clearCountForDateRange ( $startDate , $endDate ) {
        $this->model->whereBetween( 'subscribe_date' , [ $startDate , $endDate ] )->delete();
    }

    public function saveSourceCounts ( $countList ) {
        foreach ( $countList as $current ) {
            $this->model->updateOrCreate([
                'source_url' => $current['source_url'],
                'feed_id' => $current['feed_id'],
                'subscribe_date' => $current['subscribe_date']
            ], $current);
        }
    }
}