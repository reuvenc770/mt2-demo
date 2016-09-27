<?php

namespace App\Repositories;

use App\Models\FeedDateEmailBreakdown;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class FeedDateEmailBreakdownRepo {
  
    private $model;

    public function __construct(FeedDateEmailBreakdown $model) {
        $this->model = $model;
    }

    public function massUpdateStatuses($statusesAssoc, $date) {
        foreach ($statusesAssoc as $feedId => $statuses) {
            $valid = $statuses['fresh'] + $statuses['non-fresh'] + $statuses['duplicate'] + $statuses['suppressed'];

            DB::statement(
                "INSERT INTO feed_date_email_breakdowns 
                (feed_id, date, total_emails, valid_emails, suppressed_emails, fresh_emails, feed_duplicates, cross_feed_duplicates)

                VALUES 
                (:feed_id, :date, :total_emails, :valid_emails, :suppressed, :fresh, :duplicates, :non_fresh)

                ON DUPLICATE KEY UPDATE
                    feed_id = feed_id,
                    date = date,
                    total_emails = total_emails + VALUES(total_emails),
                    valid_emails = valid_emails + VALUES(valid_emails),
                    suppressed_emails = suppressed_emails + VALUES(suppressed_emails),
                    fresh_emails = fresh_emails + VALUES(fresh_emails),
                    feed_duplicates = feed_duplicates + VALUES(feed_duplicates),
                    cross_feed_duplicates = cross_feed_duplicates + VALUES(cross_feed_duplicates)", [

                    ':feed_id' => $feedId,
                    ':date' => $date,
                    ':total_emails' => 0, // currently not putting in anything for total emails because we don't get them from MT1
                    ':valid_emails' => $valid,
                    ':suppressed' => $statuses['suppressed'],
                    ':fresh' => $statuses['fresh'],
                    ':duplicates' => $statuses['duplicate'],
                    ':non_fresh' => $statuses['non_fresh'],
                ]);

        }

    }

}