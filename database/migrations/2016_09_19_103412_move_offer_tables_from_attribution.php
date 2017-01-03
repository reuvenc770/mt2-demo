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
        $from = config('database.connections.attribution.database');
        $to = config('database.connections.mysql.database');

        DB::statement("ALTER TABLE {$from}.offer_payout_types RENAME {$to}.offer_payout_types");
        DB::statement("ALTER TABLE {$from}.offer_payouts RENAME {$to}.offer_payouts");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $to = config('database.connections.attribution.database');
        $from = config('database.connections.mysql.database');

        DB::statement("ALTER TABLE {$from}.offer_payout_types RENAME {$to}.offer_payout_types");
        DB::statement("ALTER TABLE {$from}.offer_payouts RENAME {$to}.offer_payouts");
    }
}
