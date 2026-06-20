<?php

namespace App\Services;

use App\Events\FeeInvoiceGenerated;
use App\Events\FeePaymentMade;
use App\Helpers\ActivityLogger;
use App\Models\Discount;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeService
{
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd');
        $last = Fee::where('invoice_number', 'like', "{$prefix}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextId = $last ? (int) explode('-', $last->invoice_number)[2] + 1 : 1;

        return "{$prefix}-" . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function generateReceiptNumber(): string
    {
        $prefix = 'RCP-' . now()->format('Ymd');
        $last = Receipt::where('receipt_number', 'like', "{$prefix}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextId = $last ? (int) explode('-', $last->receipt_number)[2] + 1 : 1;

        return "{$prefix}-" . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function createInvoice(Student $student, FeeStructure $structure, array $overrides = []): Fee
    {
        return DB::transaction(function () use ($student, $structure, $overrides) {
            $totalAmount = $structure->items->sum('amount');

            $applicableDiscounts = $this->getApplicableDiscounts($student);
            $totalDiscount = $this->calculateTotalDiscount($totalAmount, $applicableDiscounts);

            $netAmount = max(0, $totalAmount - $totalDiscount);

            $fee = Fee::create([
                'student_id' => $student->id,
                'fee_structure_id' => $structure->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'amount' => $totalAmount,
                'paid_amount' => 0,
                'balance' => $netAmount,
                'due_date' => $overrides['due_date'] ?? null,
                'issue_date' => $overrides['issue_date'] ?? now(),
                'academic_term' => $structure->academic_term,
                'academic_year' => $structure->academic_year,
                'status' => 'active',
                'payment_status' => 'unpaid',
                'description' => $overrides['description'] ?? $structure->name,
                'created_by' => Auth::id(),
            ]);

            foreach ($structure->items as $item) {
                $itemDiscount = $this->calculateItemDiscount($item->amount, $totalAmount, $totalDiscount);
                InvoiceItem::create([
                    'fee_id' => $fee->id,
                    'fee_category_id' => $item->fee_category_id,
                    'description' => $item->description ?? $item->category?->name ?? 'Fee',
                    'amount' => $item->amount,
                    'discount_amount' => $itemDiscount,
                    'net_amount' => $item->amount - $itemDiscount,
                ]);
            }

            ActivityLogger::log('invoice-generated', 'Fee', $fee->id,
                "Generated invoice {$fee->invoice_number} for {$student->user?->name} (\${$netAmount})");

            event(new FeeInvoiceGenerated($fee));

            return $fee->fresh()->load(['items.category', 'student.user']);
        });
    }

    public function processPayment(Fee $fee, float $amount, string $method, array $extra = []): Payment
    {
        return DB::transaction(function () use ($fee, $amount, $method, $extra) {
            $payment = Payment::create([
                'fee_id' => $fee->id,
                'student_id' => $fee->student_id,
                'parent_id' => $extra['parent_id'] ?? null,
                'user_id' => $extra['user_id'] ?? Auth::id(),
                'amount' => $amount,
                'paid_at' => $extra['paid_at'] ?? now(),
                'method' => $method,
                'reference' => $extra['reference'] ?? null,
                'transaction_id' => $extra['transaction_id'] ?? null,
                'channel' => $extra['channel'] ?? null,
                'payment_type' => $extra['payment_type'] ?? 'manual',
                'status' => $extra['status'] ?? 'completed',
                'notes' => $extra['notes'] ?? null,
            ]);

            $this->updateInvoiceBalance($fee);

            $receipt = Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => $this->generateReceiptNumber(),
                'amount' => $amount,
                'payment_method' => $method,
                'transaction_reference' => $extra['transaction_id'] ?? $extra['reference'] ?? null,
                'issued_at' => now(),
                'generated_by' => Auth::id(),
            ]);

            ActivityLogger::log('payment-processed', 'Payment', $payment->id,
                "Payment of \${$amount} received for invoice {$fee->invoice_number}");

            event(new FeePaymentMade($payment));

            return $payment->fresh()->load(['receipt', 'fee.student.user']);
        });
    }

    public function updateInvoiceBalance(Fee $fee): Fee
    {
        $totalPaid = Payment::where('fee_id', $fee->id)
            ->where('status', 'completed')
            ->sum('amount');

        $balance = max(0, $fee->amount - $totalPaid);

        $paymentStatus = match (true) {
            $totalPaid <= 0 => 'unpaid',
            $balance <= 0 => 'paid',
            default => 'partial',
        };

        $fee->update([
            'paid_amount' => $totalPaid,
            'balance' => $balance,
            'payment_status' => $paymentStatus,
        ]);

        if ($balance <= 0 && $totalPaid > 0) {
            ActivityLogger::log('invoice-paid', 'Fee', $fee->id,
                "Invoice {$fee->invoice_number} fully paid");
        }

        return $fee->fresh();
    }

    public function bulkGenerateInvoices(int $structureId, ?array $studentIds = null): Collection
    {
        $structure = FeeStructure::with('items.category')->findOrFail($structureId);

        $students = $studentIds
            ? Student::whereIn('id', $studentIds)->with('user')->get()
            : $this->getStudentsForStructure($structure);

        $invoices = collect();

        foreach ($students as $student) {
            $existing = Fee::where('student_id', $student->id)
                ->where('fee_structure_id', $structureId)
                ->where('academic_term', $structure->academic_term)
                ->where('academic_year', $structure->academic_year)
                ->first();

            if (!$existing) {
                $invoices->push($this->createInvoice($student, $structure));
            }
        }

        return $invoices;
    }

    public function getApplicableDiscounts(Student $student): Collection
    {
        return Discount::active()
            ->where(function ($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->orWhereHas('schoolClass', function ($cq) use ($student) {
                        $cq->whereHas('students', fn($sq) => $sq->where('student_id', $student->id));
                    })
                    ->orWhereNull('student_id')->whereNull('class_id');
            })
            ->get();
    }

    private function calculateTotalDiscount(float $totalAmount, Collection $discounts): float
    {
        $totalDiscount = 0;

        foreach ($discounts as $discount) {
            if ($discount->application === 'percentage') {
                $totalDiscount += $totalAmount * ($discount->value / 100);
            } else {
                $totalDiscount += $discount->value;
            }
        }

        return min($totalDiscount, $totalAmount);
    }

    private function calculateItemDiscount(float $itemAmount, float $totalAmount, float $totalDiscount): float
    {
        if ($totalAmount <= 0) return 0;
        return round(($itemAmount / $totalAmount) * $totalDiscount, 2);
    }

    private function getStudentsForStructure(FeeStructure $structure): Collection
    {
        $query = Student::with('user')->whereHas('classes');

        if ($structure->class_id) {
            $query->whereHas('classes', fn($q) => $q->where('classes.id', $structure->class_id));
        }

        if ($structure->grade_level) {
            $query->whereHas('classes', fn($q) => $q->where('classes.grade_level', $structure->grade_level));
        }

        return $query->get();
    }

    public function getStudentFeeSummary(Student $student): array
    {
        $invoices = Fee::where('student_id', $student->id)->get();

        return [
            'total_invoiced' => $invoices->sum('amount'),
            'total_paid' => $invoices->sum('paid_amount'),
            'total_balance' => $invoices->sum('balance'),
            'invoice_count' => $invoices->count(),
            'paid_count' => $invoices->where('payment_status', 'paid')->count(),
            'partial_count' => $invoices->where('payment_status', 'partial')->count(),
            'unpaid_count' => $invoices->where('payment_status', 'unpaid')->count(),
            'overdue_count' => $invoices->filter(fn($i) => $i->payment_status !== 'paid' && $i->due_date && $i->due_date->isPast())->count(),
        ];
    }

    public function getOverallRevenueSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Payment::where('status', 'completed');

        if ($startDate) $query->where('paid_at', '>=', $startDate);
        if ($endDate) $query->where('paid_at', '<=', $endDate);

        $totalRevenue = $query->sum('amount');
        $parentPayments = (clone $query)->whereNotNull('parent_id')->sum('amount');
        $studentPayments = (clone $query)->whereNull('parent_id')->whereNotNull('user_id')->sum('amount');
        $manualPayments = (clone $query)->where('payment_type', 'manual')->sum('amount');
        $onlinePayments = (clone $query)->where('payment_type', '!=', 'manual')->sum('amount');

        return [
            'total_revenue' => $totalRevenue,
            'parent_payments' => $parentPayments,
            'student_payments' => $studentPayments,
            'manual_payments' => $manualPayments,
            'online_payments' => $onlinePayments,
            'transaction_count' => $query->count(),
        ];
    }

    public function getOutstandingSummary(): array
    {
        $totalOutstanding = Fee::whereIn('payment_status', ['unpaid', 'partial'])->sum('balance');
        $overdueTotal = Fee::whereIn('payment_status', ['unpaid', 'partial'])
            ->where('due_date', '<', now())
            ->sum('balance');

        return [
            'total_outstanding' => $totalOutstanding,
            'overdue_total' => $overdueTotal,
            'unpaid_invoices' => Fee::where('payment_status', 'unpaid')->count(),
            'partial_invoices' => Fee::where('payment_status', 'partial')->count(),
            'overdue_invoices' => Fee::overdue()->count(),
        ];
    }

    public function getClassFinancialSummary(int $classId): array
    {
        $studentIds = Student::whereHas('classes', fn($q) => $q->where('classes.id', $classId))
            ->pluck('id');

        $invoices = Fee::whereIn('student_id', $studentIds)->get();

        return [
            'total_invoiced' => $invoices->sum('amount'),
            'total_collected' => $invoices->sum('paid_amount'),
            'total_outstanding' => $invoices->sum('balance'),
            'student_count' => $studentIds->count(),
            'paid_count' => $invoices->where('payment_status', 'paid')->count(),
            'partial_count' => $invoices->where('payment_status', 'partial')->count(),
            'unpaid_count' => $invoices->where('payment_status', 'unpaid')->count(),
        ];
    }
}
