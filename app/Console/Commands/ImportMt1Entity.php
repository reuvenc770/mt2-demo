<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\DataProcessingJob;

class ImportMt1Entity extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt1Import {type} {lookback?} {--d|delay= : Delay in minutes } {--runtime-threshold=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Keep entities in MT2 up to date';

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
    public function handle() {
        $lookback = $this->argument('lookback') ?: 5;
        $jobName = $this->getJobName($this->argument('type'));
        $delay = $this->option( 'delay' ) ?: null;

        $job = new DataProcessingJob($jobName, str_random(16), $lookback, $this->option('runtime-threshold'));

        $validDelayPresent = ( !is_null( $delay ) && is_numeric( $delay ) && $delay > 0 );
        if ( $validDelayPresent ) {
            $job->delay( $delay * 60 ); #convert to seconds
        }

        $this->dispatch($job);
    }

    protected function getJobName($type) {
        switch ($type) {
            case "offer":
                return 'ImportMt1Offers';

            case "advertiser":
                return "ImportMt1Advertisers";

            case "creative":
                return "ImportMt1Creatives";

            case "from":
                return "ImportMt1Froms";

            case "subject":
                return "ImportMt1Subjects";

            case "listProfile":
                return "ImportMT1ListProfiles";

            case "deploy":
                return "ImportMT1Deploys";

            case "offerCreativeMap":
                return "ImportMT1OfferCreativeMapping";

            case "offerFromMap":
                return "ImportMT1OfferFromMapping";

            case "offerSubjectMap":
                return "ImportMT1OfferSubjectMapping";
                
            case "feed":
                return "ImportMt1Feeds";

            case "cakeEncryptedLinkMap":
                return "ImportMt1CakeEncryptionMapping";

            case "link":
                return "ImportMt1Links";

            case "offerTrackingLink":
                return "ImportMt1OfferTracking";

            case "mailingTemplate":
                return "ImportMt1MailingTemplate";

            case "cakeOffer":
                return "ImportMt1CakeOffers";

            case "cakeVertical":
                return "ImportMt1CakeVertical";

            case "cakeOfferMap":
                return "ImportMt1CakeOfferMapping";

            case "client":
                return "ImportMt1Client";

            case "vendorSuppressionInfo":
                return "ImportMt1VendorSuppressionInfo";

            case "vendorSuppression":
                return "ImportMt1VendorSuppression";

            case "offerSuppressionListMap":
                return "ImportMt1OfferSuppressionListMap";

            case "globalSuppression":
                return "ImportMt1GlobalSuppression";

            default:
                throw new \Exception('Unsupported entity type: ' . $type);
        }
    }
}
