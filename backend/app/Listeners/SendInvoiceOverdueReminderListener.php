<?php

namespace App\Listeners;

use App\Constants\InvoiceActivityConstants;
use App\Events\InvoiceOverdue;
use App\Mail\InvoiceOverdueReminderMail;
use App\Modules\V1\Invoice\Dto\InvoiceOverdueReminderMailDto;
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

class SendInvoiceOverdueReminderListener implements ShouldQueue
{
    public function __construct(
        private InvoiceActivityHelper $invoiceActivityHelper,
        private readonly InvoiceActivityRepository $invoiceActivityRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
        private readonly InvoiceBusinessHelper $invoiceBusinessHelper,
        private readonly InvoicePdfHelper $invoicePdfHelper,
    ) {}

    public function handle(InvoiceOverdue $event): void
    {
        $invoice = collect($this->invoiceRepository->findByIdAndUserId($event->invoiceId, $event->userId)->first());
        $client = collect($invoice->get('client'));
        $email = $client->get('email');

        if (! $email) {
            Log::warning('Invoice overdue reminder not sent — client has no email on file.', [
                'invoice_id' => $event->invoiceId,
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_OVERDUE_REMINDER_FAILED, 'Overdue reminder not sent — client has no email on file.');

            return;
        }

        try {
            $this->sendReminder($event, $invoice, $client, $email);

            Log::info('Invoice overdue reminder sent.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $email,
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_OVERDUE_REMINDER_SENT, 'Overdue reminder emailed to '.$email.'.');
        } catch (Throwable $e) {
            report($e);
            Log::error('Invoice overdue reminder failed to send.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $email,
                'exception' => $e->getMessage(),
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_OVERDUE_REMINDER_FAILED, 'Overdue reminder email failed to send.');
        }
    }

    private function sendReminder(InvoiceOverdue $event, Collection $invoice, Collection $client, string $email): void
    {
        $lineItems = $this->invoiceLineItemRepository->findByInvoiceId($event->invoiceId)
            ->map(fn ($lineItem) => collect($lineItem));
        $business = $this->invoiceBusinessHelper->format($event->userId);
        $pdf = $this->invoicePdfHelper->render($invoice, $client, $lineItems, $business);

        $dto = (new InvoiceOverdueReminderMailDto)
            ->setClientName($client->get('name'))
            ->setInvoiceNumber($invoice->get('invoice_number'))
            ->setTotal((float) $invoice->get('total'))
            ->setDueDate($invoice->get('due_date'))
            ->setBusinessName($business->get('displayName'))
            ->setPdfContent($pdf->output());

        Mail::to($email)->send(new InvoiceOverdueReminderMail($dto));
    }

    private function logOutcome(InvoiceOverdue $event, string $type, string $description): void
    {
        $this->invoiceActivityRepository->insert($this->invoiceActivityHelper->prepareDao(
            $event->invoiceId,
            $event->userId,
            $type,
            $description,
        ));
    }
}
