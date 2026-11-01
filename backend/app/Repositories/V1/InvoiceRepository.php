<?php

namespace App\Repositories\V1;

use App\Models\Invoice;
use App\Repositories\DAO\V1\InvoiceDAO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface InvoiceRepository
{
    public function insert(InvoiceDAO $invoiceDao): Invoice;

    public function findByUserId(int $userId): Collection;

    public function findByIdAndUserId(int $id, int $userId): Collection;

    public function findByRecurringInvoiceIdAndUserId(int $recurringInvoiceId, int $userId): Collection;

    public function updateById(int $id, InvoiceDAO $invoiceDao): bool;

    public function findByUserIdAndStatusAndSince(int $userId, string $status, string $since): SupportCollection;

    public function findRecentByUserIdAndLimit(int $userId, int $limit): Collection;

    public function findByRazorpayOrderId(string $razorpayOrderId): Collection;

    public function findByStatusAndDueDate(string $status, string $date): Collection;

    public function findByPublicToken(string $publicToken): Collection;
}
