<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('mst_vouchers', function (Blueprint $table) {
            $table->integer('auto_no')->nullable()->change();

            $table->unsignedSmallInteger('sup_org_id')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->unsignedSmallInteger('currency_id')->nullable();
            $table->unsignedSmallInteger('series_no_id')->nullable();

            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('currency_id')->references('id')->on('currencies')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('series_no_id')->references('id')->on('series_number')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('voucher_details', function (Blueprint $table) {
            $table->dropForeign('voucher_details_general_ledger_id_foreign');
            $table->dropForeign('voucher_details_sub_ledger_id_foreign');
            
            $table->foreign('general_ledger_id')->references('id')->on('charts_of_accounts')->change()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sub_ledger_id')->references('id')->on('charts_of_accounts')->change()->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('voucher_master', function (Blueprint $table) {
            $table->dropForeign('voucher_master_general_ledger_id_foreign');
            $table->dropForeign('voucher_master_sub_ledger_id_foreign');
        });

        Schema::rename('voucher_master', 'account_transactions');
        
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->unsignedSmallInteger('series_no_id')->nullable();

            $table->foreign('series_no_id')->references('id')->on('series_number')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('general_ledger_id')->references('id')->on('charts_of_accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sub_ledger_id')->references('id')->on('charts_of_accounts')->cascadeOnDelete()->cascadeOnUpdate();
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
