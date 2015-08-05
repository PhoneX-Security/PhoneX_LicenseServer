<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupportNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('sent_at')->nullable();

            // fk users
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('notification_type_id')->unsigned();
            $table->foreign('notification_type_id')->references('id')->on('notification_types');

            $table->integer('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('support_notifications');
    }
}
