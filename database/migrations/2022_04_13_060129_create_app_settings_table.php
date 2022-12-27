<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sup_org_id');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->text('header')->nullable();
            $table->string('logo')->nullable();
            $table->string('background')->nullable();
            $table->text('footer')->nullable();
            $table->string('pan_vat')->nullable();
            $table->boolean('display_pan__vat')->default(false);
            $table->boolean('is_active')->default(true);

            $table->foreign('sup_org_id')->references('id')
                ->on('sup_organizations')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')
                ->on('mst_stores')
                ->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('app_settings');
    }
}
