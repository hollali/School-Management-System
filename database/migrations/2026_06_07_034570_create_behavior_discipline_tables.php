<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('behavior_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('type')->default('positive');
            $table->integer('points')->default(0);
            $table->text('description')->nullable();
            $table->date('recorded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('discipline_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('action');
            $table->text('notes')->nullable();
            $table->date('action_date')->nullable();
            $table->date('resolved_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discipline_actions');
        Schema::dropIfExists('behavior_records');
    }
};
