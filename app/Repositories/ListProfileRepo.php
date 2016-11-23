<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/3/16
 * Time: 4:08 PM
 */

namespace App\Repositories;


use App\Models\ListProfile;
use App\Models\ListProfileVertical;
use App\Models\ListProfileSchedule;
use App\Models\ListProfileOffer;
use App\Models\ListProfileFeed;
use App\Models\ListProfileDomainGroup;
use App\Models\ListProfileFeedGroup;

class ListProfileRepo
{
    private $listProfile;
    private $vertical;
    private $schedule;
    private $offer;
    private $feed;
    private $isp;

    private $feedGroup;

    public function __construct(
        ListProfile $listProfile ,
        ListProfileVertical $vertical ,
        ListProfileSchedule $schedule ,
        ListProfileOffer $offer ,
        ListProfileFeed $feed ,
        ListProfileDomainGroup $isp,
        ListProfileFeedGroup $feedGroup
    ) {
        $this->listProfile = $listProfile;
        $this->vertical = $vertical;
        $this->schedule = $schedule;
        $this->offer = $offer;
        $this->feed = $feed;
        $this->isp = $isp;
        $this->feedGroup = $feedGroup;
    }

    public function getModel () {
        return $this->listProfile;
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

    public function returnActiveProfiles(){
       return $this->listProfile->where("status", "A")->select('id','profile_name')->get();
    }

    public function getAllListProfiles(){
        return $this->listProfile->select('id','name')->orderBy('name')->get();
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

    public function assignOffers ( $id , $offers ) {
        $this->offer->where( 'list_profile_id' , $id )->delete();

        foreach ( $offers as $currentOffer ) {
            $this->offer->insert( [ 'list_profile_id' => $id , 'offer_id' => $currentOffer[ 'id' ] ] );
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

    public function assignIsps ( $id , $isps ) {
        $this->isp->where( 'list_profile_id' , $id )->delete();

        foreach ( $isps as $currentIsp ) {
            $this->isp->insert( [ 'list_profile_id' => $id , 'domain_group_id' => $currentIsp ] );
        }
    }

}
