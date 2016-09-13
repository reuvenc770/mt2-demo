<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionLevel;
use App\Repositories\AttributionModelRepo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use DB;

class AttributionLevelRepo {
    protected $levels;

    public function __construct ( $attributionModelId = null ) {
        if ( !is_null( $attributionModelId ) && is_numeric( $attributionModelId ) ) {
            $this->levels = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $attributionModelId );
        } else {
            $this->levels = new AttributionLevel();
        }
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
        $mt1Levels = DB::connection( 'mt1_data' )->table( 'user' )
                    ->select( 'user_id as feedId' , 'AttributeLevel as level' )
                    ->where( [
                        [ 'status' => 'A' ] ,
                        [ 'OrangeClient' => 'Y' ] ,
                        [ 'AttributeLevel' , '<>' , 255 ]
                    ] )->get();

        $liveModelId = AttributionModelRepo::getLiveModelId();

        if ( !is_null( $liveModelId ) ) {
            DB::connection( 'attribution' )->table( AttributionLevel::BASE_TABLE_NAME . $liveModelId )->truncate();
        }

        foreach ( $mt1Levels as $current ) {
            $feed = $this->levels->find( $current->feedId );

            if ( !$feed->isEmpty() ) {
                $feed->level = $current->level;
                $feed->save();
            } else {
                $newFeed = new AttributionLevel();
                $newFeed->feed_id = $current->feedId;
                $newFeed->level = $current->level;
                $newFeed->save();
            }

            if ( !is_null( $liveModelId ) ) {
                $modelFeed = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $liveModelId );
                $modelFeed->feed_id = $current->feedId;
                $modelFeed->level = $current->level;
                $modelFeed->save();
            }
        }

        return true;
    }
}
