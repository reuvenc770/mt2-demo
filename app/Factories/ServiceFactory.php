<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/23/16
 * Time: 9:42 AM
 */

namespace App\Factories;
use App;

class ServiceFactory
{
    public static function createModelService($serviceName)
    {
        $formattedName = studly_case($serviceName);
        $servicePath =  "App\\Services\\{$formattedName}Service";
        $mt1path =  "App\\Services\\MT1Services\\{$formattedName}Service";
        if (class_exists($servicePath)) {
            return App::make($servicePath); //this just made everything so much easier...
        }
        elseif (class_exists($mt1path)) {
            return App::make($mt1path);
        } else {
            throw new \Exception("That Service does not exist");
        }
    }


    public static function createFilterService($modelName){
        $casedName = ucfirst($modelName);
        $formattedName = "App\\Models\\Attribution{$casedName}Schedule";

        if (class_exists($formattedName)) {
            $repo  = new App\Repositories\AttributionScheduleRepo(new $formattedName);
            return new App\Services\ScheduledFilterService($repo, $modelName);
        }
        else {
            throw new \Exception("That Service does not exist");
        }
    }


    public static function createAttributionService($modelId) {
        $truthModel = "App\\Models\\AttributionRecordTruth";
        $truthRepo = "App\\Repositories\\AttributionRecordTruthRepo";

        $attributionLevelRepo = "App\\Repositories\\AttributionLevelRepo";

        $etlPickupModel = "App\\Models\\EtlPickup";
        $etlPickupRepo = "App\\Repositories\\EtlPickupRepo";

        $truth = new $truthRepo(new $truthModel());
        $etlPickup = new $etlPickupRepo(new $etlPickupModel());

        // when left empty, this instantiates the currently-selected model
        if ( 'none' !== $modelId ) {
            $level = new $attributionLevelRepo($modelId); 
        } else {
            $level = new $attributionLevelRepo(); 
        }

        $service = "App\\Services\\AttributionService";

        return new $service($truth, $level, $etlPickup);
    }


    public static function createAttributionBatchService($modelId) {
        $truthModel = "App\\Models\\AttributionRecordTruth";
        $truthRepo = "App\\Repositories\\AttributionRecordTruthRepo";

        $scheduleModel = "App\\Models\\AttributionExpirationSchedule";
        $scheduleRepo = "App\\Repositories\\AttributionScheduleRepo";

        $assignmentModel = "App\\Models\\EmailFeedAssignment";
        $historyModel = "App\\Models\\EmailFeedAssignmentHistory";
        $assignmentRepo = "App\\Repositories\\EmailFeedAssignmentRepo";

        $emailFeedInstanceModel = "App\\Models\\EmailFeedInstance";
        $emailFeedInstanceRepo = "App\\Repositories\\EmailFeedInstanceRepo";

        $truth = new $truthRepo(new $truthModel());
        $schedule = new $scheduleRepo(new $scheduleModel());
        $assignment = new $assignmentRepo(new $assignmentModel(), new $historyModel());
        $instance = new $emailFeedInstanceRepo(new $emailFeedInstanceModel());

        // when left empty, this instantiates the currently-selected model
        if ( 'none' !== $modelId ) {
            $assignment->setLevelModel( $modelId );
        }

        $service = "App\\Services\\AttributionBatchService";

        return new $service($truth, $schedule, $assignment, $instance);
    }

    public static function createStandardReportService () {
        return new App\Services\StandardReportService( App::make( App\Repositories\StandardReportRepo::class ) );
    }

    public static function createAggregatorService ( $aggregatorName ) {
        $className = "\App\Services\Attribution\\" . $aggregatorName . "AggregatorService";

        if ( !class_exists( $className ) ) {
            throw new \Exception( "Aggregator Service {$aggregatorName} does not exist. Either enter an existing service or make a new one." );
        }

        return App::make( $className ); 
    }
}
