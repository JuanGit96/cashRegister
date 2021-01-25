<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashOutflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('cash_outflows', function (Blueprint $table) {
            $table->id();
            $table->integer('fifty_cop');
            $table->integer('hundred_cop');
            $table->integer('two_hundred_cop');
            $table->integer('five_hundred_cop');
            $table->integer('One_thousand_cop');
            $table->integer('two_thousand_cop');
            $table->integer('five_thousand_cop');
            $table->integer('ten_thousand_cop');
            $table->integer('twenty_thousand_cop');
            $table->integer('fifty_thousand_cop');
            $table->integer('one_hundred_thousand_cop');
            $table->unsignedBigInteger('transaction_id');
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_outflows');
    }
}
