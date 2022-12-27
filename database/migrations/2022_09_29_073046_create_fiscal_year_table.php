<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiscalYearTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('mst_fiscal_year');

        Schema::create('mst_fiscal_year', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->timestamps();
            $table->string('code',20);
            $table->string('from_date_bs',10)->nullable();
            $table->date('from_date_ad')->nullable();
            $table->string('to_date_bs',10)->nullable();
            $table->date('to_date_ad')->nullable();
            $table->string('remarks',500)->nullable();
            $table->foreignId('created_by')
            ->nullable()
            ->constrained('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

        $table->foreignId('updated_by')
            ->nullable()
            ->constrained('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->unique('code','uq_mst_fiscal_year_code');
            $table->unique('from_date_bs','uq_mst_fiscal_year_from_date_bs');
            $table->unique('from_date_ad','uq_mst_fiscal_year_from_date_ad');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_fiscal_year');
    }
}
