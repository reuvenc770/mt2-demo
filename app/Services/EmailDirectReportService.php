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

    public function __construct ( ReportRepo $reportRepo , $apiName , $espAccountId ) {
        parent::__construct( $apiName , $espAccountId );

        $this->reportRepo = $reportRepo;
    }

    public function retrieveApiReportStats ( $date ) {
        try {
            return $reportStats = $this->sendApiRequest( array( 'date' => $date ) );
        } catch ( Exception $e ) {
            throw $e;
        }
    }

    public function insertApiRawStats ( $rawStats ) {
        $convertedRecordCollection = array();

        foreach ( $rawStats as $rawCampaignStats ) {
            try {
                $convertedRecord = $this->mapToRawReport( $rawCampaignStats );

                $convertedRecordCollection []= $convertedRecord;

                $this->reportRepo->insertStats( $this->getEspAccountId() , $convertedRecord );
            } catch ( Exception $e ) {
                throw $e;
            }
        }

        Event::fire( new RawReportDataWasInserted( $this->getApiName() , $this->getEspAccountId() , $convertedRecordCollection ) );
    }

    public function mapToStandardReport ( $data ) {
        $formatedData = $this->mapToRawReport( $data );

        return array(
            "internal_id" => $formatedData[ 'campaign_id' ] ,
            "esp_account_id" => $this->getEspAccountId() ,
            "name" => $formatedData[ 'name' ] ,
            "subject" => $formatedData[ 'subject' ] ,
            "opens" => $formatedData[ 'opens' ] ,
            "clicks" => $formatedData[ 'total_clicks' ]
        );
    }

    public function mapToRawReport ( $rawData ) {
        $formattedData = array();

        array_walk( $rawData , function ( $campaignDatapoint , $key ) use ( &$formattedData ) {
            $isValidField = !in_array( $key , $this->invalidFields );

            if ( $isValidField ) {
                switch ( $key ) {
                    case 'CampaignID' :
                        $formattedData[ 'campaign_id' ] = $campaignDatapoint;
                        $formattedData[ 'internal_id' ] = $campaignDatapoint;
                        break;

                    case 'ArchiveURL' :
                        $formattedData[ 'archive_url' ] = $campaignDatapoint;
                        break;

                    case 'CTR' :
                        $formattedData[ 'ctr' ] = $campaignDatapoint;
                        break;

                    case 'Creative' :
                        $formattedData[ 'creative_id' ] = $campaignDatapoint[ 'CreativeID' ];
                        break;

                    default :
                        $formattedData[ snake_case( $key ) ] = $campaignDatapoint;
                }
            }
        } );

        return $formattedData;
    }
}
