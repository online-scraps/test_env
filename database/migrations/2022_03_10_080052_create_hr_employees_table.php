<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name',200);
            $table->date('date_ad')->nullable();
            $table->string('date_bs')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('contact_number')->nullable();
            $table->string('photo');
            $table->bigInteger('pan_number')->nullable();
            $table->string('pan_photo_upload')->nullable();
            $table->bigInteger('citizenship_number')->nullable();
            $table->string('citizenship_file_upload')->nullable();
            $table->bigInteger('national_identity_number')->nullable();
            $table->string('national_identity_file_upload')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();


            // $table->json('contact_person_details')->nullable();

           //contact person details migration

            $table->string('person_full_name')->nullable();
            $table->string('person_email')->nullable();
            $table->bigInteger('person_contact_number')->nullable();
            $table->string('person_address')->nullable();
            $table->bigInteger('person_citizenship_number')->nullable();
            $table->string('person_citizenship_photo_upload')->nullable();
            $table->boolean('is_active')->default(true);

            //end person details migration

            $table->timestamps();

            $table->unsignedBigInteger('gender_id');
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('relation_id')->nullable();

            $table->foreign('gender_id')->references('id')->on('mst_genders')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('position_id')->references('id')->on('mst_positions')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('department_id')->references('id')->on('mst_departments')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('province_id')->references('id')->on('mst_provinces')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('district_id')->references('id')->on('mst_districts')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('country_id')->references('id')->on('mst_countries')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('relation_id')->references('id')->on('mst_relations')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('store_id')->references('id')->on('mst_stores')
            ->cascadeOnDelete()->cascadeOnUpdate();



            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->nullable()->default(1);

            
            $table->unique(['full_name','deleted_uq_code'],'uq_hr_employees_full_name_deleted_uq_code');
            $table->unique(['email','deleted_uq_code'],'uq_hr_employees_email_deleted_uq_code');
            $table->unique(['pan_number','deleted_uq_code'],'uq_hr_pan_number_en_deleted_uq_code');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_employees');
    }
}
