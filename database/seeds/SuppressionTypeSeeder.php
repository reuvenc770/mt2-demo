<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Seeder;
use App\Models\SuppressionType;

class SuppressionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $s1 = new SuppressionType();
        $s1->name = 'Unsub';
        $s1->save();

        $s2 = new SuppressionType();
        $s2->name = 'Hard Bounce';
        $s2->save();

        $s3 = new SuppressionType();
        $s3->name = 'Complaint';
        $s3->save();
    }
}
