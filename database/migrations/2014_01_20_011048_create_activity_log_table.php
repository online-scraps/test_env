<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        Schema::connection('pgsql2')->dropIfExists('activity_log');
        Schema::connection('pgsql2')->dropIfExists('session_logs');

        Schema::connection('pgsql2')->create('session_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('user_id')->nullable();
            $table->string('username')->nullable();
            $table->string('session_history_id')->nullable();
            $table->string('session_name')->nullable();
            $table->string('user_ip')->nullable();
            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('user_email')->nullable();
            $table->string('login_date')->nullable();
            $table->string('login_time')->nullable();
            $table->boolean('is_currently_logged_in')->nullable();
            $table->string('logout_time')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::connection('pgsql2')->create('activity_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('session_id')->nullable();
            $table->string('activity_name')->nullable();
            $table->string('activity_type')->nullable();
            $table->string('activity_time')->nullable();
            $table->date('activity_date_ad')->nullable();
            $table->string('activity_date_bs')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('request_method')->nullable();
            $table->text('url_query_string')->nullable();
            $table->string('url_response')->nullable();
            $table->string('status')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        // Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name'));
        Schema::connection('pgsql2')->dropIfExists('session_logs');
        Schema::connection('pgsql2')->dropIfExists('activity_log');
    }
}
