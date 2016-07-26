<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\EspInternalIdDeployIdMap;
use DB;
use App\Facades\JobTracking;
use App\Models\JobEntry;

class MapEspInternalIdToDeployId extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $jobName = 'MapEspInternalIdToDeployId';
    private $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking )
    {
        $this->tracking = $tracking;
        JobTracking::startAggregationJob( $this->jobName , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        JobTracking::changeJobState( JobEntry::RUNNING , $this->tracking );

        $schema = config('database.connections.reporting_data.database');

        $missingDeploys = DB::select( "
            SELECT
                sr.esp_internal_id , 
                sr.m_deploy_id
            FROM
                {$schema}.esp_internal_id_deploy_id_maps m
                right outer join {$schema}.standard_reports sr ON( m.esp_internal_id = sr.esp_internal_id )
            WHERE
                sr.m_deploy_id > 0
                AND m.esp_internal_id IS NULL" );

        foreach ( $missingDeploys as $deploy ) {
            $mapping = new EspInternalIdDeployIdMap();
            $mapping->esp_internal_id = $deploy->esp_internal_id;
            $mapping->deploy_id = $deploy->m_deploy_id;
            $mapping->save();
        }

        JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
    }

    public function failed () {
        JobTracking::changeJobState( JobEntry::FAILED , $this->tracking );
    }
}
