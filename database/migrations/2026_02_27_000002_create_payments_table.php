<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->string('id_transaksi')->primary();
            $table->string('id_kasus')->nullable();
            $table->string('amount')->nullable(); // e.g., "Rp 15.000.000"
            $table->enum('status', ['lunas', 'sebagian', 'belum'])->default('belum');
            $table->timestamps();
        });

        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id'); // FK to id_transaksi
            $table->string('from_status');
            $table->string('to_status');
            $table->integer('amount_paid')->default(0); 
            $table->string('note')->nullable();
            $table->string('changed_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
        Schema::dropIfExists('payments');
    }
};
