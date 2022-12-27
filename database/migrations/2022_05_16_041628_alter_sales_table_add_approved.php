<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesTableAddApproved extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // $table->string('return_bill_no')->nullable();
            $table->boolean('approved_by')->nullable();
            // $table->boolean('is_returned')->nullable();
            // $table->boolean('return_type')->nullable();
            // $table->integer('return_qty')->nullable();
            // $table->float('return_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            //
            // $table->dropColumn('return_bill_no');
            $table->dropColumn('approved_by');
            // $table->dropColumn('is_returned');
            // $table->dropColumn('return_type');
            // $table->dropColumn('return_qty');
            // $table->dropColumn('return_amount');
        });
    }
}
