<?php

namespace App\Console\Commands;

use App\Services\FtpUserService;
use App\Services\RemoteLinuxSystemService;
use App\Services\FeedService;
use App\Services\DomainGroupService;
use App;
use Illuminate\Console\Command;
use Storage;
use Maknz\Slack\Facades\Slack;

use Log;

class FtpAdmin extends Command
{
    const FTP_ADMIN_INTERFACE_NAME = "App\\Services\\Interfaces\\IFtpAdmin";
    const PASSWORD_LENGTH = 8;
    CONST SLACK_TARGET_SUBJECT = "#mt2-new-ftp-users";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:admin {--H|host= : The host to create users on. } {--P|port=22 : The port for ssh connections. } {--U|sshUser= : User to login as.} {--k|sshPublicKey= : Path to public ssh keyfile.} {--K|sshPrivateKey= : Path to private ssh keyfile} {--u|user= : Username to use. } {--p|password= : Password to set.} {--s|service= : The service to use when saving the username and password. The service must implement IFtpAdmin.} {--r|reset= : If True Reset Given Users Password} {--D|updateFeedDirectories : Update directory structure for feeds. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin command for generating ftp users. Credentials are automatically stored via Services. The service must implement IFtpAdmin.';

    protected $username = null;
    protected $password = null;
    protected $reset = false;
    protected $directory = null;
    protected $shouldUpdateFeedDirectories = false;

    protected $ftpUserService = null;
    protected $systemService = null;
    protected $feedService = null;
    protected $domainGroupService = null;
    protected $service = null;

    protected $errorFound = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( FtpUserService $ftpUserService , RemoteLinuxSystemService $systemService , FeedService $feedService , DomainGroupService $domainGroupService )
    {
        parent::__construct();

        $this->ftpUserService = $ftpUserService;
        $this->systemService = $systemService;
        $this->feedService = $feedService;
        $this->domainGroupService = $domainGroupService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->processOptions();

            if ( $this->shouldUpdateFeedDirectories ) {
                $this->updateFeedDirectories();
            } else if ( $this->shouldCreateUser() && !$this->shouldResetPassword() ) {
                $this->loadService();

                $this->setupFtpUser();
            } else if ($this->shouldResetPassword() && $this->shouldCreateUser()) {
                $this->resetPassword();
            } else {
                $this->loadService();

                $this->generateNewUsersFromDb();
            }
        } catch ( \Exception $e ) {
            Log::error( "FtpAdmin Command - " . $e->getMessage() );

            $this->error( $e->getMessage() );
        }
    }

    protected function processOptions () {
        $this->sshConnection = $this->systemService->init(
            $this->option( 'host' ) ,
            $this->option( 'port' ) ,
            $this->option( 'sshUser' ) , 
            $this->option( 'sshPublicKey' ) ,
            $this->option( 'sshPrivateKey' )
        );

        $this->reset = (bool) $this->option('reset');
        $this->username = $this->option( 'user' );
        $this->password = $this->option( 'password' );
        $this->directory = '/home/' . $this->username;
        $this->shouldUpdateFeedDirectories = $this->option( 'updateFeedDirectories' );
    }

    protected function loadService () {
        $formattedName = studly_case( $this->option( 'service' ) );

        $servicePath =  "App\\Services\\{$formattedName}Service";
        $mt1Path =  "App\\Services\\MT1Services\\{$formattedName}Service";

        if ( class_exists( $servicePath ) ) {
            $this->service = App::make( $servicePath );
        } elseif ( class_exists( $mt1Path ) ) {
            $this->service = App::make( $mt1Path );
        } else {
            throw new \Exception( "'{$formattedName}Service' does not exist. Valid Service required." );
        } 

        if ( !$this->serviceUsesInterface() ) {
            throw new \Exception( "'{$formattedName}Service' must implement IFtpAdmin." );
        }
    }

    protected function serviceUsesInterface () {
        return in_array( self::FTP_ADMIN_INTERFACE_NAME , class_implements( $this->service ) );        
    }

    protected function shouldCreateUser () {
        return !empty( $this->username );
    }

    protected function shouldResetPassword () {
        return $this->reset;
    }

