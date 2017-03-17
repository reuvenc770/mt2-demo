<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Storage;
use Carbon\Carbon;
use League\Csv\Writer;

class TestFeedFileGenerator extends Command
{
    const DEFAULT_FILE_SIZE = 10000;

    protected $faker;
    protected $feedService;

    protected $fieldFakerMap = [
        'email_address' => 'email' ,
        'source_url' => 'url' ,
        'ip' => 'ipv4' ,
        'first_name' => 'firstName' ,
        'last_name' => 'lastName' ,
        'address' => 'streetAddress' ,
        'address2' => 'streetSuffix' ,
        'city' => 'city' ,
        'state' => 'state' ,
        'zip' => 'postcode' ,
        'country' => 'country'
    ]; 

    protected $feeds = [];
    protected $fileSizes = [];
    protected $invalidRate = 0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:generateFeedFile {--f|feedIds= : Comma-delimited list of feed IDs to generate files for. } {--s|fileSizes= : Comma-delimited list of record counts to generate per file. This is randomly chosen during creation. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will generate files for feeds.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->processOptions();

        $this->faker = \Faker\Factory::create();
        $this->feedService = \App::make( \App\Services\FeedService::class );

        foreach ( $this->feeds as $currentFeed ) {
            $fields = $this->feedService->getFeedFields( $currentFeed->id , true );

            $this->generateFile( $currentFeed->short_name , $fields );
        }
    }

    protected function processOptions () {
        if ( $this->option( 'feedIds' ) ) {
            $this->feeds = \App\Models\Feed::whereIn( 'id' , explode( ',' , $this->option( 'feedIds' ) ) )->get();
        }

        if ( $this->option( 'fileSizes' ) ) {
            $this->fileSizes = explode( ',' , $this->option( 'fileSizes' ) );
        }
    }

    protected function generateFile ( $feedName , $fields ) {
        $writer = Writer::createFromFileObject( new \SplTempFileObject() );

        $fileSize = $this->getFileSize();

        for ( $count = 1 ; $count <= $fileSize ; $count++ ) {
            $record = [];

            foreach ( $fields  as $currentField ) {
                if ( array_key_exists( $currentField , $this->fieldFakerMap ) ) {
                    $fakerField = $this->fieldFakerMap[ $currentField ];

                    array_push( $record , $this->faker->$fakerField );
                } elseif ( method_exists( $this , $this->toCamelCase( $currentField ) ) ) {
                    array_push( $record , call_user_func( [ $this , $this->toCamelCase( $currentField ) ] ) );
                } else {
                    array_push( $record , '' );
                }
            }

            $writer->insertOne( $record );
        }

        $this->saveLocally( $feedName , $writer );
    }

    protected function getFileSize () {
        if ( count( $this->fileSizes ) == 0 ) {
            return self::DEFAULT_FILE_SIZE; 
        }

        $sizes = $this->fileSizes;

        shuffle( $sizes );

        return array_pop( $sizes );
    }

    protected function saveLocally ( $feedName , Writer $writer ) {
        $filePath = 'testFeedFiles/' . $feedName . '/' . Carbon::now()->toDateTimeString() . '.csv';
        
        Storage::disk("local")->put( $filePath , $writer->__toString() );
    }

    protected function toCamelCase ( $text ) {
        $str = str_replace( '_' , ' ' , $text );
        $str = str_replace( '-' , ' ' , $str );
        $str = str_replace( ' ' , '' , ucwords( $str ) );

        $str[0] = strtolower($str[0]);

        return $str;
    }

    protected function hasInsurance () {
        return rand( 0 , 1 );
    }

    protected function hasMedicaid () {
        return rand( 0 , 1 );
    }

    protected function relationshipStatus () {
        $statuses = [ 'single' , 'married' , 'open' ];

        shuffle( $statuses );

        return array_pop( $statuses );
    }

    protected function genderPreference () {
        $genders = [ 'male' , 'female' , 'transgender' ];

        shuffle( $genders );

        return array_pop( $genders );
    }

    protected function levelOfEducation () {
        $educationLevels = [ 'high school' , 'some college' , 'associates' , 'bachelors' , 'masters' , 'phd' ];

        shuffle( $educationLevels );

        return array_pop( $educationLevels );
    }

    protected function needFinancing () {
        return rand( 0 , 1 );
    }

    protected function captureDate () {
        return Carbon::now()->subDays( rand( 1 , 180 ) )->toDateString();
    }

    protected function dob () {
        $baseDate = Carbon::now()->subYears( rand( 18 , 40 ) );

        ( rand( 0 , 1 ) ? $baseDate->addDays( rand( 1 , 365 ) ) : $baseDate->subDays( rand( 1 , 365 ) ) );

        return $baseDate->toDateString();
    }

    protected function gender () {
        return ( rand( 0 , 1 ) ? 'M' : 'F' );
    }
}
