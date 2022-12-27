<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleHasMenuAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_has_menu_access', function (Blueprint $table) {
            $table->unsignedSmallInteger('role_id');
            $table->unsignedSmallInteger('user_id');
            $table->unsignedSmallInteger('menu_item_id');
            $table->unsignedSmallInteger('display_order');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('depth')->unsigned()->nullable();
            $table->timestamps();

            $table->primary(['user_id','menu_item_id'], 'role_has_menu_access_user_id_menu_item_id_primary');
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_has_menu_access');
    }
}
