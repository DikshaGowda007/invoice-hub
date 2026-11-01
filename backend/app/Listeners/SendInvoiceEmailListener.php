<?php

namespace App\Listeners;

use App\Constants\InvoiceActivityConstants;
use App\Events\InvoiceSent;
use App\Mail\InvoiceSentMail;
use App\Modules\V1\Invoice\Helpers\InvoiceActivityHelper;
use App\Modules\V1\Invoice\Helpers\InvoiceBusinessHelper;
use App\Repositories\V1\InvoiceActivityRepository;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendInvoiceEmailListener implements ShouldQueue
{
    public function __construct(
        private InvoiceActivityHelper $invoiceActivityHelper,
        private readonly InvoiceActivityRepository $invoiceActivityRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
        private readonly InvoiceBusinessHelper $invoiceBusinessHelper,
    ) {}

    public function handle(InvoiceSent $event): void
    {
        $invoice = collect($this->invoiceRepository->findByIdAndUserId($event->invoiceId, $event->userId)->first());
        $client = collect($invoice->get('client'));

        if (! $client->get('email')) {
            Log::warning('Invoice email not sent — client has no email on file.', [
                'invoice_id' => $event->invoiceId,
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_INVOICE_EMAIL_FAILED, 'Invoice not emailed — client has no email on file.');

            return;
        }

        try {
            $lineItems = $this->invoiceLineItemRepository->findByInvoiceId($event->invoiceId)
                ->map(fn ($lineItem) => collect($lineItem));
            $business = $this->invoiceBusinessHelper->format($event->userId);

            Mail::to($client->get('email'))->send(new InvoiceSentMail($invoice, $client, $lineItems, $business));

            Log::info('Invoice email sent.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $client->get('email'),
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_INVOICE_EMAILED, 'Invoice emailed to '.$client->get('email').'.');
        } catch (Throwable $e) {
            report($e);
            Log::error('Invoice email failed to send.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $client->get('email'),
                'exception' => $e->getMessage(),
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_INVOICE_EMAIL_FAILED, 'Invoice email failed to send.');
        }
    }

    private function logOutcome(InvoiceSent $event, string $type, string $description): void
    {
        $this->invoiceActivityRepository->insert($this->invoiceActivityHelper->prepareDao(
            $event->invoiceId,
            $event->userId,
            $type,
            $description,
        ));
    }
}
