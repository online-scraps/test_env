<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralLedgerOpeningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_ledger_opening', function (Blueprint $table) {
            $table->id();
 	 	 	$table->string('auto_number')->nullable();
 	 	 	$table->float('total_dr_amt')->nullable();
 	 	 	$table->float('total_cr_amt')->nullable();
 	 	 	$table->string('voucher_no')->nullable();
 	 	 	$table->string('voucher_date_bs')->nullable();
 	 	 	$table->date('voucher_date_ad')->nullable();
 	 	 	$table->unsignedSmallInteger('sup_org_id')->nullable();
 	 	 	$table->unsignedSmallInteger('store_id')->nullable();
 	 	 	$table->string('remarks')->nullable();
 	 	 	$table->boolean('is_active')->default(true);
 	 	 	$table->timestamps();

			$table->unsignedSmallInteger('created_by');
			$table->unsignedSmallInteger('updated_by')->nullable();
			$table->unsignedSmallInteger('deleted_by')->nullable();
			$table->unsignedInteger('deleted_uq_code')->default(1);
			$table->timestamp('deleted_at')->nullable();
			
 
 	 	 	$table->foreign('sup_org_id')->references('id')->on('sup_organizations')->onDelete('cascade');
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
        Schema::dropIfExists('general_ledger_opening');
    }
}
