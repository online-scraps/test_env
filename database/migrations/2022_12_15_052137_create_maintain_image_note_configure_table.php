<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintainImageNoteConfigureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintain_image_note_configure', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('account_setting_id')->nullable();
            $table->boolean('image_with_account_master')->default(false);
            $table->boolean('note_with_account_master')->default(false);
            $table->integer('account_master_char')->nullable();
            $table->boolean('account_note_in_data_entry')->default(false);
            $table->boolean('image_with_account_voucher')->default(false);
            $table->boolean('note_with_account_voucher')->default(false);
            $table->integer('account_voucher_char')->nullable();

            $table->timestamps();

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('account_setting_id')->references('id')->on('account_settings')->cascadeOnDelete()->cascadeOnUpdate();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintain_image_note_configure');
    }
}
