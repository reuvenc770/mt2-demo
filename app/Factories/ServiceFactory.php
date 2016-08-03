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

        $scheduleModel = "App\\Models\\AttributionExpirationSchedule";
        $scheduleRepo = "App\\Repositories\\AttributionScheduleRepo";

        $assignmentModel = "App\\Models\\EmailClientAssignment";
        $historyModel = "App\\Models\\EmailClientAssignmentHistory";
        $assignmentRepo = "App\\Repositories\\EmailClientAssignmentRepo";

        $emailClientInstanceModel = "App\\Models\\EmailClientInstance";
        $emailClientInstanceRepo = "App\\Repositories\\EmailClientInstanceRepo";

        $attributionLevelRepo = "App\\Repositories\\AttributionLevelRepo";

        $etlPickupModel = "App\\Models\\EtlPickup";
        $etlPickupRepo = "App\\Repositories\\EtlPickupRepo";

        $truth = new $truthRepo(new $truthModel());
        $schedule = new $scheduleRepo(new $scheduleModel());
        $assignment = new $assignmentRepo(new $assignmentModel(), new $historyModel());
        $instance = new $emailClientInstanceRepo(new $emailClientInstanceModel());
        $etlPickup = new $etlPickupRepo(new $etlPickupModel());

        // when left empty, this instantiates the currently-selected model
        $level = 'none' === $modelId ? new $attributionLevelRepo() : new $attributionLevelRepo($modelId); 

        $service = "App\\Services\\AttributionService";

        return new $service($truth, $schedule, $assignment, $instance, $level, $etlPickup);
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
