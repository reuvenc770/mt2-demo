<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Services\Interfaces\IDataService;
use App\Services\AbstractReportService;

use App\Repositories\ReportRepo;
use App\Services\API\PublicatorsApi;
use App\Services\EmailRecordService;

use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;

use Log;

class PublicatorsReportService extends AbstractReportService implements IDataService {
    public function __construct ( ReportRepo $repo , PublicatorsApi $api , EmailRecordService $emailRecord ) {
        parent::__construct( $repo , $api , $emailRecord );
    }

    public function retrieveApiStats ( $date ) {
        $this->api->setDate( $date );

        if ( !$this->api->isAuthenticated() ) {
            try {
                $this->api->authenticate();
            } catch ( \Exception $e ) {
                throw new JobException( "Failed to Retrieve API Stats. " . $e->getMessage() , JobException::CRITICAL , $e );
            }
        }

        $campaigns = $this->api->getCampaigns();

        foreach ( $campaigns as $campaignId ) {
            $campaignData = $this->api->getCampaignStats( $campaignId ); 

            var_dump( $campaignData );

            die();
        }
    }

    public function insertApiRawStats ( $data ) {

    }

    public function mapToRawReport ( $data ) {

    }

    public function mapToStandardReport ( $data ) {

    }
}
