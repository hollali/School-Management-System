<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('start_point')->nullable();
            $table->string('end_point')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->nullable()->constrained('routes')->cascadeOnDelete();
            $table->string('plate_number')->unique();
            $table->string('driver_name')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('student_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->string('pickup_point')->nullable();
            $table->string('drop_off_point')->nullable();
            $table->boolean('active')->default(true);
            $table->date('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'route_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_routes');
        Schema::dropIfExists('buses');
        Schema::dropIfExists('routes');
    }
};
