<?php

namespace App\Modules\V1\Invoice\Helpers;

use App\Constants\InvoiceConstants;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfDocument;
use Illuminate\Support\Collection;

class InvoicePdfHelper
{
    private const STATUS_COLORS = [
        InvoiceConstants::STATUS_DRAFT => ['text' => '#7a7168'],
        InvoiceConstants::STATUS_SENT => ['text' => '#3d6fd1'],
        InvoiceConstants::STATUS_PAID => ['text' => '#3f7d4f'],
        InvoiceConstants::STATUS_OVERDUE => ['text' => '#c1483f'],
        InvoiceConstants::STATUS_CANCELLED => ['text' => '#7a7168'],
    ];

    public function render(Collection $invoice, Collection $client, Collection $lineItems, Collection $business): PdfDocument
    {
        return Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'client' => $client,
            'lineItems' => $lineItems,
            'business' => $business,
            'businessInitials' => $this->initials($business->get('displayName')),
            'statusColors' => self::STATUS_COLORS[$invoice->get('status')] ?? self::STATUS_COLORS[InvoiceConstants::STATUS_DRAFT],
        ])->setPaper('a4');
    }

    private function initials(string $name): string
    {
        $words = array_filter(explode(' ', trim($name)));
        $initials = array_map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)), $words);

        return mb_substr(implode('', $initials), 0, 2) ?: '—';
    }
}
