<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id');
            $table->string('receiver_id')->nullable();
            $table->text('message');
            $table->enum('type', ['message', 'request', 'system', 'approval'])->default('message');
            $table->json('meta')->nullable(); // Extra data (e.g. temp password for request type)
            $table->boolean('is_approved')->nullable(); // For request type
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
