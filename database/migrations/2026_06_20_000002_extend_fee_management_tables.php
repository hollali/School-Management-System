<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('grade_level')->nullable();
            $table->string('academic_term')->nullable();
            $table->string('academic_year');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('fee_structure_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_category_id')->constrained('fee_categories')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['fee_structure_id', 'fee_category_id'], 'fee_structure_item_unique');
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('type'); // discount, scholarship, waiver
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('application', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 14, 2);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('fees', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->change();
            $table->decimal('paid_amount', 14, 2)->default(0)->after('amount');
            $table->decimal('balance', 14, 2)->default(0)->after('paid_amount');
            $table->foreignId('fee_structure_id')->nullable()->constrained('fee_structures')->nullOnDelete()->after('student_id');
            $table->foreignId('fee_category_id')->nullable()->constrained('fee_categories')->nullOnDelete()->after('fee_structure_id');
            $table->string('academic_term')->nullable()->after('fee_category_id');
            $table->string('academic_year')->nullable()->after('academic_term');
            $table->date('issue_date')->nullable()->after('academic_year');
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('payment_status');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('created_by');
            $table->softDeletes();
            $table->index('invoice_number');
            $table->index('payment_status');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_id')->constrained('fees')->cascadeOnDelete();
            $table->foreignId('fee_category_id')->constrained('fee_categories')->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 14, 2);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2);
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete()->after('fee_id');
            $table->foreignId('parent_id')->nullable()->constrained('parents')->nullOnDelete()->after('student_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('parent_id');
            $table->string('payment_type')->default('manual')->after('user_id');
            $table->string('transaction_id')->nullable()->unique()->after('reference');
            $table->string('channel')->nullable()->after('transaction_id');
            $table->text('notes')->nullable()->after('channel');
            $table->index('transaction_id');
            $table->index('payment_type');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->string('receipt_number')->unique()->nullable()->change();
            $table->decimal('amount', 14, 2)->default(0)->after('receipt_number');
            $table->string('payment_method')->nullable()->after('amount');
            $table->string('transaction_reference')->nullable()->after('payment_method');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('generated_by');
            $table->dropColumn(['amount', 'payment_method', 'transaction_reference']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['payment_type']);
            $table->dropColumn(['student_id', 'parent_id', 'user_id', 'payment_type', 'transaction_id', 'channel', 'notes']);
        });

        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('fee_structure_items');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_categories');

        Schema::table('fees', function (Blueprint $table) {
            $table->dropIndex(['invoice_number']);
            $table->dropIndex(['payment_status']);
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('fee_structure_id');
            $table->dropConstrainedForeignId('fee_category_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('updated_by');
            $table->dropColumn(['paid_amount', 'balance', 'academic_term', 'academic_year', 'issue_date', 'payment_status']);
        });
    }
};
