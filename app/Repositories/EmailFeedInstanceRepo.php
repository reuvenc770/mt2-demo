<?php

namespace App\Repositories;

use App\Models\EmailFeedInstance;
use App\Models\SourceUrlCount;
use DB;
use Illuminate\Database\Query\Builder;
use App\Repositories\RepoTraits\Batchable;
use App\Repositories\RepoInterfaces\ICanonicalDataSource;

/**
 *
 */
class EmailFeedInstanceRepo implements ICanonicalDataSource {
    use Batchable;

    private $model;
    private $countModel;

    public function __construct(EmailFeedInstance $model, SourceUrlCount $countModel) {
        $this->model = $model;
        $this->countModel = $countModel;
    }

    private function buildBatchedQuery($batchInstances) {
        return "INSERT INTO email_feed_instances
                (email_id, feed_id, subscribe_date, subscribe_datetime, capture_date,
                first_name, last_name, address, address2, city, state, 
                zip, country, dob, gender, phone, mobile_phone, work_phone, 
                source_url, ip, other_fields, created_at, updated_at)

                VALUES

                {$batchInstances}

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                feed_id = feed_id,
                subscribe_date = subscribe_date,
                subscribe_datetime = subscribe_datetime,
                capture_date = capture_date,
                first_name = first_name,
                last_name = last_name,
                address = address,
                address2 = address2,
                city = city,
                state = state,
                zip = zip,
                country = country,
                dob = dob,
                gender = gender,
                phone = phone,
                mobile_phone = mobile_phone,
                work_phone = work_phone,
                source_url = source_url,
                ip = ip,
                other_fields = other_fields,
                created_at = created_at,
                updated_at = updated_at";
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['feed_id']) . ','
            . $pdo->quote($row['subscribe_date']) . ','
            . $pdo->quote($row['subscribe_datetime']) . ','
            . $pdo->quote($row['capture_date']) . ','
            . $pdo->quote($row['first_name']) . ','
            . $pdo->quote($row['last_name']) . ','
            . $pdo->quote($row['address']) . ','
            . $pdo->quote($row['address2']) . ','
            . $pdo->quote($row['city']) . ','
            . $pdo->quote($row['state']) . ','
            . $pdo->quote($row['zip']) . ','
            . $pdo->quote($row['country']) . ','
            . $pdo->quote($row['dob']) . ','
            . $pdo->quote($row['gender']) . ','
            . $pdo->quote($row['phone']) . ','
            . $pdo->quote($row['mobile_phone']) . ','
            . $pdo->quote($row['work_phone']) . ','
            . $pdo->quote($row['source_url']) . ','
            . $pdo->quote($row['ip']) . ','
            . $pdo->quote($row['other_fields'])
            . ', NOW(), NOW())';
    }


    public function getEmailInstancesAfterDate($emailId, $date, $feedId) {
        $attrDb = config('database.connections.attribution.database');

        $reps = DB::table('email_feed_instances as efi')
                ->select('efi.feed_id', 'level', 'efi.subscribe_date')
                ->join($attrDb . '.attribution_levels as al', 'efi.feed_id', '=', 'al.feed_id')
                ->join('feeds as f', 'efi.feed_id', '=', 'f.id')
                ->where('efi.subscribe_date', '>=', $date)
                ->where('efi.feed_id', '<>', $feedId)
                ->where('email_id', $emailId)
                ->where('f.party', 3)
                ->where('f.status', 'Active')
                ->orderBy('subscribe_date', 'asc')
                ->get();

        return $reps;
    }

    public function getInstances($emailId) {
        $attrDb = config('database.connections.attribution.database');

        $reps = DB::table('email_feed_instances as efi')
                ->select('efi.feed_id', 'level', 'efi.subscribe_date')
                ->join($attrDb . '.attribution_levels as al', 'efi.feed_id', '=', 'al.feed_id')
                ->join('feeds as f', 'efi.feed_id', '=', 'f.id')
                ->where('email_id', $emailId)
                ->where('f.party', 3)
                ->where('f.status', 'Active')
                ->orderBy('subscribe_date', 'asc')
                ->get();

        return $reps;
    }

    public function getInstancesForDateRange($startDate , $endDate) {
        return $this->model->whereBetween( 'subscribe_date' , [ $startDate , $endDate ] );
    }

    public function getMt1UniqueCountForFeedAndDate( $feedId , $date ) {
        $results =  DB::connection( 'mt1_data' )->table( 'ClientRecordTotalsByIsp' )
            ->select( DB::raw( "sum( uniqueRecords ) as 'uniques'" ) )
            ->where( [
                [ 'clientID' , $feedId ] ,
                [ 'processedDate' , $date ]
            ] )->get();

        if ( count( $results ) > 0 ) {
            return $results[ 0 ]->uniques;
        } else {
            return 0;
        }
    }

    public function getMt2UniqueCountForFeedAndDate( $feedId , $date ) {
        $mt2Db = config( 'database.connections.slave_data.database' );
        $attrDb = config( 'database.connections.attribution.database' );
        $reportDb = config( 'database.connections.reporting_data.database' );

        $results = DB::select( DB::raw( "
            SELECT
                COUNT( x.email_address ) AS 'uniques'
            FROM
               ( SELECT
                   e.email_address
               FROM
                   $mt2Db.email_feed_instances efi1
                   LEFT JOIN $mt2Db.email_feed_instances efi2 ON efi1.email_id = efi2.email_id
                   INNER JOIN $mt2Db.emails e ON efi1.email_id = e.id
                  
               WHERE
                   efi1.subscribe_date = '{$date}'
                   AND
                   efi1.id <> efi2.id
                   AND
                   efi2.subscribe_date <= '{$date}'
                   AND
                   efi2.id IS NULL
                   AND
                   efi1.feed_id = '{$feedId}'
             
               UNION DISTINCT
             
               SELECT
                   e.email_address
               FROM
                   $mt2Db.email_feed_instances efi
                   INNER JOIN $attrDb.email_feed_assignments efa ON efi.email_id = efa.email_id
                   INNER JOIN $mt2Db.emails e ON efa.email_id = e.id
               WHERE
                   efi.feed_id = '{$feedId}'
                   AND
                   efi.subscribe_date = '{$date}'
                   AND
                   efa.subscribe_date < '{$date}' - INTERVAL 90 DAY
                   AND
                   efi.feed_id <> efa.feed_id
                   AND
                   efi.status = 'A'
             
               UNION DISTINCT
             
               SELECT
                   e.email_address
               FROM
                   $mt2Db.email_feed_instances efi FORCE INDEX(registration_date)
                   INNER JOIN $attrDb.email_feed_assignments efa ON efi.email_id = efa.email_id
                   LEFT JOIN $reportDb.email_campaign_statistics ecs ON efi.email_id = ecs.email_id
                   INNER JOIN $mt2Db.emails e ON efa.email_id = e.id
                   INNER JOIN $attrDb.attribution_levels alImport ON efi.feed_id = alImport.feed_id
                   INNER JOIN $attrDb.attribution_levels alOld ON efa.feed_id = alOld.feed_id
               WHERE
                   efi.subscribe_date = '{$date}'
                   AND
                   efa.subscribe_date BETWEEN '{$date}' - INTERVAL 90 DAY AND '{$date}' - INTERVAL 10 DAY
                   AND
                   alImport.level < alOld.level
                   AND
                   efi.status = 'A'
                   AND
                   efi.feed_id = '{$feedId}'
               GROUP BY
                   efi.email_id
              
               HAVING
                   SUM(IFNULL(ecs.esp_total_opens, 0)) = 0 ) x" )
        );

/**
    Uh oh. This is a problem.
*/

        if ( count( $results ) > 0 ) {
            return $results[ 0 ]->uniques;
        } else {
            return 0;
        }
    }

    public function getRecordsFromFeedStartingAt($feedId, $startingId) {
        return $this->model
                    ->selectRaw("email_feed_instances.*, e.email_address")
                    ->join('emails as e', 'email_feed_instances.email_id', '=', 'e.id')
                    ->where('feed_id', $feedId)
                    ->where('email_feed_instances.id', '>', $startingId)
                    ->orderBy('id');
    }

    public function getRecordCountForSource ( $search ) {
        $db = config('database.connections.mysql.database');
        $reportDb = config( 'database.connections.reporting_data.database' );

        $builder = $this->countModel
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
        $this->countModel->whereBetween( 'subscribe_date' , [ $startDate , $endDate ] )->delete();
    }

    public function saveSourceCounts ( $countList ) {
        foreach ( $countList as $current ) {
            $this->countModel->updateOrCreate( $current );
        }
    }

    public function compareSourcesWithField($tableName, $startPoint, $segmentEnd) {}


    public function compareSources($tableName, $startPoint, $segmentEnd) {

        return $this->model
                    ->leftJoin("feeds as f", 'email_feed_instances.feed_id', '=', 'f.id')
                    ->leftJoin("$tableName as tbl", function($join) {
                        $join->on('email_feed_instances.email_id', '=', 'tbl.email_id');
                        $join->on('email_feed_instances.feed_id', '=', 'tbl.feed_id');
                    })
                    ->whereRaw("(email_feed_instances.id BETWEEN {$startPoint} AND {$segmentEnd}) AND (tbl.email_id IS NULL)")
                    ->select('email_feed_instances.*', 'f.party')
                    ->get()
                    ->toArray();
    }


    public function maxId() {
        return $this->model->orderBy('id', 'desc')->first()['id'];
    }


    public function nextNRows($startPoint, $offset) {
        return $this->model
            ->whereRaw("id >= $startPoint")
            ->orderBy('id')
            ->skip($offset)
            ->first()['id'];
    }

    public function lessThan($startPoint, $endPoint) {
        return (int)$startPoint < (int)$endPoint;
    }

}
