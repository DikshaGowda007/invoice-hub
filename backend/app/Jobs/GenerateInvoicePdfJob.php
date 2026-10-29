<?php

namespace App\Jobs;

use App\Constants\InvoiceConstants;
use App\Modules\V1\Invoice\Helpers\InvoiceBusinessHelper;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const STATUS_COLORS = [
        InvoiceConstants::STATUS_DRAFT => ['text' => '#7a7168'],
        InvoiceConstants::STATUS_SENT => ['text' => '#3d6fd1'],
        InvoiceConstants::STATUS_PAID => ['text' => '#3f7d4f'],
        InvoiceConstants::STATUS_OVERDUE => ['text' => '#c1483f'],
        InvoiceConstants::STATUS_CANCELLED => ['text' => '#7a7168'],
    ];

    public function __construct(
        private readonly string $requestId,
        private readonly int $invoiceId,
        private readonly int $userId,
    ) {}

    public function handle(InvoiceRepository $invoiceRepository, InvoiceLineItemRepository $invoiceLineItemRepository, InvoiceBusinessHelper $invoiceBusinessHelper): void
    {
        $invoice = $invoiceRepository->findByIdAndUserId($this->invoiceId, $this->userId)->first();

        if (! $invoice) {
            Cache::put($this->cacheKey(), ['status' => 'failed', 'user_id' => $this->userId], now()->addMinutes(10));

            return;
        }

        $invoice = collect($invoice);
        $lineItems = $invoiceLineItemRepository->findByInvoiceId($this->invoiceId)->map(fn ($lineItem) => collect($lineItem));
        $business = $invoiceBusinessHelper->format($this->userId);

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'client' => collect($invoice->get('client')),
            'lineItems' => $lineItems,
            'business' => $business,
            'businessInitials' => $this->initials($business->get('displayName')),
            'statusColors' => self::STATUS_COLORS[$invoice->get('status')] ?? self::STATUS_COLORS[InvoiceConstants::STATUS_DRAFT],
        ])->setPaper('a4');

        Cache::put($this->cacheKey(), [
            'status' => 'ready',
            'user_id' => $this->userId,
            'content' => base64_encode($pdf->output()),
            'filename' => $invoice->get('invoice_number').'.pdf',
        ], now()->addMinutes(10));
    }

    private function cacheKey(): string
    {
        return "invoice-pdf:{$this->requestId}";
    }

    private function initials(string $name): string
    {
        $words = array_filter(explode(' ', trim($name)));
        $initials = array_map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)), $words);

        return mb_substr(implode('', $initials), 0, 2) ?: '—';
    }
}
