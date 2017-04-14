<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserService;

class ResetUserPasswordCommand extends Command
{
    const ERROR_MESSAGE = 'Missing Password';

    private $service;
    private $user;
    private $newPassword;
    private $hasErrors = false;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:resetPassword {--p|password= : New Password} {--u|username= : User to reset.} {--e|email= : Email of user to reset. } {--i|userid= : User ID to reset.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets the user\'s password.';

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
    public function handle( UserService $service )
    {
        $this->service = $service;

        $this->processOptions();
        
        if ( !$this->hasErrors ) {
            $result = $this->service->resetPassword( $this->user , $this->newPassword );

            if ( !$result[ 'status' ] ) {
                $this->error( json_encode( $result[ 'errorMessages' ] ) );
            } else {
                $this->info( "User's password successfully reset." );
            }
        }
    }

    private function processOptions () {
        if ( is_null( $this->option( 'password' ) ) ) {
            $this->error( self::ERROR_MESSAGE );

            $this->hasErrors = true;

            return;
        }

        $this->newPassword = $this->option( 'password' );

        if ( $this->option( 'username' ) ) {
            $this->user = $this->service->findByUsername( $this->option( 'username' ) );
        } elseif ( $this->option( 'email' ) ) {
            $this->user = $this->service->findByEmail( $this->option( 'email' ) );
        } elseif ( $this->option( 'userid' ) ) {
            $this->user = $this->service->findById( (int) $this->option( 'userid' ) );
        }

        if ( is_null( $this->user ) ) {
            $this->error( 'User does not exist.' );

            $this->hasErrors = true;
        }
    }
}
