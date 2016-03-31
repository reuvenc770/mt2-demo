<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\AdoptOrphanEmails as Orphanage;

class AdoptOrphanEmails extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:adoptOrphans {--maxOrphans=all} {--chunkSize=1000} {--queueName=orphanage} {--chunkDelay=0} {--order=newest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes Orphan Email Actions for required data. Available options: maxOrphans, chunkSize, queueName, chunkDelay, order';

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
        $this->table( [ 'Option' , 'Value' ] , [
            [ 'option' => 'maxOrphans' , 'value' => $this->option( 'maxOrphans' ) ] ,
            [ 'option' => 'chunkSize' , 'value' => $this->option( 'chunkSize' ) ] ,
            [ 'option' => 'queueName' , 'value' => $this->option( 'queueName' ) ] ,
            [ 'option' => 'chunkDelay' , 'value' => $this->option( 'chunkDelay' ) ] ,
            [ 'option' => 'order' , 'value' => $this->option( 'order' ) ]
        ] );

        $orderOrphans = ( $this->option( 'order' ) == 'newest' ? 'desc' : 'asc' );

        $orphanTable = DB::table( 'orphan_emails' )
            ->select( 'id' , 'email_address' )
            ->orderBy( 'created_at' , $orderOrphans );

        if ( $this->option( 'maxOrphans' ) != 'all' ) { $orphanTable->take( $this->option( 'maxOrphans' ) ); }

        $orphanList = collect( $orphanTable->get() );

        $chunks = $orphanList->chunk( $this->option( 'chunkSize' ) );
       
        $chunks->each( function ( $orphans , $chunkKey ) {
            $job = new Orphanage( $orphans , $orphans->first()->id , $orphans->last()->id );
            $job->onQueue( $this->option( 'queueName' ) );

            if ( $this->option( 'chunkDelay' ) > 0 ) $job->delay( $this->option( 'chunkDelay' ) );

            $this->dispatch( $job );
        } );
    }
}
