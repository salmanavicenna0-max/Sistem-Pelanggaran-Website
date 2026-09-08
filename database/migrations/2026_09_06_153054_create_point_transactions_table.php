<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('school_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('reference');
            $table->enum('type', ['initial_balance', 'violation', 'achievement', 'correction', 'reversal']);
            $table->integer('points');
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->text('description')->nullable();
            $table->timestamp('transacted_at');
            $table->timestamps();

            $table->index(['student_id', 'school_year_id', 'transacted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
