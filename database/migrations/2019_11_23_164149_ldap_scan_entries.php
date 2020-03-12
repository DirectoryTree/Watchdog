<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LdapScanEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ldap_scan_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('scan_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
            $table->timestamp('ldap_updated_at')->nullable();
            $table->string('guid');
            $table->string('name');
            $table->string('dn');
            $table->string('type')->nullable();
            $table->longText('values')->nullable();
            $table->boolean('processed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ldap_scan_entries');
    }
}
