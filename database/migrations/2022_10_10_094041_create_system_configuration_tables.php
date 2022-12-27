<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemConfigurationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_configurations', function (Blueprint $table) {
            $table->id();

            //General
            $table->integer('currency_type_id')->unsigned()->nullable();

            $table->string('date_ad')->nullable();
            $table->string('date_bs')->nullable();
            $table->float('currency')->nullable();
            $table->float('quantity')->nullable();
            $table->float('amount')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('checked_by_position')->nullable();
            $table->string('approved_by_position')->nullable();
            $table->string('prepared_by_position')->nullable();

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


            $table->foreignId('fiscal_year_id')
                ->nullable()
                ->constrained('mst_fiscal_year')
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

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('prepared_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('checked_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');


                // Account
            $table->integer('budget_type_id')->unsigned()->nullable();
            $table->integer('cash_type_id')->unsigned()->nullable();

            $table->boolean('code_wise_transcation')->nullable()->default(false);
            $table->boolean('transcation_negative')->nullable()->default(false);
            $table->json('entry_controls_options')->nullable();
            $table->json('entry_controls_mandatory')->nullable();

            $table->string('font_name')->nullable();
            $table->integer('font_size')->unsigned()->nullable();
            $table->integer('paper_size')->unsigned()->nullable();
            $table->boolean('printing_date')->nullable()->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_configurations');
    }
}
