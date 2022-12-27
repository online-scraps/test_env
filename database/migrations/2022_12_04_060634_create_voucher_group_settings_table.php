<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherGroupSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_group_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('sup_org_id')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();
            
            $table->json('voucher_id')->nullable();
            $table->json('dr_cr')->nullable();
            $table->json('group_id')->nullable();

            $table->timestamps();

			$table->unsignedSmallInteger('created_by');
			$table->unsignedSmallInteger('updated_by')->nullable();
			$table->unsignedSmallInteger('deleted_by')->nullable();
			$table->unsignedInteger('deleted_uq_code')->default(1);
			$table->timestamp('deleted_at')->nullable();
			
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            // $table->foreign('group_id')->references('id')->on('charts_of_accounts')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_group_settings');
    }
}
