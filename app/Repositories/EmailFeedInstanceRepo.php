<?php

namespace App\Repositories;

use App\Models\EmailFeedInstance;
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

    public function __construct(EmailFeedInstance $model) {
        $this->model = $model;
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

    public function getSourceUrlCountsForDates($startDate, $endDate) {
        return $this->model
                    ->selectRaw("feed_id, source_url, subscribe_date, count(*) as count")
                    ->whereBetween('subscribe_date', [$startDate, $endDate])
                    ->groupBy('feed_id', 'source_url', 'subscribe_date')
                    ->get()
                    ->toArray();
    }

    public function getRecordsFromFeedStartingAt($feedId, $startingId) {
        return $this->model
                    ->selectRaw("email_feed_instances.*, e.email_address")
                    ->join('emails as e', 'email_feed_instances.email_id', '=', 'e.id')
                    ->where('feed_id', $feedId)
                    ->where('email_feed_instances.id', '>', $startingId)
                    ->orderBy('id');
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
