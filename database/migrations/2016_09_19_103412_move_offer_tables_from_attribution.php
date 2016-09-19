<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveOfferTablesFromAttribution extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $from = env('ATTR_DB_DATABASE', '');
        $to = env('DB_DATABASE', 'forge');
        DB::statement("ALTER TABLE {$from}.offer_payout_types RENAME {$to}.offer_payout_types");
        DB::statement("ALTER TABLE {$from}.offer_payouts RENAME {$to}.offer_payouts");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $to = env('ATTR_DB_DATABASE', '');
        $from = env('DB_DATABASE', 'forge');
        DB::statement("ALTER TABLE {$from}.offer_payout_types RENAME {$to}.offer_payout_types");
        DB::statement("ALTER TABLE {$from}.offer_payouts RENAME {$to}.offer_payouts");
    }
}
