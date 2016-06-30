<?php

namespace App\Console\Commands;

use App\Services\FtpUserService;
use App;
use Illuminate\Console\Command;
use Storage;
use Maknz\Slack\Facades\Slack;

use Log;

class FtpAdmin extends Command
{
    const FTP_ADMIN_INTERFACE_NAME = "App\\Services\\Interfaces\\IFtpAdmin";
    const PASSWORD_LENGTH = 8;

    const CREATE_USER_COMMAND = "useradd -g sftp -d %s %s";
    const SET_PASSWORD_COMMAND = "echo %s:%s | chpasswd";
    const CREATE_DIR_COMMAND = "mkdir %s";
    const CHANGE_DIR_OWNER_COMMAND = "chown -R %s:sftp %s";
    const CHANGE_DIR_PERMS_COMMAND = "chmod 755 %s";

    CONST SLACK_TARGET_SUBJECT = "#mt2-new-ftp-users";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:admin {--H|host= : The host to create users on. } {--P|port=22 : The port for ssh connections. } {--U|sshUser= : User to login as.} {--k|sshPublicKey= : Path to public ssh keyfile.} {--K|sshPrivateKey= : Path to private ssh keyfile} {--u|user= : Username to use. } {--p|password= : Password to set.} {--s|service= : The service to use when saving the username and password. The service must implement IFtpAdmin.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin command for generating ftp users. Credentials are automatically stored via Services. The service must implement IFtpAdmin.';

    protected $host = null;
    protected $port = null;
    protected $sshConnection = null;
    protected $sshUser = null;
    protected $sshPublicKey = null;
    protected $sshPrivateKey = null;

    protected $username = null;
    protected $password = null;
    protected $ftpUrl = null;
    protected $directory = null;

    protected $ftpUserService = null;
    protected $service = null;

    protected $commandOutput = [];
    protected $errorFound = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( FtpUserService $ftpUserService )
    {
        parent::__construct();

        $this->ftpUserService = $ftpUserService;
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

            $this->initSshConnection();

            $this->loadService();

            if ( $this->shouldCreateUser() ) {
                $this->setupFtpUser();
            } else {
                $this->generateNewUsersFromDb();
            }
        } catch ( \Exception $e ) {
            Log::error( "FtpAdmin Command - " . $e->getMessage() );

            $this->error( $e->getMessage() );
        }
    }

    protected function processOptions () {
        $this->host = $this->option( 'host' );
        $this->port = $this->option( 'port' );
        $this->sshUser = $this->option( 'sshUser' );
        $this->sshPublicKey = $this->option( 'sshPublicKey' );
        $this->sshPrivateKey = $this->option( 'sshPrivateKey' );

        $this->username = $this->option( 'user' );
        $this->password = $this->option( 'password' );
        $this->directory = '/home/' . $this->username;
    }

    protected function initSshConnection () {
        if ( is_null( $this->host ) ) { throw new \Exception( "FTP Server Host is required." ); }
        if ( is_null( $this->port ) ) { throw new \Exception( "FTP Server Port is required." ); }
        if ( is_null( $this->sshUser ) ) { throw new \Exception( "SSH user is required." ); }
        if ( is_null( $this->sshPublicKey ) ) { throw new \Exception( "SSH public key is required." ); }
        if ( is_null( $this->sshPrivateKey ) ) { throw new \Exception( "SSH private key is required." ); }

        $this->sshConnection = ssh2_connect( $this->host , $this->port , [ 'hostkey' => 'ssh-rsa' ] );

        ssh2_auth_pubkey_file(
            $this->sshConnection ,
            $this->sshUser ,
            $this->sshPublicKey ,
            $this->sshPrivateKey
        );
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

    protected function setupFtpUser () {
        $this->createUser();

        $this->saveUserAndPassword();

        Slack::to( self::SLACK_TARGET_SUBJECT )->send(
            $this->option( 'service' ) . " FTP User generation successful."
            . "\n\tUsername: " . $this->username
            . "\n\tPassword: " . $this->password
        );

        Log::info( json_encode( $this->commandOutput ) );

        Log::info( 'Finished Creating user...' );
    }

    protected function createUser () {
        $this->createUserCommand();
        $this->setPasswordCommand();
        $this->setDirectoryPermissionsCommand();
        $this->setDirectoryOwnerCommand();
    }

    protected function generatePassword () {
        $this->password = str_random( self::PASSWORD_LENGTH );
    }

    protected function createUserDirectoryCommand () {
        $command = sprintf( self::CREATE_DIR_COMMAND , $this->directory );

        ssh2_exec( $this->sshConnection , $command );
    }

    protected function createUserCommand () {
        $command = sprintf( self::CREATE_USER_COMMAND , $this->directory , $this->username );

        ssh2_exec( $this->sshConnection , $command );
    }

    protected function setPasswordCommand () {
        if ( is_null( $this->password ) ) {
            $this->generatePassword();
        }

        $command = sprintf( self::SET_PASSWORD_COMMAND , $this->username , $this->password );

        ssh2_exec( $this->sshConnection , $command );
    }

    protected function setDirectoryOwnerCommand () {
        $command = sprintf( self::CHANGE_DIR_OWNER_COMMAND , $this->username , $this->directory );

        $this->commandOutput []= $command;

        ssh2_exec( $this->sshConnection , $command );
    }

    protected function setDirectoryPermissionsCommand () {
        $command = sprintf( self::CHANGE_DIR_PERMS_COMMAND , $this->directory );

        ssh2_exec( $this->sshConnection , $command );
    }

    /**
     *
     * Uncomment when setting live!!!
     *
     */
    protected function saveUserAndPassword () {
        $this->ftpUserService->save( [ 'username' => $this->username , 'password' => $this->password ] , $this->directory , 'localhost' , get_class( $this->service ) );

        $this->service->saveFtpUser( [ "username" => $this->username , "password" => $this->password, "ftp+url" => $this->ftpUrl ] );
    }

    protected function generateNewUsersFromDb () {
        $users = $this->service->findNewFtpUsers();

        foreach ( $users as $currentUser ) {
            $this->username = null;
            $this->password = null;

            $this->username = $currentUser->username;
            $this->directory = '/home/' . $currentUser->username;
            $this->ftpUrl = "ftp://52.205.67.250";
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
}
