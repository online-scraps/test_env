<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_ledgers', function (Blueprint $table) {
            $table->id();

            // first tab
            $table->float('budget_title_no')->nullable();
            $table->string('name',250);
            $table->integer('accounting_code')->nullable();
            $table->smallInteger('levels')->nullable();
            $table->smallInteger('category')->nullable();
            $table->smallInteger('station')->nullable();
            $table->boolean('is_ledger')->default(0);
            $table->boolean('is_subledger')->default(0);
            $table->bigInteger('gl_code')->nullable();
            $table->string('cashflow_classification',250)->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_active')->default(1);

            $table->unsignedSmallInteger('parent_id')->nullable();
            $table->unsignedSmallInteger('user_id')->nullable();
            $table->string('code',250)->nullable();

            // not used
            $table->string('serial_no',250)->nullable();
            $table->string('url',250)->nullable();
            
            // second tab
            $table->unsignedSmallInteger('country_id')->nullable();
            $table->unsignedSmallInteger('province_id')->nullable();
            $table->unsignedSmallInteger('district_id')->nullable();
            $table->string('city',250)->nullable();
            $table->string('address',250)->nullable();
            $table->string('phone_no',20)->nullable();
            $table->string('fax_no',250)->nullable();
            $table->string('pan_no',250)->nullable();
            $table->string('identity_no',250)->nullable();
            $table->string('website',250)->nullable();
            $table->string('email',250)->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('contact_person_no')->nullable();
            $table->string('contact_person_fax_no')->nullable();

            // third tab
            $table->text('photo')->nullable();
            $table->text('signature')->nullable();

            $table->timestamps();
            
            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['gl_code','deleted_uq_code'], 'uq_general_ledgers_gl_code_deleted_uq_code');

            $table->foreign('parent_id')->references('id')->on('general_ledgers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('province_id')->references('id')->on('mst_provinces')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('district_id')->references('id')->on('mst_districts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('country_id')->references('id')->on('mst_countries')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('sub_ledgers', function (Blueprint $table) {
            $table->id();

            $table->string('name',250);
            $table->string('code',250)->nullable();
            $table->unsignedBigInteger('general_ledger_id')->nullable();
            $table->string('address',250)->nullable();
            $table->string('phone',250)->nullable();
            $table->string('mobile',250)->nullable();
            $table->string('email',250)->nullable();
            $table->string('pan_no',250)->nullable();
            $table->string('station',250)->nullable();

            $table->timestamps();
            
            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('general_ledger_id')->references('id')->on('general_ledgers')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_ledgers');
        Schema::dropIfExists('sub_ledgers');
    }
}
