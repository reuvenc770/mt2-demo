<?php
/**
 *
 */

namespace App\Services;

use App\Services\API\EmailDirectApi;
use App\Repositories\ReportRepo;
use App\Services\Interfaces\IAPIReportService;
use App\Services\Interfaces\IReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;

/**
 *
 */
class EmailDirectReportService extends EmailDirectApi implements IAPIReportService , IReportService {
    protected $reportRepo;

    public function __construct ( ReportRepo $reportRepo , $apiName , $accountNumber ) {
        parent::__construct( $apiName , $accountNumber );
        $this->reportRepo = $reportRepo;
    }

    public function retrieveReportStats ( $date ) {
        try {
            $reportStats = $this->sendApiRequest( array( 'date' => $date ) );
        } catch ( Exception $e ) {
            echo "\nException Found:\n";
            echo $e->getMessage();
        }

        if ( empty( $reportStats ) ) throw new Exception( 'Sent Campaign List empty.' );

        return $reportStats;
    }

    public function insertRawStats ( $data ) {
        foreach ( $data as $campaignData ) {
            try {
                $convertedRecord = $this->mapToRawReport( $campaignData );

                $this->reportRepo->insertStats( $this->getAccountName() , $convertedRecord );
            } catch ( Exception $e ) {
                echo "\nFailed to insert raw stats.\n" . $e->getMessage();
            }
        }

        Event::fire( new RawReportDataWasInserted( $this->getApiName() , $this->getAccountName() , $data ) );
    }

    public function mapToStandardReport ( $data ) {
        return array(
            "internal_id" => $data[ 'CampaignID' ] ,
            "account_name" => $this->getAccountName() ,
            "name" => $data[ 'Name' ] ,
            "subject" => $data[ 'Subject' ] ,
            "opens" => $data[ 'Opens' ] ,
            "clicks" => $data[ 'TotalClicks' ]
        );
    }

    /*public function mapToRawReport ( $data ) {
        $formattedData = array();

        array_walk( $data , array( $this , 'convertToSnakeCase' ) , $formattedData );

        $fomattedData[ 'internal_id' ] = $formattedData[ 'campaign_id' ];

        return $formattedData;
    }*/

    public function mapToRawReport ( $data ) {

        $formattedData = array();

        array_walk( $data , function ( $item , $key ) use ( &$formattedData ) {
            if ( !in_array( $key , array( 'Publication' , 'Links' ) ) ) {
                if ( $key === 'CampaignID' ) {
                    $formattedData[ 'campaign_id' ] = $item;
                } elseif ( $key === 'ArchiveURL' ) {
                    $formattedData[ 'archive_url' ] = $item;
                } elseif ( $key === 'CTR' ) {
                    $formattedData[ 'ctr' ] = $item;
                } elseif ( $key === 'Creative' ) {
                    $formattedData[ 'creative_id' ] = $item[ 'CreativeID' ];
                } else {
                    $formattedData[ snake_case( $key ) ] = $item;
                }
            }
        } );

        dd( $formattedData );

        $fomattedData[ 'internal_id' ] = $formattedData[ 'campaign_id' ];

        return $formattedData;
    }

    /*static public function convertToSnakeCase ( $item , $key , &$newArray ) {
        dd( $newArray );
        $newArray [ snake_case( $key ) ] = $item;
    }*/
}
