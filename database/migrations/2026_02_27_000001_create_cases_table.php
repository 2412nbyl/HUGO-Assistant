<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->string('id_kasus')->primary();
            $table->string('id_klien')->nullable();
            $table->string('id_user')->nullable(); // mapped from created_by logic
            $table->string('id_dok')->nullable();
            
            $table->string('client_name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('case_name');
            $table->enum('type', ['PT', 'CV', 'Pribadi']);
            $table->enum('status', ['proses', 'selesai', 'tertunda'])->default('proses');
            $table->date('deadline');
            $table->decimal('nominal_bayar', 15, 2)->default(0);
            
            // File paths (nullable)
            $table->string('file_ktp')->nullable();
            $table->string('file_npwp')->nullable();
            $table->string('file_kk')->nullable();
            $table->string('file_surat_tanah')->nullable();
            $table->string('file_surat_perintah')->nullable();
            $table->string('file_buku_nikah')->nullable();
            
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
