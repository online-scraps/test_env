<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterChartsOfAccountsTableAddLedgerType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charts_of_accounts', function (Blueprint $table) {
            $table->integer('ledger_type')->nullable();
            $table->unsignedBigInteger('ledger_id')->nullable();

            $table->foreign('ledger_id')->references('id')->on('charts_of_accounts')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('mst_vouchers', function (Blueprint $table) {
            $table->string('voucher_image')->nullable();
            $table->string('voucher_note')->nullable();

            $table->string('account_image')->nullable();
            $table->string('account_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
