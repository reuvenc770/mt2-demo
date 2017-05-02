<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;


class DownloadSuppressionFromESPCliTest extends TestCase
{

    protected function setUp(){
       parent::setUp();
       $this->markTestSkipped('off');
    }

    public function testCommandOutput()
    {

        $esp = "Campaigner";

        Artisan::call('suppression:downloadESP', [
            'espName' => $esp,
            'lookBack' => '3',
        ]);



        //$this->expectOutputRegex("/$esp/");
        $this->getActualOutput();
        print Artisan::output();
        var_dump(JobTracking::getJobByTracking('mFWEP3U7lLtZPvJe'));

    }

}