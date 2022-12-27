<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMstVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_vouchers', function (Blueprint $table) {
            $table->string('image_with_account_master')->nullable();
            $table->string('note_with_account_master')->nullable();
            $table->string('image_with_account_voucher')->nullable();
            $table->string('note_with_account_voucher')->nullable();
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
