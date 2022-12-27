<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_vouchers', function (Blueprint $table) {
            $table->id();

            $table->integer('auto_no');
            $table->string('voucher_no')->nullable();
            $table->date('voucher_date_ad',)->nullable();
            $table->string('voucher_date_bs',10)->nullable();
            $table->string('type',225)->nullable();
            $table->string('cheque_no',225)->nullable();
            $table->date('cheque_date_ad')->nullable();
            $table->string('cheque_date_bs',225)->nullable();
            $table->string('pay_to')->nullable();
            $table->float('net_amount')->nullable();
            $table->string('audit')->nullable();
            $table->string('export')->nullable();
            $table->string('posting')->nullable();
            $table->date('effected_date_ad')->nullable();
            $table->string('effected_date_bs')->nullable();
            $table->string('ref_voucher_no')->nullable();
            $table->date('ref_voucher_date_ad')->nullable();
            $table->string('ref_voucher_date_bs')->nullable();
            $table->string('ref_bill_no')->nullable();
            $table->date('ref_bill_date_ad')->nullable();
            $table->string('ref_bill_date_bs')->nullable();
            $table->string('checked_by')->nullable();
            $table->date('checked_date_ad')->nullable();
            $table->string('checked_date_bs')->nullable();
            $table->string('audit_by')->nullable();
            $table->date('audit_date_ad')->nullable();
            $table->string('audit_date_bs')->nullable();
            $table->string('approved_by')->nullable();
            $table->date('approved_date_ad')->nullable();
            $table->string('approved_date_bs')->nullable();
            $table->string('posted_by')->nullable();
            $table->date('posted_date_ad')->nullable();
            $table->string('posted_date_bs')->nullable();
            $table->string('cleared_by')->nullable();
            $table->date('cleared_date_ad')->nullable();
            $table->string('cleared_date_bs')->nullable();
            $table->string('station')->nullable();
            $table->text('narration')->nullable();
            $table->float('total_dr_amount')->nullable();
            $table->float('total_cr_amount')->nullable();
            
            $table->timestamps();

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('voucher_details', function (Blueprint $table) {
            $table->id();

            $table->integer('sno')->nullable();
            $table->unsignedSmallInteger('dr_cr')->nullable();
            $table->unsignedSmallInteger('voucher_id')->nullable();
            $table->unsignedSmallInteger('general_ledger_id')->nullable();
            $table->unsignedSmallInteger('sub_ledger_id')->nullable();
            $table->float('dr_amount')->nullable();
            $table->float('cr_amount')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('voucher_id')->references('id')->on('mst_vouchers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('general_ledger_id')->references('id')->on('general_ledgers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sub_ledger_id')->references('id')->on('general_ledgers')->cascadeOnDelete()->cascadeOnUpdate();
        });

        // this table name is changed into account_transactions
        Schema::create('voucher_master', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('refrence_id')->nullable();
            $table->string('voucher_no')->nullable();
            $table->date('voucher_date_ad')->nullable();
            $table->string('voucher_date_bs')->nullable();
            $table->unsignedSmallInteger('general_ledger_id')->nullable();
            $table->unsignedSmallInteger('sub_ledger_id')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date_ad')->nullable();
            $table->string('cheque_date_bs')->nullable();
            $table->float('dr_amount')->nullable();
            $table->float('cr_amount')->nullable();
            $table->string('station')->nullable();
            $table->text('narration')->nullable();

            $table->timestamps();

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('refrence_id')->references('id')->on('mst_vouchers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('general_ledger_id')->references('id')->on('general_ledgers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sub_ledger_id')->references('id')->on('general_ledgers')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('mst_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('brand_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_master');
    }
}
