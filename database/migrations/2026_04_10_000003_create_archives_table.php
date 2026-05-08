<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->string('id_arsip', 20)->primary();
            $table->string('id_dok')->nullable();
            $table->string('id_akta')->nullable();
            $table->string('id_kasus')->nullable();
            $table->string('id_klien')->nullable();
            $table->string('client_name');
            $table->string('folder_location');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('archives');
    }
};
