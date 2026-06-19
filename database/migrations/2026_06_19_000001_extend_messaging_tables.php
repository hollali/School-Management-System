<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('group_type')->nullable()->after('is_group');
            $table->foreignId('class_id')->nullable()->after('group_type')->constrained('classes')->nullOnDelete();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('conversation_id')->constrained('messages')->nullOnDelete();
            $table->foreignId('forwarded_from')->nullable()->after('parent_id')->constrained('messages')->nullOnDelete();
            $table->string('type', 20)->default('text')->after('body');
            $table->string('file_path')->nullable()->after('type');
            $table->string('file_name')->nullable()->after('file_path');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
            $table->string('file_mime')->nullable()->after('file_size');
            $table->json('metadata')->nullable()->after('file_mime');
            $table->timestamp('edited_at')->nullable()->after('read_at');
            $table->softDeletes()->after('updated_at');
        });

        Schema::create('message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reaction', 50);
            $table->timestamps();
            $table->unique(['message_id', 'user_id', 'reaction']);
        });

        Schema::create('message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->unique(['message_id', 'user_id']);
        });

        Schema::table('conversation_user', function (Blueprint $table) {
            $table->string('role', 20)->default('member')->after('user_id');
            $table->timestamp('last_read_at')->nullable()->after('role');
            $table->boolean('is_archived')->default(false)->after('last_read_at');
            $table->boolean('is_pinned')->default(false)->after('is_archived');
            $table->boolean('notifications_enabled')->default(true)->after('is_pinned');
            $table->timestamp('joined_at')->nullable()->after('notifications_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('conversation_user', function (Blueprint $table) {
            $table->dropColumn(['role', 'last_read_at', 'is_archived', 'is_pinned', 'notifications_enabled', 'joined_at']);
        });

        Schema::dropIfExists('message_reads');
        Schema::dropIfExists('message_reactions');

        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['parent_id', 'forwarded_from', 'type', 'file_path', 'file_name', 'file_size', 'file_mime', 'metadata', 'edited_at']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['group_type', 'class_id']);
        });
    }
};
