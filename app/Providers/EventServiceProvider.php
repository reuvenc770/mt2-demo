<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\RawReportDataWasInserted' => [
            'App\Listeners\CreateStandardReport',
        ],
        'App\Events\AttributionFileUploaded' => [
            'App\Listeners\AttributionMassUpdate',
        ],
        'App\Events\BulkSuppressionFileWasUploaded' => [
            'App\Listeners\ParseBulkSuppressionFiles',
        ],
        'App\Events\DeploysMissingDataFound' => [
            'App\Listeners\RerunDeployStats',
        ],
        'App\Events\NewRecords' => [
            'App\Listeners\NewRecordResolver'
        ],
        'App\Events\NewActions' => [
            'App\Listeners\NewActionResolver'
        ],
        'App\Events\AttributionCompleted' => [
            'App\Listeners\RerunAttributionAggregator'
        ],
        'App\Events\NewDeployWasCreated' => [
            'App\Listeners\DeployDataCleanseListener'
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
