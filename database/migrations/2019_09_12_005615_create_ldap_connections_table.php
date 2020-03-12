<?php

use DirectoryTree\Watchdog\LdapConnection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLdapConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ldap_connections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->timestamp('attempted_at')->nullable();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('model');
            $table->tinyInteger('status')->default(LdapConnection::STATUS_OFFLINE);
            $table->tinyInteger('type')->default(LdapConnection::TYPE_ACTIVE_DIRECTORY);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ldap_connections');
    }
}
