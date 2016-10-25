<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\FeedGroup;
use App\Models\FeedGroupFeed;

class FeedGroupRepo {
    protected $feedGroups;

    public function __construct ( FeedGroup $feedGroups ) {
        $this->feedGroups = $feedGroups;
    }

    public function getModel () {
        return $this->feedGroups;
    }

    public function updateOrCreate ( $data ) {
        $record = $this->feedGroups->updateOrCreate(
            [ 'id' => isset( $data[ 'id' ] ) ? $data[ 'id' ] : null ] ,
            [ 'name' => $data[ 'name' ] ]
        );

        return $record->id;
    }

    public function updateFeeds ( $data ) {
        $currentGroup = $this->feedGroups->find( $data[ 'id' ] );

        $currentGroup->feedGroupFeeds()->delete();

        foreach ( $data[ 'feeds' ] as $feedId ) {
            $currentGroup->feedGroupFeeds()->create( [
                'feedgroup_id' => $data[ 'id' ] ,
                'feed_id' => $feedId
            ] );
        }
    }

    public function getName ( $id ) {
        return $this->feedGroups->find( $id )->name;
    }

    public function getFeeds ( $id ) {
        return $this->feedGroups->find( $id )->feeds()->pluck( 'id' );
    }
}
