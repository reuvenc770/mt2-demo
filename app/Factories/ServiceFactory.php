<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/23/16
 * Time: 9:42 AM
 */

namespace App\Factories;
use App;
use Aws;

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


    public static function createAttributionBatchService() {
        return App::make(\App\Services\AttributionBatchService::class);
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


    public static function createAwsExportService($entity) {
        if ('RecordData' === $entity) {
            // Keeping the redshift schema the same
            // but there's been a big schema change on our side
            $jobRepo =  App::make("App\\Repositories\\EmailAttributableFeedLatestDataRepo");
        }
        else {
            $jobRepo =  App::make("App\\Repositories\\{$entity}Repo");
        }

        $config = [
            'region' => config('aws.region'),
            'version' => config('aws.version'),
            'credentials' => [
                'key' => config('aws.s3.key'),
                'secret' => config('aws.s3.secret')
            ]
        ];

        $sdk = new Aws\Sdk($config);
        $s3Client = $sdk->createS3();

        $redshiftRepo = App::make("App\\Repositories\\RedshiftRepositories\\{$entity}Repo");
        $pickupRepo = App::make(\App\Repositories\EtlPickupRepo::class);

        if (in_array($entity, ['Feed', 'Email', 'EmailDomain', 'DomainGroup', 'SuppressionGlobalOrange', 'SuppressionListSuppression', 'Client'])) {
            $func = function($row) { return $row->id; };
        }
        else {
            // ListProfileFlatTable, RecordData, EmailFeedAssignments
            $func = function($row) {
                $updatedAt = preg_replace('/\s|\-|:/', '', $row->updated_at);
                return (int)$updatedAt; 
            };
        }

        return new \App\Services\S3RedshiftExportService($jobRepo, $s3Client, $redshiftRepo, $pickupRepo, $entity, $func);
    }

    public static function createDataValidationService($source, $type) {
        $checkRepos = [];
        $checkClasses = [];
        $canonicalDataRepo = null;

        if ('emails' === $source) {
            $canonicalDataRepo = App::make(\App\Repositories\EmailRepo::class);
            $checkClasses = [
                'AttributionRecordTruthRepo',
                'ThirdPartyEmailStatusRepo'
            ];

            $model = App::make(\App\Models\AttributionExpirationSchedule::class);
            $repo = new \App\Repositories\AttributionScheduleRepo($model);
            $checkRepos[] = $repo;

        }
        elseif ('emailFeedInstances' === $source && 'exists' === $type) {
            // Ensures that email-feed rows are set up
            $canonicalDataRepo = App::make(\App\Repositories\EmailFeedInstanceRepo::class);
            $checkClasses = [
                'EmailAttributableFeedLatestDataRepo',
                'FirstPartyRecordDataRepo'
            ];
        }
        elseif('emailFeedAssignments' === $source && 'value' === $type) {
            $canonicalDataRepo = App::make(\App\Repositories\EmailFeedAssignmentRepo::class);
            $checkClasses = ['EmailAttributableFeedLatestDataRepo'];
        }


        foreach($checkClasses as $class) {
            $checkRepos[] = App::make("App\\Repositories\\$class");
        }

        return new \App\Services\DataValidationService($canonicalDataRepo, App::make(\App\Repositories\EtlPickupRepo::class), $checkRepos);
    }


    public static function createRedshiftValidator($entity) {
        if ('RecordData' === $entity) {
            // Keeping the redshift schema the same
            // but there's been a big schema change on our side
            $cmpRepo =  App::make("App\\Repositories\\EmailAttributableFeedLatestDataRepo");
        }
        else {
            $cmpRepo =  App::make("App\\Repositories\\{$entity}Repo");
        }

        $redshiftRepo = App::make("App\\Repositories\\RedshiftRepositories\\{$entity}Repo");

        $testingStrategy = '';

        if ('SuppressionGlobalOrange' === $entity || 'SuppressionListSuppression' === $entity) {
            $testingStrategy = $entity;
        }
        elseif (in_array($entity, ['Client', 'DomainGroup', 'EmailDomain', 'Feed'])) {
            // Small enough that we can be exact and check row-by-row
            $testingStrategy = 'Exact';
        }
        else {
            // More sophisticated and per-entity testing
            $testingStrategy = $entity;
        }

        $testingStrategy = "App\\Services\\RedshiftDataValidationStrategies\\{$testingStrategy}RedshiftDataValidation";

        return new $testingStrategy($cmpRepo, $redshiftRepo);
    }
}
