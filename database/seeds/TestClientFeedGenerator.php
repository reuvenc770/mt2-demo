<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Seeder;

class TestClientFeedGenerator extends Seeder
{
    const CLIENT_COUNT = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = $this->generateClients( self::CLIENT_COUNT );

        foreach ( $clients as $currentClient ) {
            $this->generateFeeds( $currentClient->id , rand( 1 , 5 ) );
        }
    }

    protected function generateClients ( $generateCount ) {
        return factory( App\Models\Client::class , $generateCount )->create();
    }

    protected function generateFeeds ( $clientId , $generateCount ) {
        $currentFeed = null;
        $feedCount = 1;

        do {
            $currentFeed = factory( App\Models\Feed::class )->create( [
                "client_id" => $clientId
            ] );

            factory( App\Models\RecordProcessingFileField::class )->create( [
                "feed_id" => $currentFeed->id
            ] );

            $feedCount++;
        } while ( $feedCount <= $generateCount );
    }
}
