<?php

namespace App\Repositories;

use App\Models\FeedDateEmailBreakdown;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\DataModels\RecordProcessingReportUpdate;

/**
 *
 */
class FeedDateEmailBreakdownRepo {
  
    private $model;
    const MAX_RETRY_ATTEMPTS = 20;

    public function __construct(FeedDateEmailBreakdown $model) {
        $this->model = $model;
    }

    public function updateProcessedData(RecordProcessingReportUpdate $reportUpdate) {
        $data = $reportUpdate->toArray();

        $pdo = DB::connection()->getPdo();
        $updates = [];

        foreach ($data as $feedId => $domains) {
            foreach($domains as $domainGroupId => $filenames) {
                foreach($filenames as $filename => $row) {
                    $insertString = '('
                        . $pdo->quote($feedId) . ','
                        . 'CURDATE(),'
                        . $pdo->quote($domainGroupId) . ', '
                        . $pdo->quote($filename) . ', '
                        . $pdo->quote($row['totalRecords']) . ', '
                        . $pdo->quote($row['validRecords']) . ', '
                        . $pdo->quote($row['suppressed']) . ', '
                        . $pdo->quote($row['badSourceUrls']) . ', '
                        . $pdo->quote($row['fullPostalCount']) . ', '
                        . $pdo->quote($row['badIpAddresses']) . ', '
                        . $pdo->quote($row['otherInvalid']) . ', '
                        . $pdo->quote($row['suppressedDomains']) . ', '
                        . $pdo->quote($row['phoneCount']) . ', '
                        . $pdo->quote($row['unique']) . ','
                        . $pdo->quote($row['duplicate']) . ','
                        . $pdo->quote($row['non-unique']) . ','
                        . $pdo->quote($row['prev_responder_count'])
                        .')';

                    $updates[] = $insertString;
                }
            }

        }

        $inserts = implode(',', $updates);

        if ($inserts) {
            $done = false;
            $attempts = 0;
            
            while (!$done) {
                if ($attempts < self::MAX_RETRY_ATTEMPTS) {
                    try {
                        DB::statement(
                            "INSERT INTO feed_date_email_breakdowns 
                            (feed_id, date, domain_group_id, filename, total_emails, valid_emails, suppressed_emails,
                            bad_source_urls, full_postal_counts, bad_ip_addresses, other_invalid, suppressed_domains,
                            phone_counts, unique_emails, feed_duplicates, cross_feed_duplicates, prev_responder_count)

                            VALUES 

                            $inserts

                            ON DUPLICATE KEY UPDATE
                                feed_id = feed_id,
                                date = date,
                                domain_group_id = domain_group_id,
                                filename = filename,
                                total_emails = total_emails + VALUES(total_emails),
                                valid_emails = valid_emails + VALUES(valid_emails),
                                suppressed_emails = suppressed_emails + VALUES(suppressed_emails),
                                bad_source_urls = bad_source_urls + VALUES(bad_source_urls),
                                full_postal_counts = full_postal_counts + VALUES (full_postal_counts),
                                bad_ip_addresses = bad_ip_addresses + VALUES(bad_ip_addresses),
                                other_invalid = other_invalid + VALUES(other_invalid),
                                suppressed_domains = suppressed_emails + VALUES(suppressed_emails),
                                phone_counts = phone_counts + VALUES(phone_counts),
                                unique_emails = unique_emails + VALUES(unique_emails),
                                feed_duplicates = feed_duplicates + VALUES(feed_duplicates),
                                cross_feed_duplicates = cross_feed_duplicates + VALUES(cross_feed_duplicates),
                                prev_responder_count = prev_responder_count + VALUES(prev_responder_count)");
                        $done = true;
                    }
                    catch (\Exception $e) {
                        $attempts++;
                        sleep(2);
                    }
                }
                else {
                    throw new \Exception("FeedDateEmailBreakdownRepo::updateProcessedData() failed too many times with {$e->getMessage()} and $inserts");
                }
            }
        }
    }
    

    public function getFeedDateUniqueCount($feedId, $date) {
        return $this->model
                    ->selectRaw("SUM(IFNULL(unique_emails, 0)) as uniques")
                    ->where('feed_id', $feedId)
                    ->where('date', $date)
                    ->first()
                    ->uniques;
    }

    public function updateRawErrors(array $insert) {
        $updates = [];

        foreach($insert as $feedId => $feedData) {
            foreach($feedData as $emailClassId => $emailClassData) {
                foreach($emailClassData as $day => $dayData) {
                    foreach ($dayData as $filename => $data) {
                        $updates[] = '(' . (int)$feedId . ", '{$day}', " . (int)$emailClassId . ', ' . "'$filename'" . ', ' . (int)$data['total'] . ','  . (int)$data['bad_ip_addresses'] . ',' . (int)$data['other_invalid'] . ')';
                    }
                }
            }
        }

        if (count($updates) == 0) {
            return;
        }

        $done = false;
        $attempts = 0;
        $inserts = implode(',', $updates);
       
        while (!$done) { 
            if ($attempts < self::MAX_RETRY_ATTEMPTS) {
                try {
                    DB::statement("INSERT INTO feed_date_email_breakdowns
                        (feed_id, date, domain_group_id, filename, total_emails, bad_ip_addresses, other_invalid)
                        VALUES

                        $inserts

                        ON DUPLICATE KEY UPDATE
                        feed_id = feed_id,
                        date = date,
                        domain_group_id = domain_group_id,
                        filename = filename,
                        total_emails = total_emails + values(total_emails),
                        valid_emails = valid_emails,
                        suppressed_emails = suppressed_emails,
                        bad_source_urls = bad_source_urls,
                        full_postal_counts = full_postal_counts,
                        bad_ip_addresses = bad_ip_addresses + VALUES(bad_ip_addresses),
                        other_invalid = other_invalid + VALUES(other_invalid),
                        suppressed_domains = suppressed_domains,
                        phone_counts = phone_counts,
                        prev_responder_count = prev_responder_count,
                        unique_emails = unique_emails,
                        feed_duplicates = feed_duplicates,
                        cross_feed_duplicates = cross_feed_duplicates");
                    $done = true;
                }
                catch (\Exception $e) {
                    $attempts++;
                    sleep(2);
                }
            }
        }
    }

    public function getFeedDateCount($feedId, $date) {
        return $this->model->where('feed_id', $feedId)->where('date', $date)->count();
    }

}
