<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterMigrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('mst_countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamps();
        });

        Schema::create('mst_provinces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->unsignedInteger('country_id');
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('mst_countries')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('mst_districts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->unsignedInteger('province_id');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('mst_provinces')->cascadeOnDelete()->cascadeOnUpdate();
        });

        //schema for super organization
        Schema::create('sup_organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('province_id')->nullable();
            $table->unsignedInteger('district_id')->nullable();

            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('logo')->nullable();
            $table->string('description')->nullable();
            $table->boolean('multiple_barcode')->default(true);
            $table->boolean('is_active')->default(true);

            $table->unsignedSmallInteger('created_by')->nullable();
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('mst_countries')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('province_id')->references('id')->on('mst_provinces')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('district_id')->references('id')->on('mst_districts')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('mst_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->string('address');
            $table->string('email');
            $table->string('phone_no');
            $table->string('logo')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('store_user_id')->nullable();
            $table->unsignedSmallInteger('sup_org_id');

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('store_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('mst_units', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('sup_org_id');
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_super_data')->default(false)->nullable();


            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['sup_org_id','store_id', 'code', 'deleted_uq_code'],'uq_mst_units');
        });

        Schema::table('mst_units', function (Blueprint $table) {
            $table->foreignId('sup_data_id')->nullable()->constrained('mst_units')->onUpdate('cascade')->onDelete('cascade');
        });


        Schema::create('mst_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sup_org_id');
            $table->boolean('is_super_data')->default(false)->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('mst_categories', function (Blueprint $table) {
            $table->foreignId('sup_data_id')->nullable()->constrained('mst_categories')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('mst_discount_modes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',20);
            $table->string('name_en',100);
            $table->string('name_lc',100);
            $table->string('description',1000)->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_super_data')->default(false)->nullable();


            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_uq_code')->nullable()->default(1);
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();



        });

        Schema::create('mst_subcategories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sup_org_id');
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->boolean('is_super_data')->default(false)->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('category_id')->references('id')->on('mst_categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('mst_subcategories', function (Blueprint $table) {
            $table->foreignId('sup_data_id')->nullable()->constrained('mst_subcategories')->onUpdate('cascade')->onDelete('cascade');
        });




        Schema::create('mst_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();

            $table->unsignedInteger('country_id');
            $table->unsignedInteger('province_id');
            $table->unsignedInteger('district_id');
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->string('address');
            $table->string('email');
            $table->string('contact_person');
            $table->string('contact_number');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sup_org_id');
            $table->boolean('is_super_data')->default(false)->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('country_id')->references('id')->on('mst_countries')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('province_id')->references('id')->on('mst_provinces')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('district_id')->references('id')->on('mst_districts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('mst_brands', function (Blueprint $table) {

            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sup_org_id');
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->boolean('is_super_data')->default(false)->nullable();

            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);


            $table->unique(['code', 'deleted_uq_code','sup_org_id'], 'uq_mst_brands_code_deleted_uq_code');
            $table->unique(['name_lc', 'deleted_uq_code','sup_org_id'], 'uq_mst_brands_name_lc_deleted_uq_code');
            $table->unique(['name_en', 'deleted_uq_code','sup_org_id'], 'uq_mst_brands_name_en_deleted_uq_code');

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('mst_brands', function (Blueprint $table) {
            $table->foreignId('sup_data_id')->nullable()->constrained('mst_brands')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('mst_genders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('store_id')->nullable();


            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);


            $table->unique(['code', 'deleted_uq_code'], 'uq_mst_genders_code_deleted_uq_code');
            $table->unique(['name_lc', 'deleted_uq_code'], 'uq_mst_genders_name_lc_deleted_uq_code');
            $table->unique(['name_en', 'deleted_uq_code'], 'uq_mst_genders_name_en_deleted_uq_code');

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();

        });

        Schema::create('mst_positions', function (Blueprint $table) {

            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->boolean('is_super_data')->default(false)->nullable();

            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->unsignedSmallInteger('sup_org_id');


            $table->unique(['code', 'deleted_uq_code'], 'uq_mst_positions_code_deleted_uq_code');
            $table->unique(['name_lc', 'deleted_uq_code'], 'uq_mst_positions_name_lc_deleted_uq_code');
            $table->unique(['name_en', 'deleted_uq_code'], 'uq_mst_positions_name_en_deleted_uq_code');

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('mst_positions', function (Blueprint $table) {
            $table->foreignId('sup_data_id')->nullable()->constrained('mst_positions')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('mst_departments', function (Blueprint $table) {

            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->boolean('is_super_data')->default(false)->nullable();


            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->unsignedSmallInteger('sup_org_id');

            $table->unique(['code', 'deleted_uq_code'], 'uq_mst_departments_code_deleted_uq_code');
            $table->unique(['name_lc', 'deleted_uq_code'], 'uq_mst_departments_name_lc_deleted_uq_code');
            $table->unique(['name_en', 'deleted_uq_code'], 'uq_mst_departments_name_en_deleted_uq_code');
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('mst_relations', function (Blueprint $table) {

            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_super_data')->default(false)->nullable();

            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->unsignedSmallInteger('sup_org_id');
            $table->unsignedSmallInteger('store_id')->nullable();


            $table->unique(['code', 'deleted_uq_code'], 'uq_mst_relations_code_deleted_uq_code');
            $table->unique(['name_lc', 'deleted_uq_code'], 'uq_mst_relations_name_lc_deleted_uq_code');
            $table->unique(['name_en', 'deleted_uq_code'], 'uq_mst_relations_name_en_deleted_uq_code');

            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();

        });
        Schema::create('mst_sequences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->integer('sequence_type');
            $table->string('sequence_code');
            $table->integer('starting_no');
            $table->boolean('is_consumed')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sup_org_id')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->boolean('is_super_data')->default(false)->nullable();
            $table->unsignedSmallInteger('created_by')->nullable();
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('mst_sequences', function (Blueprint $table) {
            $table->foreignId('sup_data_id')->nullable()->constrained('mst_sequences')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sup_status', function (Blueprint $table) {

            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);


            $table->unique(['code','deleted_uq_code'],'uq_sup_status_code_deleted_uq_code');
            $table->unique(['name_lc','deleted_uq_code'],'uq_sup_status_name_lc_deleted_uq_code');
            $table->unique(['name_en','deleted_uq_code'],'uq_sup_status_name_en_deleted_uq_code');
        });

        Schema::create('payment_modes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_super_data')->default(false)->nullable();

            $table->timestamps();

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();

            $table->unsignedSmallInteger('sup_org_id');
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
        });
        Schema::create('return_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name_en');
            $table->string('name_lc')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sup_org_id');
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->boolean('is_super_data')->default(false)->nullable();


            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
        });
        Schema::create('mst_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('barcode_details')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('category_id')->nullable();
            $table->unsignedSmallInteger('subcategory_id')->nullable();
            $table->unsignedSmallInteger('supplier_id');
            $table->unsignedSmallInteger('brand_id');
            $table->unsignedSmallInteger('unit_id');
            $table->float('item_price')->nullable();
            $table->string('stock_alert_minimum')->nullable();
            $table->string('tax_vat')->nullable();
            $table->unsignedSmallInteger('discount_mode_id')->nullable();
            $table->boolean('is_damaged')->default(false);
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_nonclaimable')->default(false);
            $table->boolean('is_staffdiscount')->default(false);
            $table->boolean('is_price_editable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_super_data')->default(false)->nullable();

            $table->unsignedSmallInteger('sup_org_id');
            $table->unsignedSmallInteger('store_id')->nullable();


            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('mst_categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('subcategory_id')->references('id')->on('mst_subcategories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('supplier_id')->references('id')->on('mst_suppliers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('brand_id')->references('id')->on('mst_brands')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('unit_id')->references('id')->on('mst_units')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('discount_mode_id')->references('id')->on('mst_discount_modes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();

        });

        Schema::table('mst_items', function (Blueprint $table) {
            $table->foreignId('sup_data_id')->nullable()->constrained('mst_items')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('mst_item_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->unsignedSmallInteger('item_id')->nullable();

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('item_id')->references('id')->on('mst_items')->cascadeOnDelete()->cascadeOnUpdate();


        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_units');
        Schema::dropIfExists('mst_discount_modes');
        Schema::dropIfExists('mst_categories');
        Schema::dropIfExists('mst_subcategories');
        Schema::dropIfExists('mst_suppliers');
        Schema::dropIfExists('mst_districts');
        Schema::dropIfExists('mst_provinces');
        Schema::dropIfExists('mst_countries');
        Schema::dropIfExists('mst_brands');
        Schema::dropIfExists('mst_genders');
        Schema::dropIfExists('mst_positions');
        Schema::dropIfExists('mst_departments');
        Schema::dropIfExists('mst_relations');
        Schema::dropIfExists('sup_status');
        Schema::dropIfExists('sup_organizations');
        Schema::dropIfExists('mst_sequences');
        Schema::dropIfExists('mst_stores');
        Schema::dropIfExists('return_reasons');
        Schema::dropIfExists('mst_items');
        Schema::dropIfExists('mst_item_stores');
    }
}
