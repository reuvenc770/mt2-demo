<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;

class CpmDeploySnapshotJob extends MonitoredJob
{
    const INSERT_CHUNK = 50000;

    protected $jobName = 'CpmDeploySnapshotJob';
    protected $tracking;
    protected $deployId;

    protected $sqlStringList = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $deployId , $tracking , $runtimeThreshold="15m" )
    {
        $this->deployId = $deployId;
        $this->tracking = $tracking;
        $this->jobName .= '-' . $tracking;

        parent::__construct(
            $this->jobName , 
            $runtimeThreshold ,
            $tracking
        );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $this->service = \App::make( \App\Services\CpmDeploySnapshotService::class );

        $this->service->clearForDeploy( $this->deployId );

        $listProfileExports = $this->service->getListProfileExportsFromDeploy( $this->deployId );

        foreach ( $listProfileExports as $export ) {
            foreach ( $export->cursor() as $record ) {
                $this->buffer( [
                    'email_address' => $record->email_address, 
                    'feed_id' => $record->feed_id ,
                    'deploy_id' => $this->deployId
                ] );

                if ( $this->bufferFull() ) {
                    $this->massInsert();

                    $this->clearBuffer();
                }
            }
        }

        if ( $this->bufferNotEmpty() ) {
            $this->massInsert();
        }
    }

    protected function buffer ( $record ) {
        $this->sqlStringList []= $this->service->toSqlFormat( $record );
    }

    protected function clearBuffer () {
        $this->sqlStringList []= [];
    }

    protected function bufferNotEmpty () {
        return count( $this->sqlStringList ) > 0;
    }

    protected function bufferFull () {
        return count( $this->sqlStringList ) >= self::INSERT_CHUNK;
    }

    protected function massInsert () {
        return $this->service->massInsert( $this->sqlStringList );
    }
}
