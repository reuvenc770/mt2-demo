<?php

namespace App\Repositories;

use App\Models\Feed;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class FeedRepo {

    private $feed;

    public function __construct(Feed $feed) {
        $this->feed = $feed;
    }

    public function getFeeds () {
        return $this->feed->orderBy('short_name')->get();
    }

    public function getAllFeedsArray() {
        return $this->feed->orderBy('short_name')->get()->toArray();
    }

    public function fetch($id) {
        return $this->feed->find($id);
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
        $this->feed->updateOrCreate(['id' => $id], $data);
    }

    public function getModel() {
        return $this->feed
            ->join( 'clients' , 'feeds.client_id' , '=' , 'clients.id' )
            ->leftJoin( 'cake_verticals' , 'feeds.vertical_id' , '=' , 'cake_verticals.id' )
            ->leftJoin( 'feed_types' , 'feeds.type_id' , '=' , 'feed_types.id' )
            ->leftJoin( 'countries' , 'feeds.country_id' , '=' , 'countries.id' )
            ->select(
                'feeds.id' ,
                'clients.name as clientName' ,
                'feeds.party' ,
                'feeds.short_name' ,
                'feeds.status' ,
                'cake_verticals.name as feedVertical',
                'feeds.frequency' ,
                'feed_types.name as feedType' ,
                'countries.abbr as country' ,
                'feeds.source_url' ,
                'feeds.created_at' ,
                'feeds.updated_at'
            );
    }

    public function getActiveFeedNames () {
        return $this->feed->where( 'status' , 'Active'  )->pluck( 'name' )->toArray();
    }

    public function getFeedIdByName ( $name ) {
        return ( $record = $this->feed->where( 'name' , $name )->pluck( 'id' ) ) ? $record->pop() : null;
    }
}
