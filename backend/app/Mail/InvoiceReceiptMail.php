<?php

namespace App\Mail;

use App\Modules\V1\Invoice\Dto\InvoiceReceiptMailDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public InvoiceReceiptMailDto $dto,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment received for invoice '.$this->dto->getInvoiceNumber(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice_receipt',
            text: 'emails.invoice_receipt_text',
            with: [
                'clientName' => $this->dto->getClientName(),
                'invoiceNumber' => $this->dto->getInvoiceNumber(),
                'total' => $this->dto->getTotal(),
                'issueDate' => $this->dto->getIssueDate(),
            ],
        );
    }
}
