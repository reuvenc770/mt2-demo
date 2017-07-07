<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;

use \App\Jobs\DownloadSuppressionFromESP;


class DownloadSuppressionFromESPJobTestOff extends TestCase
{

    protected function setUp(){
       parent::setUp();
       $this->markTestSkipped('off');
    }

    public function testJobOutput()
    {

        $apiName = "Campaigner";
        $espAccountId = "2";
        $lookback = 1;
        $tracking = str_random(16);

        dispatch(new DownloadSuppressionFromESP($apiName, $espAccountId, $lookback, $tracking));

        var_dump(JobTracking::getJobByTracking($tracking));


    }

}