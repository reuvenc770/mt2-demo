<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Seeder;

class CpaDeploySnapshotSeeder extends Seeder
{
    protected $feeds = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        #create 5 feeds
        $counter = 1;                                                                                                                                         
        while ( $counter <= 5 ) {                                                                                                                             
            $this->feeds []= factory( \App\Models\Feed::class )->create( [ "name" => 'CpaDeploySnapshotFeed' . $counter ] );
            $counter++;
        }

        #get deploys and cake offers from Cake Action for generating data in multiple tables
        $cakeOffersAndDeploys = \App\Models\CakeAction::select( 'cake_offer_id' , 'deploy_id' )->groupBy( 'cake_offer_id' , 'deploy_id' )->get();
        foreach ( $cakeOffersAndDeploys as $current ) {
            if ( $current->cake_offer_id == 0 ) {
                continue;
            }
            
            #create offers if missing
            if ( \App\Models\CakeOffer::where( 'id' , $current->cake_offer_id )->count() === 0 ) { 
                #create system offer
                $currentOffer = factory( \App\Models\Offer::class )->create();

                $currentCakeOffer = factory( \App\Models\CakeOffer::class )->create( [ 'id' => $current->cake_offer_id ] , [
                    "id" =>  $current->cake_offer_id ,
                    "name" => $currentOffer->name
                ] );

                $mapper = new \App\Models\MtOfferCakeOfferMapping();
                $mapper->offer_id = $currentOffer->id;
                $mapper->cake_offer_id = $current->cake_offer_id;
                $mapper->save();
            }

            #create deploy
            factory( \App\Models\Deploy::class )->create( [ 'id' => $current->deploy_id , 'offer_id' => $currentOffer->id ] );
        }
        
        #get all email_ids from conversions and distribute to feeds in deploy snapshot
        $cakeActions = \App\Models\CakeAction::where( [ [ 'action_id' , '=' , '3' ] , [ 'revenue' , '>' , 0 ] , [ 'email_id' , '<>' , 0 ] ] )->get();

        $faker = Faker\Factory::create();
        #generate partial deploy snapshot from conversions
        foreach ( $cakeActions as $currentAction ) {
            \App\Models\DeploySnapshot::create( [
                'email_id' => $currentAction->email_id ,
                'email_address' => $faker->unique()->safeEmail ,
                'deploy_id' => $currentAction->deploy_id ,
                'feed_id' => $faker->numberBetween( 1 , 5 )
            ] );
        }

        #augment each feed's deploy snaphost to have more email ids
        $deployIds = \App\Models\CakeAction::select( \DB::raw( 'distinct( deploy_id ) as did' ) )
                        ->where( [ [ 'action_id' , '=' , '3' ] , [ 'revenue' , '>' , 0 ] ] )
                        ->pluck( 'did' );

        foreach ( $deployIds as $currentId ) {
            foreach ( $this->feeds as $feed ) {
                $recordCountToGenerate = $faker->randomElement( [ 5 , 10 , 20 , 40 ] );

                $counter = 1;
                while ( $counter != $recordCountToGenerate ) {
                    $snapshot = \App\Models\DeploySnapshot::create( [
                        'email_id' => $faker->unique()->numberBetween( 3000000000 , 4000000000 ) ,
                        'email_address' => $faker->unique()->safeEmail ,
                        'deploy_id' => $currentId ,
                        'feed_id' => $feed->id
                    ] );

                    $counter++;
                }
            }
        }
    }
}
