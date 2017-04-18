<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionLevel;
use App\Models\MT1Models\User as MT1Feed;
use App\Repositories\AttributionModelRepo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use DB;

class AttributionLevelRepo {
    protected $levels;
    protected $modelRepo;
    protected $mt1Feeds;

    public function __construct ( $attributionModelId = null , AttributionModelRepo $modelRepo , MT1Feed $mt1Feeds ) {
        if ( !is_null( $attributionModelId ) && is_numeric( $attributionModelId ) ) {
            $this->levels = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $attributionModelId );
        } else {
            $this->levels = new AttributionLevel();
        }

        $this->modelRepo = $modelRepo;
        $this->mt1Feeds = $mt1Feeds;
    }

    public function setLevel ( $feedId , $level ) {
        #insert or update given feed and level.
    }

    public function getLevel ( $feedId ) {
        $result = $this->levels
             ->select('level')
             ->where('feed_id', $feedId)
             ->first();

        if (!$result) {
            return 255; // assume this to be the negative state
        }
        else {
            return $result->level;
        }
    }

    public function getAllLevels () {
        #returns all levels
    }

    public function toggleActiveStatus ( $feedId , $isActive ) {
        #sets active filed for the given feed.
    }

    static public function generateTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->create( AttributionLevel::BASE_TABLE_NAME . $modelId , function (Blueprint $table) {
            $table->integer( 'feed_id' )->unsigned();
            $table->integer( 'level' )->unsigned();
            $table->boolean( 'active' )->default( true );
            $table->timestamps();

            $table->primary( 'feed_id' );
            $table->index( [ 'feed_id' , 'level' ] );
        });
    }

    static public function dropTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->drop( AttributionLevel::BASE_TABLE_NAME . $modelId );
    }

    public function getLastUpdate() {
        $result = $this->levels
             ->select(DB::raw("MAX(updated_at) AS last_update"))
             ->first();

        if ($result) {
            return $result->last_update;
        }
        else {
            // No last updated. Shouldn't happen. Return today.
            return strftime('%Y-%m-%d 00:00:00');
        }
    }

    public function syncLevelsWithMT1 () {
        $mt1Levels = $this->mt1Feeds
                        ->select( 'user_id as feed_id' , 'AttributeLevel as level' , \DB::raw( 'NOW() as `created_at`' ) , \DB::raw( 'NOW() as `updated_at`' ) )
                        ->where( [
                            [ 'status' , 'A' ] ,
                            [ 'OrangeClient' , 'Y' ] ,
                            [ 'AttributeLevel' , '<>' , 255 ]
                        ] )->get()->keyBy( 'level' )->toArray();

        $liveModelId = $this->modelRepo->getLiveModelId();

        $this->modelRepo->copyLevels( $liveModelId , 0 , false , $mt1Levels );

        $this->modelRepo->setLive( $liveModelId );

        return true;
    }

    public function updateFeedLevel ( $feedId , $level , $modelId = null ) {
        if ( !is_null( $modelId ) ) {
            $feed = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $modelId );
        } else {
            $feed = new AttributionLevel();
        }

        $feed->feed_id = $feedId;
        $feed->level = $level;
        $feed->save();
    }

    public function removeFeed ( $modelId , $feedId ) {
        $this->levels->setTable( AttributionLevel::BASE_TABLE_NAME . $modelId ); 

        $currentLevelOrder = $this->levels->get();

        $newLevelOrder = $currentLevelOrder->reject( function ( $value , $key ) use ( &$feedId ) {
            return $value->feed_id == $feedId;
        } );

        $isLiveModel = ( $modelId == AttributionModelRepo::getLiveModelId() );

        DB::connection( 'attribution' )->table( AttributionLevel::BASE_TABLE_NAME . $modelId )->truncate();

        if ( $isLiveModel ) {
            DB::connection( 'attribution' )->table( AttributionLevel::LIVE_TABLE_NAME )->truncate();
        }

        $newLevelOrder->each( function ( $currentFeed , $key ) use ( $modelId , $isLiveModel ) {
            $this->updateFeedLevel( $currentFeed->feed_id , $key + 1 , $modelId );

            if ( $isLiveModel ) {
                $this->updateFeedLevel( $currentFeed->feed_id , $key + 1 );
            }
        } );
    }
}
