<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkCashInflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('cash_inflows', function (Blueprint $table) {
            $table->foreign('transaction_id', 'cash_inflows_fk_transactions')->references('id')->on('transactions');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('cash_inflows', function (Blueprint $table) {
            $table->dropForeign('cash_inflows_fk_transactions');
        });*/
    }
}
