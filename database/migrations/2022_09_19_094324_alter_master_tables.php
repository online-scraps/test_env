<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_departments', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('mst_departments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('mst_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('category_id')->change()->nullable();
            $table->unsignedSmallInteger('subcategory_id')->change()->nullable();
            $table->unsignedSmallInteger('asset_type_id')->nullable();
            $table->boolean('is_fixed_asset')->default(false);
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('mst_departments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('sub_department_id')
                ->nullable()
                ->constrained('mst_departments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_departments', function (Blueprint $table) {
            $table->dropForeign('parent_id');
        });

        Schema::table('mst_items', function (Blueprint $table) {
            $table->dropForeign(['department_id', 'sub_department_id']);
        });
    }
}
