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
use App\Models\ListProfileCountry;

class ListProfileRepo
{
    private $listProfile;
    private $vertical;
    private $schedule;
    private $offer;
    private $feed;
    private $isp;
    private $country;

    public function __construct(
        ListProfile $listProfile ,
        ListProfileVertical $vertical ,
        ListProfileSchedule $schedule ,
        ListProfileOffer $offer ,
        ListProfileFeed $feed ,
        ListProfileDomainGroup $isp ,
        ListProfileCountry $country
    ) {
        $this->listProfile = $listProfile;
        $this->vertical = $vertical;
        $this->schedule = $schedule;
        $this->offer = $offer;
        $this->feed = $feed;
        $this->isp = $isp;
        $this->country = $country;
    }

    public function create ( $data ) {
        return $this->listProfile->insertGetId( $data );
    }

    public function updateOrCreate($data) {
        $this->listProfile->updateOrCreate(['profile_id' => $data['profile_id']], $data);
    }

    public function returnActiveProfiles(){
       return $this->listProfile->where("status", "A")->select('id','profile_name')->get();
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
        if ( $this->vertical->where( 'list_profile_id' , $id )->count() > 0 ) {
            $this->vertical->where( 'list_profile_id' , $id )->delete();
        }

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
        if ( $this->offer->where( 'list_profile_id' , $id )->count() > 0 ) {
            $this->offer->where( 'list_profile_id' , $id )->delete();
        }        

        foreach ( $offers as $currentOffer ) {
            $this->offer->insert( [ 'list_profile_id' => $id , 'offer_id' => $currentOffer[ 'id' ] ] );
        }
    }

    public function assignFeeds ( $id , $feeds ) {
        if ( $this->feed->where( 'list_profile_id' , $id )->count() > 0 ) {
            $this->feed->where( 'list_profile_id' , $id )->delete();
        }

        foreach ( $feeds as $currentFeed ) {
            $this->feed->insert( [ 'list_profile_id' => $id , 'feed_id' => $currentFeed ] );
        }
    }

    public function assignIsps ( $id , $isps ) {
        if ( $this->isp->where( 'list_profile_id' , $id )->count() > 0 ) {
            $this->isp->where( 'list_profile_id' , $id )->delete();
        }

        foreach ( $isps as $currentIsp ) {
            $this->isp->insert( [ 'list_profile_id' => $id , 'domain_group_id' => $currentIsp ] );
        }
    }

    public function assignCountries ( $id , $countries ) {
        if ( $this->country->where( 'list_profile_id' , $id )->count() > 0 ) {
            $this->country->where( 'list_profile_id' , $id )->delete();
        }

        foreach ( $countries as $currentCountry ) {
            $this->country->insert( [ 'list_profile_id' => $id , 'country_id' => $currentCountry ] );
        }
    }
}
