<?php

namespace App\Repositories;

use App\Models\Feed;
use App\Models\EmailOversightFeed;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Repositories\RepoInterfaces\Mt2Export;
use App\Repositories\RepoInterfaces\IAwsRepo;

/**
 *
 */
class FeedRepo implements Mt2Export, IAwsRepo {
    private $feed;

    public function __construct(Feed $feed) {
        $this->feed = $feed;
    }

    public function getFeeds () {
        return $this->feed->orderBy('short_name')->get();
    }

    public function getFeedsForParty($party) {
        return $this->feed->where('party', $party)->get();
    }

    public function getAllFeedsArray() {
        return $this->feed->orderBy('short_name')->get()->toArray();
    }

    public function fetch($id) {
        return $this->feed->where( 'id' , $id )
            ->leftJoin( 'email_oversight_feeds' , 'feeds.id' , '=' , 'email_oversight_feeds.feed_id' ) 
            ->select(
                'feeds.*' , 
                'email_oversight_feeds.list_id as email_oversight_list_id' 
            )
            ->first();
    }

    public function isActive($id) {
        $result = $this
                ->feed
                ->select('status')
                ->where('id', $id)
                ->where('party', 3) // third party only, otherwise ignore
                ->first();

        if ($result) {
            return $result->status === 'Active';
        }
        return false;
    }

    public function getMaxFeedId() {
        return (int)$this->feed->orderBy('id', 'desc')->first()['id'];
    }

    public function insert($data) {
        $this->feed->insert($data);
    }

    public function updateOrCreate( $data , $id = null ) {
        $emailOversightListId = 0;
        if (
            isset( $data[ 'email_oversight_list_id' ] )
            && 0 < (int)$data[ 'email_oversight_list_id' ] 
        ) {
            $emailOversightListId  = (int)$data[ 'email_oversight_list_id' ];
        }

        if ( isset( $data[ 'email_oversight_list_id' ] ) ) {
            unset( $data[ 'email_oversight_list_id' ] );
        }

        if ($id) {
            $feed = $this->feed->updateOrCreate(['id' => $id], $data);
        }
        else {
            $feed = $this->feed->updateOrCreate(['id' => $data['id']], $data);
        }

        $this->changeEmailOversightListId( $feed , $emailOversightListId );
    }

    protected function changeEmailOversightListId ( Feed $feed , $emailOversightListId ) {
        if ( 0 < $emailOversightListId ) {
            $m = new EmailOversightFeed();            
            $m->feed_id = $feed->id;
            $m->list_id = $emailOversightListId;
            $m->save();
        } else {
            $m = EmailOversightFeed::find( $feed->id );            
            $m->delete();
        }
    }

    public function create ( $data ) {
        $emailOversightListId = 0;
        if (
            isset( $data[ 'email_oversight_list_id' ] )
            && 0 < (int)$data[ 'email_oversight_list_id' ] 
        ) {
            $emailOversightListId  = (int)$data[ 'email_oversight_list_id' ];
        }

        if ( isset( $data[ 'email_oversight_list_id' ] ) ) {
            unset( $data[ 'email_oversight_list_id' ] );
        }

        unset( $data[ 'id' ] );
        $feed = $this->feed->create( $data );

        $this->changeEmailOversightListId( $feed , $emailOversightListId );
    }

    public function getModel( $searchData ) {
        $query = $this->feed
            ->join( 'clients' , 'feeds.client_id' , '=' , 'clients.id' )
            ->leftJoin( 'cake_verticals' , 'feeds.vertical_id' , '=' , 'cake_verticals.id' )
            ->leftJoin( 'feed_types' , 'feeds.type_id' , '=' , 'feed_types.id' )
            ->leftJoin( 'countries' , 'feeds.country_id' , '=' , 'countries.id' )
            ->leftJoin( 'email_oversight_feeds' , 'feeds.id' , '=' , 'email_oversight_feeds.feed_id' )
            ->select(
                'feeds.id' ,
                'clients.name as clientName' ,
                'feeds.party' ,
                'feeds.name' ,
                'feeds.short_name' ,
                'feeds.password' ,
                'feeds.host_ip as hostIp' ,
                'feeds.status' ,
                'cake_verticals.name as feedVertical',
                'feed_types.name as feedType' ,
                'countries.abbr as country' ,
                'feeds.source_url' ,
                'email_oversight_feeds.list_id as email_oversight_list_id' ,
                'feeds.created_at' ,
                'feeds.updated_at'
            );

        if ( '' !== $searchData ) {
            $query = $this->mapQuery( $searchData , $query );
        }
        return $query;
    }

    public function getSourceUrl($id) {
        $urlSearch = $this->feed->where('id', $id)->first();
        if ($urlSearch) {
            return $urlSearch->source_url;
        }
        else {
            return '';
        }
    }

    public function getActiveFeedNames () {
        return $this->feed->where( 'status' , 'Active'  )->pluck( 'name' )->toArray();
    }

    public function getActiveFeedShortNames () {
        return $this->feed->where( 'status' , 'Active'  )->pluck( 'short_name' )->toArray();
    }

    public function getFeedIdByName ( $name ) {
        return ( $record = $this->feed->where( 'name' , $name )->pluck( 'id' ) ) ? $record->pop() : null;
    }

    public function getFeedIdByShortName ( $name ) {
        return ( $record = $this->feed->where( 'short_name' , $name )->pluck( 'id' ) ) ? $record->pop() : null;
    }

    public function passwordExists ( $password ) {
        return $this->feed->where( 'password' , $password )->count() > 0;
    }

    public function getFeedIdFromPassword ( $password ) {
        $feed = $this->feed->where( 'password' , $password )->first();

        if ( is_null( $feed ) ) {
            return 0;
        }

        return $feed->id;
    }

