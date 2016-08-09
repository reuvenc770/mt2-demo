<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Repositories;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;

class RecordReportRepoIntegrationTest extends TestCase {
    use DatabaseMigrations;

    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = \App::make( \App\Repositories\Attribution\RecordReportRepo::class );
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    public function test_singleInsert () {
        $email = factory( \App\Models\Email::class )->create();

        $this->sut->insertAction( [
            "email_id" => $email->id , 
            "deploy_id" => 1 ,
            "offer_id" => 0 ,
            "delivered" => 0 ,
            "opened" => 0 ,
            "clicked" => 0 , 
            "converted" => 1 ,
            "bounced" => 0 ,
            "unsubbed" => 0 ,
            "revenue" => 1.00 ,
            "date" => \Carbon\Carbon::now()->toDateString()
        ] );

        $this->assertEquals( 1 , \App\Models\AttributionRecordReport::all()->count() );
    }

    public function test_accumulativeInsert () {
        $email = factory( \App\Models\Email::class )->create();

        $this->sut->insertAction( [
            "email_id" => $email->id , 
            "deploy_id" => 1 ,
            "offer_id" => 0 ,
            "delivered" => 0 ,
            "opened" => 0 ,
            "clicked" => 0 , 
            "converted" => 1 ,
            "bounced" => 0 ,
            "unsubbed" => 0 ,
            "revenue" => 1.00 ,
            "date" => \Carbon\Carbon::now()->toDateString()
        ] );

        $this->sut->insertAction( [
            "email_id" => $email->id , 
            "deploy_id" => 1 ,
            "offer_id" => 0 ,
            "delivered" => 1 ,
            "opened" => 0 ,
            "clicked" => 0 , 
            "converted" => 0 ,
            "bounced" => 0 ,
            "unsubbed" => 0 ,
            "revenue" => 0 ,
            "date" => \Carbon\Carbon::now()->toDateString()
        ] );

        $record = \App\Models\AttributionRecordReport::all()->pop();

        $this->assertEquals( 1 , $record[ 'delivered' ] );
        $this->assertEquals( 1 , $record[ 'converted' ] );
        $this->assertEquals( 1.00 , $record[ 'revenue' ] );
    }
}
