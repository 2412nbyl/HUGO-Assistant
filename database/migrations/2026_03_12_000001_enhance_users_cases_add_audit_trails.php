<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance users: soft delete + is_active
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 2. Enhance cases: birthday, nominal, extra doc
        Schema::table('cases', function (Blueprint $table) {
            if (!Schema::hasColumn('cases', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('address');
            }
            if (!Schema::hasColumn('cases', 'nominal_bayar')) {
                $table->unsignedBigInteger('nominal_bayar')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('cases', 'file_buku_nikah')) {
                $table->string('file_buku_nikah')->nullable()->after('file_surat_perintah');
            }
        });

        // 3. Create audit_trails
        if (!Schema::hasTable('audit_trails')) {
            Schema::create('audit_trails', function (Blueprint $table) {
                $table->id();
                $table->string('user_id')->nullable();
                $table->string('table_name');
                $table->string('record_id');
                $table->string('action');          // created, updated, deleted, status_changed
                $table->json('old_value')->nullable();
                $table->json('new_value')->nullable();
                $table->timestamps();

                $table->index(['table_name', 'record_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('is_active');
        });
        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'nominal_bayar', 'file_buku_nikah']);
        });
        Schema::dropIfExists('audit_trails');
    }
};