    public function transformForMt1($startingId) {
        $attr = config('database.connections.attribution.database');
        $supp = config('database.connections.suppression.database');

        return $this->feed
            ->join('clients as c', 'feeds.client_id', '=', 'c.id')
            ->join("$attr.attribution_levels as al", 'feeds.id', '=', 'al.feed_id')
            ->selectRaw("feeds.id as tracking_id,
                feeds.id as user_id,
                c.id as clientStatsGroupingID,
                c.name as first_name,
                c.address,
                c.address2,
                c.city,
                c.state,
                c.zip,
                c.email_address as email_addr,
                c.phone,
                feeds.updated_at as overall_updated,
                feeds.name as password,
                feeds.name as username,
                feeds.name as ftp_user,
                feeds.password as rt_pw,
                feeds.password as ftp_pw,
                IF(feeds.party = 3, 'Y', 'N') as OrangeClient,
                al.level as AttributeLevel,
                IF(feeds.party = 3, 'Y', 'N') as CheckGlobalSuppression,
                feeds.source_url as clientRecordSourceURL,
                feeds.country_id as countryID,
                'TBD' as upload_freq,
                IF(feeds.status = 'Active', 'A', 'D') as status,
                13 as cakeAffiliateID")
            ->where('feeds.id', '>=', $startingId);
    }

    private function mapQuery( $searchData , $query ) {
        $searchData = json_decode($searchData, true);

        if ( isset( $searchData['client_name'] ) ) {
            $query->where('clients.name' , 'LIKE' , $searchData['client_name'].'%' );
        }

        if ( isset( $searchData['feed_name'] ) ) {
            $query->where('feeds.name' , 'LIKE' , $searchData['feed_name'].'%' );
        }

        if ( isset( $searchData['feed_short_name'] ) ) {
            $query->where('feeds.short_name' , 'LIKE' , $searchData['feed_short_name'].'%' );
        }

        if ( isset($searchData['status']) ) {
            $query = $query->where( 'feeds.status' , $searchData['status'] );
        }

        if ( isset($searchData['feed_vertical_id']) ) {
            $query = $query->where( 'cake_verticals.id' , (int)$searchData['feed_vertical_id'] );
        }

        if ( isset($searchData['country']) ) {
            $query = $query->where( 'countries.id' , (int)$searchData['country'] );
        }

        if ( isset($searchData['feed_type_id']) ) {
            $query = $query->where( 'feed_types.id' , (int)$searchData['feed_type_id'] );
        }

        if ( isset($searchData['party']) ) {
            $query = $query->where( 'feeds.party' , (int)$searchData['party'] );
        }

        if ( isset( $searchData['source_url'] ) ) {
            $query->where('feeds.source_url' , 'LIKE' , '%'.$searchData['source_url'].'%' );
        }

        if ( isset( $searchData['email_oversight_enabled'] ) && $searchData['email_oversight_enabled'] == 1 ) {
            $query->where('email_oversight_feeds.list_id' , '<>' , 'NULL' );
        }

         return $query;
    }

    public function extractForS3Upload($stopPoint) {
        return $this->feed->whereRaw("id > $stopPoint");
    }

    public function extractAllForS3() {
        return $this->feed;
    }

    public function specialExtract($data) {}


    public function mapForS3Upload($row) {
        $pdo = DB::connection('redshift')->getPdo();
        return $pdo->quote($row->id) . ','
            . $pdo->quote($row->client_id) . ','
            . $pdo->quote($row->name) . ','
            . $pdo->quote($row->party) . ','
            . $pdo->quote($row->short_name) . ','
            . $pdo->quote($row->status) . ','
            . $pdo->quote($row->vertical_id) . ','
            . $pdo->quote($row->frequency) . ','
            . $pdo->quote($row->type_id) . ','
            . $pdo->quote($row->country_id) . ','
            . $pdo->quote($row->source_url) . ','
            . $pdo->quote($row->suppression_list_id) . ','
            . $pdo->quote($row->created_at) . ','
            . $pdo->quote($row->updated_at);
    }

    public function getConnection() {
        return $this->feed->getConnectionName();
    }

    public function getNewUsersForToday () {
        //Import does not have created dates. Changing this to attempt to create ftp users for all feeds.
        return $this->feed->get();
    }   

    public function updatePassword ( $shortName , $password ) {
        $currentFeed = $this->feed->where( 'short_name' , $shortName )->first();
        $currentFeed->password = $password;
        $currentFeed->save();
    }

    public function getCount() {
        return $this->feed->count();
    }

    public function getFeedCountry ( $feedId ) {
        return $this->feed->where( 'id' , $feedId )->pluck( 'country_id'  )->first();
    }

    public function getCountryFeedMap () {
        $map = [];
        $feeds = $this->getFeeds();

        foreach ($feeds as $feed) {

            $map[$feed->country_id][] = $feed->id;
        }

        return $map;
    }

    public function getPartyFeedMap () {
        $map = [];
        $feeds = $this->getFeeds();

        foreach ($feeds as $feed) {

            $map[$feed->party][] = $feed->id;
        }

        return $map;
    }

    public function getFeedNameFromId ( $id ) {
        $feedResult = $this->feed->where( 'id' , $id )->first();

        if ( count( $feedResult ) !== 1 ) {
            return null;
        }

        return $feedResult->name;
    }

    public function getPartyFromId ( $id ) {
        $party = $this->feed->where( 'id' , $id )->pluck( 'party' )->first();

        if ( count( $party ) !== 1 ) {
            return 0;
        }

        return $party;
    } 
    
    public function prepareTableForSync() {}
}
