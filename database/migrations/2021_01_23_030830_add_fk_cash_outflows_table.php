<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkCashOutflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('cash_outflows', function (Blueprint $table) {
            $table->foreign('transaction_id', 'cash_outflows_fk_transactions')->references('id')->on('transactions');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('cash_outflows', function (Blueprint $table) {
            $table->dropForeign('cash_outflows_fk_transactions');
        });*/
    }
}
