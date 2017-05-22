<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/3/16
 * Time: 4:08 PM
 */

namespace App\Repositories;


use App\Models\ListProfile;
use App\Models\ListProfileClient;
use App\Models\ListProfileVertical;
use App\Models\ListProfileSchedule;
use App\Models\ListProfileOfferAction;
use App\Models\ListProfileFeed;
use App\Models\ListProfileDomainGroup;
use App\Models\ListProfileFeedGroup;
use App\Models\ListProfileOfferSuppression;

class ListProfileRepo
{
    private $listProfile;
    private $vertical;
    private $schedule;
    private $offerAction;
    private $feed;
    private $isp;
    private $feedGroup;
    private $client;
    private $offerSuppression;

    public function __construct(
        ListProfile $listProfile ,
        ListProfileVertical $vertical ,
        ListProfileSchedule $schedule ,
        ListProfileOfferAction $offerAction ,
        ListProfileFeed $feed ,
        ListProfileDomainGroup $isp,
        ListProfileFeedGroup $feedGroup,
        ListProfileClient $client,
        ListProfileOfferSuppression $offerSuppression
    ) {
        $this->listProfile = $listProfile;
        $this->vertical = $vertical;
        $this->schedule = $schedule;
        $this->offerAction = $offerAction;
        $this->feed = $feed;
        $this->isp = $isp;
        $this->feedGroup = $feedGroup;
        $this->client = $client;
        $this->offerSuppression = $offerSuppression;
    }

    public function getModel ( $options = [] ) {
        $model = $this->listProfile->with( 'schedule' );
        if ( isset( $options['partyType'] ) && in_array( $options['partyType'], [1,2,3] ) ){
            $model->where('party' , $options['partyType'] );
        }
        return $model;
    }

    public function create ( $data ) {
        $listProfile = $this->listProfile->create( $data );

        return $listProfile->id;
    }

    public function updateOrCreate($data) {
        $listProfileId = $data['profile_id'];

        unset( $data[ 'profile_id' ] );

        $this->listProfile->updateOrCreate(['id' => $listProfileId ], $data);
    }

    public function prepareTableForSync() {}

    public function canBeDeleted ( $id ) {
        return $this->listProfile->find( $id )->canModelBeDeleted();
    }

    public function delete ( $id ) {
        return $this->listProfile->destroy( $id );
    }

    public function returnActiveProfiles(){
       return $this->listProfile->where("status", "A")->select('id','profile_name')->get();
    }

    public function getAllListProfiles(){
        return $this->listProfile->select('id','name','party')->orderBy('name')->get();
    }

    public function getProfile($id) {
        return $this->listProfile->where('id', $id)->firstOrFail();
    }

    public function updateTotalCount($id, $count) {
        $this->listProfile->where('id', $id)->update(['total_count' => $count]);
    }

    public function shouldInsertHeader($id) {
        return $this->listProfile->where('id', $id)->firstOrFail()->insert_header === 1;
    }

    public function assignVerticals ( $id , $verticals ) {
        $this->vertical->where( 'list_profile_id' , $id )->delete();

        foreach ( $verticals as $currentVertical ) {
            $this->vertical->insert( [ 'list_profile_id' => $id , 'cake_vertical_id' => $currentVertical ] );
        }
    }

    public function assignSchedule ( $id , $options ) {
        $currentSchedule = $this->schedule->where( 'list_profile_id' , $id )->first();
        $currentScheduleId = null;

        if ( $currentSchedule ) {
            $currentScheduleId = $currentSchedule->id;
        }

        $this->schedule->updateOrCreate( [ 'id' => $currentScheduleId ] , [
            'list_profile_id' => $id ,
            'run_daily' => in_array( 'Daily' , $options[ 'interval' ] ) ,
            'run_weekly' => in_array( 'Weekly' , $options[ 'interval' ] ),
            'run_monthly' => in_array( 'Monthly' , $options[ 'interval' ] ) ,
            'day_of_week' => $options[ 'dayOfWeek' ] ? $options[ 'dayOfWeek' ] : '' ,
            'day_of_month' => $options[ 'dayOfMonth' ] ? $options[ 'dayOfMonth' ] : ''
        ] );
    }

    public function assignOfferActions ( $id , $offers ) {
        $this->offerAction->where( 'list_profile_id' , $id )->delete();

        foreach ( $offers as $currentOffer ) {
            $this->offerAction->insert( [ 'list_profile_id' => $id , 'offer_id' => $currentOffer[ 'id' ] ] );
        }
    }

    public function assignOfferSuppression ($id , $offers) {
        $this->offerSuppression->where('list_profile_id', $id)->delete();

        foreach ($offers as $currentOffer) {
            $this->offerSuppression->insert(['list_profile_id' => $id, 'offer_id' => $currentOffer[ 'id' ]]);
        }
    }

    public function assignFeeds ( $id , $feeds ) {
        $this->feed->where( 'list_profile_id' , $id )->delete();

        foreach ( $feeds as $currentFeed ) {
            $this->feed->insert( [ 'list_profile_id' => $id , 'feed_id' => $currentFeed ] );
        }
    }

    public function assignFeedGroups ( $id , $feedGroups ) {
        $this->feedGroup->where( 'list_profile_id' , $id )->delete();

        foreach ( $feedGroups as $currentFeed ) {
            $this->feedGroup->insert( [ 'list_profile_id' => $id , 'feed_group_id' => $currentFeed ] );
        }
    }

    public function assignClients ( $id , $clients ) {
        $this->client->where( 'list_profile_id' , $id )->delete();

        foreach ( $clients as $client ) {
            $this->client->insert( [ 'list_profile_id' => $id , 'client_id' => $client ] );
        }
    }

    public function assignIsps ( $id , $isps ) {
        $this->isp->where( 'list_profile_id' , $id )->delete();

        foreach ( $isps as $currentIsp ) {
            $this->isp->insert( [ 'list_profile_id' => $id , 'domain_group_id' => $currentIsp ] );
        }
    }

    public function getFeedIdsForProfile($id) {
        // Feeds can belong to a list profile one of three ways:
        // 1. Directly attached to the list profile (list_profile_feeds)
        // 2. Indirectly attached via a feed group (list_profile_feed_groups)
        // 3. Indirectly attached via a client (list_profile_clients)
        // We need to return all of these and dedupe

        $output = [];

        $directFeeds = $this->feed->where('list_profile_id', $id)->pluck('feed_id')->all();

        $data = config('database.connections.mysql.database');
        $feedGroupFeeds = $this->feedGroup
            ->join("$data.feedgroup_feed as fgf", 'list_profile_feed_groups.feed_group_id', '=', 'fgf.feedgroup_id')
            ->where('list_profile_id', $id)
            ->pluck('feed_id')->all();

        $clientFeeds = $this->client
                            ->join("$data.clients as c", 'list_profile_clients.client_id', '=', 'c.id')
                            ->join("$data.feeds as f", 'c.id', '=', 'f.client_id')
                            ->where('list_profile_id', $id)
                            ->pluck('f.id')->all();

        $output = array_merge($feedGroupFeeds, $directFeeds);
        $output = array_merge($output, $clientFeeds);

        return array_unique($output);
    }

    public function getSuppressionListIds($id) {
        $feeds = $this->getFeedIdsForProfile($id);

        $data = config('database.connections.mysql.database');
        $list = $this->feed->join("$data.feeds as f", 'list_profile_feeds.feed_id', '=', 'f.id')
                    ->pluck('suppression_list_id')->all();

        return array_unique($list);
    }
}
