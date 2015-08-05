<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Phonex\Model\NotificationType;

class NotificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('type');
        });

        Schema::create('notification_type_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('notification_type_id')->unsigned();
            $table->string('locale')->index();
            // translated strings
            $table->text('text');
            // fk
            $table->unique(['notification_type_id','locale'], 'notification_type_trans_nti_locale');
            $table->foreign('notification_type_id')->references('id')->on('notification_types')->onDelete('cascade');
        });

        NotificationType::create(['type' => 'welcome_message']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notification_types');
        Schema::drop('notification_type_translations');
    }
}
