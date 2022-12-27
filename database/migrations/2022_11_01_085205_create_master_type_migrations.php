<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterTypeMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_type_master', function (Blueprint $table) {
            $table->id();

            $table->string('purchase_type')->nullable();

            //Purchase Account Information
            $table->smallInteger('purchase_ac_info')->nullable();

            //Taxation Type
            $table->smallInteger('taxation_type')->nullable();

            //Other Information
            $table->boolean('tax_invoice')->default(true);
            $table->boolean('skip_vat')->default(false);

            //Region
            $table->smallInteger('region')->nullable();

            //Form Information
            $table->boolean('issue_st_form')->nullable();
            $table->boolean('form_issubale')->nullable();
            $table->boolean('receive_st_form')->nullable();
            $table->boolean('form_receivable')->nullable();

            //Tax Calculation
            $table->smallInteger('tax_calculation')->nullable();

            $table->string('tax_percent')->nullable();
            $table->string('surcharge_percent')->nullable();
            $table->string('cess_percent')->nullable();

            $table->boolean('freeze_tax_purchase')->nullable();
            $table->boolean('freeze_tax_purchase_returns')->nullable();

            //For Printing in Documents
            $table->string('inv_heading')->nullable();
            $table->string('inv_description')->nullable();

            //Default Columns (Must Add)
			$table->unsignedInteger('deleted_uq_code')->default(1);
			$table->timestamp('deleted_at')->nullable();
            $table->foreignId('sup_org_id')->nullable()->constrained('sup_organizations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained('mst_stores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('sales_type_master', function (Blueprint $table) {
            $table->id();

            $table->string('sales_type')->nullable();

            //Purchase Account Information
            $table->smallInteger('sales_ac_info')->nullable();

            //Taxation Type
            $table->smallInteger('taxation_type')->nullable();

            //Other Information
            $table->boolean('tax_invoice')->default(true);
            $table->boolean('skip_vat')->default(false);

            //Region
            $table->smallInteger('region')->nullable();

            //Form Information
            $table->boolean('issue_st_form')->nullable();
            $table->boolean('form_issubale')->nullable();
            $table->boolean('receive_st_form')->nullable();
            $table->boolean('form_receivable')->nullable();

            //Tax Calculation
            $table->smallInteger('tax_calculation')->nullable();

            $table->string('tax_percent')->nullable();
            $table->string('surcharge_percent')->nullable();
            $table->string('cess_percent')->nullable();

            $table->boolean('freeze_tax_sales')->nullable();
            $table->boolean('freeze_tax_sales_returns')->nullable();

            //For Printing in Documents
            $table->string('inv_heading')->nullable();
            $table->string('inv_description')->nullable();

            //Default Columns (Must Add)
			$table->unsignedInteger('deleted_uq_code')->default(1);
			$table->timestamp('deleted_at')->nullable();
            $table->foreignId('sup_org_id')->nullable()->constrained('sup_organizations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained('mst_stores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_type_master');
        Schema::dropIfExists('sales_type_master');
    }
}
