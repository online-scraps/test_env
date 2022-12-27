<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCurrenciesTableForAllCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {
            // $table->id();
 	 	 	$table->string('code')->nullable();
            $table->string('symbol')->nullable();
            $table->string('currency')->nullable();
            $table->string('sub_currency')->nullable();
            $table->string('no_format')->nullable();
            $table->string('decimal_places')->nullable();

 	 	 	$table->unsignedSmallInteger('sup_org_id')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();

 	 	 	$table->boolean('is_active')->default(true);
 	 	 	$table->timestamps();

			$table->unsignedSmallInteger('created_by')->nullable();
			$table->unsignedSmallInteger('updated_by')->nullable();
			$table->unsignedSmallInteger('deleted_by')->nullable();
			$table->unsignedInteger('deleted_uq_code')->default(1);
			$table->timestamp('deleted_at')->nullable();

            $table->foreign('store_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
 	 	 	$table->foreign('sup_org_id')->references('id')->on('sup_organizations')->onDelete('cascade');
        });


        Schema::create('currency_conversion', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->unsignedSmallInteger('sup_org_id')->nullable();
            $table->unsignedSmallInteger('currency_tyoe_id')->nullable();
            $table->float('standard_rate')->nullable();
            $table->float('selling_rate')->nullable();
            $table->float('buying_rate')->nullable();


            
            $table->timestamps();

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();

        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            //
            $table->dropColumn('code');
            $table->dropColumn('symbol');
            $table->dropColumn('currency');
            $table->dropColumn('sub_currency');
            $table->dropColumn('no_format');
            $table->dropColumn('decimal_places');
 	 	 	$table->dropColumn('sup_org_id');
            $table->dropColumn('store_id');
 	 	 	$table->dropColumn('is_active');
			$table->dropColumn('created_by');
			$table->dropColumn('updated_by');
			$table->dropColumn('deleted_by');
			$table->dropColumn('deleted_uq_code');
			$table->dropColumn('deleted_at');
        });

        Schema::dropIfExists('currency_conversion');
    }
}
