<?php

namespace App\Jobs;

use App\Modules\V1\Invoice\Helpers\InvoiceBusinessHelper;
use App\Modules\V1\Invoice\Helpers\InvoicePdfHelper;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $requestId,
        private readonly int $invoiceId,
        private readonly int $userId,
    ) {}

    public function handle(InvoiceRepository $invoiceRepository, InvoiceLineItemRepository $invoiceLineItemRepository, InvoiceBusinessHelper $invoiceBusinessHelper, InvoicePdfHelper $invoicePdfHelper): void
    {
        $invoice = $invoiceRepository->findByIdAndUserId($this->invoiceId, $this->userId)->first();

        if (! $invoice) {
            Cache::put($this->cacheKey(), ['status' => 'failed', 'user_id' => $this->userId], now()->addMinutes(10));

            return;
        }

        $invoice = collect($invoice);
        $client = collect($invoice->get('client'));
        $lineItems = $invoiceLineItemRepository->findByInvoiceId($this->invoiceId)->map(fn ($lineItem) => collect($lineItem));
        $business = $invoiceBusinessHelper->format($this->userId);

        $pdf = $invoicePdfHelper->render($invoice, $client, $lineItems, $business);

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
}
