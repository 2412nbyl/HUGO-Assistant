<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_documents', function (Blueprint $table) {
            $table->string('id_dok')->primary();
            $table->string('id_kasus')->nullable();
            $table->string('id_klien')->nullable();
            $table->string('id_arsip')->nullable();
            
            $table->string('filename');
            $table->string('filepath');
            $table->string('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_documents');
    }
};