    protected function setupFtpUser () {
        $this->createUser();

        $this->saveUserAndPassword();

        Slack::to( self::SLACK_TARGET_SUBJECT )->send(
            $this->option( 'service' ) . " FTP User generation successful."
            . "\n\tUsername: " . $this->username
            . "\n\tPassword: " . $this->password
        );

        Log::info( 'Finished Creating user...' );
    }

    protected function resetPassword () {
        if ( is_null( $this->password ) ) {
            $this->generatePassword();
        }

        $this->systemService->setPasswordCommand( $this->username , $this->password );

        $this->saveUserAndPassword();

        Slack::to( self::SLACK_TARGET_SUBJECT )->send(
            $this->option( 'service' ) . " FTP User Password Reset."
            . "\n\tUsername: " . $this->username
            . "\n\tPassword: " . $this->password
        );

        Log::info( 'Finished Reseting password' );
    }

    protected function createUser () {
        if ( is_null( $this->password ) ) {
            $this->generatePassword();
        }

        $this->systemService->createUserCommand( $this->username , $this->directory );
        $this->systemService->setPasswordCommand( $this->username , $this->password );
        $this->systemService->setDirectoryPermissionsCommand( $this->directory );
        $this->systemService->setDirectoryOwnerCommand( $this->username , $this->directory );
    }

    protected function generatePassword () {
        $this->password = str_random( self::PASSWORD_LENGTH );
    }

    protected function saveUserAndPassword () {
        $this->ftpUserService->save( [ 'username' => $this->username , 'password' => $this->password ] , $this->directory , 'localhost' , get_class( $this->service ) );

        $this->service->saveFtpUser( [ "username" => $this->username , "password" => $this->password, "ftp_url" => 'ftp://' . $this->option( 'host' ) ] );
    }

    protected function generateNewUsersFromDb () {
        $users = $this->service->findNewFtpUsers();

        foreach ( $users as $currentUser ) {
            $this->username = null;
            $this->password = null;

            $this->username = $currentUser->username;
            $this->directory = '/home/' . $currentUser->username;

            if ( isset( $currentUser->password ) ) {
                $this->password = $currentUser->password;
            }

            try {
                $this->setupFtpUser();
            } catch ( \Exception $e ) {
                Log::error( "FtpAdmin Command - " . $e->getMessage() );

                $this->error( $e->getMessage() );
            }
        }
    }

    protected function updateFeedDirectories () {
        $countries = [ 'US' , 'UK' ];
        $isps = $this->domainGroupService->getAllActiveNames();
        $directoryList = $this->getValidDirectories();

        foreach ( $directoryList as $feedDir ) {
            foreach ( $countries as $country ) {
                $country = escapeshellarg( $country );
                $countryDir = "{$feedDir}/{$country}";

                if ( !$this->systemService->directoryExists( $countryDir ) ) {
                    $this->info( 'Creating directory: ' . $countryDir );

                    $this->systemService->createDirectoryCommand( $countryDir );
                }

                foreach ( $isps as $isp ) {
                    $isp = escapeshellarg( $isp );
                    $ispDir = "{$countryDir}/{$isp}";

                    if( !$this->systemService->directoryExists( $ispDir ) ) {
                        $this->info( 'Creating directory: ' . $ispDir );

                        $this->systemService->createDirectoryCommand( $ispDir );
                    }
                }
            }
        }
    }

    protected function getValidDirectories () {
        $rawDirectoryList = $this->systemService->listDirectoriesCommand( '/home' );        

        array_pop( $rawDirectoryList );
        array_shift( $rawDirectoryList );

        $validFeedList = $this->feedService->getActiveFeedNames();

        $directoryList = array_filter( $rawDirectoryList , function ( $dir ) use ( $validFeedList ) {
            $matches = [];
            preg_match( '/^(?:.+\/)(?:\.{0,})([\w\s]+)$/' , $dir , $matches );

            $notSystemUser = ( strpos( $dir , 'centos' ) === false );
            $notCustomUser = ( strpos( $dir , 'mt2PullUser' ) === false );
            $isValidFeed = in_array( $matches[ 1 ] , $validFeedList );
            if ( $notSystemUser && $notCustomUser && $isValidFeed ) {
                return $dir;
            } 
        } );

        return $directoryList;
    }
}
