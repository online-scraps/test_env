<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralLedgerOpeningDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_ledger_opening_detail', function (Blueprint $table) {
            $table->id();
 	 	 	$table->string('code')->nullable();
 	 	 	$table->float('dr_amt')->nullable();
 	 	 	$table->float('cr_amt')->nullable();
 	 	 	$table->unsignedSmallInteger('ledger_id')->nullable();
 	 	 	$table->unsignedSmallInteger('sub_ledger_id')->nullable();
 	 	 	$table->unsignedSmallInteger('sup_org_id')->nullable();
 	 	 	$table->unsignedSmallInteger('glob_id')->nullable();
 	 	 	$table->unsignedSmallInteger('store_id')->nullable();
 	 	 	$table->string('item_remarks')->nullable();
 	 	 	$table->boolean('is_active')->default(true);
 	 	 	$table->timestamps();

			$table->unsignedSmallInteger('created_by')->nullable();
			$table->unsignedSmallInteger('updated_by')->nullable();
			$table->unsignedSmallInteger('deleted_by')->nullable();
			$table->unsignedInteger('deleted_uq_code')->default(1);
			$table->timestamp('deleted_at')->nullable();
			
 
 	 	 	$table->foreign('ledger_id')->references('id')->on('general_ledgers')->onDelete('cascade');
 	 	 	$table->foreign('sub_ledger_id')->references('id')->on('sub_ledgers')->onDelete('cascade');
 	 	 	$table->foreign('sup_org_id')->references('id')->on('sup_organizations')->onDelete('cascade');
 	 	 	$table->foreign('glob_id')->references('id')->on('general_ledger_opening')->onDelete('cascade');
 	 	 	$table->foreign('store_id')->references('id')->on('mst_stores')->onDelete('cascade');
 	 	 	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_ledger_opening_detail');
    }
}
