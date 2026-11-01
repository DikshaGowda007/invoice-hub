<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvoiceOverdue
{
    use Dispatchable;

    public function __construct(
        public readonly int $invoiceId,
        public readonly int $userId,
    ) {}
}
