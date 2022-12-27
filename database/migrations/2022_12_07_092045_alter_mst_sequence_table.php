<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMstSequenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_sequences', function (Blueprint $table) {
            $table->dropColumn('starting_no');
            $table->dropColumn('sup_data_id');
            $table->dropColumn('is_consumed');
        });

        Schema::table('batch_qty_detail', function (Blueprint $table) {
            $table->dropColumn('batch_no');
        });

        Schema::table('batch_qty_detail', function (Blueprint $table) {
            $table->foreignId('batch_no')->nullable()->constrained('mst_sequences')->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('bill_no');
            $table->dropColumn('return_bill_no');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('bill_no')->nullable()->constrained('mst_sequences')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('return_bill_no')->nullable()->constrained('mst_sequences')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_sequences', function (Blueprint $table) {
            $table->integer('starting_no');
            $table->boolean('is_consumed');
            $table->foreignId('sup_data_id')->nullable()->constrained('mst_sequences')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('batch_qty_detail', function (Blueprint $table) {
            $table->dropForeign('batch_no');
            $table->tinyText('batch_no')->nullable();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('bill_no')->nullable();
            $table->string('return_bill_no')->nullable();
        });
    }
}
