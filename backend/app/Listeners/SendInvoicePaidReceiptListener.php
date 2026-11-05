<?php

namespace App\Listeners;

use App\Constants\InvoiceActivityConstants;
use App\Events\InvoicePaid;
use App\Mail\InvoiceReceiptMail;
use App\Modules\V1\Invoice\Dto\InvoiceReceiptMailDto;
use App\Modules\V1\Invoice\Helpers\InvoiceActivityHelper;
use App\Repositories\V1\InvoiceActivityRepository;
use App\Repositories\V1\InvoiceRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendInvoicePaidReceiptListener implements ShouldQueue
{
    public function __construct(
        private InvoiceActivityHelper $invoiceActivityHelper,
        private readonly InvoiceActivityRepository $invoiceActivityRepository,
        private readonly InvoiceRepository $invoiceRepository,
    ) {}

    public function handle(InvoicePaid $event): void
    {
        $invoice = collect($this->invoiceRepository->findByIdAndUserId($event->invoiceId, $event->userId)->first());
        $client = collect($invoice->get('client'));
        $email = $client->get('email');

        if (! $email) {
            Log::warning('Invoice receipt not sent — client has no email on file.', [
                'invoice_id' => $event->invoiceId,
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_RECEIPT_FAILED, 'Receipt not sent — client has no email on file.');

            return;
        }

        try {
            $this->sendReceipt($invoice, $client, $email);

            Log::info('Invoice receipt email sent.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $email,
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_RECEIPT_SENT, 'Receipt emailed to '.$email.'.');
        } catch (Throwable $e) {
            report($e);
            Log::error('Invoice receipt email failed to send.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $email,
                'exception' => $e->getMessage(),
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_RECEIPT_FAILED, 'Receipt email failed to send.');
        }
    }

    private function sendReceipt(Collection $invoice, Collection $client, string $email): void
    {
        $dto = (new InvoiceReceiptMailDto)
            ->setClientName($client->get('name'))
            ->setInvoiceNumber($invoice->get('invoice_number'))
            ->setTotal((float) $invoice->get('total'))
            ->setIssueDate($invoice->get('issue_date'));

        Mail::to($email)->send(new InvoiceReceiptMail($dto));
    }

    private function logOutcome(InvoicePaid $event, string $type, string $description): void
    {
        $this->invoiceActivityRepository->insert($this->invoiceActivityHelper->prepareDao(
            $event->invoiceId,
            $event->userId,
            $type,
            $description,
        ));
    }
}
