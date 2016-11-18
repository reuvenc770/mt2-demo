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
                (feed_id, date, total_emails, valid_emails, suppressed_emails, unique_emails, feed_duplicates, cross_feed_duplicates)

                VALUES 
                (:feed_id, CURDATE(), :total_emails, :valid_emails, :suppressed, :fresh, :duplicates, :non_fresh)

                ON DUPLICATE KEY UPDATE
                    feed_id = feed_id,
                    date = date,
                    total_emails = total_emails + VALUES(total_emails),
                    valid_emails = valid_emails + VALUES(valid_emails),
                    suppressed_emails = suppressed_emails + VALUES(suppressed_emails),
                    unique_emails = unique_emails + VALUES(unique_emails),
                    feed_duplicates = feed_duplicates + VALUES(feed_duplicates),
                    cross_feed_duplicates = cross_feed_duplicates + VALUES(cross_feed_duplicates)", [

                    ':feed_id' => $feedId,
                    ':total_emails' => 0, // currently not putting in anything for total emails because we don't get them from MT1
                    ':valid_emails' => $valid,
                    ':suppressed' => $statuses['suppressed'],
                    ':fresh' => $statuses['fresh'],
                    ':duplicates' => $statuses['duplicate'],
                    ':non_fresh' => $statuses['non-fresh'],
                ]);

        }

    }

    public function massUpdateValidEmailStatus($data) {
        $pdo = DB::connection()->getPdo();
        $updates = [];

        foreach ($data as $feedId => $domains) {
            foreach($domains as $domainGroupId => $row) {
                $insertString = '('
                    . $pdo->quote($feedId) . ','
                    . 'CURDATE(),'
                    . $pdo->quote($domainGroupId) . ','
                    . $pdo->quote($row['unique']) . ','
                    . $pdo->quote($row['duplicate']) . ','
                    . $pdo->quote($row['non-unique'])
                    .')';

                $updates[] = $insertString;
            }

        }

        $inserts = implode(',', $updates);

        if ($inserts) {
            DB::statement(
                "INSERT INTO feed_date_email_breakdowns 
                (feed_id, date, domain_group_id, unique_emails, feed_duplicates, cross_feed_duplicates)

                VALUES 
                
                $insertString

                ON DUPLICATE KEY UPDATE
                    feed_id = feed_id,
                    date = date,
                    domain_group_id = domain_group_id,
                    unique_emails = unique_emails + VALUES(unique_emails),
                    feed_duplicates = feed_duplicates + VALUES(feed_duplicates),
                    cross_feed_duplicates = cross_feed_duplicates + VALUES(cross_feed_duplicates)");
        }
        
    }


    public function updateExtendedStatuses($data) {
        $pdo = DB::connection()->getPdo();
        $updates = [];
        foreach($data as $feedId => $domains) {
            foreach($domains as $domainGroupId => $row) {
                $insertString = '(' 
                    . $pdo->quote($feedId) . ', '
                    . "CURDATE(), "
                    . $pdo->quote($domainGroupId) . ', '
                    . $pdo->quote($row['totalRecords']) . ', '
                    . $pdo->quote($row['validRecords']) . ', '
                    . $pdo->quote($row['suppressed']) . ', '
                    . $pdo->quote($row['badSourceUrls']) . ', '
                    . $pdo->quote($row['fullPostalCount']) . ', '
                    . $pdo->quote($row['badIpAddresses']) . ', '
                    . $pdo->quote($row['otherInvalid']) . ', '
                    . $pdo->quote($row['suppressedDomains']) . ', '
                    . $pdo->quote($row['phoneCount'])
                    . ')';
                $updates[] = $insertString;
            }
        }

        $inserts = implode(',', $updates);

        if ($inserts) {
            DB::statement("INSERT INTO feed_date_email_breakdowns
                (feed_id, date, domain_group_id, total_emails, valid_emails, suppressed_emails,
                bad_source_urls, full_postal_counts, bad_ip_addresses, other_invalid, suppressed_domains,
                phone_counts)
                VALUES

                $inserts

                ON DUPLICATE KEY UPDATE
                feed_id = feed_id,
                date = date,
                domain_group_id = domain_group_id,
                total_emails = total_emails + VALUES(total_emails),
                valid_emails = valid_emails + VALUES(valid_emails),
                suppressed_emails = suppressed_emails + VALUES(suppressed_emails),
                bad_source_urls = bad_source_urls + VALUES(bad_source_urls),
                full_postal_counts = full_postal_counts + VALUES (full_postal_counts),
                bad_ip_addresses = bad_ip_addresses + VALUES(bad_ip_addresses),
                other_invalid = other_invalid + VALUES(other_invalid),
                suppressed_domains = suppressed_emails + VALUES(suppressed_emails),
                phone_counts = phone_counts + VALUES(phone_counts)");
        }

        
    }

}