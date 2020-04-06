<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLdapNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ldap_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('object_id');
            $table->timestamps();
            $table->string('watchdog');
            $table->string('notification');
            $table->string('channels');
            $table->boolean('sent');
            $table->longText('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ldap_notifications');
    }
}
