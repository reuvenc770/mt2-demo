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

    private $invalidFields = array( 'Publication' , 'Links' );

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

    public function mapToRawReport ( $data ) {
        $formattedData = array();

        array_walk( $data , function ( $item , $key ) use ( &$formattedData ) {
            $isValidField = !in_array( $key , $this->invalidFields );

            if ( $isValidField ) {
                if ( $key === 'CampaignID' ) {
                    $formattedData[ 'campaign_id' ] = $item;
                    $formattedData[ 'internal_id' ] = $item;
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

        $fomattedData[ 'internal_id' ] = $formattedData[ 'campaign_id' ];

        return $formattedData;
    }
}
