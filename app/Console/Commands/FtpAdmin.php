<?php

namespace App\Console\Commands;

use App;
use Illuminate\Console\Command;
use Storage;
use Maknz\Slack\Facades\Slack;

use Log;

class FtpAdmin extends Command
{
    const FTP_ADMIN_INTERFACE_NAME = "App\\Services\\Interfaces\\IFtpAdmin";
    const PASSWORD_LENGTH = 8;

    const CREATE_GROUP_COMMAND = "groupadd sftp";
    const CHECK_GROUP_COMMAND = "getent group sftp";
    const CREATE_USER_COMMAND = "useradd -g sftp -d %s %s";
    const DELETE_USER_COMMAND = "userdel %s";
    const CHECK_USER_COMMAND = "getent passwd %s";
    const SET_PASSWORD_COMMAND = "echo %s:%s | chpasswd";
    const DISABLE_SSH_ACCESS_COMMAND = "usermod -s nologin %s";
    const SET_USER_SHELL_COMMAND = "usermod -s /bin/false %s";
    const CHANGE_DIR_OWNER_COMMAND = "chown -R %s:sftp %s";
    const CHANGE_DIR_PERMS_COMMAND = "chmod 755 %s";

    CONST SLACK_TARGET_SUBJECT = "#mt2";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:admin {--g|createGroup : Creates the ftp group for users. You must be root to use this option. } {--d|deleteUser : Deletes user instead of creating one. } {--u|user= : Username to use. } {--p|password= : Password to set.} {--s|service= : The service to use when saving the username and password. The service must implement IFtpAdmin.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin command for generating ftp users. Credentials are automatically stored via Services. The service must implement IFtpAdmin.';

    protected $username = null;
    protected $password = null;
    protected $directory = null;

    protected $service = null;

    protected $commandOutput = [];
    protected $errorFound = false;

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
        try {
            $this->processOptions();

            if ( $this->shouldCreateGroup() ) {
                $this->createFtpGroup();
            } elseif ( $this->shouldDeleteUser() ) {
                $this->deleteUser(); 
            }else {
                $this->loadService();

                if ( $this->shouldCreateUser() ) {
                    $this->setupFtpUser();
                } else {
                    $this->generateNewUsersFromDb();
                }
            }
        } catch ( \Exception $e ) {
            Log::error( "FtpAdmin Command - " . $e->getMessage() );

            $this->error( $e->getMessage() );
        }
    }

