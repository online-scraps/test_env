<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMstSuppliersTableAddCustomerDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_suppliers', function (Blueprint $table) {
            $table->boolean('is_customer')->nullable()->default(false);
            $table->boolean('is_coorporate')->nullable()->default(false);
            $table->integer('pan_no')->nullable();
            $table->string('contact_person')->change()->nullable();
            $table->renameColumn('contact_person', 'company_name');
            $table->unsignedInteger('country_id')->change()->nullable();
            $table->unsignedInteger('province_id')->change()->nullable();
            $table->unsignedInteger('district_id')->change()->nullable();
            $table->string('email')->change()->nullable();
        });

        DB::statement('ALTER TABLE sales ALTER COLUMN full_name TYPE INT USING full_name::smallint');

        Schema::table('sales', function (Blueprint $table) {
            $table->renameColumn('full_name', 'customer_id');
            $table->foreign('customer_id')->references('id')->on('mst_suppliers')->onUpdate('cascade')->onDelete('cascade');
            $table->dropColumn('gender_id');
            $table->dropColumn('age');
            $table->dropColumn('address');
            $table->dropColumn('contact_number');
            $table->dropColumn('pan_vat');
            $table->dropColumn('company_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_suppliers', function($table) {
            $table->dropColumn('is_customer');
            $table->dropColumn('is_coorporate');
            $table->dropColumn('pan_no');
            $table->renameColumn('company_name', 'contact_person');
        });

        DB::statement('ALTER TABLE sales ALTER COLUMN full_name TYPE VARCHAR(255) USING full_name::VARCHAR(255)');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign('customer_id');
            $table->renameColumn('customer_id', 'full_name');

            $table->smallInteger('gender_id')->nullable();
            $table->integer('age')->nullable();
            $table->string('address', 200)->nullable();
            $table->string('contact_number')->nullable();
            $table->string('pan_vat')->nullable();
            $table->string('company_name')->nullable();
        });
    }
}
