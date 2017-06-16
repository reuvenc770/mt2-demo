<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MT1Models\User as Feeds;
use Maknz\Slack\Facades\Slack;

class CheckBatchFeedProcessingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:checkBatchCmpte';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if there are any old files that were not moved for MT1.';

    const SLACK_CHANNEL = '#cmp_hard_start_errors';
    protected $remote;

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
        $this->remote = \App::make( \App\Services\RemoteLinuxSystemService::class );

        $this->remote->initSshConnection(
            config('ssh.servers.cmpte_feed_file_server.host'),
            config('ssh.servers.cmpte_feed_file_server.port'),
            config('ssh.servers.cmpte_feed_file_server.username'),
            config('ssh.servers.cmpte_feed_file_server.public_key'),
            config('ssh.servers.cmpte_feed_file_server.private_key')
        );

        $this->createNewFeedMt1Directories();

        $this->alertForOrangeFiles();

        $this->moveRedFiles();
    }

    protected function createNewFeedMt1Directories () {
        $homeDirectoryNames = Feeds::where( 'status' , 'A' )->pluck( 'username' )->toArray();

        foreach ( $homeDirectoryNames as $currentFeedName ) {
            if ( !$this->remote->directoryExists( '/home/mt1/' . $currentFeedName ) ) {
                $this->remote->createDirectory( '/home/mt1/' . $currentFeedName );
            }
        }
    }

    protected function alertForOrangeFiles () {
        $homeDirectoryNames = Feeds::where( [ [ 'status' , '=' , 'A'] , [ 'OrangeClient' , '=' , 'Y' ] ] )->pluck( 'username' )->toArray();

        $findOptions = [
            '-type f' ,
            '-mtime -1' ,
            '-mmin +45' ,
            ' -not -path "/home/mt1/*"' ,
            "\( -name '*.csv' -o -name '*.txt' \)" ,
            '-print' 
        ];

        foreach ( $homeDirectoryNames as $currentFeedName ) {
            if ( !$this->remote->directoryExists( '/home/' . $currentFeedName ) ) {
                continue;
            }

            if ( $files = $this->remote->getRecentFiles( '/home/' . $currentFeedName , $findOptions ) ) {
                Slack::to( self::SLACK_CHANNEL )->send( "Found Orange Feed Files which were not migrated to MT1 folders. File List:\n" . $files ); 

                foreach ( explode( "\n" , $newFileString ) as $orangeFile ) {
                    if ( $orangeFile !== '' ) {
                        \Log::info( 'Moving orange file ' . $orangeFile );
                        $newPath = '/home/mt1' . str_replace( '/home' , '' , $orangeFile ); 
                        $output = $this->remote->moveFile( $orangeFile , $newPath );
                    }
                }
            }
        }
    }

    protected function moveRedFiles () {
        $homeDirectoryNames = Feeds::where( [ [ 'status' , '=' , 'A'] , [ 'OrangeClient' , '=' , 'N' ] ] )->pluck( 'username' )->toArray();

        $findOptions = [
            '-type f' ,
            '-mtime -1' ,
            ' -not -path "/home/mt1/*"' ,
            "\( -name '*.csv' -o -name '*.txt' \)" ,
            '-print' 
        ];

        foreach ( $homeDirectoryNames as $currentFeedName ) {
            if ( !$this->remote->directoryExists( '/home/' . $currentFeedName ) ) {
                continue;
            }

            if ( $newFileString = $this->remote->getRecentFiles( '/home/' . $currentFeedName , $findOptions ) ) {
                foreach ( explode( "\n" , $newFileString ) as $redFile ) {
                    if ( $redFile !== '' ) {
                        \Log::debug( 'Moving red file ' . $redFile );
                        $newPath = '/home/mt1' . str_replace( '/home' , '' , $redFile ); 
                        $output = $this->remote->moveFile( $redFile , $newPath );
                    }
                }
            }
        }
    }
}
