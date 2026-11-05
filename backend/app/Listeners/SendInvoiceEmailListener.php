<?php

namespace App\Listeners;

use App\Constants\InvoiceActivityConstants;
use App\Events\InvoiceSent;
use App\Mail\InvoiceSentMail;
use App\Modules\V1\Invoice\Dto\InvoiceSentMailDto;
use App\Modules\V1\Invoice\Helpers\InvoiceActivityHelper;
use App\Modules\V1\Invoice\Helpers\InvoiceBusinessHelper;
use App\Modules\V1\Invoice\Helpers\InvoicePdfHelper;
use App\Repositories\V1\InvoiceActivityRepository;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
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
        private readonly InvoicePdfHelper $invoicePdfHelper,
    ) {}

    public function handle(InvoiceSent $event): void
    {
        $invoice = collect($this->invoiceRepository->findByIdAndUserId($event->invoiceId, $event->userId)->first());
        $client = collect($invoice->get('client'));
        $email = $client->get('email');

        if (! $email) {
            Log::warning('Invoice email not sent — client has no email on file.', [
                'invoice_id' => $event->invoiceId,
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_INVOICE_EMAIL_FAILED, 'Invoice not emailed — client has no email on file.');

            return;
        }

        try {
            $this->sendEmail($event, $invoice, $client, $email);

            Log::info('Invoice email sent.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $email,
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_INVOICE_EMAILED, 'Invoice emailed to '.$email.'.');
        } catch (Throwable $e) {
            report($e);
            Log::error('Invoice email failed to send.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $email,
                'exception' => $e->getMessage(),
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_INVOICE_EMAIL_FAILED, 'Invoice email failed to send.');
        }
    }

    private function sendEmail(InvoiceSent $event, Collection $invoice, Collection $client, string $email): void
    {
        $lineItems = $this->invoiceLineItemRepository->findByInvoiceId($event->invoiceId)
            ->map(fn ($lineItem) => collect($lineItem));
        $business = $this->invoiceBusinessHelper->format($event->userId);
        $pdf = $this->invoicePdfHelper->render($invoice, $client, $lineItems, $business);

        $dto = (new InvoiceSentMailDto)
            ->setClientName($client->get('name'))
            ->setInvoiceNumber($invoice->get('invoice_number'))
            ->setTotal((float) $invoice->get('total'))
            ->setDueDate($invoice->get('due_date'))
            ->setBusinessName($business->get('displayName'))
            ->setPdfContent($pdf->output());

        Mail::to($email)->send(new InvoiceSentMail($dto));
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
