<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performed_by')->constrained('users')->restrictOnDelete();
            $table->string('action');
            $table->text('note')->nullable();
            $table->timestamp('performed_at');
            $table->timestamps();

            $table->index(['case_id', 'performed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_actions');
    }
};
