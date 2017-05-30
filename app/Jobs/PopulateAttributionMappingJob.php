<?php

namespace App\Jobs;
use App\Repositories\EtlPickupRepo;
use DB;

class PopulateAttributionMappingJob extends SafeJob {

    private $pickupRepo;
    const NAME = 'Mt1CmpMap';
    private $feeds = [2758, 2911];

    public function __construct(EtlPickupRepo $repo, $tracking) {
        $this->pickupRepo = $repo;
        parent::__construct(self::NAME, $tracking);
    }

    protected function handleJob() {
        $start = $this->pickupRepo->getLastInsertedForName(self::NAME);
	$final = $this->getMaxId();
	// Get all more recent than this that either:
	// 1. Are imported from the target feeds
	// 2. Are currently attributed to the target feeds
	// 3. Already exist in the current mapping table

	// This is a temporary job so we'll be more lax with standards
	while ($start < $final) { 
		$rows = $this->next10kRows($start);
		foreach ($rows as $row) {
		    if ($this->inTable($row->email_addr)) {
			$cmpFeedId = $this->getCmpFeedId($row->email_addr, $row->current_attributed_feed);
			DB::table('mt2_shuttle.mt1_cmp_attribution_map')->where('email_address', $row->email_addr)
			    ->update([
				'mt1_feed_id' => $row->current_attributed_feed,
				'cmp_feed_id' => $cmpFeedId,
				'last_mt1_action' => $row->last_action_date
			]);

		    }
		    elseif (in_array($row->client_id, $this->feeds) || in_array($row->current_attributed_feed, $this->feeds)) {
			$cmpFeedId = $this->getCmpFeedId($row->email_addr, $row->current_attributed_feed);
			DB::statement("INSERT INTO mt2_shuttle.mt1_cmp_attribution_map 
				(email_address, mt1_feed_id, cmp_feed_id, last_mt1_action)
			VALUES
			(:email, :mt1feed, :cmpfeed, :date)
			ON DUPLICATE KEY UPDATE
				email_address = email_address,
				mt1_feed_id = values(mt1_feed_id),
				cmp_feed_id = values(cmp_feed_id),
				last_mt1_action = values(last_mt1_action)",
			[
				':email' => $row->email_addr,
				':mt1feed' => $row->current_attributed_feed,
				':cmpfeed' => $cmpFeedId,
				':date' => $row->last_action_date
			]);
		    }
		    $start = $row->ID;
		}

	    }
	    $this->pickupRepo->updatePosition(self::NAME, $final);
    }

    private function inTable($emailAddress) {
        return DB::table('mt2_shuttle.mt1_cmp_attribution_map')->where('email_address', $emailAddress)->count() === 1;
    }

    private function getCmpFeedId($emailAddress, $currentAttribution) {
	$email = DB::table('mt2_data.emails')->where('email_address', $emailAddress)->first();
	
	if ($email) {
            $currentCmpFeed = DB::table('attribution.email_feed_assignments')->whereRaw('email_id = ' . $email->id)->first();
 	    if ($currentCmpFeed) {
	        return $currentCmpFeed->feed_id;
            }
        }
        return $currentAttribution;
    }

    private function next10kRows($id) {
        return DB::table('mt2_shuttle.client_record_log')->where('ID', '>', $id)->orderBy('id')->take(10000)->get();
    }
    
    private function getMaxId() {
	return DB::table('mt2_shuttle.client_record_log')->max('ID');
    }
}
