<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use DB;
use App\Repositories\AttributionLevelRepo;
use App\Repositories\AttributionFeedReportRepo;
use App\Repositories\EmailFeedAssignmentRepo;
use App\Models\AttributionLevel;
use App\Models\AttributionModel;
use App\Models\Feed;
use Maknz\Slack\Facades\Slack;
use Carbon\Carbon;
use Log;

class AttributionModelRepo {
    CONST SLACK_TARGET_SUBJECT = '#mt2-dev-failed-jobs';

    protected $models;
    protected $feeds;

    public function __construct ( AttributionModel $models , Feed $feeds ) {
        $this->models = $models;
        $this->feeds = $feeds;
    }

    public function getModel () {
        return $this->models;
    }

    public function create ( $name , $levels = null ) {
        $response = [ "status" => false ];

        #creates new AttributionModel record.
        $newModel = new AttributionModel();
        $newModel->name = $name;
        $newModel->save();
            
        #generates temp level table
        AttributionLevelRepo::generateTempTable( $newModel->id );
        AttributionFeedReportRepo::generateTempTable( $newModel->id );
        EmailFeedAssignmentRepo::generateTempTable( $newModel->id );

        if ( !is_null( $levels ) ) {
            foreach ( $levels as $currentLevel ) {
                $tempLevelModel = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $newModel->id );
                $tempLevelModel->feed_id = $currentLevel[ 'id' ];
                $tempLevelModel->level = $currentLevel[ 'level' ];
                $tempLevelModel->save();

                unset( $tempLevelModel );
            }
        } 

        $response[ 'status' ] = true;

