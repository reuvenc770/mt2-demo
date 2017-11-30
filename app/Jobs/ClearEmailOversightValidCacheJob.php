<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;

class ClearEmailOversightValidCacheJob extends MonitoredJob
{
    protected $jobName = "ClearEmailOversightValidCacheJob";
    protected $minimumAgeInDays;
    protected $tracking;

    public function __construct ( $minimumAgeInDays , $tracking , $runtimeThreshold="5m" ) {
        $this->minimumAgeInDays = $minimumAgeInDays;
        $this->tracking = $tracking;
        $this->jobName .= $tracking;

        parent::__construct(
            $this->jobName , 
            $runtimeThreshold ,
            $tracking
        );
    }

    public function handleJob () {
        $cache = \App::make( \App\Repositories\EmailOversightValidCacheRepo::class );

        return $cache->clearPriorToDate( \Carbon\Carbon::now()->subDays( $this->minimumAgeInDays )->toDateString() );
    }
}
