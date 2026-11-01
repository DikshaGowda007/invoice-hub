<?php

namespace App\Repositories\MySql\V1;

use App\Constants\CommonConstant;
use App\Models\Invoice;
use App\Models\InvoiceStatusHistory;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\V1\InvoiceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class InvoiceRepositoryImpl implements InvoiceRepository
{
    public function insert(InvoiceDAO $invoiceDao): Invoice
    {
        return Invoice::create($invoiceDao->toArray());
    }

    public function findByUserId(int $userId): Collection
    {
        return Invoice::with('client')
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByIdAndUserId(int $id, int $userId): Collection
    {
        return Invoice::with('client')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByRecurringInvoiceIdAndUserId(int $recurringInvoiceId, int $userId): Collection
    {
        return Invoice::where('recurring_invoice_id', $recurringInvoiceId)
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function updateById(int $id, InvoiceDAO $invoiceDao): bool
    {
        return Invoice::where('id', $id)->update($invoiceDao->toArray());
    }

    public function findByUserIdAndStatusAndSince(int $userId, string $status, string $since): SupportCollection
    {
        return InvoiceStatusHistory::from('invoice_status_histories as ish')
            ->join('invoices as inv', 'inv.id', '=', 'ish.invoice_id')
            ->select('inv.total', 'ish.created_at as paid_at')
            ->where('inv.user_id', $userId)
            ->where('inv.is_deleted', CommonConstant::IS_DELETED_NO)
            ->where('ish.new_status', $status)
            ->where('ish.created_at', '>=', $since)
            ->get();
    }

    public function findRecentByUserIdAndLimit(int $userId, int $limit): Collection
    {
        return Invoice::with('client')
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function findByRazorpayOrderId(string $razorpayOrderId): Collection
    {
        return Invoice::where('razorpay_order_id', $razorpayOrderId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByStatusAndDueDate(string $status, string $date): Collection
    {
        return Invoice::with('client')
            ->where('status', $status)
            ->where('due_date', '<', $date)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByPublicToken(string $publicToken): Collection
    {
        return Invoice::with('client')
            ->where('public_token', $publicToken)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }
}
