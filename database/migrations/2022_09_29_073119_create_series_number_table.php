<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeriesNumberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('series_number', function (Blueprint $table) {
            $table->id();
            $table->text('description')->unique()->nullable();
            $table->integer('terminal_id')->nullable();
            $table->string('starting_word')->nullable();
            $table->integer('starting_no')->nullable();
            $table->string('ending_word')->nullable();
            $table->integer('padding_length')->nullable();
            $table->integer('padding_no')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreignId('fiscal_year_id')
                ->nullable()
                ->constrained('mst_fiscal_year')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('sup_org_id')
                ->nullable()
                ->constrained('sup_organizations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('store_id')
                ->nullable()
                ->constrained('mst_stores')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('series_number');
    }
}
