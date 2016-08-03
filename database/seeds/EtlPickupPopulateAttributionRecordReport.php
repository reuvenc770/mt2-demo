<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Seeder;
use App\Models\EtlPickup;

class EtlPickupPopulateAttributionRecordReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $x = new EtlPickup();
        $x->name = 'PopulateAttributionRecordReport';
        $x->stop_point = 0;
        $x->save();
    }
}