        return $response;
    }

    public function get ( $modelId ) {
        return $this->models->where( 'id' , $modelId )->get();
    }

    public function toggleLiveStatus ( $modelId , $isLive ) {
        #throws exception if $isLive is not a boolean
        
        #resets all models to paused(false)
        #sets the given model to live status
    }

    public function levels ( $modelId ) {
        $modelLevelTable = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $modelId );

        return $modelLevelTable->get();
    }

    public function getLevel ( $feedId , $modelId = null ) {
        $levelTable = null;

        if ( !is_null( $modelId ) ) {
            $levelTable = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $modelId );
        } else {
            $levelTable = new AttributionLevel();
        } 

        $result = $levelTable
             ->select( 'level' )
             ->where( 'feed_id' , $feedId )
             ->first();

        if ( !$result ) {
            return 255; // assume this to be the negative state
        }
        else {
            return $result->level;
        }
    }

    #sort by level
    public function getModelFeeds ( $modelId ) {
        $schema = config('database.connections.mysql.database');
        return DB::connection( 'attribution' )
            ->table( AttributionLevel::BASE_TABLE_NAME . $modelId . ' AS al' )
            ->select( 'al.feed_id as id' , 'f.name as name' )
            ->leftJoin( "$schema.feeds AS f" , 'f.id' , '=' , 'al.feed_id' )
            ->orderBy( 'al.level' )
            ->get();
    }

    public function copyLevels ( $destinationModelId , $templateModelId , $copyToLive = false , $newLevelList = [] ) {
        $currentModelLevel = ( $copyToLive ? new AttributionLevel() : new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $destinationModelId ) );
        $currentLevels = $currentModelLevel->get()->keyBy( 'level' )->toArray();

        if ( count( $newLevelList ) > 0 ) {
            $templateLevels = $newLevelList;
        } else {
            $templateModelLevel = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $templateModelId );
            $templateLevels = $templateModelLevel->get()->keyBy( 'level' )->toArray();
        }

        $currentDatetime = Carbon::now()->toDateTimeString();

        foreach ( $currentLevels as $level => $details ) {
            if ( $details[ 'feed_id' ] == $templateLevels[ $level ][ 'feed_id' ] ) {
                $templateLevels[ $level ][ 'created_at' ] = $details[ 'created_at' ];
                $templateLevels[ $level ][ 'updated_at' ] = $currentDatetime;
            } else {
                $templateLevels[ $level ][ 'created_at' ] = $currentDatetime; 
                $templateLevels[ $level ][ 'updated_at' ] = $currentDatetime; 
            }
        }

        if ( $copyToLive ) {
            $currentModelLevel->truncate();
        } else {
            DB::connection( 'attribution' )->table( AttributionLevel::BASE_TABLE_NAME . $destinationModelId )->truncate();
        }

        foreach ( $templateLevels as $item ) {
            $newLevel = ( $copyToLive ? new AttributionLevel() : new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $destinationModelId ) );

            $newLevel->feed_id = $item[ 'feed_id' ];
            $newLevel->level = $item[ 'level' ];
            $newLevel->created_at = $item[ 'created_at' ];
            $newLevel->updated_at = $item[ 'updated_at' ];
            $newLevel->save();

            unset( $newLevel );
        }

        return true;
    }

    static public function getLiveModelId () {
        $levelResult = DB::connection( 'attribution' )->table( 'attribution_models' )->select( 'id' )->where( 'live' , '1' )->get();

        if ( count( $levelResult ) <= 0 ) {
            return null;
        }

        return $levelResult[ 0 ]->id;
    }

    public function setLive ( $modelId ) {
        try {
            DB::connection( 'attribution' )->table( 'attribution_models' )
                ->update( [ 'live' => 0 ] );

            DB::connection( 'attribution' )->table( 'attribution_models' )
                ->where( 'id' , $modelId )
                ->update( [ 'live' => 1 ] );

            $this->copyLevels( 0 , $modelId , true );

            return true;
        } catch ( \Exception $e ) {
            Slack::to( self::SLACK_TARGET_SUBJECT )->send( "Failed to set Model {$modelId} live.\n\n" . $e->getMessage() );

            return false;
        }
    }

    public function updateModel ( $currentModelId , $currentModelName , $levels ) {
        $currentModel = $this->models->find( $currentModelId );
        $currentModel->name = $currentModelName;
        $currentModel->save();

        $newLevels = [];
        $currentDateTime = Carbon::now()->toDateTimeString();

        foreach ( $levels as $index => $current ) {
            $currentLevelNumber = $index + 1;
            $newLevels[ $currentLevelNumber ][ 'feed_id' ] = $current[ 'id' ];
            $newLevels[ $currentLevelNumber ][ 'level' ] = $currentLevelNumber;
            $newLevels[ $currentLevelNumber ][ 'created_at' ] = $currentDateTime;
            $newLevels[ $currentLevelNumber ][ 'updated_at' ] = $currentDateTime;
        }

        $this->copyLevels( $currentModelId , 0 , false , $newLevels );

        return true;
    }

    public function syncModelsWithNewFeeds () {
        $feedIds = $this->feeds->pluck( 'id' )->all();

        foreach ( $this->models->pluck( 'id' )->all() as $modelId ) {
            $modelLevels = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $modelId );
            $modelFeedIds = $modelLevels->pluck( 'feed_id' )->all();

            $missingFeeds = array_diff( $feedIds , $modelFeedIds );

            sort( $missingFeeds );

            $maxLevel = $modelLevels->orderBy( 'level' , 'desc' )->take( 1 )->pluck( 'level' )->all();

            $currentLevel = 1;
            if ( count( $maxLevel ) > 0 ) {
                $currentLevel += array_pop( $maxLevel );
            }

            foreach ( $missingFeeds as $feedId ) {
                $newLevel = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $modelId );
                $newLevel->feed_id = $feedId;
                $newLevel->level = $currentLevel;
                $newLevel->active = 1;
                $newLevel->save();

                $currentLevel++;
            }
        }

        $this->setLive( self::getLiveModelId() );
    }

    public function setProcessingFlag ( $modelId , $running ) {
        $currentModel = $this->models->find( $modelId );
        $currentModel->processing = ( $running ? 1 : 0 );
        $currentModel->update();
    }

    public function transientRecords ( $modelId ) {
        #returns the current transient IDs
    }

    public function getNonliveModels () {
        return $this->models->select( 'id' , 'name' )->where( 'live' , '=' , 0 )->get();
    }
}
