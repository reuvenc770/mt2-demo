<?php

namespace App\Console\Commands;

use App\Services\FtpUserService;
use App\Services\RemoteLinuxSystemService;
use App\Services\RemoteFeedFileService;
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
    protected $remoteFeedFileService = null;
    protected $service = null;

    protected $errorFound = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( FtpUserService $ftpUserService , RemoteLinuxSystemService $systemService , RemoteFeedFileService $remoteFeedFileService )
    {
        parent::__construct();

        $this->ftpUserService = $ftpUserService;
        $this->systemService = $systemService;
        $this->remoteFeedFileService = $remoteFeedFileService;
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
                $this->loadService();

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
    }

    protected function resetPassword () {
        if ( is_null( $this->password ) ) {
            $this->generatePassword();
        }

        $this->systemService->initSshConnection(
            $this->option( 'host' ) ,
            $this->option( 'port' ) ,
            $this->option( 'sshUser' ) , 
            $this->option( 'sshPublicKey' ) ,
            $this->option( 'sshPrivateKey' )
        );

        $this->systemService->setPassword( $this->username , $this->password );

        $this->saveUserAndPassword();

        $feedServiceClass = "\\App\\Services\\FeedService";
        if ( $this->service instanceof $feedServiceClass ) {
            $this->service->updatePassword( $this->username , $this->password );
        }

        Slack::to( self::SLACK_TARGET_SUBJECT )->send(
            $this->option( 'service' ) . " FTP User Password Reset."
            . "\n\tUsername: " . $this->username
            . "\n\tPassword: " . $this->password
        );
    }

    protected function createUser () {
        if ( is_null( $this->password ) ) {
            $this->generatePassword();
        }

        $this->systemService->initSshConnection(
            $this->option( 'host' ) ,
            $this->option( 'port' ) ,
            $this->option( 'sshUser' ) , 
            $this->option( 'sshPublicKey' ) ,
            $this->option( 'sshPrivateKey' )
        );

        $this->systemService->createUser( $this->username , $this->directory );
        $this->systemService->setPassword( $this->username , $this->password );

        $this->systemService->setDirectoryPermissions( $this->directory );
        $this->systemService->setDirectoryOwner( 'root' , $this->directory );

        $uploadDir = $this->directory . '/upload';
        $this->systemService->createDirectory( $uploadDir );

        $this->systemService->setDirectoryPermissions( $uploadDir );
        $this->systemService->setDirectoryOwner( $this->username , $uploadDir );
    }

    protected function generatePassword () {
        $this->password = str_random( self::PASSWORD_LENGTH );
    }

    protected function saveUserAndPassword () {
        $this->ftpUserService->save( [ 'username' => $this->username , 'password' => $this->password ] , $this->directory , $this->option( 'host' ) , get_class( $this->service ) );
    }

    protected function generateNewUsersFromDb () {
        $users = $this->service->findNewFtpUsers();

        $this->systemService->initSshConnection(
            $this->option( 'host' ) ,
            $this->option( 'port' ) ,
            $this->option( 'sshUser' ) , 
            $this->option( 'sshPublicKey' ) ,
            $this->option( 'sshPrivateKey' )
        );

        foreach ( $users as $currentUser ) {
            $this->username = null;
            $this->password = null;

            $this->username = $currentUser->short_name;
            $this->directory = '/home/' . $currentUser->short_name;

            $responseString = $this->systemService->userExists( $this->username );
            $response = json_decode( $responseString );

            if ( $response->status === 0 ) {
                if ( isset( $currentUser->password ) ) {
                    $this->password = $currentUser->password;
                }

                try {
                    $this->setupFtpUser();
                } catch ( \Exception $e ) {
                    Log::error( "FtpAdmin Command - " . $e->getMessage() );

                    $this->error( $e );
                }
            }
        }
    }

    protected function updateFeedDirectories () {
        $this->remoteFeedFileService->init();
        $this->remoteFeedFileService->updateFeedDirectories();
    }
}