    protected function processOptions () {
        $this->username = $this->option( 'user' );
        $this->password = $this->option( 'password' );
        $this->directory = 'sftp/' . $this->username;
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

    protected function shouldCreateGroup () {
        return ( $this->option( 'createGroup' ) === true && !$this->groupExists() );
    }

    protected function groupExists () {
        return !empty( exec( self::CHECK_GROUP_COMMAND ) );
    }

    protected function createFtpGroup () {
        exec( self::CREATE_GROUP_COMMAND , $this->commandOutput , $this->errors );

        if ( $this->errors ) {
            throw new \Exception( 'Errors found when trying to create sftp group. ' . json_encode( $this->commandOutput ) );
        }

        Storage::makeDirectory( 'sftp' );
    }

    protected function shouldDeleteUser () {
        $deleteOptionPresent = ( $this->option( 'deleteUser' ) === true );

        if ( $deleteOptionPresent && !$this->userExists() ) {
            throw new \Exception( 'User does not exist. Canceling user deletion.' );
        }

        return $deleteOptionPresent;
    }

    protected function shouldCreateUser () {
        return ( !empty( $this->username ) && !$this->userExists() );
    }

    protected function userExists () {
        return !empty(
            exec( escapeshellcmd( sprintf(
                self::CHECK_USER_COMMAND ,
                $this->username
            ) ) )
        );
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
        $this->disableSshAccessCommand();
        $this->setUsersShellCommand();
    }

    protected function generatePassword () {
        $this->password = str_random( self::PASSWORD_LENGTH );
    }

    protected function createUserCommand () {
        Storage::makeDirectory( $this->directory );

        $command = sprintf( self::CREATE_USER_COMMAND , storage_path() . '/' . $this->directory , $this->username );

        $this->commandOutput []= $command;

        exec( escapeshellcmd( $command ) , $this->commandOutput , $this->errors );

        if ( $this->errors ) {
            throw new \Exception( 'Failied to create user. output - ' . json_encode( $this->commandOutput )  );
        }
    }

    protected function setPasswordCommand () {
        if ( is_null( $this->password ) ) {
            $this->generatePassword();
        }

        $command = sprintf( self::SET_PASSWORD_COMMAND , $this->username , $this->password );

        $this->commandOutput []= $command;

        exec( escapeshellcmd( $command ) , $this->commandOutput , $this->errors );

        if ( $this->errors ) {
            $this->deleteUser();

            throw new \Exception( 'Failied to set user\'s password. output - ' . json_encode( $this->commandOutput )  );
        }
    }

    protected function setDirectoryOwnerCommand () {
        $command = sprintf( self::CHANGE_DIR_OWNER_COMMAND , $this->username , storage_path() . '/app/' . $this->directory );

        $this->commandOutput []= $command;

        exec( escapeshellcmd( $command ) , $this->commandOutput , $this->errors );

        if ( $this->errors ) {
            $this->deleteUser();

            throw new \Exception( 'Failied to set directory\'s owner. output - ' . json_encode( $this->commandOutput )  );
        }
    }

    protected function setDirectoryPermissionsCommand () {
        $command = sprintf( self::CHANGE_DIR_PERMS_COMMAND , storage_path() . '/app/' . $this->directory );

        $this->commandOutput []= $command;

        exec( escapeshellcmd( $command ) , $this->commandOutput , $this->errors );

        if ( $this->errors ) {
            $this->deleteUser();

            throw new \Exception( 'Failied to set directory\'s permissions. output - ' . json_encode( $this->commandOutput )  );
        }
    }

    protected function disableSshAccessCommand () {
        $command = sprintf( self::DISABLE_SSH_ACCESS_COMMAND , $this->username );

        $this->commandOutput []= $command;

        exec( escapeshellcmd( $command ) , $this->commandOutput , $this->errors );

        if ( $this->errors ) {
            $this->deleteUser();

            throw new \Exception( 'Failied to disable user\'s ssh access. output - ' . json_encode( $this->commandOutput )  );
        }

    }

    protected function setUsersShellCommand () {
        $command = sprintf( self::SET_USER_SHELL_COMMAND , $this->username );

        $this->commandOutput []= $command;
        
        exec( escapeshellcmd( $command ) , $this->commandOutput , $this->errors );

        if ( $this->errors ) {
            $this->deleteUser();

            throw new \Exception( 'Failied to set user\'s shell. output - ' . json_encode( $this->commandOutput )  );
        }
    }

    protected function deleteUser () {
        $this->resetErrors();

        Storage::deleteDirectory( $this->directory );

        $command = sprintf( self::DELETE_USER_COMMAND , $this->username );

        $this->commandOutput []= $command;

        exec( escapeshellcmd( $command ) , $this->commandOutput , $this->errors );

        if ( $this->errors ) {
            throw new \Exception( 'Failied to delete user. output - ' . json_encode( $this->commandOutput )  );
        }
    }

    protected function resetErrors () {
        $this->errors = false;
    }

    protected function saveUserAndPassword () {
        return $this->service->saveFtpUser( [ "username" => $this->username , "password" => $this->password ] );
    }

    protected function generateNewUsersFromDb () {
        $users = $this->service->findNewFtpUsers();

        foreach ( $users as $currentUser ) {
            $this->username = null;
            $this->password = null;

            $this->username = $currentUser->username;

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
