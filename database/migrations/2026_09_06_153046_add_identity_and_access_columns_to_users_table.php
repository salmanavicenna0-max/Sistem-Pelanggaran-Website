<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('nip')->nullable()->unique()->after('username');
            $table->enum('role', ['kesiswaan_bk', 'guru', 'siswa'])->default('siswa')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
            $table->boolean('is_first_login')->default(true)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'nip', 'role', 'is_active', 'is_first_login']);
        });
    }
};
