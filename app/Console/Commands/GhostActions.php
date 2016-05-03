<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Maknz\Slack\Facades\Slack;

class GhostActions extends Command
{
    CONST SLACK_TARGET_SUBJECT = '#mt2-dev-failed-jobs';

    protected $queryList = [
        "eiid" => "
            SELECT
                esp_account_id,
                account_name,
                COUNT(*) as `count`
            FROM
                email_actions ea
                INNER JOIN mt2_data.esp_accounts eacc ON ea.esp_account_id = eacc.id
            WHERE
                esp_internal_id = 0
                AND ea.created_at >= CURDATE()
            GROUP BY
                account_name;" ,
        "did" => "
            SELECT
                esp_account_id,
                account_name,
                COUNT(*) as `count`
            FROM
                email_actions ea
                INNER JOIN mt2_data.esp_accounts eacc ON ea.esp_account_id = eacc.id
            WHERE
                deploy_id = 0
                AND ea.created_at >= CURDATE()
            GROUP BY
                account_name;" ,
        "both" => "
            SELECT
                esp_account_id,
                account_name,
                COUNT(*) as `count`
            FROM
                email_actions ea
                INNER JOIN mt2_data.esp_accounts eacc ON ea.esp_account_id = eacc.id
            WHERE
                deploy_id = 0
                AND esp_internal_id = 0
                AND ea.created_at >= CURDATE() - INTERVAL 2 DAY
            GROUP BY
                account_name;"
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:ghostActions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for email actions that do not have a deploy id ';

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
        $slackOutput = str_repeat( '=' , 20 ) . "\n*Ghost Actions for Today*\n" . str_repeat( '=' , 20 ) . "\n\n";

        foreach ( $this->queryList as $queryType => $query ) {
            $records = DB::connection( 'reporting_data' )->select( $query );

            if ( $queryType == 'eiid' ) { $slackOutput .= "_Missing ESP Internal ID:_\n"; }
            elseif ( $queryType == 'did' ) { $slackOutput .= "\n_Missing Deploy ID:_\n"; }
            else { $slackOutput .= "\n_Missing ESP Internal ID & Deploy ID:_\n"; }

            $slackOutput .= "\t*ESP Account ID*\t*Account Name*\t*Record Count*\t\n";

            foreach ( $records as $current ) {
                $slackOutput .= "\t" . str_pad( $current->esp_account_id , 26 - strlen( $current->esp_account_id ) + 1 )
                    . "\t" . str_pad( $current->account_name , 22 - strlen( $current->account_name ) + 1 )
                    . "\t" . $current->count . "\n";
            }
        }

        Slack::to( self::SLACK_TARGET_SUBJECT )->send( $slackOutput );
    }
}
